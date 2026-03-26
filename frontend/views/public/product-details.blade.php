@extends('layouts.app')

@section('title', __('messages.product_details'))

@push('styles')
<style>
    .breadcrumb {
        background: transparent;
        padding: 20px 0;
        margin-bottom: 20px;
        color: var(--text-muted);
    }
    .breadcrumb a {
        color: var(--primary);
        text-decoration: none;
    }
    
    .product-layout {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 40px;
        margin-bottom: 60px;
    }
    @media (max-width: 768px) {
        .product-layout {
            grid-template-columns: 1fr;
        }
    }

    .product-images {
        background: var(--white);
        border-radius: 10px;
        padding: 20px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.05);
    }
    .main-image {
        width: 100%;
        height: 400px;
        object-fit: cover;
        border-radius: 8px;
        background: #fdfdfd;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #ddd;
        position: relative;
        overflow: hidden;
    }
    
    .thumbnail-gallery {
        display: flex;
        gap: 10px;
        margin-top: 15px;
        overflow-x: auto;
        padding-bottom: 5px;
    }
    .thumbnail {
        width: 70px;
        height: 70px;
        border-radius: 5px;
        object-fit: cover;
        cursor: pointer;
        opacity: 0.6;
        transition: opacity 0.3s, border 0.3s;
        border: 2px solid transparent;
        flex-shrink: 0;
    }
    .thumbnail:hover, .thumbnail.active {
        opacity: 1;
        border-color: var(--primary);
    }
    .slider-btn {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        background: rgba(0,0,0,0.5);
        color: white;
        border: none;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        transition: background 0.3s;
        z-index: 10;
        backdrop-filter: blur(5px);
    }
    .slider-btn:hover {
        background: var(--primary);
    }
    .slider-prev {
        left: 10px;
    }
    .slider-next {
        right: 10px;
    }
    html[dir="rtl"] .slider-prev { left: auto; right: 10px; transform: translateY(-50%) rotate(180deg); }
    html[dir="rtl"] .slider-next { right: auto; left: 10px; transform: translateY(-50%) rotate(180deg); }

    .product-info {
        background: var(--white);
        border-radius: 10px;
        padding: 30px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.05);
    }
    .product-title {
        font-size: 28px;
        font-weight: 700;
        margin-top: 0;
        margin-bottom: 10px;
        color: var(--text-dark);
    }
    .product-meta {
        display: flex;
        align-items: center;
        gap: 20px;
        margin-bottom: 20px;
        border-bottom: 1px solid #eee;
        padding-bottom: 20px;
    }
    .store-link {
        color: var(--text-muted);
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 5px;
    }
    .store-link:hover { color: var(--primary); }
    .product-price {
        font-size: 32px;
        font-weight: 700;
        color: var(--primary);
        margin-bottom: 20px;
    }
    .product-desc {
        color: var(--text-muted);
        line-height: 1.8;
        margin-bottom: 30px;
    }
    .whatsapp-btn {
        background: #25D366;
        color: white;
        padding: 15px 30px;
        border-radius: 5px;
        font-size: 18px;
        font-weight: 600;
        text-decoration: none;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        border: none;
        width: 100%;
        cursor: pointer;
        transition: background 0.3s;
    }
    .whatsapp-btn:hover { background: #1ebd5a; }

    /* Reviews Section */
    .reviews-section {
        background: var(--white);
        border-radius: 10px;
        padding: 40px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        margin-bottom: 60px;
    }
    .review-item {
        border-bottom: 1px solid #eee;
        padding: 20px 0;
    }
    .review-item:last-child { border-bottom: none; }
    .review-header { display: flex; justify-content: space-between; margin-bottom: 10px; }
    .review-user { font-weight: 600; color: var(--text-dark); }
    .review-date { color: var(--text-muted); font-size: 13px; }

    #loading-container {
        text-align: center;
        padding: 100px 0;
        font-size: 24px;
        color: var(--primary);
    }
</style>
@endpush

@section('content')

<div class="container">
    <div id="loading-container">
        <i class="fa-solid fa-spinner fa-spin"></i> {{ __('messages.loading_product_details') }}
    </div>

    <div id="product-content" style="display: none;" data-aos="fade-up" data-aos-duration="1000">
        <div class="breadcrumb">
            <a href="/">{{ __('messages.home') }}</a> /
            <a href="/products">{{ __('messages.products') }}</a> /
            <span id="bc-product-name">...</span>
        </div>

        <div class="product-layout">
            <!-- Images -->
            <div class="product-images">
                <div class="main-image" id="p-image-container">
                    <i class="fa-solid fa-image fa-5x"></i>
                </div>
                <div class="thumbnail-gallery" id="p-thumbnails-container">
                    <!-- JS Injected Thumbnails -->
                </div>
            </div>

            <!-- Details -->
            <div class="product-info">
                <h1 class="product-title" id="p-name">--</h1>
                
                <div class="product-meta">
                    <a href="#" class="store-link" id="p-store-link"><i class="fa-solid fa-store"></i> <span id="p-store-name">--</span></a>
                    <div style="color: #FFD700;">
                        <i class="fa-solid fa-star"></i> <span style="color:var(--text-muted);" id="p-rating">(0)</span>
                    </div>
                    <div style="color: var(--text-muted);"><i class="fa-solid fa-eye"></i> <span id="p-views">0</span> {{ __('messages.views') }}</div>
                </div>

                <div class="product-price" id="p-price">0 {{ __('messages.sar') }}</div>
                
                <h4 style="margin-bottom: 10px; color: var(--text-dark);">{{ __('messages.product_details') }}:</h4>
                <div class="product-desc" id="p-desc">
                    --
                </div>

                <div style="display:flex; gap: 15px; flex-wrap: wrap;">
                    <button class="btn-primary" id="add-to-cart-btn" style="flex: 1; padding: 15px; font-weight: bold; display: flex; align-items: center; justify-content: center; gap: 10px;">
                        <i class="fa-solid fa-cart-shopping fa-lg"></i> {{ __('messages.add_to_cart') }}
                    </button>
                    <button class="whatsapp-btn" id="order-btn" style="flex: 1;">
                        <i class="fa-brands fa-whatsapp fa-lg"></i> {{ __('messages.direct_buy') }}
                    </button>
                    <button class="btn-secondary" id="favorite-btn" style="display:none; flex-basis: 50px;" title="{{ __('messages.add_favorite') }}">
                        <i class="fa-regular fa-heart" id="fav-icon"></i>
                    </button>
                    <button class="btn-secondary" id="ai-analyze-btn" onclick="analyzeProductWithAI()" style="flex-basis: 100%; margin-top: 5px; background: linear-gradient(45deg, #a770ef, #cf8bf3, #fdb99b); border: none; font-weight: bold; position:relative; overflow:hidden;"><i class="fa-solid fa-robot"></i> الذكاء الاصطناعي (Manzili AI)</button>
                </div>
                <p style="font-size: 12px; color: var(--text-muted); margin-top: 15px; text-align: center;">
                    {{ __('messages.whatsapp_note') }}
                </p>
            </div>
        </div>

        <!-- AI Analysis Box -->
        <div id="ai-analysis-box" style="display:none; background: linear-gradient(135deg, rgba(230, 233, 255, 0.5) 0%, rgba(243, 230, 255, 0.5) 100%); border: 1px solid rgba(167, 112, 239, 0.2); border-radius: 12px; padding: 25px; margin-bottom: 30px; box-shadow: 0 5px 15px rgba(0,0,0,0.03);">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 15px; border-bottom: 1px solid rgba(167,112,239,0.1); padding-bottom: 15px;">
                <h3 style="margin:0; color:#4a00e0; display:flex; align-items:center; gap:10px;"><i class="fa-solid fa-wand-magic-sparkles"></i> رأي المساعد الذكي في المنتج</h3>
                <button onclick="document.getElementById('ai-analysis-box').style.display='none'" style="background:none; border:none; cursor:pointer; color:#999; font-size:18px;"><i class="fa-solid fa-times"></i></button>
            </div>
            <div id="ai-analysis-content" style="color:var(--text-dark); line-height: 1.8; font-size: 15px;">
                <div style="text-align:center; padding: 20px;"><i class="fa-solid fa-circle-notch fa-spin fa-2x" style="color:#a770ef;"></i><p>جاري استخلاص النصائح الذكية...</p></div>
            </div>
        </div>

        <!-- Reviews -->
        <div class="reviews-section">
            <h2 style="color: var(--primary); border-bottom: 2px solid #eee; padding-bottom: 15px; margin-bottom:20px;">{{ __('messages.reviews') }}</h2>
            
            <div id="add-review-section" style="display:none; background:#f9f9f9; padding:20px; border-radius:8px; margin-bottom:30px;">
                <h4 style="margin-top:0;">{{ __('messages.add_review') }}</h4>
                <div style="margin-bottom:15px;" id="star-rating-select">
                    <i class="fa-solid fa-star" data-rating="1" style="cursor:pointer; color:#ccc; font-size:20px;"></i>
                    <i class="fa-solid fa-star" data-rating="2" style="cursor:pointer; color:#ccc; font-size:20px;"></i>
                    <i class="fa-solid fa-star" data-rating="3" style="cursor:pointer; color:#ccc; font-size:20px;"></i>
                    <i class="fa-solid fa-star" data-rating="4" style="cursor:pointer; color:#ccc; font-size:20px;"></i>
                    <i class="fa-solid fa-star" data-rating="5" style="cursor:pointer; color:#ccc; font-size:20px;"></i>
                </div>
                <input type="hidden" id="selected-rating" value="0">
                <textarea id="review-comment" placeholder="{{ __('messages.share_opinion') }}" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px; margin-bottom:10px; font-family:inherit; min-height:80px; box-sizing: border-box;"></textarea>
                <button id="submit-review-btn" class="btn-primary">{{ __('messages.submit_review') }}</button>
            </div>

            <div id="reviews-list">
                <div style="text-align:center; color: #999; padding: 20px;">{{ __('messages.no_reviews') }}</div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    const productSlug = "{{ $slug }}";
    let currentProduct = null;

    document.addEventListener('DOMContentLoaded', async () => {
        const token = localStorage.getItem('auth_token');
        const userStr = localStorage.getItem('user');
        
        if (token) {
            document.getElementById('add-review-section').style.display = 'block';
            
            // Check if user is customer to show favorite button
            try {
                const userObj = JSON.parse(userStr);
                if(userObj && userObj.role === 'customer') {
                    document.getElementById('favorite-btn').style.display = 'flex';
                }
            } catch(e) {}
        }

        try {
            const headers = { 'Accept': 'application/json' };
            if (token) headers['Authorization'] = `Bearer ${token}`;

            const res = await fetch(`/api/products/${productSlug}`, { headers });
            if(!res.ok) {
                document.getElementById('loading-container').innerHTML = '<div style="color:red;">{{ __('messages.product_not_found') }}</div>';
                return;
            }
            const product = await res.json();
            currentProduct = product;

            // Populate Info
            document.getElementById('bc-product-name').innerText = product.name;
            document.getElementById('p-name').innerText = product.name;
            document.getElementById('p-price').innerText = product.price + ' {{ __('messages.sar') }}';
            document.getElementById('p-desc').innerText = product.description || '{{ __('messages.no_desc_available') }}';
            document.getElementById('p-views').innerText = product.views || 0;
            document.getElementById('p-rating').innerText = `(${product.reviews_avg_rating ? Number(product.reviews_avg_rating).toFixed(1) : 0})`;
            
            if(product.store) {
                document.getElementById('p-store-name').innerText = product.store.name;
                document.getElementById('p-store-link').href = `/stores/${product.store.slug}`;
            }

            if(product.images && product.images.length > 0) {
                let currentImageIndex = 0;
                const images = product.images;

                window.renderMainImage = (index) => {
                    let imgHtml = `<img src="${images[index]}" style="width:100%; height:100%; object-fit:cover; border-radius:8px; transition: opacity 0.3s;">`;
                    
                    if (images.length > 1) {
                        imgHtml += `
                            <button class="slider-btn slider-prev" onclick="changeImage(-1, event)"><i class="fa-solid fa-chevron-left"></i></button>
                            <button class="slider-btn slider-next" onclick="changeImage(1, event)"><i class="fa-solid fa-chevron-right"></i></button>
                        `;
                    }
                    document.getElementById('p-image-container').innerHTML = imgHtml;
                    
                    document.querySelectorAll('.thumbnail').forEach((thumb, i) => {
                        if (i === index) thumb.classList.add('active');
                        else thumb.classList.remove('active');
                    });
                };

                window.changeImage = (step, e) => {
                    if(e) e.stopPropagation();
                    currentImageIndex = (currentImageIndex + step + images.length) % images.length;
                    renderMainImage(currentImageIndex);
                };

                window.setImage = (index) => {
                    currentImageIndex = index;
                    renderMainImage(currentImageIndex);
                };

                renderMainImage(0);

                if (images.length > 1) {
                    let thumbsHtml = '';
                    images.forEach((img, idx) => {
                        thumbsHtml += `<img src="${img}" class="thumbnail ${idx === 0 ? 'active' : ''}" onclick="setImage(${idx})" alt="Thumb ${idx}">`;
                    });
                    document.getElementById('p-thumbnails-container').innerHTML = thumbsHtml;
                }
            }

            if(product.is_favorited) {
                const icon = document.getElementById('fav-icon');
                icon.classList.remove('fa-regular');
                icon.classList.add('fa-solid');
                icon.parentElement.style.color = 'red';
            }

            // Show Content
            document.getElementById('loading-container').style.display = 'none';
            document.getElementById('product-content').style.display = 'block';

            // Load Reviews
            fetchReviews();
        } catch(e) {
            document.getElementById('loading-container').innerHTML = '<div style="color:red;">{{ __('messages.server_connection_error') }}</div>';
        }
    });

    // Handle Favorite Toggle
    document.getElementById('favorite-btn').addEventListener('click', async () => {
        if(!currentProduct) return;
        const token = localStorage.getItem('auth_token');
        
        try {
            const res = await fetch(`/api/favorites/products/${currentProduct.id}`, {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            });
            const data = await res.json();
            if(res.ok) {
                const icon = document.getElementById('fav-icon');
                if(data.status === 'added') {
                    icon.classList.remove('fa-regular');
                    icon.classList.add('fa-solid');
                    icon.parentElement.style.color = 'red';
                } else {
                    icon.classList.remove('fa-solid');
                    icon.classList.add('fa-regular');
                    icon.parentElement.style.color = 'var(--text-dark)';
                }
            }
        } catch(e) { console.error('Error toggling favorite', e); }
    });

    // Handle Star Selection
    const stars = document.querySelectorAll('#star-rating-select .fa-star');
    stars.forEach(star => {
        star.addEventListener('click', () => {
            const rating = star.getAttribute('data-rating');
            document.getElementById('selected-rating').value = rating;
            stars.forEach(s => {
                if(s.getAttribute('data-rating') <= rating) {
                    s.style.color = '#FFD700';
                } else {
                    s.style.color = '#ccc';
                }
            });
        });
    });

    // Handle Review Submit
    document.getElementById('submit-review-btn').addEventListener('click', async () => {
        if(!currentProduct) return;
        
        const rating = document.getElementById('selected-rating').value;
        const comment = document.getElementById('review-comment').value;
        const token = localStorage.getItem('auth_token');

        if(rating == 0) {
            alert('{{ __('messages.select_rating_error') }}');
            return;
        }

        try {
            const res = await fetch(`/api/products/${currentProduct.id}/reviews`, {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ rating, comment })
            });

            if(res.ok) {
                // Reset form
                document.getElementById('selected-rating').value = 0;
                document.getElementById('review-comment').value = '';
                stars.forEach(s => s.style.color = '#ccc');
                alert('{{ __('messages.your_review_added_success') }}');
                fetchReviews(); // Reload reviews list
            } else {
                const err = await res.json();
                alert(err.message || '{{ __('messages.error_adding_review') }}');
            }
        } catch(e) {
            alert('{{ __('messages.connection_error') }}');
        }
    });

    async function fetchReviews() {
        try {
            const res = await fetch(`/api/products/${productSlug}/reviews`);
            const data = await res.json();
            
            if(data.data && data.data.length > 0) {
                let html = '';
                data.data.forEach(r => {
                    let stars = '';
                    for(let i=1; i<=5; i++) {
                        stars += `<i class="fa-solid fa-star" style="color:${i <= r.rating ? '#FFD700' : '#eee'}"></i> `;
                    }
                    html += `
                        <div class="review-item">
                            <div class="review-header">
                                <span class="review-user"><i class="fa-solid fa-user-circle"></i> ${r.user ? r.user.name : '{{ __('messages.customer_label') }}'}</span>
                                <span class="review-date">${new Date(r.created_at).toLocaleDateString()}</span>
                            </div>
                            <div style="margin-bottom: 10px;">${stars}</div>
                            <div style="color: var(--text-dark);">${r.comment || ''}</div>
                        </div>
                    `;
                });
                document.getElementById('reviews-list').innerHTML = html;
            }
        } catch(e) {}
    }

    // Handle Order WhatsApp
    document.getElementById('order-btn').addEventListener('click', async () => {
        if(!currentProduct) return;

        const token = localStorage.getItem('auth_token');
        const headers = { 'Accept': 'application/json', 'Content-Type': 'application/json' };
        if(token) headers['Authorization'] = `Bearer ${token}`;

        try {
            // Register Pending Sale
            const req = await fetch('/api/sales/initiate', {
                method: 'POST',
                headers,
                body: JSON.stringify({ product_id: currentProduct.id })
            });
            const resData = await req.json();

            // Construct Whatsapp URL
            const storePhone = currentProduct.store ? (currentProduct.store.whatsapp_number || (currentProduct.store.user ? currentProduct.store.user.phone : null) || currentProduct.store.contact_info) : null;
            const text = `{{ __('messages.hello_order_product') }} ( ${currentProduct.name} ) {{ __('messages.at_price') }} ${currentProduct.price} {{ __('messages.sar') }}.\n{{ __('messages.product_link') }}: ${window.location.href}`;
            const waUrl = `https://wa.me/${storePhone || "966500000000"}?text=${encodeURIComponent(text)}`;
            
            // Redirect
            window.open(waUrl, '_blank');

        } catch(e) { alert('{{ __('messages.error_try_later') }}'); }
    });

    document.getElementById('add-to-cart-btn').addEventListener('click', () => {
        if(!currentProduct) return;
        addToCart({
            id: currentProduct.id,
            name: currentProduct.name,
            price: currentProduct.price,
            slug: currentProduct.slug,
            image: currentProduct.images && currentProduct.images.length > 0 ? currentProduct.images[0] : null,
            store_id: currentProduct.store ? currentProduct.store.id : 0,
            store_name: currentProduct.store ? currentProduct.store.name : 'غير محدد',
            store_phone: currentProduct.store ? (currentProduct.store.whatsapp_number || (currentProduct.store.user ? currentProduct.store.user.phone : null) || currentProduct.store.contact_info) : ""
        });
    });

    async function analyzeProductWithAI() {
        if(!currentProduct) return;
        
        const box = document.getElementById('ai-analysis-box');
        const content = document.getElementById('ai-analysis-content');
        
        box.style.display = 'block';
        content.innerHTML = '<div style="text-align:center; padding: 20px;"><i class="fa-solid fa-circle-notch fa-spin fa-2x" style="color:#a770ef;"></i><p style="margin-top:10px; color:var(--text-muted);">يقوم المساعد الذكي بكتابة رأيه في المنتج الآن... 🤖</p></div>';
        
        box.scrollIntoView({ behavior: 'smooth', block: 'center' });
        
        try {
            const res = await fetch(`/api/assistant/analyze-product/${currentProduct.slug}`);
            const data = await res.json();
            
            if(res.ok) {
                content.innerHTML = data.reply;
            } else {
                content.innerHTML = `<div style="color:red; text-align:center;">${data.reply || 'حدث خطأ، يرجى المحاولة لاحقاً'}</div>`;
            }
        } catch(e) {
            content.innerHTML = '<div style="color:red; text-align:center;">تعذر الاتصال بخادم الذكاء الاصطناعي حالياً.</div>';
        }
    }
</script>
@endpush
