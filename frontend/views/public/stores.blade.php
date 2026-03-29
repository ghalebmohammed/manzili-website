@extends('layouts.app')

@section('title', __('messages.stores'))

@push('styles')
<style>
    .page-header {
        background: linear-gradient(135deg, var(--primary) 0%, rgba(140, 163, 121,0.8) 100%);
        color: white;
        padding: 60px 0;
        text-align: center;
        margin-bottom: 40px;
    }
    .page-header h1 {
        font-size: 36px;
        margin-bottom: 10px;
    }
    
    .filter-bar {
        background: white;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.05);
        margin-bottom: 40px;
        display: flex;
        gap: 15px;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
    }
    
    .search-input {
        flex: 1;
        min-width: 250px;
        padding: 12px 20px;
        border: 1px solid #ddd;
        border-radius: 50px;
        font-size: 16px;
        outline: none;
    }
    .search-input:focus {
        border-color: var(--primary);
    }
    
    .grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 30px;
    }

    .card {
        background: white;
        border-radius: 10px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        overflow: hidden;
        transition: transform 0.3s, box-shadow 0.3s;
        display: flex;
        flex-direction: column;
    }
    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 15px rgba(0,0,0,0.1);
    }
    .card-img {
        width: 100%;
        height: 180px;
        object-fit: cover;
        background-color: #eee;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #999;
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
        color: var(--text-dark);
        text-decoration: none;
    }
    .card-title:hover {
        color: var(--primary);
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
    .badges {
        margin-bottom: 10px;
    }
    .badge {
        display: inline-block;
        padding: 4px 10px;
        font-size: 12px;
        border-radius: 20px;
        background: rgba(140, 163, 121,0.1);
        color: var(--primary);
    }
    
    #loading {
        text-align: center;
        padding: 50px 0;
        font-size: 24px;
        color: var(--primary);
    }
</style>
@endpush

@section('content')

<div class="page-header">
    <div class="container">
        <h1>{{ __('messages.discover_stores') }}</h1>
        <p>{{ __('messages.explore_best_stores') }}</p>
    </div>
</div>

<div class="container" style="margin-bottom: 60px;">
    <!-- Filter Bar -->
    <div class="filter-bar" data-aos="fade-up">
        <input type="text" id="search-input" class="search-input" placeholder="{{ __('messages.search_store_placeholder') }}">
        
        <div style="display: flex; gap:10px;">
            <select id="sort-select" style="padding: 12px; border: 1px solid #ddd; border-radius: 5px; outline:none; font-family: inherit;">
                <option value="newest">{{ __('messages.sort_newest') }}</option>
                <option value="oldest">{{ __('messages.sort_oldest') }}</option>
                <option value="rating">{{ __('messages.highest_rated') }}</option>
            </select>
            <button id="search-btn" class="btn-primary" style="border-radius: 5px;">{{ __('messages.search') }}</button>
        </div>
    </div>

    <style>
        .store-tab {
            padding: 12px 30px; 
            border: none; 
            background: transparent; 
            border-radius: 30px; 
            cursor: pointer; 
            font-family: inherit; 
            font-weight: bold; 
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275); 
            color: var(--text-muted);
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 15px;
        }
        .store-tab:hover {
            color: var(--primary);
            background: rgba(140, 163, 121, 0.1);
            transform: translateY(-2px);
        }
        .store-tab.active {
            background: var(--primary) !important;
            color: white !important;
            box-shadow: 0 4px 15px rgba(140, 163, 121, 0.3);
            transform: scale(1.05);
        }
        .store-tab i {
            transition: transform 0.3s;
        }
        .store-tab:hover i {
            transform: scale(1.2) rotate(-5deg);
        }
        .store-tab.active i {
            transform: scale(1.1);
        }
    </style>

    <!-- Tabs for Filtering Types -->
    <div style="display:flex; justify-content:center; margin-bottom: 35px;" data-aos="fade-up" data-aos-delay="100">
        <div style="display:inline-flex; background:rgba(140, 163, 121, 0.05); border-radius: 35px; padding: 6px; box-shadow: inset 0 2px 4px rgba(0,0,0,0.02); gap: 5px;">
            <button class="store-tab active" data-type="all">
                <i class="fa-solid fa-border-all"></i> الكل
            </button>
            <button class="store-tab" data-type="home_projects">
                <i class="fa-solid fa-house-chimney-window"></i> مشاريع منزلية
            </button>
            <button class="store-tab" data-type="merchants">
                <i class="fa-solid fa-store"></i> تجار
            </button>
        </div>
    </div>

    <!-- Stores Listing -->
    <div id="loading" style="display: none;">
        <i class="fa-solid fa-spinner fa-spin"></i> {{ __('messages.loading') }}
    </div>

    <div class="grid" id="stores-grid">
        <!-- JS will populate this -->
    </div>

    <!-- Pagination -->
    <div id="pagination" style="display: flex; justify-content: center; gap: 10px; margin-top: 40px;">
        <!-- JS will populate this -->
    </div>
</div>

@endsection

@push('scripts')
<script>
    let currentPage = 1;
    let currentStoreType = 'all';

    async function fetchStores(page = 1, searchQuery = '', sortQuery = 'newest', typeQuery = 'all') {
        const grid = document.getElementById('stores-grid');
        const loading = document.getElementById('loading');
        
        grid.innerHTML = '';
        loading.style.display = 'block';

        try {
            let url = `/api/stores?page=${page}&sort=${sortQuery}`;
            if(searchQuery) url += `&q=${encodeURIComponent(searchQuery)}`;
            if(typeQuery && typeQuery !== 'all') url += `&type=${typeQuery}`;
            
            const response = await fetch(url);
            const data = await response.json();
            
            loading.style.display = 'none';

            if (data.data.length === 0) {
                grid.innerHTML = '<div style="grid-column: 1 / -1; text-align:center; padding: 40px; color: #999;">{{ __('messages.no_stores_match') }}</div>';
                return;
            }

            // Render stores
            data.data.forEach((store, idx) => {
                const delay = (idx % 12) * 50;
                const cardHtml = `
                    <div class="card" data-aos="fade-up" data-aos-delay="${delay}">
                        <div class="card-img" style="position:relative;">
                            ${store.cover_image ? `<img src="${store.cover_image}" style="width:100%; height:100%; object-fit:cover;">` : `<i class="fa-solid fa-store fa-4x" style="color:#ddd;"></i>`}
                            ${store.logo ? `<div style="position:absolute; bottom:-25px; right:20px; width:50px; height:50px; border-radius:50%; background:white; overflow:hidden; border:2px solid white; box-shadow:0 2px 4px rgba(0,0,0,0.1);"><img src="${store.logo}" style="width:100%; height:100%; object-fit:cover;"></div>` : ''}
                        </div>
                        <div class="card-body" style="padding-top: ${store.logo ? '35px' : '20px'};">
                            <div class="badges">
                                <span class="badge" style="background:var(--primary); color:white;">${store.store_activity || store.store_type || '{{ __('messages.miscellaneous') }}'}</span>
                            </div>
                            <a href="/stores/${store.slug}" class="card-title">${store.name} ${store.kyc_status === 'approved' ? '<i class="fa-solid fa-circle-check" style="color:#25D366; font-size:14px;"></i>' : ''}</a>
                            <p class="card-text">${store.description ? store.description.substring(0,80)+'...' : '{{ __('messages.store_default_desc') }}'}</p>
                            <div class="rating" style="display:flex; align-items:center; justify-content:space-between;">
                                <div><i class="fa-solid fa-star"></i> <span>(${store.reviews_avg_rating ? Number(store.reviews_avg_rating).toFixed(1) : '0.0'})</span></div>
                                <div style="font-size:11px; color:#ccc; font-weight:bold;">ID: ${store.id}</div>
                            </div>
                            
                            ${new URLSearchParams(window.location.search).get('select_mode') === 'featured' ? 
                                `<a href="/admin/dashboard?featured_type=store&featured_id=${store.id}" class="btn-primary" style="display:block; text-align:center; background:#ffc107; color:black; margin-bottom:10px;"><i class="fa-solid fa-check"></i> اختيار هذا المتجر للإعلان</a>` : ''}
                                
                            <a href="/stores/${store.slug}" class="btn-secondary" style="display:block; text-align:center; margin-top:auto;">{{ __('messages.store_details') }}</a>
                        </div>
                    </div>
                `;
                grid.innerHTML += cardHtml;
            });

            // Handle Pagination buttons
            // ... omitting complex pagination build for simplicity in prototype
        } catch (error) {
            loading.style.display = 'none';
            grid.innerHTML = '<div style="color:red; text-align:center; grid-column: 1/-1;">{{ __('messages.error_loading_data') }}</div>';
        }
    }

    document.getElementById('search-btn').addEventListener('click', () => {
        const q = document.getElementById('search-input').value;
        const sort = document.getElementById('sort-select').value;
        currentPage = 1;
        fetchStores(currentPage, q, sort, currentStoreType);
    });

    document.getElementById('sort-select').addEventListener('change', () => {
        document.getElementById('search-btn').click();
    });

    document.getElementById('search-input').addEventListener('keypress', (e) => {
        if (e.key === 'Enter') {
            document.getElementById('search-btn').click();
        }
    });

    document.querySelectorAll('.store-tab').forEach(tab => {
        tab.addEventListener('click', (e) => {
            document.querySelectorAll('.store-tab').forEach(t => t.classList.remove('active'));
            e.target.classList.add('active');
            
            currentStoreType = e.target.dataset.type;
            const q = document.getElementById('search-input').value;
            const sort = document.getElementById('sort-select').value;
            currentPage = 1;
            fetchStores(currentPage, q, sort, currentStoreType);
        });
    });

    // Initial fetch
    document.addEventListener('DOMContentLoaded', () => {
        fetchStores();
    });
</script>
@endpush
