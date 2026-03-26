<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <script>
        if(localStorage.getItem('theme') === 'dark') {
            document.documentElement.classList.add('dark-mode');
        }
    </script>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="{{ __('messages.manzili_desc') }}">
    <meta name="keywords" content="منزلي, أسر منتجة, متاجر محلية, تسوق عبر واتساب, منتجات يدوية">
    <meta property="og:title" content="{{ __('messages.manzili') }}">
    <meta property="og:description" content="{{ __('messages.manzili_desc') }}">
    
    <title>{{ __('messages.manzili') }} - @yield('title', __('messages.home'))</title>
    <!-- Fonts -->
    <link href="{{ asset('css/fonts.css') }}" rel="stylesheet">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="{{ asset('css/all.min.css') }}">
    <!-- AOS Animation CSS -->
    <link href="{{ asset('css/aos.css') }}" rel="stylesheet">
    <!-- Leaflet CSS for Maps -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    
    <style>
        :root {
            --primary: #8CA379;
            --primary-dark: #6B8259;
            --secondary: #D4A373;
            --secondary-dark: #BF8751;
            --bg-light: #F9F9F6;
            --text-dark: #2C302A;
            --text-muted: #7A8275;
            --white: #ffffff;
            --font-ar: 'Tajawal', sans-serif;
            --font-en: 'Poppins', sans-serif;
        }

        html.dark-mode {
            --bg-light: #161815;
            --text-dark: #F4F6F2;
            --text-muted: #9FA89A;
            --white: #1E221D;
            background-color: var(--bg-light);
        }

        html.dark-mode .navbar {
            background: rgba(30, 34, 42, 0.9);
            box-shadow: 0 4px 30px rgba(0,0,0,0.5);
            border-bottom: 1px solid rgba(255,255,255,0.05);
        }

        html.dark-mode .footer {
            background-color: #0b0d10;
        }

        /* Forms in dark mode */
        html.dark-mode input, 
        html.dark-mode textarea, 
        html.dark-mode select {
            background-color: #2D3748 !important;
            color: var(--text-dark) !important;
            border-color: #4A5568 !important;
        }

        html.dark-mode .section-header {
            border-color: #333;
        }

        html.dark-mode .data-table th {
            background-color: #2D3748;
            color: var(--text-dark);
            border-color: #4A5568;
        }
        
        html.dark-mode .data-table td {
            border-color: #4A5568;
        }

         /* Dashboard specific dark mode fixes */
        html.dark-mode .sidebar {
            background-color: var(--white);
        }
        
        html.dark-mode .sidebar-menu a:hover, html.dark-mode .sidebar-menu a.active {
            background-color: #2D3748;
        }
        
        html.dark-mode .stat-card, html.dark-mode .content-section, html.dark-mode .table-container {
            background-color: var(--white);
            color: var(--text-dark);
        }
        
        html.dark-mode table th { background: #2D3748; color: var(--text-dark); border-bottom: 1px solid #4A5568; }
        html.dark-mode table td { border-bottom: 1px solid #4A5568; }

        /* Chat and notifs borders in dark mode */
        html.dark-mode #ai-chatbox, 
        html.dark-mode #notifications-box,
        html.dark-mode #admin-products-modal > div,
        html.dark-mode #product-modal > div {
            background: var(--white) !important;
            border: 1px solid #333 !important;
            box-shadow: 0 5px 20px rgba(0,0,0,0.5) !important;
        }
        html.dark-mode #chat-messages > div[style*="background: #e9ecef;"] {
            background: #2D3748 !important;
            color: #F8F9FA !important;
        }
        html.dark-mode #chat-messages {
            background: #121212 !important;
        }
        
        body {
            font-family: var(--font-ar);
            background-color: var(--bg-light);
            color: var(--text-dark);
            margin: 0;
            padding: 0;
            line-height: 1.6;
            -webkit-font-smoothing: antialiased;
        }

        html[dir="ltr"] body {
            font-family: var(--font-en);
        }

        /* Utility classes */
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 15px;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: var(--white);
            border: none;
            padding: 10px 24px;
            border-radius: 8px;
            cursor: pointer;
            transition: transform 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275), box-shadow 0.4s ease, filter 0.3s ease;
            text-decoration: none;
            font-weight: 600;
            box-shadow: 0 4px 10px rgba(140, 163, 121, 0.25);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        .btn-primary:hover {
            transform: translateY(-4px) scale(1.05);
            box-shadow: 0 10px 20px rgba(140, 163, 121, 0.4);
            color: var(--white);
            filter: brightness(1.1);
        }

        .btn-secondary {
            background: linear-gradient(135deg, var(--secondary) 0%, var(--secondary-dark) 100%);
            color: var(--white);
            border: none;
            padding: 10px 24px;
            border-radius: 8px;
            cursor: pointer;
            transition: transform 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275), box-shadow 0.4s ease, filter 0.3s ease;
            text-decoration: none;
            font-weight: 600;
            box-shadow: 0 4px 10px rgba(212, 163, 115, 0.25);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        .btn-secondary:hover {
            transform: translateY(-4px) scale(1.05);
            box-shadow: 0 10px 20px rgba(212, 163, 115, 0.4);
            color: var(--white);
            filter: brightness(1.1);
        }

        .password-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }
        .password-wrapper i {
            position: absolute;
            left: 15px;
            cursor: pointer;
            color: var(--text-muted);
            transition: color 0.3s;
        }
        .password-wrapper i:hover {
            color: var(--primary);
        }
        html[dir="rtl"] .password-wrapper input {
            padding-left: 45px;
        }
        html[dir="ltr"] .password-wrapper input {
            padding-right: 45px;
        }
        html[dir="ltr"] .password-wrapper i {
            left: auto;
            right: 15px;
        }

        /* Global Card Dynamic Effect */
        .card {
            transition: transform 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275), box-shadow 0.5s ease;
        }
        .card:hover {
            transform: translateY(-12px) scale(1.02);
            box-shadow: 0 20px 40px rgba(0,0,0,0.12);
        }

        /* Navbar */
        .navbar {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            box-shadow: 0 4px 30px rgba(0,0,0,0.05);
            position: sticky;
            top: 0;
            z-index: 1000;
            border-bottom: 1px solid rgba(0,0,0,0.05);
        }
        .navbar .container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            height: 70px;
        }
        .navbar-brand {
            font-size: 24px;
            font-weight: 800;
            color: var(--primary);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .navbar-nav {
            display: flex;
            gap: 20px;
            list-style: none;
            margin: 0;
            padding: 0;
        }
        .navbar-nav a {
            color: var(--text-dark);
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s, transform 0.3s;
            display: inline-block;
            position: relative;
        }
        .navbar-nav a::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: -5px;
            right: 0;
            background-color: var(--primary);
            transition: width 0.3s;
        }
        .navbar-nav a:hover,
        .navbar-nav a.active {
            color: var(--primary);
            transform: translateY(-1px);
        }
        .navbar-nav a:hover::after,
        .navbar-nav a.active::after {
            width: 100%;
        }
        .navbar-actions {
            display: flex;
            gap: 15px;
            align-items: center;
        }
        .navbar-actions a i {
            transition: transform 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275), color 0.3s ease;
            display: inline-block;
        }
        .navbar-actions a:hover i {
            transform: scale(1.2) rotate(-10deg);
            color: var(--primary) !important;
        }


        /* Footer */
        .footer {
            background-color: var(--text-dark);
            color: var(--white);
            padding: 40px 0 20px;
            margin-top: 50px;
        }
        .footer-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
            margin-bottom: 30px;
        }
        .footer h4 {
            color: var(--primary);
            margin-bottom: 20px;
            position: relative;
            padding-bottom: 5px;
            display: inline-block;
        }
        .footer h4::after {
            content: '';
            position: absolute;
            bottom: 0;
            right: 0;
            width: 50%;
            height: 2px;
            background: var(--secondary);
            border-radius: 2px;
        }
        .footer ul {
            list-style: none;
            padding: 0;
        }
        .footer ul li {
            margin-bottom: 10px;
        }
        .footer ul li a {
            color: #ddd;
            text-decoration: none;
        }
        .footer ul li a:hover {
            color: var(--secondary);
        }
        
        /* Footer Contact Hover Animation */
        .footer-contact-link {
            text-decoration: none;
            color: #ddd;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            transition: all 0.3s ease;
        }
        .footer-contact-link i {
            color: var(--primary);
            transition: transform 0.3s ease, color 0.3s ease;
        }
        .footer-contact-link:hover {
            color: var(--white);
            transform: translateX(-5px);
        }
        html[dir="ltr"] .footer-contact-link:hover {
            transform: translateX(5px);
        }
        .footer-contact-link:hover i {
            transform: scale(1.2) rotate(-10deg);
            color: var(--secondary);
        }
        
        .social-link {
            color: white;
            font-size: 18px;
            display: inline-flex;
            width: 40px;
            height: 40px;
            background: rgba(255,255,255,0.05);
            border-radius: 50%;
            align-items: center;
            justify-content: center;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            text-decoration: none;
        }
        .social-link:hover {
            transform: translateY(-5px) scale(1.1);
            background: var(--primary);
            box-shadow: 0 5px 15px rgba(140, 163, 121, 0.3);
            color: white;
        }

        .footer-bottom {
            text-align: center;
            padding-top: 20px;
            border-top: 1px solid #444;
            color: var(--text-muted);
        }

        /* Responsive Design */
        .mobile-menu-btn {
            display: none;
            background: transparent;
            border: none;
            font-size: 24px;
            color: var(--text-dark);
            cursor: pointer;
            padding: 5px;
        }

        html.dark-mode .mobile-menu-btn {
            color: var(--text-dark);
        }

        @media (max-width: 900px) {
            .navbar-actions a.btn-primary {
                padding: 8px 15px;
                font-size: 14px;
            }
        }

        @media (max-width: 768px) {
            .mobile-menu-btn {
                display: block;
            }

            .navbar .container {
                flex-wrap: wrap;
                height: auto;
                padding: 10px 15px;
            }

            .navbar-nav {
                width: 100%;
                flex-direction: column;
                gap: 0;
                max-height: 0;
                overflow: hidden;
                transition: max-height 0.4s cubic-bezier(0.175, 0.885, 0.32, 1), opacity 0.4s ease;
                opacity: 0;
                margin-top: 5px;
            }
            .navbar-nav.show {
                max-height: 400px;
                opacity: 1;
            }
            .navbar-nav li {
                width: 100%;
                border-top: 1px solid rgba(0,0,0,0.05);
            }
            html.dark-mode .navbar-nav li {
                border-top-color: rgba(255,255,255,0.05);
            }
            .navbar-nav a {
                padding: 15px 5px;
                width: 100%;
                display: block;
                box-sizing: border-box;
                font-size: 16px;
                font-weight: bold;
            }
            .navbar-nav a::after {
                display: none !important;
            }

            .navbar-actions {
                width: 100%;
                flex-wrap: wrap;
                gap: 20px;
                justify-content: center;
                align-items: center;
                max-height: 0;
                overflow: hidden;
                transition: max-height 0.4s cubic-bezier(0.175, 0.885, 0.32, 1), padding 0.4s ease, opacity 0.4s ease;
                opacity: 0;
                padding: 0;
            }
            .navbar-actions.show {
                max-height: 250px;
                padding: 20px 0;
                opacity: 1;
                border-top: 1px solid rgba(0,0,0,0.05);
            }
            html.dark-mode .navbar-actions.show {
                border-top-color: rgba(255,255,255,0.05);
            }
            
            .navbar-actions a.btn-primary {
                width: 100%;
                text-align: center;
            }

            .dashboard-container {
                flex-direction: column;
            }
            .sidebar {
                width: 100% !important;
                height: auto !important;
                position: static !important;
                margin-bottom: 20px;
            }
            
            #ai-chatbox {
                width: 90% !important;
                left: 5% !important;
                right: 5% !important;
                bottom: 90px !important;
            }
            
            .footer-grid {
                text-align: center;
            }
            .footer h4::after {
                left: 25%;
                right: auto;
            }
            html[dir="ltr"] .footer h4::after {
                left: auto;
                right: 25%;
            }
        }

        @media (max-width: 480px) {
            .stat-card {
                flex-direction: column;
                text-align: center;
            }
            .navbar-brand img {
                height: 40px !important;
            }
            .filter-bar {
                flex-direction: column;
            }
            .filter-bar > div {
                width: 100%;
                justify-content: space-between;
                flex-wrap: wrap;
            }
            .filter-bar select {
                flex: 1;
            }
        }

    </style>
    @stack('styles')
</head>
<body>

    <nav class="navbar">
        <div class="container">
            <a href="/" class="navbar-brand">
                <img src="/images/logo.png" alt="منزلي" style="height: 48px; width: auto; object-fit: contain; border-radius: 4px;">
            </a>
            
            <button class="mobile-menu-btn" onclick="toggleMobileMenu()">
                <i class="fa-solid fa-bars"></i>
            </button>

            <ul class="navbar-nav" id="main-nav">
                <li><a href="/" class="{{ request()->is('/') ? 'active' : '' }}">{{ __('messages.home') }}</a></li>
                <li><a href="/stores" class="{{ request()->is('stores') || request()->is('stores/*') ? 'active' : '' }}">{{ __('messages.stores') }}</a></li>
                <li><a href="/products" class="{{ request()->is('products') || request()->is('products/*') ? 'active' : '' }}">{{ __('messages.products') }}</a></li>
                <li><a href="/about" class="{{ request()->is('about') ? 'active' : '' }}">{{ __('messages.about') }}</a></li>
                <li><a href="/contact" class="{{ request()->is('contact') ? 'active' : '' }}">{{ __('messages.contact') }}</a></li>
            </ul>
            <div class="navbar-actions">
                <a href="/cart" style="color:var(--text-dark); position:relative; margin-left: 10px;">
                    <i class="fa-solid fa-cart-shopping"></i>
                    <span id="cart-count" style="position:absolute; top:-8px; right:-10px; background:var(--primary); color:white; font-size:10px; padding:2px 6px; border-radius:10px; font-weight:bold;">0</span>
                </a>
                <a href="#" class="search-icon" style="color:var(--text-dark);"><i class="fa-solid fa-magnifying-glass"></i></a>
                <a href="#" class="theme-toggle-btn" style="color:var(--text-dark); margin: 0 10px; font-size: 18px;" onclick="toggleTheme(event)">
                    <i class="fa-solid fa-moon"></i>
                </a>
                @if(app()->getLocale() == 'ar')
                    <a href="/lang/en" style="color:var(--text-dark); text-decoration:none; font-weight:bold; margin: 0 10px;">En</a>
                @else
                    <a href="/lang/ar" style="color:var(--text-dark); text-decoration:none; font-weight:bold; margin: 0 10px;">عربي</a>
                @endif
                <a href="/login" class="btn-primary">{{ __('messages.login_register') }}</a>
            </div>
        </div>
    </nav>

    <main style="min-height: 80vh;">
        @yield('content')
    </main>

    <footer class="footer">
        <div class="container">
            <div class="footer-grid">
                <div>
                    <h4>{{ __('messages.about_manzili') }}</h4>
                    <p>{{ __('messages.manzili_desc') }}</p>
                </div>
                <div>
                    <h4>{{ __('messages.important_links') }}</h4>
                    <ul>
                        <li><a href="/">{{ __('messages.home') }}</a></li>
                        <li><a href="/stores">{{ __('messages.browse_stores') }}</a></li>
                        <li><a href="/products">{{ __('messages.latest_products') }}</a></li>
                        <li><a href="/register?role=seller">{{ __('messages.join_as_seller') }}</a></li>
                    </ul>
                </div>
                <div>
                    <h4>{{ __('messages.contact_us') }}</h4>
                    <ul>
                        <li><a href="mailto:support@manzili.com" class="footer-contact-link"><i class="fa-solid fa-envelope"></i> support@manzili.com</a></li>
                        <li dir="ltr" style="text-align: right;"><a href="tel:+967775552127" class="footer-contact-link"><i class="fa-solid fa-phone"></i> +967 775 552 127</a></li>
                    </ul>
                    <div style="margin-top: 20px; display: flex; gap: 15px;">
                        <a href="https://x.com" target="_blank" class="social-link">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" style="width:18px; height:18px; fill:currentColor;"><path d="M389.2 48h70.6L305.6 224.2 487 464H345L233.7 318.6 106.5 464H35.8L200.7 275.5 26.8 48H172.4L272.9 180.9 389.2 48zM364.4 421.8h39.1L151.1 88h-42L364.4 421.8z"/></svg>
                        </a>
                        <a href="https://instagram.com" target="_blank" class="social-link"><i class="fa-brands fa-instagram"></i></a>
                        <a href="https://wa.me/967775552127" target="_blank" class="social-link"><i class="fa-brands fa-whatsapp"></i></a>
                        <a href="https://maps.google.com/?q=Sanaa,Yemen" target="_blank" class="social-link"><i class="fa-solid fa-location-dot"></i></a>
                    </div>
                </div>
            </div>
            <div class="footer-bottom">
                &copy; {{ date('Y') }} {{ __('messages.all_rights_reserved') }}
            </div>
        </div>
    </footer>

    <!-- Smart Assistant FAB -->
    <div id="ai-assistant-fab" style="position: fixed; bottom: 30px; left: 30px; background: var(--secondary); color: white; width: 60px; height: 60px; border-radius: 50%; display: flex; justify-content: center; align-items: center; font-size: 24px; box-shadow: 0 4px 10px rgba(0,0,0,0.2); cursor: pointer; z-index: 1000; transition: transform 0.3s;">
        <i class="fa-solid fa-robot"></i>
    </div>

    <!-- AI Chatbox Window -->
    <div id="ai-chatbox" style="display: none; position: fixed; bottom: 100px; left: 30px; width: 350px; height: 500px; background: white; border-radius: 12px; box-shadow: 0 5px 20px rgba(0,0,0,0.15); z-index: 999; flex-direction: column; overflow: hidden; border: 1px solid #eee;">
        <div style="background: var(--secondary); color: white; padding: 15px; display: flex; justify-content: space-between; align-items: center;">
            <div style="display:flex; align-items:center; gap:10px; font-weight:600;"><i class="fa-solid fa-robot fa-lg"></i> {{ __('messages.manzili_assistant') }}</div>
            <i class="fa-solid fa-xmark" id="close-chat" style="cursor: pointer; font-size: 18px;"></i>
        </div>
        <div id="chat-messages" style="flex: 1; padding: 15px; overflow-y: auto; display: flex; flex-direction: column; gap: 10px; background: #fafafa;">
            <div style="align-self: flex-start; background: #e9ecef; padding: 10px 15px; border-radius: 15px 15px 15px 0; color: #333; max-width: 80%; font-size: 14px;">{{ __('messages.assistant_welcome') }}</div>
        </div>
        <div style="padding: 15px; background: white; border-top: 1px solid #eee; display: flex; gap: 10px;">
            <input type="text" id="chat-input" placeholder="{{ __('messages.type_message') }}" style="flex: 1; padding: 10px 15px; border: 1px solid #ddd; border-radius: 20px; outline: none; font-family: inherit;">
            <button id="send-chat" style="background: var(--primary); color: white; border: none; width: 40px; height: 40px; border-radius: 50%; cursor: pointer; display: flex; justify-content: center; align-items: center;"><i class="fa-solid fa-paper-plane"></i></button>
        </div>
    </div>

    <!-- Notifications Box -->
    <div id="notifications-box" style="display: none; position: fixed; top: 80px; left: 30px; width: 320px; max-height: 400px; background: white; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); z-index: 1001; flex-direction: column; overflow: hidden; border: 1px solid #eee;">
        <div style="background: var(--bg-light); padding: 10px 15px; border-bottom: 1px solid #eee; display: flex; justify-content: space-between; align-items: center;">
            <strong style="color: var(--text-dark);">{{ __('messages.notifications') }}</strong>
            <button onclick="markAllNotificationsAsRead()" style="background:none; border:none; color:var(--primary);cursor:pointer;font-size:12px;font-family:inherit;">{{ __('messages.mark_all_read') }}</button>
        </div>
        <div id="notifications-list" style="overflow-y: auto; flex:1; padding: 10px;">
            <div style="text-align:center; padding: 20px; color: var(--text-muted); font-size:13px;">{{ __('messages.no_notifications') }}</div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const fab = document.getElementById('ai-assistant-fab');
            const chatbox = document.getElementById('ai-chatbox');
            const closeChat = document.getElementById('close-chat');
            const chatMessages = document.getElementById('chat-messages');
            const chatInput = document.getElementById('chat-input');
            const sendBtn = document.getElementById('send-chat');

            fab.addEventListener('click', () => {
                chatbox.style.display = chatbox.style.display === 'none' ? 'flex' : 'none';
            });

            closeChat.addEventListener('click', () => {
                chatbox.style.display = 'none';
            });

            async function sendMessage() {
                const message = chatInput.value.trim();
                if(!message) return;

                // User msg
                const uMsg = document.createElement('div');
                uMsg.style.cssText = 'align-self: flex-end; background: var(--primary); color: white; padding: 10px 15px; border-radius: 15px 15px 0 15px; max-width: 80%; font-size: 14px;';
                uMsg.innerText = message;
                chatMessages.appendChild(uMsg);
                
                chatInput.value = '';
                chatMessages.scrollTop = chatMessages.scrollHeight;

                // Loading msg
                const lMsg = document.createElement('div');
                lMsg.style.cssText = 'align-self: flex-start; background: #e9ecef; color: #333; padding: 10px 15px; border-radius: 15px 15px 15px 0; max-width: 80%; font-size: 14px;';
                lMsg.innerHTML = '<i class="fa-solid fa-ellipsis fa-fade"></i>';
                chatMessages.appendChild(lMsg);
                chatMessages.scrollTop = chatMessages.scrollHeight;

                try {
                    const res = await fetch('/api/assistant/chat', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                        body: JSON.stringify({ message })
                    });
                    const data = await res.json();
                    
                    lMsg.innerHTML = data.reply ? data.reply.replace(/\n/g, '<br>') : 'عذراً لا استطيع الرد الآن.';
                } catch(e) {
                    lMsg.innerText = 'حدث خطأ بالاتصال بالانترنت.';
                }
                chatMessages.scrollTop = chatMessages.scrollHeight;
            }

            sendBtn.addEventListener('click', sendMessage);
            chatInput.addEventListener('keypress', (e) => {
                if(e.key === 'Enter') sendMessage();
            });
        });
    </script>


    <!-- Auth Navbar Script -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const token = localStorage.getItem('auth_token');
            const userStr = localStorage.getItem('user');
            
            if (token && userStr) {
                try {
                    const user = JSON.parse(userStr);
                    const actionsContainer = document.querySelector('.navbar-actions');
                    
                    if (actionsContainer) {
                        let dashLink = '/customer/dashboard';
                        let dynamicButtons = '';
                        if(user.role === 'customer') {
                            dynamicButtons = `
                                <span style="font-weight:600; color:var(--primary); margin:0 10px;">👋 {{ __('messages.welcome') }} ${user.name}</span>
                                <a href="/customer/dashboard" style="color:var(--text-dark); font-weight:bold; margin:0 10px;"><i class="fa-solid fa-list-check"></i> {{ __('messages.my_orders') }}</a>
                            `;
                        } else {
                            if(user.role === 'seller') dashLink = '/seller/dashboard';
                            if(user.role === 'admin') dashLink = '/admin/dashboard';
                            dynamicButtons = `<a href="${dashLink}" class="btn-primary" style="background-color: var(--secondary);"><i class="fa-solid fa-user"></i> {{ __('messages.dashboard') }}</a>`;
                        }

                        const langHtml = `
                            @if(app()->getLocale() == 'ar')
                                <a href="/lang/en" style="color:var(--text-dark); text-decoration:none; font-weight:bold; margin: 0 10px;">En</a>
                            @else
                                <a href="/lang/ar" style="color:var(--text-dark); text-decoration:none; font-weight:bold; margin: 0 10px;">عربي</a>
                            @endif
                        `;
                        
                        actionsContainer.innerHTML = `
                            <a href="/cart" style="color:var(--text-dark); position:relative; margin: 0 10px;">
                                <i class="fa-solid fa-cart-shopping"></i>
                                <span id="cart-count" style="position:absolute; top:-8px; right:-10px; background:var(--primary); color:white; font-size:10px; padding:2px 6px; border-radius:10px; font-weight:bold;">0</span>
                            </a>
                            <a href="#" class="search-icon" style="color:var(--text-dark); margin-left: 10px;"><i class="fa-solid fa-magnifying-glass"></i></a>
                            <a href="#" class="theme-toggle-btn" style="color:var(--text-dark); margin: 0 10px; font-size: 18px;" onclick="toggleTheme(event)">
                                <i class="fa-solid fa-moon"></i>
                            </a>
                            ${langHtml}
                            <a href="#" id="notif-btn" style="color:var(--text-dark); position:relative; margin: 0 10px;">
                                <i class="fa-solid fa-bell"></i>
                                <span id="notif-count" style="position:absolute; top:-8px; right:-10px; background:#dc3545; color:white; font-size:10px; padding:2px 6px; border-radius:10px; font-weight:bold; display:none;">0</span>
                            </a>
                            ${dynamicButtons}
                        `;

                        // Notification logic
                        const notifBtn = document.getElementById('notif-btn');
                        const notifBox = document.getElementById('notifications-box');
                        
                        if(notifBtn && notifBox) {
                            notifBtn.addEventListener('click', (e) => {
                                e.preventDefault();
                                notifBox.style.display = notifBox.style.display === 'none' ? 'flex' : 'none';
                            });
                        }

                        fetchNotifications();
                    }
                } catch(e) {}
            }
        });

        async function fetchNotifications() {
            try {
                const token = localStorage.getItem('auth_token');
                const res = await fetch('/api/notifications', {
                    headers: { 'Authorization': `Bearer ${token}` }
                });
                const data = await res.json();
                
                const notifCount = document.getElementById('notif-count');
                if(data.unread_count > 0) {
                    notifCount.style.display = 'block';
                    notifCount.innerText = data.unread_count;
                } else {
                    notifCount.style.display = 'none';
                }

                const list = document.getElementById('notifications-list');
                list.innerHTML = '';
                if(data.notifications.length === 0) {
                    list.innerHTML = '<div style="text-align:center; padding: 20px; color: var(--text-muted); font-size:13px;">لا توجد إشعارات حالياً</div>';
                } else {
                    data.notifications.forEach(n => {
                        const isLightMode = !document.documentElement.classList.contains('dark-mode');
                        const bgLight = n.read_at ? '#fff' : '#f0fdfa';
                        const bgDark = n.read_at ? '#1E222A' : '#2D3748';
                        const bg = isLightMode ? bgLight : bgDark;
                        const dot = n.read_at ? '' : '<span style="height:8px; width:8px; background:var(--primary); border-radius:50%; display:inline-block; margin-left:5px;"></span>';
                        list.innerHTML += `
                            <div style="background:${bg}; padding: 10px; border-bottom: 1px solid #eee; font-size: 13px; cursor: pointer;" onclick="markNotificationAsRead('${n.id}', '${n.data.url}')">
                                <div style="font-weight:bold; color:var(--text-dark); margin-bottom:5px;">${dot}${n.data.title}</div>
                                <div style="color:var(--text-muted);">${n.data.message}</div>
                            </div>
                        `;
                    });
                }
            } catch(e) {}
        }

        async function markNotificationAsRead(id, url) {
            try {
                const token = localStorage.getItem('auth_token');
                await fetch(`/api/notifications/${id}/read`, {
                    method: 'PUT',
                    headers: { 'Authorization': `Bearer ${token}` }
                });
                if(url && url !== 'null') {
                    window.location.href = url;
                } else {
                    fetchNotifications();
                }
            } catch(e) {}
        }

        async function markAllNotificationsAsRead() {
            try {
                const token = localStorage.getItem('auth_token');
                await fetch('/api/notifications/read-all', {
                    method: 'PUT',
                    headers: { 'Authorization': `Bearer ${token}` }
                });
                fetchNotifications();
            } catch(e) {}
        }
    </script>

    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    @stack('scripts')
    
    <!-- AOS Animation Script -->
    <script src="{{ asset('js/aos.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            AOS.init({
                duration: 800,
                once: true,
                offset: 50
            });
            updateCartBadge();
        });

        function updateCartBadge() {
            const cartText = document.getElementById('cart-count');
            if (cartText) {
                const cart = JSON.parse(localStorage.getItem('manzili_cart')) || [];
                cartText.innerText = cart.length;
            }
        }

        function addToCart(product) {
            const cart = JSON.parse(localStorage.getItem('manzili_cart')) || [];
            cart.push(product);
            localStorage.setItem('manzili_cart', JSON.stringify(cart));
            updateCartBadge();
            
            // Nice Toast Notification
            const toast = document.createElement('div');
            toast.style.cssText = `
                position: fixed;
                bottom: 30px;
                right: 30px;
                background: var(--primary);
                color: white;
                padding: 15px 25px;
                border-radius: 8px;
                box-shadow: 0 5px 15px rgba(0,0,0,0.2);
                z-index: 9999;
                display: flex;
                align-items: center;
                gap: 15px;
                font-family: 'Tajawal', sans-serif;
                transform: translateY(100px);
                opacity: 0;
                transition: all 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55);
            `;
            toast.innerHTML = `<i class="fa-solid fa-circle-check fa-lg"></i> {{ __('messages.added_to_cart') }} (${product.name})`;
            document.body.appendChild(toast);
            
            setTimeout(() => {
                toast.style.transform = 'translateY(0)';
                toast.style.opacity = '1';
            }, 100);
            
            setTimeout(() => {
                toast.style.transform = 'translateY(100px)';
                toast.style.opacity = '0';
                setTimeout(() => toast.remove(), 400);
            }, 3000);
        }

        function toggleTheme(e) {
            if(e) e.preventDefault();
            document.documentElement.classList.toggle('dark-mode');
            const isDark = document.documentElement.classList.contains('dark-mode');
            localStorage.setItem('theme', isDark ? 'dark' : 'light');
            updateThemeIcons();
        }

        function updateThemeIcons() {
            const isDark = document.documentElement.classList.contains('dark-mode');
            document.querySelectorAll('.theme-toggle-btn i').forEach(icon => {
                icon.className = isDark ? 'fa-solid fa-sun' : 'fa-solid fa-moon';
            });
        }
        
        function toggleMobileMenu() {
            const nav = document.getElementById('main-nav');
            const actions = document.querySelector('.navbar-actions');
            if (nav) nav.classList.toggle('show');
            if (actions) actions.classList.toggle('show');
        }
        
        function togglePassword(icon, inputId) {
            const input = document.getElementById(inputId);
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            updateThemeIcons();
        });
    </script>
</body>
</html>
