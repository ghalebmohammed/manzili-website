@extends('layouts.app')

@section('title', 'تسجيل الدخول')

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
        margin-bottom: 30px;
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
    .auth-links {
        margin-top: 20px;
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
    .alert-error {
        background-color: #f8d7da;
        color: #721c24;
        padding: 12px;
        border-radius: 5px;
        margin-bottom: 20px;
        display: none;
    }
    .tabs {
        display: flex;
        border-bottom: 2px solid #eee;
        margin-bottom: 20px;
    }
    .tab-btn {
        flex: 1;
        background: none;
        border: none;
        padding: 15px;
        font-size: 16px;
        font-weight: 600;
        color: var(--text-muted);
        cursor: pointer;
        border-bottom: 3px solid transparent;
        transition: all 0.3s;
    }
    .tab-btn.active {
        color: var(--primary);
        border-bottom-color: var(--primary);
    }
    .tab-content {
        display: none;
    }
    .tab-content.active {
        display: block;
    }
</style>
@endpush

@section('content')
<div class="container">
    <div class="auth-container">
        <div style="text-align: center; margin-bottom: 15px;">
            <a href="/"><img src="/images/logo.png" alt="منزلي" style="height: 120px; width: auto; object-fit: contain;"></a>
        </div>
        <h2 class="auth-title">{{ __('messages.login_title') }}</h2>
        
        <div class="tabs">
            <button class="tab-btn active" onclick="switchTab('seller-login')">{{ __('messages.seller_admin_login') }}</button>
            <button class="tab-btn" onclick="switchTab('customer-login')">{{ __('messages.quick_customer_login') }}</button>
        </div>
        
        <div id="error-message" class="alert-error"></div>

        <!-- Seller/Admin Login -->
        <div id="seller-login" class="tab-content active">
            <form id="login-form">
                <div class="form-group">
                    <label for="email">{{ __('messages.email') }}</label>
                    <input type="email" id="email" class="form-control" placeholder="{{ __('messages.email_placeholder') }}" required>
                </div>
                
                <div class="form-group">
                    <label for="password">{{ __('messages.password') }}</label>
                    <div class="password-wrapper">
                        <input type="password" id="password" class="form-control" placeholder="{{ __('messages.password_placeholder') }}" required>
                        <i class="fa-solid fa-eye" onclick="togglePassword(this, 'password')"></i>
                    </div>
                </div>
                
                <button type="submit" class="btn-primary btn-block" id="submit-btn" style="background-color: var(--secondary);">
                    <span>{{ __('messages.confirm_login') }}</span> <i class="fa-solid fa-arrow-right-to-bracket" style="margin-right: 8px;"></i>
                </button>
            </form>

            <div class="auth-links">
                <p style="margin-bottom: 10px;"><a href="/forgot-password">{{ __('messages.forgot_password') }}</a></p>
                <p>{{ __('messages.no_seller_account') }} <a href="/register?role=seller">{{ __('messages.create_seller_account') }}</a></p>
            </div>
        </div>

        <!-- Customer Quick Login -->
        <div id="customer-login" class="tab-content">
            <form id="quick-login-form">
                <div class="form-group">
                    <label for="quick-name">{{ __('messages.name') }}</label>
                    <input type="text" id="quick-name" class="form-control" placeholder="{{ __('messages.name_placeholder') }}" required>
                </div>
                
                <div class="form-group">
                    <label for="quick-phone">{{ __('messages.phone_whatsapp') }}</label>
                    <input type="text" id="quick-phone" class="form-control" placeholder="{{ __('messages.phone_placeholder') }}" required>
                </div>
                
                <button type="submit" class="btn-primary btn-block" id="quick-submit-btn">
                    <span>{{ __('messages.quick_login') }}</span> <i class="fa-solid fa-bolt" style="margin-right: 8px;"></i>
                </button>
            </form>
            
            <div class="auth-links">
                <p>{{ __('messages.create_full_account') }} <a href="/register?role=customer">{{ __('messages.register_new_customer') }}</a></p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function switchTab(tabId) {
        document.querySelectorAll('.tab-content').forEach(el => el.classList.remove('active'));
        document.querySelectorAll('.tab-btn').forEach(el => el.classList.remove('active'));
        
        document.getElementById(tabId).classList.add('active');
        event.currentTarget.classList.add('active');
        document.getElementById('error-message').style.display = 'none';
    }

    // Customer Quick Login Handler
    document.getElementById('quick-login-form').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const name = document.getElementById('quick-name').value;
        const phone = document.getElementById('quick-phone').value;
        const submitBtn = document.getElementById('quick-submit-btn');
        const errorDiv = document.getElementById('error-message');
        
        errorDiv.style.display = 'none';
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> جاري التحقق...';
        
        try {
            const response = await fetch('/api/quick-login', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                body: JSON.stringify({ name, phone })
            });
            
            const data = await response.json();
            
            if (response.ok) {
                localStorage.setItem('auth_token', data.access_token);
                localStorage.setItem('user', JSON.stringify(data.user));
                window.location.href = '/'; // Redirect customers to home page
            } else {
                errorDiv.style.display = 'block';
                errorDiv.innerText = data.message || 'حدث خطأ. يرجى التأكد من البيانات.';
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<span>دخول سريع</span> <i class="fa-solid fa-bolt" style="margin-right: 8px;"></i>';
            }
        } catch (error) {
            errorDiv.style.display = 'block';
            errorDiv.innerText = 'حدث خطأ في الاتصال بالخادم.';
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<span>دخول سريع</span> <i class="fa-solid fa-bolt" style="margin-right: 8px;"></i>';
        }
    });

    // Seller/Admin Login Handler
    document.getElementById('login-form').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const email = document.getElementById('email').value;
        const password = document.getElementById('password').value;
        const submitBtn = document.getElementById('submit-btn');
        const errorDiv = document.getElementById('error-message');
        
        errorDiv.style.display = 'none';
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> جاري التحقق...';
        
        try {
            const response = await fetch('/api/login', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                body: JSON.stringify({ email, password })
            });
            
            const data = await response.json();
            
            if (response.ok) {
                localStorage.setItem('auth_token', data.access_token);
                localStorage.setItem('user', JSON.stringify(data.user));
                
                if (data.user.role === 'admin') window.location.href = '/admin/dashboard';
                else if (data.user.role === 'seller') window.location.href = '/seller/dashboard';
                else window.location.href = '/customer/dashboard';
            } else {
                errorDiv.style.display = 'block';
                errorDiv.innerText = data.message || 'بيانات الدخول غير صحيحة، يرجى المحاولة مرة أخرى.';
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<span>تأكيد الدخول</span> <i class="fa-solid fa-arrow-right-to-bracket" style="margin-right: 8px;"></i>';
            }
        } catch (error) {
            errorDiv.style.display = 'block';
            errorDiv.innerText = 'حدث خطأ في الاتصال بالخادم.';
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<span>تأكيد الدخول</span> <i class="fa-solid fa-arrow-right-to-bracket" style="margin-right: 8px;"></i>';
        }
    });
</script>
@endpush
