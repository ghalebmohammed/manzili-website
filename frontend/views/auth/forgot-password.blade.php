@extends('layouts.app')

@section('title', __('messages.forgot_password_title'))

@push('styles')
<style>
    .auth-container {
        max-width: 500px;
        margin: 60px auto;
        background: var(--white);
        padding: 40px;
        border-radius: 10px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
    }
    .auth-title {
        text-align: center;
        color: var(--primary);
        font-size: 28px;
        margin-bottom: 20px;
        font-weight: 700;
    }
    .auth-description {
        text-align: center;
        color: var(--text-muted);
        margin-bottom: 30px;
        font-size: 15px;
        line-height: 1.6;
    }
    .form-group {
        margin-bottom: 20px;
    }
    .form-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: 500;
        color: var(--text-dark);
    }
    .form-control {
        width: 100%;
        padding: 12px 15px;
        border: 1px solid #ddd;
        border-radius: 5px;
        font-family: inherit;
        font-size: 16px;
        transition: border-color 0.3s;
        box-sizing: border-box;
    }
    .form-control:focus {
        border-color: var(--primary);
        outline: none;
        box-shadow: 0 0 0 2px rgba(140, 163, 121, 0.2);
    }
    .btn-block {
        display: block;
        width: 100%;
        text-align: center;
        font-size: 16px;
        padding: 12px;
        margin-top: 10px;
    }
    .auth-links {
        margin-top: 25px;
        text-align: center;
        font-size: 14px;
    }
    .auth-links a {
        color: var(--primary);
        text-decoration: none;
        font-weight: 500;
    }
    .auth-links a:hover {
        text-decoration: underline;
    }
    
    .alert {
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 20px;
        display: none;
        font-weight: 500;
    }
    .alert-error {
        background-color: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }
    .alert-success {
        background-color: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }
</style>
@endpush

@section('content')
<div class="container">
    <div class="auth-container">
        <h2 class="auth-title">{{ __('messages.forgot_password_title') }}</h2>
        <p class="auth-description">
            {{ __('messages.forgot_password_desc') }}
        </p>
        
        <div id="error-message" class="alert alert-error"></div>
        <div id="success-message" class="alert alert-success"></div>

        <form id="forgot-password-form">
            <div class="form-group">
                <label for="email">{{ __('messages.email') }}</label>
                <input type="email" id="email" class="form-control" placeholder="{{ __('messages.email_placeholder') }}" required>
            </div>
            
            <button type="submit" class="btn-primary btn-block" id="submit-btn">
                <span>{{ __('messages.send_reset_link') }}</span> <i class="fa-solid fa-paper-plane" style="margin-right: 8px;"></i>
            </button>
        </form>

        <div class="auth-links">
            <p>{{ __('messages.remembered_password') }} <a href="/login">{{ __('messages.back_to_login') }}</a></p>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.getElementById('forgot-password-form').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const email = document.getElementById('email').value;
        const submitBtn = document.getElementById('submit-btn');
        const errorDiv = document.getElementById('error-message');
        const successDiv = document.getElementById('success-message');
        
        errorDiv.style.display = 'none';
        successDiv.style.display = 'none';
        
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> جاري الإرسال...';
        
        try {
            const response = await fetch('/api/forgot-password', {
                method: 'POST',
                headers: { 
                    'Content-Type': 'application/json', 
                    'Accept': 'application/json' 
                },
                body: JSON.stringify({ email: email })
            });
            
            const data = await response.json();
            
            if (response.ok) {
                // Success branch
                successDiv.style.display = 'block';
                successDiv.innerHTML = '<i class="fa-solid fa-circle-check"></i> ' + (data.message || 'تم إرسال رابط استعادة كلمة المرور بنجاح. يرجى التحقق من بريدك الإلكتروني.');
                document.getElementById('email').value = '';
                submitBtn.innerHTML = '<span>إرسال رابط الاستعادة</span> <i class="fa-solid fa-check" style="margin-right: 8px;"></i>';
                
                // For demonstration purposes since Mail server is likely not fully set up in development:
                if (data.reset_token) {
                    successDiv.innerHTML += `<br><br><span style="font-size:12px; color:#666;">(للتجربة المباشرة للتطوير، الرابط: <a href="/reset-password?token=${data.reset_token}&email=${email}">اضغط هنا</a>)</span>`;
                }
            } else {
                errorDiv.style.display = 'block';
                errorDiv.innerHTML = '<i class="fa-solid fa-circle-exclamation"></i> ' + (data.message || 'البريد الإلكتروني غير موجود في نظامنا.');
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<span>إرسال رابط الاستعادة</span> <i class="fa-solid fa-paper-plane" style="margin-right: 8px;"></i>';
            }
        } catch (error) {
            errorDiv.style.display = 'block';
            errorDiv.innerHTML = '<i class="fa-solid fa-triangle-exclamation"></i> حدث خطأ في الاتصال بالخادم. يرجى المحاولة لاحقاً.';
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<span>إرسال رابط الاستعادة</span> <i class="fa-solid fa-paper-plane" style="margin-right: 8px;"></i>';
        }
    });
</script>
@endpush
