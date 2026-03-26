@extends('layouts.app')

@section('title', __('messages.contact_us_page_title'))

@push('styles')
<style>
    .contact-header {
        background: linear-gradient(135deg, var(--primary) 0%, rgba(140, 163, 121,0.8) 100%);
        color: white;
        padding: 80px 0;
        text-align: center;
        margin-bottom: 50px;
    }
    .contact-header h1 {
        font-size: 42px;
        margin-bottom: 15px;
    }
    .contact-header p {
        font-size: 18px;
        max-width: 700px;
        margin: 0 auto;
        opacity: 0.9;
    }
    .contact-container {
        display: flex;
        flex-wrap: wrap;
        gap: 40px;
        margin-bottom: 80px;
    }
    .contact-info-card {
        flex: 1;
        min-width: 300px;
        background: white;
        padding: 40px;
        border-radius: 12px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.05);
    }
    html.dark-mode .contact-info-card {
        background: #1E222A;
    }
    .contact-info-card h3 {
        color: var(--primary);
        font-size: 24px;
        margin-bottom: 20px;
        border-bottom: 2px solid #eee;
        padding-bottom: 15px;
    }
    html.dark-mode .contact-info-card h3 {
        border-color: #333;
    }
    .info-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    .info-list li {
        margin-bottom: 15px;
    }
    .contact-action-link {
        display: flex;
        align-items: center;
        gap: 15px;
        font-size: 16px;
        color: var(--text-dark);
        text-decoration: none;
        padding: 15px;
        border-radius: 12px;
        transition: all 0.3s ease;
        background: transparent;
        border: 1px solid transparent;
    }
    html.dark-mode .contact-action-link {
        color: white;
    }
    .contact-action-link:hover {
        transform: translateY(-5px);
        background: rgba(140, 163, 121, 0.05);
        border: 1px solid rgba(140, 163, 121, 0.2);
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
    }
    .contact-action-link:active {
        transform: scale(0.98);
    }
    .contact-action-link:hover .info-icon {
        background: var(--primary);
        color: white;
        animation: contactIconPulse 0.4s ease-in-out alternate infinite;
    }
    @keyframes contactIconPulse {
        0% { transform: scale(1); }
        100% { transform: scale(1.15); }
    }
    .info-icon {
        width: 50px;
        height: 50px;
        background: rgba(140, 163, 121,0.1);
        color: var(--primary);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        transition: all 0.3s ease;
    }
    .contact-form-wrapper {
        flex: 2;
        min-width: 350px;
        background: white;
        padding: 40px;
        border-radius: 12px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.05);
    }
    html.dark-mode .contact-form-wrapper {
        background: #1E222A;
    }
    .contact-form-wrapper h3 {
        color: var(--secondary);
        font-size: 24px;
        margin-bottom: 25px;
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
        border-radius: 8px;
        font-family: inherit;
        font-size: 15px;
        outline: none;
        box-sizing: border-box;
        transition: border-color 0.3s;
    }
    .form-control:focus {
        border-color: var(--primary);
    }
    html.dark-mode .form-control {
        background: #2D3748;
        border-color: #4A5568;
        color: white;
    }
    .btn-submit {
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
        color: white;
        border: none;
        padding: 15px 30px;
        font-size: 18px;
        font-weight: 600;
        border-radius: 8px;
        cursor: pointer;
        width: 100%;
        transition: transform 0.3s, box-shadow 0.3s;
    }
    .btn-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(140, 163, 121, 0.3);
    }
    
    #contact-alert {
        display: none;
        padding: 15px;
        border-radius: 8px;
        margin-top: 20px;
        text-align: center;
        font-weight: bold;
    }
</style>
@endpush

@section('content')

<div class="contact-header" data-aos="fade-down">
    <div class="container">
        <h1>{{ __('messages.contact_hero_title') }}</h1>
        <p>{{ __('messages.contact_hero_desc') }}</p>
    </div>
</div>

<div class="container">
    <div class="contact-container">
        
        <!-- Contact Info -->
        <div class="contact-info-card" data-aos="fade-right">
            <h3><i class="fa-solid fa-address-card"></i> {{ __('messages.contact_info') }}</h3>
            <p style="color: var(--text-muted); margin-bottom: 30px;">{{ __('messages.contact_info_desc') }}</p>
            
            <ul class="info-list">
                <li>
                    <a href="https://maps.google.com/?q=Sanaa,Yemen" target="_blank" class="contact-action-link">
                        <div class="info-icon"><i class="fa-solid fa-location-dot"></i></div>
                        <div>
                            <strong style="display:block; margin-bottom: 5px;">{{ __('messages.our_location') }}</strong>
                            <span style="color: var(--text-muted); transition: color 0.3s;">{{ __('messages.location_sanaa') }}</span>
                        </div>
                    </a>
                </li>
                <li>
                    <a href="tel:+967775552127" class="contact-action-link">
                        <div class="info-icon"><i class="fa-solid fa-phone"></i></div>
                        <div>
                            <strong style="display:block; margin-bottom: 5px;">{{ __('messages.phone_whatsapp') }}</strong>
                            <span style="color: var(--text-muted); direction: ltr; display: inline-block; transition: color 0.3s;">+967 775 552 127</span>
                        </div>
                    </a>
                </li>
                <li>
                    <a href="mailto:support@manzili.com" class="contact-action-link">
                        <div class="info-icon"><i class="fa-solid fa-envelope"></i></div>
                        <div>
                            <strong style="display:block; margin-bottom: 5px;">{{ __('messages.email') }}</strong>
                            <span style="color: var(--text-muted); transition: color 0.3s;">support@manzili.com</span>
                        </div>
                    </a>
                </li>
            </ul>
        </div>
        
        <!-- Contact Form -->
        <div class="contact-form-wrapper" data-aos="fade-left">
            <h3><i class="fa-solid fa-envelope-open-text"></i> {{ __('messages.send_us_message') }}</h3>
            
            <form id="contact-form">
                <div style="display:flex; gap:15px; flex-wrap: wrap;">
                    <div class="form-group" style="flex:1; min-width: 200px;">
                        <label>{{ __('messages.your_name') }}</label>
                        <input type="text" id="contact-name" class="form-control" placeholder="{{ __('messages.your_name') }}" required>
                    </div>
                    <div class="form-group" style="flex:1; min-width: 200px;">
                        <label>{{ __('messages.your_email') }}</label>
                        <input type="email" id="contact-email" class="form-control" placeholder="{{ __('messages.email_placeholder') }}" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>{{ __('messages.message_subject') }}</label>
                    <input type="text" id="contact-subject" class="form-control" placeholder="{{ __('messages.message_subject') }}" required>
                </div>
                
                <div class="form-group">
                    <label>{{ __('messages.your_message') }}</label>
                    <textarea id="contact-message" class="form-control" rows="5" placeholder="{{ __('messages.share_opinion') }}" required></textarea>
                </div>
                
                <button type="submit" class="btn-submit" id="contact-submit-btn">
                    <i class="fa-solid fa-paper-plane"></i> <span id="btn-text">{{ __('messages.send_message_btn') }}</span>
                </button>
                
                <div id="contact-alert"></div>
            </form>
        </div>
        
    </div>
</div>

@endsection

@push('scripts')
<script>
    document.getElementById('contact-form').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const btn = document.getElementById('contact-submit-btn');
        const alert = document.getElementById('contact-alert');
        const btnText = document.getElementById('btn-text');
        
        // Disable button, show loading
        btn.disabled = true;
        btn.style.opacity = '0.7';
        btnText.innerText = '{{ __('messages.sending') }}';
        alert.style.display = 'none';
        
        // Fake API simulation for prototype demo purposes
        setTimeout(() => {
            btn.disabled = false;
            btn.style.opacity = '1';
            btnText.innerText = '{{ __('messages.send_message_btn') }}';
            
            // Show success alert
            alert.style.display = 'block';
            alert.style.background = '#d4edda';
            alert.style.color = '#155724';
            alert.style.border = '1px solid #c3e6cb';
            alert.innerHTML = '<i class="fa-solid fa-circle-check"></i> رسالتك قيد المراجعة؛ شكراً لتواصلك معنا.';
            
            // Reset form
            document.getElementById('contact-form').reset();
            
            // Hide alert after 5s
            setTimeout(() => {
                alert.style.display = 'none';
            }, 5000);
            
        }, 1500);
    });
</script>
@endpush
