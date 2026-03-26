<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f7f9fc; color: #333; margin: 0; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: #ffffff; padding: 40px; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); text-align: center; border-top: 5px solid #a770ef; }
        .logo { font-size: 28px; font-weight: bold; color: #4a00e0; margin-bottom: 20px; }
        .title { font-size: 22px; color: #333; margin-bottom: 15px; }
        .desc { font-size: 16px; color: #666; line-height: 1.6; margin-bottom: 30px; }
        .code-box { background: #f4fdfb; border: 2px dashed #a770ef; padding: 20px; border-radius: 10px; font-size: 32px; font-weight: bold; letter-spacing: 5px; color: #4a00e0; margin-bottom: 30px; display: inline-block; min-width: 200px; }
        .footer { font-size: 13px; color: #999; margin-top: 30px; border-top: 1px solid #eee; padding-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">منصة منزلي</div>
        <h1 class="title">مرحباً بك في منصة منزلي! 🎉</h1>
        <p class="desc">شكراً لتسجيلك معنا. لإكمال عملية التسجيل وتفعيل حسابك، يرجى إدخال رمز التحقق التالي في صفحة التسجيل:</p>
        
        <div class="code-box">
            {{ $code }}
        </div>
        
        <p class="desc" style="font-size: 14px; color: #888;">هذا الرمز صالح لمدة 10 دقائق فقط. إذا لم تقم بطلب هذا الرمز، يمكنك تجاهل هذه الرسالة بأمان.</p>
        
        <div class="footer">
            &copy; {{ date('Y') }} منصة منزلي. جميع الحقوق محفوظة.
        </div>
    </div>
</body>
</html>
