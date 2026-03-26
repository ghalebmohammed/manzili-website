@extends('layouts.app')

@section('title', __('messages.store_details'))

@push('styles')
<style>
    .store-header {
        position: relative;
        background: #f8f9fa;
        margin-bottom: 50px;
    }
    .store-cover {
        width: 100%;
        height: 300px;
        background: linear-gradient(135deg, var(--secondary) 0%, rgba(212, 163, 115,0.5) 100%);
        object-fit: cover;
    }
    .store-info-box {
        background: white;
        border-radius: 10px;
        padding: 30px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        max-width: 800px;
        margin: -80px auto 0;
        position: relative;
        z-index: 10;
        display: flex;
        align-items: center;
        gap: 30px;
    }
    .store-logo {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        background: white;
        border: 4px solid white;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #ddd;
        font-size: 3rem;
        object-fit: cover;
    }
    .store-details {
        flex: 1;
    }
    .store-title {
        font-size: 28px;
        font-weight: 700;
        margin: 0 0 10px;
        color: var(--text-dark);
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .verified-badge {
        color: #25D366;
        font-size: 18px;
    }
    .meta-list {
        display: flex;
        gap: 20px;
        color: var(--text-muted);
        font-size: 14px;
        margin-bottom: 15px;
    }
    .store-actions {
        display: flex;
        gap: 15px;
    }

    .tabs {
        display: flex;
        gap: 20px;
        border-bottom: 2px solid #eee;
        margin-bottom: 30px;
    }
    .tab-btn {
        background: none;
        border: none;
        padding: 15px 20px;
        font-size: 18px;
        font-weight: 600;
        color: var(--text-muted);
        cursor: pointer;
        border-bottom: 3px solid transparent;
        margin-bottom: -2px;
        transition: all 0.3s;
    }
    .tab-btn.active {
        color: var(--primary);
        border-bottom-color: var(--primary);
    }
    .tab-content { display: none; }
    .tab-content.active { display: block; }

    /* Product Grid inside Store */
    .grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 25px;
    }
    .card {
        background: white;
        border-radius: 10px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        overflow: hidden;
        transition: transform 0.3s;
        display: flex;
        flex-direction: column;
    }
    .card:hover { transform: translateY(-5px); box-shadow: 0 10px 15px rgba(0,0,0,0.1); }
    .card-img { height: 160px; background: #eee; display:flex; align-items:center; justify-content:center; color:#999; }
    .card-body { padding: 15px; flex-grow: 1; display:flex; flex-direction:column; }
    .card-title { font-size: 18px; font-weight: 700; color: var(--text-dark); text-decoration: none; margin-bottom: 10px; }
    .card-title:hover { color: var(--primary); }
    
    #loading-container { text-align: center; padding: 100px 0; font-size: 24px; color: var(--primary); }

    .phone-link-animated {
        text-decoration: none;
        color: inherit;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.3s ease;
        padding: 5px 10px;
        border-radius: 8px;
        margin: -5px -10px;
    }
    .phone-link-animated:hover {
        color: #25D366 !important;
        background-color: rgba(37, 211, 102, 0.1);
        transform: scale(1.05);
    }
    .phone-link-animated:active {
        transform: scale(0.95);
    }
    .phone-link-animated:hover i {
        color: #25D366 !important;
        animation: phoneShake 0.4s ease-in-out infinite alternate;
    }
    @keyframes phoneShake {
        0% { transform: rotate(0deg); }
        25% { transform: rotate(15deg); }
        50% { transform: rotate(0deg); }
        75% { transform: rotate(-15deg); }
        100% { transform: rotate(0deg); }
    }
</style>
@endpush

@section('content')

<div id="loading-container">
    <i class="fa-solid fa-spinner fa-spin"></i> {{ __('messages.loading_store_data') }}
</div>

<div id="store-content" style="display: none;" data-aos="fade-up" data-aos-duration="1000">
    <!-- Top Header -->
    <div class="store-header">
        <div class="store-cover" id="s-cover"></div>
        <div class="store-info-box">
            <div class="store-logo" id="s-logo">
                <i class="fa-solid fa-store"></i>
            </div>
            <div class="store-details">
                <h1 class="store-title">
                    <span id="s-name">--</span> 
                    <i class="fa-solid fa-circle-check verified-badge" title="{{ __('messages.verified_in_manzili') }}" id="s-verified" style="display:none;"></i>
                </h1>
                <div class="meta-list">
                    <span><i class="fa-solid fa-tags"></i> <span id="s-type">--</span></span>
                    <span><i class="fa-solid fa-box"></i> <span id="s-pcount">0</span> {{ __('messages.products_count_label') }}</span>
                    <span style="color:#FFD700;"><i class="fa-solid fa-star"></i> <span style="color:var(--text-muted);" id="s-rating">0</span></span>
                    <span><a href="#" id="s-phone-link-top" class="phone-link-animated"><i class="fa-solid fa-phone"></i> <span id="s-phone-top" dir="ltr">--</span></a></span>
                </div>
                <p style="color:var(--text-muted); margin-bottom: 20px;" id="s-desc">{{ __('messages.no_store_desc') }}</p>
                <div class="store-actions">
                    <button class="btn-primary" id="follow-btn" style="display:none;"><i class="fa-solid fa-user-plus"></i> <span id="follow-text">{{ __('messages.follow') }}</span></button>
                    <button class="btn-secondary" id="ai-analyze-btn" onclick="analyzeStoreWithAI()" style="background: linear-gradient(45deg, #a770ef, #cf8bf3, #fdb99b); border: none; font-weight: bold; position:relative; overflow:hidden;"><i class="fa-solid fa-robot"></i> تحليل ذكي للمتجر</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container" style="margin-bottom: 60px;">
        <!-- AI Analysis Box -->
        <div id="ai-analysis-box" style="display:none; background: linear-gradient(135deg, rgba(230, 233, 255, 0.5) 0%, rgba(243, 230, 255, 0.5) 100%); border: 1px solid rgba(167, 112, 239, 0.2); border-radius: 12px; padding: 25px; margin-bottom: 30px; box-shadow: 0 5px 15px rgba(0,0,0,0.03);">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 15px; border-bottom: 1px solid rgba(167,112,239,0.1); padding-bottom: 15px;">
                <h3 style="margin:0; color:#4a00e0; display:flex; align-items:center; gap:10px;"><i class="fa-solid fa-wand-magic-sparkles"></i> رأي المساعد الذكي (Manzili AI)</h3>
                <button onclick="document.getElementById('ai-analysis-box').style.display='none'" style="background:none; border:none; cursor:pointer; color:#999; font-size:18px;"><i class="fa-solid fa-times"></i></button>
            </div>
            <div id="ai-analysis-content" style="color:var(--text-dark); line-height: 1.8; font-size: 15px;">
                <div style="text-align:center; padding: 20px;"><i class="fa-solid fa-circle-notch fa-spin fa-2x" style="color:#a770ef;"></i><p>جاري تحليل بيانات المتجر...</p></div>
            </div>
        </div>

        <div class="tabs">
            <button class="tab-btn active" data-target="tab-products">{{ __('messages.store_products_tab') }}</button>
            <button class="tab-btn" data-target="tab-reviews">{{ __('messages.reviews_tab') }}</button>
            <button class="tab-btn" data-target="tab-about">{{ __('messages.about_store_tab') }}</button>
        </div>

        <!-- Products -->
        <div id="tab-products" class="tab-content active">
            <div class="grid" id="store-products-grid">
                <div style="grid-column:1/-1; text-align:center; padding:40px; color:#999;">{{ __('messages.loading_products') }}</div>
            </div>
        </div>

        <!-- Reviews -->
        <div id="tab-reviews" class="tab-content">
            <div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.05);" id="store-reviews-container">
                <div id="add-store-review-section" style="display:none; background:#f9f9f9; padding:20px; border-radius:8px; margin-bottom:30px;">
                    <h4 style="margin-top:0;">{{ __('messages.add_store_review') }}</h4>
                    <div style="margin-bottom:15px;" id="store-star-rating-select">
                        <i class="fa-solid fa-star" data-rating="1" style="cursor:pointer; color:#ccc; font-size:20px;"></i>
                        <i class="fa-solid fa-star" data-rating="2" style="cursor:pointer; color:#ccc; font-size:20px;"></i>
                        <i class="fa-solid fa-star" data-rating="3" style="cursor:pointer; color:#ccc; font-size:20px;"></i>
                        <i class="fa-solid fa-star" data-rating="4" style="cursor:pointer; color:#ccc; font-size:20px;"></i>
                        <i class="fa-solid fa-star" data-rating="5" style="cursor:pointer; color:#ccc; font-size:20px;"></i>
                    </div>
                    <input type="hidden" id="store-selected-rating" value="0">
                    <textarea id="store-review-comment" placeholder="{{ __('messages.share_store_opinion_placeholder') }}" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px; margin-bottom:10px; font-family:inherit; min-height:80px; box-sizing: border-box;"></textarea>
                    <button id="submit-store-review-btn" class="btn-primary">{{ __('messages.submit_review') }}</button>
                </div>

                <div id="store-reviews-list">
                    <div style="text-align:center; color:#999;">{{ __('messages.no_store_reviews') }}</div>
                </div>
            </div>
        </div>

        <!-- About -->
        <div id="tab-about" class="tab-content">
            <div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
                <h3 style="margin-top:0; color:var(--primary);">{{ __('messages.details_and_contact') }}</h3>
                <p id="s-about-desc">...</p>
                <hr style="border:0; border-top:1px solid #eee; margin: 20px 0;">
                <p><a href="#" id="s-phone-link-bottom" class="phone-link-animated"><i class="fa-solid fa-phone" style="color:var(--secondary); width:30px;"></i> <span id="s-contact">{{ __('messages.not_available') }}</span></a></p>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    const storeSlug = "{{ $slug }}";

    let currentStoreId = null;

    document.addEventListener('DOMContentLoaded', async () => {
        const token = localStorage.getItem('auth_token');
        const userStr = localStorage.getItem('user');

        if (token) {
            document.getElementById('add-store-review-section').style.display = 'block';

            try {
                const userObj = JSON.parse(userStr);
                if(userObj && userObj.role === 'customer') {
                    document.getElementById('follow-btn').style.display = 'inline-block';
                }
            } catch(e) {}
        }

        try {
            // Load Store Detail
            const headers = { 'Accept': 'application/json' };
            if (token) {
                headers['Authorization'] = `Bearer ${token}`;
            }

            const res = await fetch(`/api/stores/${storeSlug}`, { headers });
            if(!res.ok) {
                document.getElementById('loading-container').innerHTML = '<div style="color:red;">{{ __('messages.store_not_found') }}</div>';
                return;
            }
            const store = await res.json();
            currentStoreId = store.id;

            // set follow button state
            if (store.is_followed) {
                document.getElementById('follow-text').innerText = '{{ __('messages.followed') }}';
                document.getElementById('follow-btn').style.background = '#2A9D8F';
            }

            // Populate Info
            document.title = `منزلي - ${store.name}`;
            document.getElementById('s-name').innerText = store.name;
            if(store.kyc_status === 'approved') document.getElementById('s-verified').style.display = 'inline-block';
            
            document.getElementById('s-type').innerText = store.store_type || '{{ __('messages.miscellaneous') }}';
            document.getElementById('s-pcount').innerText = store.products_count || 0;
            document.getElementById('s-rating').innerText = `(${store.reviews_avg_rating ? Number(store.reviews_avg_rating).toFixed(1) : 0})`;
            
            const sellerPhone = store.whatsapp_number || (store.user ? store.user.phone : null) || store.contact_info || '{{ __('messages.not_available') }}';
            document.getElementById('s-phone-top').innerText = sellerPhone;
            
            document.getElementById('s-desc').innerText = store.description ? store.description.substring(0, 120) + '...' : '{{ __('messages.default_store_desc_long') }}';
            document.getElementById('s-about-desc').innerText = store.description || '{{ __('messages.no_deep_desc') }}';
            document.getElementById('s-contact').innerText = sellerPhone;

            if (sellerPhone !== '{{ __('messages.not_available') }}') {
                document.getElementById('s-phone-link-top').href = `tel:${sellerPhone}`;
                document.getElementById('s-phone-link-bottom').href = `tel:${sellerPhone}`;
            }

            if(store.logo) document.getElementById('s-logo').innerHTML = `<img src="${store.logo}" style="width:100%; height:100%; object-fit:cover; border-radius:50%;">`;
            if(store.cover_image) document.getElementById('s-cover').style.background = `url(${store.cover_image}) center/cover`;

            // Display Content
            document.getElementById('loading-container').style.display = 'none';
            document.getElementById('store-content').style.display = 'block';

            // Load Products
            fetchStoreProducts();
            
            // Load Reviews
            fetchReviews();

        } catch(e) {
            document.getElementById('loading-container').innerHTML = '<div style="color:red;">{{ __('messages.connection_error') }}</div>';
        }
    });

    // Handle Follow Button
    document.getElementById('follow-btn').addEventListener('click', async () => {
        if(!currentStoreId) return;
        const token = localStorage.getItem('auth_token');
        
        try {
            const res = await fetch(`/api/followers/stores/${currentStoreId}`, {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            });
            const data = await res.json();
            if(res.ok) {
                const followText = document.getElementById('follow-text');
                if(data.status === 'followed') {
                    followText.innerText = '{{ __('messages.followed') }}';
                    document.getElementById('follow-btn').style.background = '#2A9D8F';
                } else {
                    followText.innerText = '{{ __('messages.follow') }}';
                    document.getElementById('follow-btn').style.background = 'var(--primary)';
                }
            }
        } catch(e) { console.error('Error toggling follow', e); }
    });


    // Handle Star Selection
    const stars = document.querySelectorAll('#store-star-rating-select .fa-star');
    stars.forEach(star => {
        star.addEventListener('click', () => {
            const rating = star.getAttribute('data-rating');
            document.getElementById('store-selected-rating').value = rating;
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
    document.getElementById('submit-store-review-btn').addEventListener('click', async () => {
        if(!currentStoreId) return;
        
        const rating = document.getElementById('store-selected-rating').value;
        const comment = document.getElementById('store-review-comment').value;
        const token = localStorage.getItem('auth_token');

        if(rating == 0) {
            alert('{{ __('messages.select_rating_error') }}');
            return;
        }

        try {
            const res = await fetch(`/api/stores/${currentStoreId}/reviews`, {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ rating, comment })
            });

            if(res.ok) {
                document.getElementById('store-selected-rating').value = 0;
                document.getElementById('store-review-comment').value = '';
                stars.forEach(s => s.style.color = '#ccc');
                alert('{{ __('messages.review_added_success') }}');
                fetchReviews();
            } else {
                const err = await res.json();
                alert(err.message || '{{ __('messages.error_adding_review') }}');
            }
        } catch(e) {
            alert('{{ __('messages.connection_error') }}');
        }
    });

    async function fetchStoreProducts() {
        try {
            const grid = document.getElementById('store-products-grid');
            const res = await fetch(`/api/stores/${storeSlug}/products`);
            const data = await res.json();
            
            if(!data.data || data.data.length === 0) {
                grid.innerHTML = '<div style="grid-column:1/-1; text-align:center; padding:40px; color:#999;">{{ __('messages.no_products_available_now') }}</div>';
                return;
            }

            let html = '';
            data.data.forEach(p => {
                const img = (p.images && p.images.length > 0) ? `<img src="${p.images[0]}" style="width:100%;height:100%;object-fit:cover;">` : `<i class="fa-solid fa-box fa-3x"></i>`;
                html += `
                    <div class="card">
                        <div class="card-img">${img}</div>
                        <div class="card-body">
                            <a href="/products/${p.slug}" class="card-title">${p.name}</a>
                            <div style="color:#FFD700; font-size:14px; margin-bottom:10px;">
                                <i class="fa-solid fa-star"></i> <span style="color:var(--text-muted);">(${p.reviews_avg_rating ? Number(p.reviews_avg_rating).toFixed(1) : '0.0'})</span>
                            </div>
                            <div style="flex-grow:1; display:flex; align-items:flex-end;">
                                <strong style="color:var(--primary); font-size:18px;">${p.price} {{ __('messages.sar') }}</strong>
                            </div>
                        </div>
                    </div>
                `;
            });
            grid.innerHTML = html;
        } catch(e) {}
    }

    async function fetchReviews() {
        try {
            const res = await fetch(`/api/stores/${storeSlug}/reviews`);
            const data = await res.json();
            
            if(data.data && data.data.length > 0) {
                let html = '';
                data.data.forEach(r => {
                    let stars = '';
                    for(let i=1; i<=5; i++) {
                        stars += `<i class="fa-solid fa-star" style="color:${i <= r.rating ? '#FFD700' : '#eee'}"></i> `;
                    }
                    html += `
                        <div style="border-bottom: 1px solid #eee; padding: 15px 0;">
                            <div style="display:flex; justify-content:space-between; margin-bottom:10px;">
                                <strong><i class="fa-solid fa-user-circle"></i> ${r.user ? r.user.name : '{{ __('messages.customer_label') }}'}</strong>
                                <small style="color:#999;">${new Date(r.created_at).toLocaleDateString()}</small>
                            </div>
                            <div style="margin-bottom:10px;">${stars}</div>
                            <p style="margin:0; color:#444;">${r.comment || ''}</p>
                        </div>
                    `;
                });
                document.getElementById('store-reviews-list').innerHTML = html;
            }
        } catch(e) {}
    }

    // Tabs logic
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
            document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
            btn.classList.add('active');
            document.getElementById(btn.getAttribute('data-target')).classList.add('active');
        });
    });

    async function analyzeStoreWithAI() {
        const box = document.getElementById('ai-analysis-box');
        const content = document.getElementById('ai-analysis-content');
        
        box.style.display = 'block';
        content.innerHTML = '<div style="text-align:center; padding: 20px;"><i class="fa-solid fa-circle-notch fa-spin fa-2x" style="color:#a770ef;"></i><p style="margin-top:10px; color:var(--text-muted);">جاري القراءة واستخلاص المعلومات الموثوقة... 🤖</p></div>';
        
        try {
            const res = await fetch(`/api/assistant/analyze-store/${storeSlug}`);
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
