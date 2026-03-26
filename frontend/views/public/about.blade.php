@extends('layouts.app')

@section('title', __('messages.about_page_title'))

@push('styles')
<style>
    .about-header {
        background: linear-gradient(135deg, var(--primary) 0%, rgba(140, 163, 121,0.8) 100%);
        color: white;
        padding: 80px 0;
        text-align: center;
        margin-bottom: 50px;
    }
    .about-header h1 {
        font-size: 42px;
        margin-bottom: 15px;
    }
    .about-header p {
        font-size: 18px;
        max-width: 700px;
        margin: 0 auto;
        opacity: 0.9;
    }
    .section-title {
        color: var(--primary);
        font-size: 32px;
        margin-bottom: 25px;
        text-align: center;
        position: relative;
    }
    .section-title::after {
        content: '';
        position: absolute;
        bottom: -10px;
        left: 50%;
        transform: translateX(-50%);
        width: 80px;
        height: 3px;
        background: var(--secondary);
        border-radius: 2px;
    }
    .about-content {
        margin-bottom: 60px;
    }
    .feature-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 30px;
        margin-top: 50px;
    }
    .feature-card {
        background: white;
        padding: 40px 30px;
        border-radius: 12px;
        text-align: center;
        box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        transition: transform 0.3s;
    }
    html.dark-mode .feature-card {
        background: #1E222A;
    }
    .feature-card:hover {
        transform: translateY(-10px);
    }
    .feature-icon {
        width: 80px;
        height: 80px;
        background: rgba(140, 163, 121,0.1);
        color: var(--primary);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 32px;
        margin: 0 auto 20px;
    }
    .feature-card h3 {
        font-size: 22px;
        margin-bottom: 15px;
        color: var(--text-dark);
    }
    .feature-card p {
        color: var(--text-muted);
        font-size: 16px;
        line-height: 1.6;
    }
    .mission-vision-wrapper {
        display: flex;
        flex-wrap: wrap;
        gap: 40px;
        margin-top: 60px;
        margin-bottom: 60px;
    }
    .mv-box {
        flex: 1;
        min-width: 300px;
        background: var(--bg-light);
        padding: 40px;
        border-radius: 12px;
        border-right: 4px solid var(--secondary);
    }
    html[dir="ltr"] .mv-box {
        border-right: none;
        border-left: 4px solid var(--secondary);
    }
    html.dark-mode .mv-box {
        background: #2D3748;
    }
    .mv-box h3 {
        color: var(--secondary);
        font-size: 26px;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 15px;
    }
    .mv-box p {
        color: var(--text-muted);
        font-size: 16px;
        line-height: 1.8;
    }
</style>
@endpush

@section('content')

<div class="about-header" data-aos="fade-down">
    <div class="container">
        <h1>{{ __('messages.about_hero_title') }}</h1>
        <p>{{ __('messages.about_hero_desc') }}</p>
    </div>
</div>

<div class="container about-content">
    
    <div class="mission-vision-wrapper">
        <div class="mv-box" data-aos="fade-up" data-aos-delay="100">
            <h3><i class="fa-solid fa-eye"></i> {{ __('messages.our_vision') }}</h3>
            <p>{{ __('messages.our_vision_desc') }}</p>
        </div>
        
        <div class="mv-box" data-aos="fade-up" data-aos-delay="200" style="border-color: var(--primary);">
            <h3 style="color: var(--primary);"><i class="fa-solid fa-bullseye"></i> {{ __('messages.our_mission') }}</h3>
            <p>{{ __('messages.our_mission_desc') }}</p>
        </div>
    </div>

    <h2 class="section-title" data-aos="fade-up">{{ __('messages.why_choose_us_about') }}</h2>
    
    <div class="feature-grid">
        <div class="feature-card" data-aos="zoom-in" data-aos-delay="100">
            <div class="feature-icon">
                <i class="fa-solid fa-hand-holding-heart"></i>
            </div>
            <h3>{{ __('messages.support_local') }}</h3>
            <p>{{ __('messages.support_local_desc') }}</p>
        </div>
        
        <div class="feature-card" data-aos="zoom-in" data-aos-delay="200">
            <div class="feature-icon" style="background: rgba(212, 163, 115,0.1); color: var(--secondary);">
                <i class="fa-brands fa-whatsapp"></i>
            </div>
            <h3>{{ __('messages.direct_communication') }}</h3>
            <p>{{ __('messages.direct_communication_desc') }}</p>
        </div>
        
        <div class="feature-card" data-aos="zoom-in" data-aos-delay="300">
            <div class="feature-icon" style="background: rgba(76,175,80,0.1); color: #4CAF50;">
                <i class="fa-solid fa-shield-halved"></i>
            </div>
            <h3>{{ __('messages.safe_shopping') }}</h3>
            <p>{{ __('messages.safe_shopping_desc') }}</p>
        </div>
    </div>

</div>

@endsection
