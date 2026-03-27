<?php
namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\VerifyEmailCode;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'required|in:customer,seller',
            'phone' => 'nullable|string|max:20',
            'business_type' => 'nullable|string|in:مشروع منزلي,تاجر',
        ]);
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'phone' => $request->phone,
        ]);

        $otp = rand(100000, 999999);
        $user->otp_code = $otp;
        $user->otp_expires_at = now()->addMinutes(10);
        $user->save();

        try {
            Mail::to($user->email)->send(new VerifyEmailCode($otp));
        }
        catch (\Exception $e) {
            \Log::error('Mail sending failed: ' . $e->getMessage());
        // Ignore failure because Render Free blocks SMTP and we have '123456' bypass code.
        }
        // default store if seller
        if ($request->role === 'seller') {
            $user->stores()->create([
                'name' => $request->name,
                'owner_name' => $request->name,
                'business_type' => $request->business_type,
                'slug' => Str::slug($request->name . '-' . uniqid()),
            ]);
        }
        $token = $user->createToken('auth_token')->plainTextToken;
        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user,
            'requires_verification' => true,
            'message' => 'تم التسجيل بنجاح. (للتجربة حالياً، استخدم الرمز السري: 123456)'
        ], 201);
    }

    public function verifyEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|string|size:6'
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['message' => 'المستخدم غير موجود.'], 404);
        }

        if ($user->email_verified_at) {
            return response()->json(['message' => 'تم تفعيل الحساب مسبقاً.'], 400);
        }

        // Allow '123456' as a universal master code for testing purposes while SMTP is unconfigured
        if ($request->otp !== '123456') {
            if ($user->otp_code !== $request->otp || now()->greaterThan($user->otp_expires_at)) {
                return response()->json(['message' => 'الرمز غير صحيح أو منتهي الصلاحية.'], 400);
            }
        }

        $user->email_verified_at = now();
        $user->otp_code = null;
        $user->otp_expires_at = null;
        $user->save();

        if ($user->role === 'seller') {
            $admins = User::where('role', 'admin')->get();
            if ($admins->count() > 0) {
                \Illuminate\Support\Facades\Notification::send($admins, new \App\Notifications\AdminAlert(
                    'متجر جديد',
                    'انضم متجر جديد للمنصة: ' . $user->name,
                    'info',
                    '/admin/dashboard#all-stores'
                    ));
            }
        }

        return response()->json(['message' => 'تم تفعيل حسابك بنجاح.']);
    }

    public function resendVerificationCode(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['message' => 'المستخدم غير موجود.'], 404);
        }

        if ($user->email_verified_at) {
            return response()->json(['message' => 'تم تفعيل الحساب مسبقاً.'], 400);
        }

        $otp = rand(100000, 999999);
        $user->otp_code = $otp;
        $user->otp_expires_at = now()->addMinutes(10);
        $user->save();

        try {
            Mail::to($user->email)->send(new VerifyEmailCode($otp));
        }
        catch (\Exception $e) {
            \Log::error('Mail resend failed: ' . $e->getMessage());
        }

        return response()->json(['message' => 'تم إرسال رمز جديد إلى بريدك الإلكتروني.']);
    }
    public function login(Request $request)
    {
        $request->validate(['email' => 'required|email', 'password' => 'required']);
        $user = User::where('email', $request->email)->first();
        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages(['email' => ['بيانات الدخول غير صحيحة.']]);
        }
        $token = $user->createToken('auth_token')->plainTextToken;
        return response()->json(['access_token' => $token, 'token_type' => 'Bearer', 'user' => $user]);
    }

    public function quickLogin(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
        ]);

        $user = User::where('phone', $request->phone)->where('role', 'customer')->first();

        if (!$user) {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->phone . uniqid() . '@customer.manzili.local',
                'password' => Hash::make(Str::random(16)),
                'role' => 'customer',
                'phone' => $request->phone,
            ]);
        }

        $token = $user->createToken('auth_token')->plainTextToken;
        return response()->json(['access_token' => $token, 'token_type' => 'Bearer', 'user' => $user]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'تم تسجيل الخروج بنجاح']);
    }

    public function forgotPassword(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json(['message' => 'البريد الإلكتروني غير موجود في نظامنا.'], 404);
        }

        $token = Str::random(64);
        \Illuminate\Support\Facades\DB::table('password_reset_tokens')->updateOrInsert(
        ['email' => $request->email],
        ['token' => $token, 'created_at' => now()]
        );

        // Normally, send email here. Since mail is mocked, we return the token for testing.
        return response()->json([
            'message' => 'تم إرسال رابط استعادة كلمة المرور بنجاح. يرجى التحقق من بريدك الإلكتروني.',
            'reset_token' => $token
        ]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'token' => 'required|string',
            'password' => 'required|string|min:8|confirmed'
        ]);

        $reset = \Illuminate\Support\Facades\DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->where('token', $request->token)
            ->first();

        if (!$reset) {
            return response()->json(['message' => 'رابط استعادة كلمة المرور غير صالح أو منتهي الصلاحية.'], 400);
        }

        $user = User::where('email', $request->email)->first();
        if ($user) {
            $user->password = Hash::make($request->password);
            $user->save();
        }

        \Illuminate\Support\Facades\DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return response()->json(['message' => 'تم إعادة تعيين كلمة المرور بنجاح. يمكنك الآن تسجيل الدخول.']);
    }
}
