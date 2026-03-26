@extends('layouts.app')

@section('title', __('messages.discover_best_stores'))

@push('styles')
<style>
    /* Hero Section */
    .hero {
        background: linear-gradient(135deg, rgba(140, 163, 121,0.05) 0%, rgba(212, 163, 115,0.08) 100%);
        padding: 120px 0 100px;
        text-align: center;
        position: relative;
        overflow: hidden;
    }
    .hero::before {
        content: '';
        position: absolute;
        width: 400px;
        height: 400px;
        background: var(--primary);
        opacity: 0.15;
        border-radius: 50%;
        top: -100px;
        right: -100px;
        filter: blur(80px);
    }
    .hero::after {
        content: '';
        position: absolute;
        width: 300px;
        height: 300px;
        background: var(--secondary);
        opacity: 0.15;
        border-radius: 50%;
        bottom: -100px;
        left: -100px;
        filter: blur(80px);
    }
    .hero h1 {
        font-size: 56px;
        font-weight: 800;
        color: var(--text-dark);
        margin-bottom: 25px;
        line-height: 1.2;
        position: relative;
        z-index: 1;
    }
    .hero h1 span {
        background: linear-gradient(to right, var(--primary), var(--secondary));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }
    .hero p {
        font-size: 20px;
        color: var(--text-muted);
        max-width: 650px;
        margin: 0 auto 40px;
        position: relative;
        z-index: 1;
        line-height: 1.8;
    }
    
    /* Section */
    .section {
        padding: 60px 0;
    }
    .section-title {
        text-align: center;
        font-size: 32px;
        color: var(--text-dark);
        margin-bottom: 40px;
        position: relative;
    }
    .section-title::after {
        content: '';
        display: block;
        width: 60px;
        height: 3px;
        background-color: var(--secondary);
        margin: 10px auto 0;
        border-radius: 2px;
    }

    /* Grid */
    .grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 30px;
    }

    /* Card */
    .card {
        background: var(--white);
        border-radius: 12px;
        border: 1px solid rgba(0,0,0,0.05);
        overflow: hidden;
        display: flex;
        flex-direction: column;
    }
    .card-img {
        width: 100%;
        height: 200px;
        object-fit: cover;
    }
    .card-body {
        padding: 20px;
        flex-grow: 1;
        display: flex;
        flex-direction: column;
    }
    .card-title {
        font-size: 20px;
        font-weight: 700;
        margin: 0 0 10px;
    }
    .card-text {
        color: var(--text-muted);
        font-size: 14px;
        margin-bottom: 15px;
        flex-grow: 1;
    }
    .rating {
        color: #FFD700;
        margin-bottom: 15px;
        font-size: 14px;
    }
    .rating span {
        color: var(--text-muted);
        margin-right: 5px;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .hero {
            padding: 80px 0 60px;
        }
        .hero h1 {
            font-size: 36px;
        }
        .hero p {
            font-size: 16px;
            padding: 0 15px;
        }
        .hero div[data-aos="fade-up"] {
            flex-direction: column;
            align-items: center;
        }
        .hero div[data-aos="fade-up"] a {
            width: 100%;
            max-width: 300px;
            text-align: center;
        }
        .section {
            padding: 40px 15px;
        }
        .section-title {
            font-size: 26px;
        }
    }
</style>
@endpush

@section('content')

<!-- Hero Section -->
<section class="hero" data-aos="fade-down">
    <div class="container" style="position: relative; z-index: 2;">
        <h1 data-aos="fade-up" data-aos-delay="100">{{ __('messages.discover_creativity') }} <span>{{ __('messages.productive_families') }}</span></h1>
        <p data-aos="fade-up" data-aos-delay="300">{{ __('messages.home_hero_desc') }}</p>
        <div style="display: flex; gap: 20px; justify-content: center; margin-top: 20px;" data-aos="fade-up" data-aos-delay="500">
            <a href="/stores" class="btn-primary" style="padding: 12px 30px; font-size: 16px;"><i class="fa-solid fa-store"></i> {{ __('messages.browse_stores') }}</a>
            <a href="/login" class="btn-secondary" style="background: white; color: var(--text-dark); border: 1px solid #ddd; box-shadow: 0 4px 6px rgba(0,0,0,0.05); padding: 12px 30px; font-size: 16px;"><i class="fa-solid fa-rocket" style="color:var(--secondary);"></i> {{ __('messages.join_as_seller') }}</a>
        </div>
    </div>
</section>

<!-- Featured Stores -->
<section class="section container" data-aos="fade-up">
    <h2 class="section-title">{{ __('messages.featured_stores') }}</h2>
    <div class="grid" id="featured-stores">
        <div style="text-align:center; grid-column:1/-1; color:var(--text-muted); padding:30px;"><i class="fa-solid fa-spinner fa-spin fa-2x"></i> {{ __('messages.loading') }}</div>
    </div>
</section>

<!-- Latest Products -->
<section class="section container" style="background-color: white; border-radius: 10px; padding: 40px; margin-bottom: 50px;" data-aos="fade-up" data-aos-delay="200">
    <h2 class="section-title">{{ __('messages.latest_products') }}</h2>
    <div class="grid" id="latest-products">
        <div style="text-align:center; grid-column:1/-1; color:var(--text-muted); padding:30px;"><i class="fa-solid fa-spinner fa-spin fa-2x"></i> {{ __('messages.loading') }}</div>
    </div>
</section>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', async () => {
        // Load Featured Stores
        try {
            const resS = await fetch('/api/featured/stores');
            const dataS = await resS.json();
            const gridS = document.getElementById('featured-stores');
            
            if(!dataS.data || dataS.data.length === 0) {
                gridS.innerHTML = '<div style="grid-column:1/-1; text-align:center; color:#999;">{{ __("messages.no_stores_found") }}</div>';
            } else {
                let htmlS = '';
                dataS.data.slice(0, 4).forEach((store, idx) => {
                    const delay = idx * 100;
                    const rating = store.reviews_avg_rating ? Number(store.reviews_avg_rating).toFixed(1) : '0.0';
                    const logo = store.logo ? `<img src="${store.logo}" style="width:100%; height:100%; object-fit:cover; border-radius:50%;">` : `<i class="fa-solid fa-store fa-3x" style="color:var(--primary);"></i>`;
                    
                    htmlS += `
                    <div class="card" data-aos="fade-up" data-aos-delay="${delay}">
                        <div style="height: 150px; background: #eee; display: flex; align-items:center; justify-content:center; position:relative;">
                            ${store.cover ? `<img src="${store.cover}" style="width:100%; height:100%; object-fit:cover;">` : ''}
                            <div style="position:absolute; width:80px; height:80px; background:white; border-radius:50%; display:flex; align-items:center; justify-content:center; box-shadow:0 2px 5px rgba(0,0,0,0.1); bottom:-40px;">
                                ${logo}
                            </div>
                        </div>
                        <div class="card-body" style="padding-top: 50px; text-align:center;">
                            <h3 class="card-title">${store.name} ${store.kyc_status === 'approved' ? '<i class="fa-solid fa-circle-check" style="color:#25D366; font-size:14px;"></i>' : ''}</h3>
                            <div class="rating" style="justify-content:center; display:flex;">
                                <i class="fa-solid fa-star"></i> <span>(${rating})</span>
                            </div>
                            <a href="/stores/${store.slug}" class="btn-primary" style="display:block; text-align:center; margin-top:auto;">{{ __('messages.visit_store') }}</a>
                        </div>
                    </div>`;
                });
                gridS.innerHTML = htmlS;
            }
        } catch(e) {}

        // Load Latest/Featured Products
        try {
            const resP = await fetch('/api/featured/products');
            const dataP = await resP.json();
            const gridP = document.getElementById('latest-products');
            if(!dataP.data || dataP.data.length === 0) {
                gridP.innerHTML = '<div style="grid-column:1/-1; text-align:center; color:#999;">{{ __("messages.no_products_found") }}</div>';
            } else {
                let htmlP = '';
                dataP.data.slice(0, 4).forEach((p, idx) => {
                    const delay = idx * 100;
                    const storeName = p.store ? p.store.name : '{{ __("messages.deleted_store") }}';
                    const img = (p.images && p.images.length > 0) ? `<img src="${p.images[0]}" style="width:100%;height:100%;object-fit:cover;">` : `<i class="fa-solid fa-cake-candles fa-4x" style="color:var(--text-muted);"></i>`;
                    
                    htmlP += `
                    <div class="card" data-aos="zoom-in" data-aos-delay="${delay}">
                        <div style="height: 200px; background: #fdfdfd; display: flex; align-items:center; justify-content:center; border-bottom: 1px solid #eee;">
                            ${img}
                        </div>
                        <div class="card-body">
                            <h3 class="card-title">${p.name}</h3>
                            <p class="card-text">${p.description ? p.description.substring(0,60)+'...' : ''}</p>
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 5px;">
                                <strong style="color: var(--primary); font-size: 18px;">${p.price} {{ __('messages.sar') }}</strong>
                                <span style="font-size: 12px; color: var(--text-muted);"><i class="fa-solid fa-store"></i> ${storeName}</span>
                            </div>
                            <div style="color:#FFD700; font-size:14px; margin-bottom:15px; display:flex; align-items:center;">
                                <i class="fa-solid fa-star"></i> <span style="color:var(--text-muted); margin:0 5px;">(${p.reviews_avg_rating ? Number(p.reviews_avg_rating).toFixed(1) : '0.0'})</span>
                            </div>
                            <a href="/products/${p.slug}" class="btn-secondary" style="display:block; text-align:center;">{{ __('messages.details_order') }}</a>
                        </div>
                    </div>`;
                });
                gridP.innerHTML = htmlP;
            }
        } catch(e) {}
    });
</script>
@endpush
