@extends('layouts.app')

@section('title', __('messages.reset_password_btn'))

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
        <h2 class="auth-title">{{ __('messages.reset_password_btn') }}</h2>
        
        <div id="error-message" class="alert alert-error"></div>
        <div id="success-message" class="alert alert-success"></div>

        <form id="reset-password-form">
            <input type="hidden" id="token" value="{{ $token }}">
            
            <div class="form-group">
                <label for="email">{{ __('messages.email') }}</label>
                <input type="email" id="email" class="form-control" value="{{ $email }}" disabled>
            </div>
            
            <div class="form-group">
                <label for="password">{{ __('messages.new_password') }}</label>
                <div class="password-wrapper">
                    <input type="password" id="password" class="form-control" placeholder="{{ __('messages.enter_new_password') }}" required minlength="8">
                    <i class="fa-solid fa-eye" onclick="togglePassword(this, 'password')"></i>
                </div>
            </div>

            <div class="form-group">
                <label for="password_confirmation">{{ __('messages.confirm_new_password') }}</label>
                <div class="password-wrapper">
                    <input type="password" id="password_confirmation" class="form-control" placeholder="{{ __('messages.retype_password') }}" required minlength="8">
                    <i class="fa-solid fa-eye" onclick="togglePassword(this, 'password_confirmation')"></i>
                </div>
            </div>
            
            <button type="submit" class="btn-primary btn-block" id="submit-btn">
                <span>{{ __('messages.set_password_btn') }}</span> <i class="fa-solid fa-floppy-disk" style="margin-right: 8px;"></i>
            </button>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.getElementById('reset-password-form').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const token = document.getElementById('token').value;
        const email = document.getElementById('email').value;
        const password = document.getElementById('password').value;
        const password_confirmation = document.getElementById('password_confirmation').value;
        
        const submitBtn = document.getElementById('submit-btn');
        const errorDiv = document.getElementById('error-message');
        const successDiv = document.getElementById('success-message');
        
        errorDiv.style.display = 'none';
        successDiv.style.display = 'none';
        
        if (password !== password_confirmation) {
            errorDiv.style.display = 'block';
            errorDiv.innerHTML = '<i class="fa-solid fa-circle-exclamation"></i> كلمات المرور غير متطابقة.';
            return;
        }
        
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> جاري الحفظ...';
        
        try {
            const response = await fetch('/api/reset-password', {
                method: 'POST',
                headers: { 
                    'Content-Type': 'application/json', 
                    'Accept': 'application/json' 
                },
                body: JSON.stringify({ email, token, password, password_confirmation })
            });
            
            const data = await response.json();
            
            if (response.ok) {
                // Success branch
                successDiv.style.display = 'block';
                successDiv.innerHTML = '<i class="fa-solid fa-circle-check"></i> ' + (data.message || 'تم إعادة تعيين كلمة المرور بنجاح.');
                submitBtn.style.display = 'none'; // hide submit button
                
                setTimeout(() => {
                    window.location.href = '/login';
                }, 3000);
            } else {
                errorDiv.style.display = 'block';
                errorDiv.innerHTML = '<i class="fa-solid fa-circle-exclamation"></i> ' + (data.message || 'خطأ في عملية إعادة التعيين.');
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<span>تعيين كلمة المرور الدخول</span> <i class="fa-solid fa-floppy-disk" style="margin-right: 8px;"></i>';
            }
        } catch (error) {
            errorDiv.style.display = 'block';
            errorDiv.innerHTML = '<i class="fa-solid fa-triangle-exclamation"></i> حدث خطأ في الاتصال بالخادم.';
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<span>تعيين كلمة المرور الدخول</span> <i class="fa-solid fa-floppy-disk" style="margin-right: 8px;"></i>';
        }
    });
</script>
@endpush
