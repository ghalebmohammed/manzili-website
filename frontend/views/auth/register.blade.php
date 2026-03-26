@extends('layouts.app')

@section('title', __('messages.create_account'))

@push('styles')
<style>
    .auth-container {
        max-width: 600px;
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
    .radio-group {
        display: flex;
        gap: 20px;
        margin-top: 5px;
    }
    .radio-option {
        display: flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
    }
    .radio-option input[type="radio"] {
        accent-color: var(--primary);
        width: 18px;
        height: 18px;
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
    .alert-error ul {
        margin: 0;
        padding-right: 20px;
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
        <h2 class="auth-title">{{ __('messages.create_account') }}</h2>
        
        <div class="tabs">
            <button class="tab-btn active" onclick="switchTab('customer-register')">{{ __('messages.customer_role') }}</button>
            <button class="tab-btn" id="btn-seller-tab" onclick="switchTab('seller-register')">{{ __('messages.seller_role') }}</button>
        </div>
        
        <div id="error-message" class="alert-error"></div>
        <div id="success-message" style="background-color: #d4edda; color: #155724; padding: 12px; border-radius: 5px; margin-bottom: 20px; display: none;"></div>

        <!-- Verification OTP Section (Initially Hidden) -->
        <div id="otp-section" class="tab-content">
            <h3 style="text-align:center; color:var(--text-dark); margin-bottom:20px;">التحقق من البريد الإلكتروني</h3>
            <p style="text-align:center; margin-bottom:25px; color:var(--text-muted);">لقد أرسلنا رمز تحقق (6 أرقام) إلى بريدك الإلكتروني. يرجى إدخاله هنا لإكمال التسجيل.</p>
            <form id="otp-form">
                <input type="hidden" id="verify_email">
                <div class="form-group">
                    <label style="text-align:center;">رمز التحقق (OTP)</label>
                    <input type="text" id="otp_code" class="form-control" style="text-align:center; font-size:24px; letter-spacing:5px;" maxlength="6" required>
                </div>
                <button type="submit" class="btn-primary btn-block" id="otp-submit-btn">
                    <span>تأكيد الحساب</span> <i class="fa-solid fa-check"></i>
                </button>
            </form>
            <div class="auth-links" style="margin-top:20px;">
                <p>لم يصلك الرمز؟ <a href="#" id="resend-otp-link" onclick="resendOtp(); return false;">إرسال مجدداً</a></p>
                <div id="resend-status" style="font-size:12px; text-align:center; margin-top:5px; display:none;"></div>
            </div>
        </div>

        <!-- Customer Register (Name & Phone Only) -->
        <div id="customer-register" class="tab-content active">
            <form id="customer-form">
                <input type="hidden" name="role" value="customer">
                <div class="form-group">
                    <label for="c_name">{{ __('messages.full_name_or_store') }}</label>
                    <input type="text" id="c_name" class="form-control" placeholder="{{ __('messages.name_placeholder') }}" required>
                </div>
                <div class="form-group">
                    <label for="c_phone">{{ __('messages.phone_whatsapp') }}</label>
                    <input type="tel" id="c_phone" class="form-control" placeholder="{{ __('messages.phone_placeholder') }}" required>
                </div>
                <button type="submit" class="btn-primary btn-block" id="c-submit-btn">
                    <span>{{ __('messages.create_account_btn') }}</span> <i class="fa-solid fa-user-plus" style="margin-right: 8px;"></i>
                </button>
            </form>
        </div>

        <!-- Seller Register (All Fields) -->
        <div id="seller-register" class="tab-content">
            <form id="seller-form">
                <input type="hidden" name="role" value="seller">
                <div class="form-group">
                    <label for="s_name">{{ __('messages.full_name_or_store') }}</label>
                    <input type="text" id="s_name" class="form-control" placeholder="{{ __('messages.name_placeholder') }}" required>
                </div>
                <div class="form-group">
                    <label>{{ __('messages.business_type') }}</label>
                    <div style="display: flex; gap: 20px; align-items: center; margin-top: 5px;">
                        <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                            <input type="radio" name="reg_business_type" value="مشروع منزلي" required style="width: 18px; height: 18px; accent-color: var(--primary);">
                            <span>{{ __('messages.home_business') }}</span>
                        </label>
                        <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                            <input type="radio" name="reg_business_type" value="تاجر" required style="width: 18px; height: 18px; accent-color: var(--primary);">
                            <span>{{ __('messages.merchant') }}</span>
                        </label>
                    </div>
                </div>
                <div class="form-group">
                    <label for="s_email">{{ __('messages.email') }}</label>
                    <input type="email" id="s_email" class="form-control" placeholder="{{ __('messages.email_placeholder') }}" required>
                </div>
                <div class="form-group">
                    <label for="s_phone">{{ __('messages.phone_whatsapp') }}</label>
                    <input type="tel" id="s_phone" class="form-control" placeholder="{{ __('messages.phone_placeholder') }}" required>
                </div>
                <div style="display: flex; gap: 15px;">
                    <div class="form-group" style="flex: 1;">
                        <label for="s_password">{{ __('messages.password') }}</label>
                        <div class="password-wrapper">
                            <input type="password" id="s_password" class="form-control" placeholder="{{ __('messages.password_min_length') }}" required minlength="8">
                            <i class="fa-solid fa-eye" onclick="togglePassword(this, 's_password')"></i>
                        </div>
                    </div>
                    <div class="form-group" style="flex: 1;">
                        <label for="s_password_confirmation">{{ __('messages.confirm_password') }}</label>
                        <div class="password-wrapper">
                            <input type="password" id="s_password_confirmation" class="form-control" placeholder="{{ __('messages.retype_password') }}" required>
                            <i class="fa-solid fa-eye" onclick="togglePassword(this, 's_password_confirmation')"></i>
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn-primary btn-block" id="s-submit-btn">
                    <span>{{ __('messages.create_account_btn') }}</span> <i class="fa-solid fa-store" style="margin-right: 8px;"></i>
                </button>
            </form>
        </div>

        <div class="auth-links">
            <p>{{ __('messages.already_have_account') }} <a href="/login">{{ __('messages.login_here') }}</a></p>
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

    // Set default role if passed via URL (e.g., /register?role=seller)
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('role') === 'seller') {
        document.getElementById('btn-seller-tab').click();
    }

    // Customer Registration
    document.getElementById('customer-form').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const name = document.getElementById('c_name').value;
        const phone = document.getElementById('c_phone').value;
        
        const submitBtn = document.getElementById('c-submit-btn');
        const errorDiv = document.getElementById('error-message');
        
        errorDiv.style.display = 'none';
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> جاري الإنشاء...';
        
        try {
            // using quick-login for customers as it handles creating account too
            const response = await fetch('/api/quick-login', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                body: JSON.stringify({ name, phone })
            });
            
            const data = await response.json();
            
            if (response.ok) {
                localStorage.setItem('auth_token', data.access_token);
                localStorage.setItem('user', JSON.stringify(data.user));
                window.location.href = '/customer/dashboard?welcome=true';
            } else {
                errorDiv.style.display = 'block';
                errorDiv.innerText = data.message || 'حدث خطأ أثناء الإنشاء.';
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<span>{{ __('messages.create_account_btn') }}</span> <i class="fa-solid fa-user-plus" style="margin-right: 8px;"></i>';
            }
        } catch (error) {
            errorDiv.style.display = 'block';
            errorDiv.innerText = 'حدث خطأ في الاتصال بالخادم.';
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<span>{{ __('messages.create_account_btn') }}</span> <i class="fa-solid fa-user-plus" style="margin-right: 8px;"></i>';
        }
    });

    // Seller Registration
    document.getElementById('seller-form').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const name = document.getElementById('s_name').value;
        const email = document.getElementById('s_email').value;
        const phone = document.getElementById('s_phone').value;
        const password = document.getElementById('s_password').value;
        const password_confirmation = document.getElementById('s_password_confirmation').value;
        const business_type = document.querySelector('input[name="reg_business_type"]:checked')?.value;
        const role = 'seller';
        
        const submitBtn = document.getElementById('s-submit-btn');
        const errorDiv = document.getElementById('error-message');
        
        if (password !== password_confirmation) {
            errorDiv.style.display = 'block';
            errorDiv.innerHTML = 'كلمات المرور غير متطابقة.';
            return;
        }

        errorDiv.style.display = 'none';
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> جاري الإنشاء...';
        
        try {
            const response = await fetch('/api/register', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                body: JSON.stringify({ name, email, phone, password, role, business_type })
            });
            
            const data = await response.json();
            
            if (response.ok) {
                if (data.requires_verification) {
                    // Show OTP section
                    document.getElementById('verify_email').value = email;
                    document.getElementById('seller-register').classList.remove('active');
                    document.getElementById('customer-register').classList.remove('active');
                    document.querySelector('.tabs').style.display = 'none';
                    document.getElementById('otp-section').classList.add('active');
                    
                    // store token/user temporarily or just use them after verify
                    window.tempAuthToken = data.access_token;
                    window.tempUser = data.user;
                } else {
                    localStorage.setItem('auth_token', data.access_token);
                    localStorage.setItem('user', JSON.stringify(data.user));
                    window.location.href = '/seller/dashboard?welcome=true';
                }
            } else {
                errorDiv.style.display = 'block';
                if (data.errors) {
                    let errorsHtml = '<ul>';
                    for (const key in data.errors) {
                        data.errors[key].forEach(err => {
                            errorsHtml += `<li>${err}</li>`;
                        });
                    }
                    errorsHtml += '</ul>';
                    errorDiv.innerHTML = errorsHtml;
                } else {
                    errorDiv.innerText = data.message || 'حدث خطأ أثناء الإنشاء.';
                }
                
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<span>{{ __('messages.create_account_btn') }}</span> <i class="fa-solid fa-store" style="margin-right: 8px;"></i>';
            }
        } catch (error) {
            errorDiv.style.display = 'block';
            errorDiv.innerText = 'حدث خطأ في الاتصال بالخادم.';
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<span>{{ __('messages.create_account_btn') }}</span> <i class="fa-solid fa-store" style="margin-right: 8px;"></i>';
        }
    });

    // OTP Verification
    document.getElementById('otp-form').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const email = document.getElementById('verify_email').value;
        const otp = document.getElementById('otp_code').value;
        const errorDiv = document.getElementById('error-message');
        const submitBtn = document.getElementById('otp-submit-btn');
        
        errorDiv.style.display = 'none';
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> جاري التحقق...';
        
        try {
            const response = await fetch('/api/verify-email', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                body: JSON.stringify({ email, otp })
            });
            
            const data = await response.json();
            
            if (response.ok) {
                if(window.tempAuthToken) {
                    localStorage.setItem('auth_token', window.tempAuthToken);
                    localStorage.setItem('user', JSON.stringify(window.tempUser));
                }
                const role = window.tempUser ? window.tempUser.role : '';
                const dashboard = role === 'seller' ? '/seller/dashboard' : '/customer/dashboard';
                window.location.href = dashboard + '?welcome=true';
            } else {
                errorDiv.style.display = 'block';
                errorDiv.innerText = data.message || 'رمز التحقق غير صحيح.';
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<span>تأكيد الحساب</span> <i class="fa-solid fa-check"></i>';
            }
        } catch (error) {
            errorDiv.style.display = 'block';
            errorDiv.innerText = 'حدث خطأ في الاتصال.';
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<span>تأكيد الحساب</span> <i class="fa-solid fa-check"></i>';
        }
    });

    async function resendOtp() {
        const email = document.getElementById('verify_email').value;
        const statusDiv = document.getElementById('resend-status');
        const link = document.getElementById('resend-otp-link');
        
        statusDiv.style.display = 'block';
        statusDiv.style.color = '#666';
        statusDiv.innerText = 'جاري الإرسال...';
        link.style.pointerEvents = 'none';
        
        try {
            const response = await fetch('/api/resend-verify-email', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                body: JSON.stringify({ email })
            });
            const data = await response.json();
            if (response.ok) {
                statusDiv.style.color = 'green';
                statusDiv.innerText = data.message || 'تم إرسال رمز جديد';
            } else {
                statusDiv.style.color = 'red';
                statusDiv.innerText = data.message || 'تم إرسال رمز جديد';
            }
        } catch(e) {
            statusDiv.style.color = 'red';
            statusDiv.innerText = 'حدث خطأ بالاتصال.';
        }
        
        setTimeout(() => { link.style.pointerEvents = 'auto'; }, 60000); // prevent spam for 60s
    }
</script>
@endpush
