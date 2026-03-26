@extends('layouts.app')

@section('title', __('messages.products'))

@push('styles')
<style>
    .page-header {
        background: linear-gradient(135deg, var(--secondary) 0%, rgba(212, 163, 115,0.8) 100%);
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
        border-color: var(--secondary);
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
        background-color: #fdfdfd;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #ddd;
        border-bottom: 1px solid #eee;
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
        color: var(--secondary);
    }
    .card-text {
        color: var(--text-muted);
        font-size: 14px;
        margin-bottom: 15px;
        flex-grow: 1;
    }
    .price-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
    }
    .price {
        color: var(--primary);
        font-size: 20px;
        font-weight: 700;
    }
    .store-link {
        font-size: 13px;
        color: var(--text-muted);
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 5px;
    }
    .store-link:hover {
        color: var(--primary);
    }
    
    #loading {
        text-align: center;
        padding: 50px 0;
        font-size: 24px;
        color: var(--secondary);
    }
    
    .category-slider-container {
        overflow-x: auto;
        white-space: nowrap;
        margin-bottom: 40px;
        padding: 5px 0 20px 0; /* extra padding for shadow */
        /* Hide scrollbar for Chrome, Safari and Opera */
        -ms-overflow-style: none;
        scrollbar-width: none;
    }
    .category-slider-container::-webkit-scrollbar {
        display: none;
    }
    .category-list {
        display: inline-flex;
        gap: 15px;
        list-style: none;
        padding: 0 10px;
        margin: 0;
    }
    .category-item {
        background: white;
        color: var(--text-dark);
        padding: 14px 28px;
        border-radius: 50px;
        cursor: pointer;
        font-weight: 600;
        font-size: 16px;
        transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        border: 2px solid transparent;
        display: flex;
        align-items: center;
        box-shadow: 0 4px 10px rgba(0,0,0,0.06);
    }
    .category-item i {
        font-size: 24px;
        transition: transform 0.3s ease, color 0.3s ease;
    }
    .category-item:hover, .category-item.active {
        background: var(--secondary);
        color: white;
        border-color: rgba(212, 163, 115, 0.8);
        box-shadow: 0 8px 15px rgba(212, 163, 115, 0.35);
        transform: translateY(-3px);
    }
    .category-item:hover i, .category-item.active i {
        color: white !important;
        transform: scale(1.2) rotate(-8deg);
    }
</style>
@endpush

@section('content')

<div class="page-header">
    <div class="container">
        <h1>{{ __('messages.latest_products') }}</h1>
        <p>{{ __('messages.manzili_desc') }}</p>
    </div>
</div>

<div class="container" style="margin-bottom: 60px;">
    <!-- Filter Bar -->
    <div class="filter-bar" style="margin-bottom: 20px;">
        <input type="text" id="search-input" class="search-input" placeholder="{{ __('messages.search_placeholder') }}">
        
        <div>
            <select id="sort-select" style="padding: 12px; border: 1px solid #ddd; border-radius: 5px; outline:none; font-family: inherit;">
                <option value="newest">{{ __('messages.sort_newest') }}</option>
                <option value="price_low">{{ __('messages.sort_price_low') }}</option>
                <option value="price_high">{{ __('messages.sort_price_high') }}</option>
                <option value="highest_rated">{{ __('messages.sort_highest_rated') }}</option>
            </select>
            <button id="search-btn" class="btn-secondary" style="border-radius: 5px; margin-right: 10px;">{{ __('messages.search') }}</button>
        </div>
    </div>

    <!-- Category Slider -->
    <div class="category-slider-container">
        <ul class="category-list" id="category-list">
            <li class="category-item active" data-category=""><i class="fa-solid fa-border-all" style="margin-inline-end: 8px; color: #6c757d;"></i>{{ __('messages.all') }}</li>
            <li class="category-item" data-category="المأكولات المنزلية"><i class="fa-solid fa-utensils" style="margin-inline-end: 8px; color: #ff9800;"></i>{{ __('messages.cat_home_food') }}</li>
            <li class="category-item" data-category="الحلويات والمعجنات"><i class="fa-solid fa-cookie-bite" style="margin-inline-end: 8px; color: #d2691e;"></i>{{ __('messages.cat_sweets') }}</li>
            <li class="category-item" data-category="العطور والبخور"><i class="fa-solid fa-bottle-droplet" style="margin-inline-end: 8px; color: #9c27b0;"></i>{{ __('messages.cat_perfumes') }}</li>
            <li class="category-item" data-category="الاكسسوارات"><i class="fa-solid fa-gem" style="margin-inline-end: 8px; color: #00bcd4;"></i>{{ __('messages.cat_accessories') }}</li>
            <li class="category-item" data-category="مستحضرات التجميل والعناية"><i class="fa-solid fa-spa" style="margin-inline-end: 8px; color: #e91e63;"></i>{{ __('messages.cat_cosmetics') }}</li>
            <li class="category-item" data-category="إلكترونيات"><i class="fa-solid fa-laptop" style="margin-inline-end: 8px; color: #607d8b;"></i>{{ __('messages.cat_electronics') }}</li>
            <li class="category-item" data-category="أعمال يدوية"><i class="fa-solid fa-palette" style="margin-inline-end: 8px; color: #f44336;"></i>{{ __('messages.cat_handicrafts') }}</li>
            <li class="category-item" data-category="الأزياء والملابس"><i class="fa-solid fa-shirt" style="margin-inline-end: 8px; color: #3f51b5;"></i>{{ __('messages.cat_fashion') }}</li>
            <li class="category-item" data-category="الألعاب"><i class="fa-solid fa-gamepad" style="margin-inline-end: 8px; color: #4caf50;"></i>{{ __('messages.cat_toys') }}</li>
        </ul>
    </div>

    <!-- Products Listing -->
    <div id="loading" style="display: none;">
        <i class="fa-solid fa-spinner fa-spin"></i> {{ __('messages.loading') }}
    </div>

    <div class="grid" id="products-grid">
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
    let currentCategory = '';

    async function fetchProducts(page = 1, searchQuery = '', category = '', sort = 'newest') {
        const grid = document.getElementById('products-grid');
        const loading = document.getElementById('loading');
        
        grid.innerHTML = '';
        loading.style.display = 'block';

        try {
            let url = `/api/products?page=${page}`;
            if(searchQuery) url += `&q=${encodeURIComponent(searchQuery)}`;
            if(category) url += `&category=${encodeURIComponent(category)}`;
            if(sort) url += `&sort=${encodeURIComponent(sort)}`;
            const token = localStorage.getItem('auth_token');
            const headers = { 'Accept': 'application/json' };
            if(token) headers['Authorization'] = `Bearer ${token}`;
            
            const response = await fetch(url, { headers });
            const data = await response.json();
            
            loading.style.display = 'none';

            if (data.data.length === 0) {
                grid.innerHTML = '<div style="grid-column: 1 / -1; text-align:center; padding: 40px; color: #999;">{{ __("messages.no_products_found") }}</div>';
                return;
            }

            // Render products
            data.data.forEach((product, idx) => {
                const imageUrl = product.images && product.images.length > 0 ? product.images[0] : null;
                const delay = (idx % 12) * 50; // staggered delay

                const cardHtml = `
                    <div class="card" data-aos="fade-up" data-aos-delay="${delay}">
                        <div class="card-img">
                            ${imageUrl ? `<img src="${imageUrl}" style="width:100%; height:100%; object-fit:cover;">` : `<i class="fa-solid fa-box-open fa-4x"></i>`}
                        </div>
                        <div class="card-body">
                            <a href="/products/${product.slug}" class="card-title">${product.name}</a>
                            <p class="card-text">${product.description ? product.description.substring(0, 100) + '...' : '{{ __('messages.product_desc_unavailable') }}'}</p>
                            
                            <div class="price-row">
                                <span class="price">${product.price} {{ __('messages.sar') }}</span>
                                ${product.store ? `<a href="/stores/${product.store.slug}" class="store-link"><i class="fa-solid fa-store"></i> ${product.store.name}</a>` : ''}
                            </div>
                            <div style="color:#FFD700; font-size:14px; margin-bottom:15px; display:flex; align-items:center; justify-content:space-between;">
                                <div><i class="fa-solid fa-star"></i> <span style="color:var(--text-muted); margin:0 5px;">(${product.reviews_avg_rating ? Number(product.reviews_avg_rating).toFixed(1) : '0.0'})</span></div>
                                <div style="font-size:11px; color:#ccc; font-weight:bold;">ID: ${product.id}</div>
                            </div>
                            
                            ${new URLSearchParams(window.location.search).get('select_mode') === 'featured' ? 
                                `<a href="/admin/dashboard?featured_type=product&featured_id=${product.id}" class="btn-primary" style="display:block; text-align:center; background:#ffc107; color:black; margin-bottom:10px;"><i class="fa-solid fa-check"></i> اختيار هذا المنتج للإعلان</a>` : ''}
                            <div style="display:flex; gap: 10px; margin-top: auto;">
                                <button onclick="toggleFavorite(${product.id}, this)" class="btn-outline" style="padding: 10px 15px; border: 1px solid #ddd; border-radius: 8px; background: white; color: ${product.is_favorited ? 'red' : '#999'};" title="{{ __('messages.add_favorite') }}"><i class="fa-solid fa-heart"></i></button>
                                <a href="/products/${product.slug}" class="btn-primary" style="flex:1; text-align:center; box-sizing: border-box; background: var(--secondary);">{{ __('messages.details') }}</a>
                                <button onclick='addToCart({
                                    id: ${product.id},
                                    name: "${product.name.replace(/"/g, '&quot;')}",
                                    price: ${product.price},
                                    slug: "${product.slug}",
                                    image: ${imageUrl ? `"${imageUrl}"` : "null"},
                                    store_id: ${product.store ? product.store.id : 0},
                                    store_name: "${product.store ? product.store.name.replace(/"/g, '&quot;') : '{{ __('messages.unspecified') }}'}",
                                    store_phone: "${product.store ? (product.store.whatsapp_number || (product.store.user ? product.store.user.phone : null) || product.store.contact_info || "966500000000") : "966500000000"}"
                                })' class="btn-primary" style="padding: 10px 15px;" title="{{ __('messages.add_to_cart') }}"><i class="fa-solid fa-cart-plus"></i></button>
                            </div>
                        </div>
                    </div>
                `;
                grid.innerHTML += cardHtml;
            });

        } catch (error) {
            loading.style.display = 'none';
            grid.innerHTML = '<div style="color:red; text-align:center; grid-column: 1/-1;">{{ __('messages.error_loading_data') }}</div>';
        }
    }

    async function toggleFavorite(productId, btn) {
        const token = localStorage.getItem('auth_token');
        if(!token) {
            alert('{{ __("messages.favorite_login_required") }}');
            window.location.href = '/login';
            return;
        }

        try {
            const res = await fetch(`/api/favorites/products/${productId}`, {
                method: 'POST',
                headers: { 
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json' 
                }
            });
            
            if (res.ok) {
                const isRed = btn.style.color === 'red';
                btn.style.color = isRed ? '#999' : 'red';
            } else {
                alert('{{ __('messages.error_occurred') }}');
            }
        } catch (error) {
            alert('{{ __('messages.connection_error') }}');
        }
    }

    function triggerFetch() {
        const q = document.getElementById('search-input').value;
        const sort = document.getElementById('sort-select').value;
        currentPage = 1;
        fetchProducts(currentPage, q, currentCategory, sort);
    }

    document.getElementById('search-btn').addEventListener('click', triggerFetch);
    
    document.getElementById('sort-select').addEventListener('change', triggerFetch);

    document.getElementById('search-input').addEventListener('keypress', (e) => {
        if (e.key === 'Enter') triggerFetch();
    });

    // Category click handler
    document.querySelectorAll('.category-item').forEach(item => {
        item.addEventListener('click', function() {
            document.querySelectorAll('.category-item').forEach(i => i.classList.remove('active'));
            this.classList.add('active');
            currentCategory = this.getAttribute('data-category');
            triggerFetch();
        });
    });

    // Initial fetch
    document.addEventListener('DOMContentLoaded', () => {
        const sort = document.getElementById('sort-select').value;
        fetchProducts(1, '', '', sort);
    });
</script>
@endpush
