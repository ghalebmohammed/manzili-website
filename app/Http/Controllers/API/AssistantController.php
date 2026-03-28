<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Product;
use App\Models\Store;

class AssistantController extends Controller
{
    public function chat(Request $request)
    {
        $request->validate([
            'message' => 'required|string'
        ]);

        $message = $request->message;

        // Simple heuristic search on local DB before hitting external API to enrich context
        $productsContext = Product::where('name', 'like', "%{$message}%")
            ->orWhere('description', 'like', "%{$message}%")
            ->take(3)
            ->get(['name', 'price', 'slug'])->toArray();

        $storesContext = Store::where('name', 'like', "%{$message}%")
            ->orWhere('store_type', 'like', "%{$message}%")
            ->take(2)
            ->get(['name', 'store_type', 'slug'])->toArray();

        $sysPrompt = "أنت مساعد ذكي لمنصة 'منزلي' (منصة للأسر المنتجة والتجار). أجب المستخدم بلغة عربية ودودة ومختصرة. بناء على الردود التالية كبيانات مقترحة من المنصة إن وجدت: " . json_encode(compact('productsContext', 'storesContext'), JSON_UNESCAPED_UNICODE);

        $apiKey = env('GEMINI_API_KEY');

        if (!$apiKey || $apiKey === '') {
            $messageLower = mb_strtolower($message);

            // Greetings parsing
            $greetings = ['مرحبا', 'هلا', 'سلام', 'السلام عليكم', 'هاي', 'اهلين'];
            $isGreeting = false;
            foreach ($greetings as $g) {
                if (str_contains($messageLower, $g)) {
                    $isGreeting = true;
                    break;
                }
            }

            if ($isGreeting && count($productsContext) == 0 && count($storesContext) == 0) {
                return response()->json([
                    'reply' => 'أهلاً بك في منصة "منزلي" 👋!<br>كيف يمكنني مساعدتك اليوم؟ يمكنك سؤالي عن منتجات معينة (مثل فساتين، كعك، عطور) أو البحث عن متاجر محددة.'
                ]);
            }

            $replyHtml = '';

            if (count($productsContext) > 0 || count($storesContext) > 0) {
                $replyHtml .= 'لقد بحثت في المنصة ووجدت النتائج التالية لك:<br><br>';

                if (count($productsContext) > 0) {
                    $replyHtml .= '<strong>🎁 المنتجات المقترحة:</strong><ul>';
                    foreach ($productsContext as $p) {
                        $replyHtml .= "<li><a href='/products/{$p['slug']}' style='color:var(--primary); font-weight:bold;'>{$p['name']}</a> - {$p['price']} ريال</li>";
                    }
                    $replyHtml .= '</ul>';
                }

                if (count($storesContext) > 0) {
                    $replyHtml .= '<strong>🏪 متاجر قد تعجبك:</strong><ul>';
                    foreach ($storesContext as $s) {
                        $replyHtml .= "<li><a href='/stores/{$s['slug']}' style='color:var(--secondary); font-weight:bold;'>{$s['name']}</a> ({$s['store_type']})</li>";
                    }
                    $replyHtml .= '</ul>';
                }

                $replyHtml .= '<br>هل هناك شيء آخر يمكنني مساعدتك به؟ 😊';

                return response()->json([
                    'reply' => $replyHtml
                ]);
            }

            return response()->json([
                'reply' => 'عذراً، لم أتمكن من العثور على ما تبحث عنه بالضبط في المتجر حالياً. 🤔<br>يمكنك تصفح صفحة المتاجر أو المنتجات لرؤية جميع الخيارات المتاحة!'
            ]);
        }

        try {
            $response = Http::withoutVerifying()->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-pro:generateContent?key={$apiKey}", [
                'contents' => [
                    [
                        'role' => 'user',
                        'parts' => [
                            ['text' => $sysPrompt . "\n\nسؤال المستخدم: " . $message]
                        ]
                    ]
                ]
            ]);

            if ($response->successful()) {
                $replyText = $response->json('candidates.0.content.parts.0.text');
                return response()->json(['reply' => $replyText]);
            }

            return response()->json(['reply' => 'تفاصيل الخطأ من جوجل: ' . json_encode($response->json())], 500);

        }
        catch (\Exception $e) {
            return response()->json(['reply' => 'خطأ اتصال: ' . $e->getMessage()], 500);
        }
    }

    public function analyzeStore($slug)
    {
        $store = Store::where('slug', $slug)->with('products')->firstOrFail();

        $apiKey = env('GEMINI_API_KEY');
        if (!$apiKey) {
            return response()->json(['reply' => 'لم يتم إعداد مفتاح API للذكاء الاصطناعي حالياً. يمكن تفعيل هذه الميزة لاحقاً.']);
        }

        $storeProducts = $store->products->take(5)->pluck('name')->implode('، ');

        $prompt = "باعتبارك مساعد منزلي الذكي (Manzili AI)، قم بتحليل هذا المتجر للمستخدم بشكل احترافي وودي ومشجع باللغة العربية.\n";
        $prompt .= "اسم المتجر: {$store->name}\n";
        $prompt .= "نوع المتجر/نشاطه: {$store->store_type}\n";
        $prompt .= "عينة من منتجاته: {$storeProducts}\n";
        $prompt .= "الوصف: {$store->description}\n";
        $prompt .= "يرجى تقديم نظرة عامة عن المتجر، وأبرز ما يميزه بناءً على اسمه ومنتجاته (إن وجدت)، وقدم بعض النصائح البسيطة أو الاقتراحات للمستخدم لتجربة أفضل. اجعل الرد منسقاً باستخدام HTML بسيط جدا (مثل br لمسافة السطر، strong، ul، li) لكي يتم عرضه مباشرة بشكل جميل وجذاب.";

        try {
            $response = Http::withoutVerifying()->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-pro:generateContent?key={$apiKey}", [
                'contents' => [
                    [
                        'role' => 'user',
                        'parts' => [['text' => $prompt]]
                    ]
                ]
            ]);

            if ($response->successful()) {
                $replyText = $response->json('candidates.0.content.parts.0.text');
                return response()->json(['reply' => $replyText]);
            }
            return response()->json(['reply' => '<div style="color:red;">تفاصيل الخطأ: ' . json_encode($response->json()) . '</div>'], 500);
        }
        catch (\Exception $e) {
            return response()->json(['reply' => '<div style="color:red;">حدث خطأ في الاتصال مع خادم الذكاء الاصطناعي.</div>'], 500);
        }
    }

    public function analyzeProduct($slug)
    {
        $product = Product::where('slug', $slug)->with('store', 'category')->firstOrFail();

        $apiKey = env('GEMINI_API_KEY');
        if (!$apiKey) {
            return response()->json(['reply' => 'لم يتم إعداد مفتاح API للذكاء الاصطناعي حالياً. يمكن تفعيل هذه الميزة لاحقاً.']);
        }

        $similarProducts = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('status', 'active')
            ->take(3)
            ->pluck('name')
            ->implode('، ');

        $prompt = "باعتبارك مساعد منزلي الذكي (Manzili AI)، قم بإعطاء رأيك عن هذا المنتج بشكل مبدع، إيجابي، ومشجع للمشتري باللغة العربية.\n";
        $prompt .= "اسم المنتج: {$product->name}\n";
        $prompt .= "السعر: {$product->price} ريال\n";
        $prompt .= "التصنيف: " . ($product->category ? $product->category->name_ar : 'غير محدد') . "\n";
        $prompt .= "وصف المنتج: {$product->description}\n";
        $prompt .= "المتجر المقدم: " . ($product->store ? $product->store->name : 'غير محدد') . "\n";
        if ($similarProducts) {
            $prompt .= "وهناك منتجات أخرى مشابهة في المنصة قد تعجبك مثل: {$similarProducts}\n";
        }

        $prompt .= "قدم شرح بسيط وجذاب ولماذا قد يكون هذا المنتج مناسباً والفوائد والقيمة المضافة المتوقعة (رأيك الخاص بشكل ذكي)، وإذا كان هناك منتجات مشابهة تم تمريرها اذكر له بإيجاز أنه قد يعجبه تصفحها أيضاً. الرد يجب أن يكون منسق بـ HTML بسيط جدا (مثل br لمسافة السطر، strong، ul، li) وليكن جذاباً بصرياً ولا تضع أكواد برمجية في المخرجات أو علامات Markdown بل أعد نصاً جاهزاً للطباعة باستخدام HTML.";

        try {
            $response = Http::withoutVerifying()->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-pro:generateContent?key={$apiKey}", [
                'contents' => [
                    [
                        'role' => 'user',
                        'parts' => [['text' => $prompt]]
                    ]
                ]
            ]);

            if ($response->successful()) {
                $replyText = $response->json('candidates.0.content.parts.0.text');
                // Remove Markdown block if genAI still outputted it
                $replyText = preg_replace('/```html\n?/', '', $replyText);
                $replyText = preg_replace('/```\n?/', '', $replyText);
                return response()->json(['reply' => $replyText]);
            }
            return response()->json(['reply' => '<div style="color:red;">تفاصيل الخطأ: ' . json_encode($response->json()) . '</div>'], 500);
        }
        catch (\Exception $e) {
            return response()->json(['reply' => '<div style="color:red;">حدث خطأ في الاتصال مع خادم الذكاء الاصطناعي.</div>'], 500);
        }
    }
}
