@extends('layouts.app')

@section('title', __('messages.cart'))

@push('styles')
<style>
    .page-header {
        background: linear-gradient(135deg, var(--secondary) 0%, rgba(212, 163, 115,0.8) 100%);
        color: white;
        padding: 50px 0;
        text-align: center;
        margin-bottom: 40px;
    }
    .page-header h1 {
        font-size: 32px;
        margin-bottom: 10px;
    }
    
    .cart-container {
        display: grid;
        grid-template-columns: 1fr;
        gap: 30px;
        margin-bottom: 60px;
    }
    
    .store-group {
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(10px);
        border-radius: 15px;
        padding: 25px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        border: 1px solid rgba(255,255,255,0.5);
        margin-bottom: 25px;
        transition: transform 0.3s;
    }
    .store-group:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 35px rgba(0,0,0,0.08);
    }
    
    .store-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 2px dashed #eee;
        padding-bottom: 15px;
        margin-bottom: 20px;
    }
    .store-header h3 {
        margin: 0;
        color: var(--primary);
        display: flex;
        font-size: 20px;
        align-items: center;
        gap: 10px;
    }

    .cart-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 15px;
        border-radius: 12px;
        background: #fdfdfd;
        border: 1px solid #f1f1f1;
        margin-bottom: 15px;
        transition: 0.3s;
    }
    .cart-item:hover {
        background: white;
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
    }
    .item-info {
        display: flex;
        align-items: center;
        gap: 20px;
    }
    .item-img {
        width: 70px;
        height: 70px;
        background: #f1f1f1;
        border-radius: 10px;
        object-fit: cover;
    }
    .item-details h4 {
        margin: 0 0 5px 0;
        font-size: 18px;
        color: var(--text-dark);
        font-family: inherit;
    }
    .item-details p {
        margin: 0;
        color: var(--primary);
        font-weight: bold;
        font-size: 16px;
    }
    .item-remove {
        color: #dc3545;
        background: rgba(220,53,69,0.1);
        border: none;
        cursor: pointer;
        width: 35px;
        height: 35px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s;
    }
    .item-remove:hover {
        background: #dc3545;
        color: white;
        transform: scale(1.1);
    }

    .store-summary {
        background: linear-gradient(135deg, #f8f9fa 0%, #eef1f5 100%);
        padding: 20px;
        border-radius: 10px;
        margin-top: 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border: 1px solid #e1e5eb;
    }
    
    .whatsapp-order-btn {
        background: linear-gradient(135deg, #25D366 0%, #1da851 100%);
        color: white;
        text-decoration: none;
        padding: 12px 25px;
        border-radius: 50px;
        font-weight: 600;
        font-size: 15px;
        display: flex;
        align-items: center;
        gap: 10px;
        box-shadow: 0 4px 15px rgba(37,211,102,0.3);
        transition: all 0.3s;
    }
    .whatsapp-order-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(37,211,102,0.4);
    }

    #empty-cart {
        text-align: center;
        padding: 80px 20px;
        background: rgba(255,255,255,0.8);
        backdrop-filter: blur(10px);
        border-radius: 15px;
        border: 1px solid rgba(255,255,255,0.5);
        box-shadow: 0 10px 30px rgba(0,0,0,0.05);
    }
    #empty-cart i {
        font-size: 80px;
        color: #ddd;
        margin-bottom: 25px;
    }
</style>
@endpush

@section('content')
<div class="page-header">
    <div class="container">
        <h1>{{ __('messages.shopping_cart') }}</h1>
        <p>{{ __('messages.cart_description') }}</p>
    </div>
</div>

<div class="container">
    <div id="cart-content" class="cart-container">
        <!-- JS populates groups here -->
    </div>
    
    <div id="empty-cart" style="display: none;">
        <i class="fa-solid fa-cart-shopping"></i>
        <h2>{{ __('messages.empty_cart') }}</h2>
        <p style="color: var(--text-muted); margin-bottom: 25px;">{{ __('messages.cart_empty_desc') }}</p>
        <a href="/products" class="btn-primary">{{ __('messages.browse_products') }}</a>
    </div>
</div>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        renderCart();
    });

    function getCart() {
        return JSON.parse(localStorage.getItem('manzili_cart')) || [];
    }

    function renderCart() {
        const cart = getCart();
        const contentDiv = document.getElementById('cart-content');
        const emptyDiv = document.getElementById('empty-cart');

        if (cart.length === 0) {
            contentDiv.style.display = 'none';
            emptyDiv.style.display = 'block';
            updateCartBadge();
            return;
        }

        contentDiv.style.display = 'block';
        emptyDiv.style.display = 'none';
        contentDiv.innerHTML = '';

        // Group by store
        const groups = {};
        cart.forEach((item, index) => {
            if (!groups[item.store_id]) {
                groups[item.store_id] = {
                    store_name: item.store_name,
                    store_phone: item.store_phone,
                    items: [],
                    total: 0
                };
            }
            // store original index to be able to remove it
            item.originalIndex = index;
            groups[item.store_id].items.push(item);
            groups[item.store_id].total += parseFloat(item.price);
        });

        // Render each group
        for (const storeId in groups) {
            const group = groups[storeId];
            
            // Build items HTML
            let itemsHtml = '';
            group.items.forEach(item => {
                const img = item.image ? `<img src="${item.image}" class="item-img">` : `<div class="item-img" style="display:flex;align-items:center;justify-content:center;background:#eee;color:#999;"><i class="fa-solid fa-box"></i></div>`;
                itemsHtml += `
                    <div class="cart-item">
                        <div class="item-info">
                            ${img}
                            <div class="item-details">
                                <h4><a href="/products/${item.slug}" style="color:inherit; text-decoration:none;">${item.name}</a></h4>
                                <p>${item.price} {{ __('messages.sar') }}</p>
                            </div>
                        </div>
                        <button class="item-remove" onclick="removeFromCart(${item.originalIndex})" title="{{ __('messages.remove_from_cart') }}">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </div>
                `;
            });

            // Build WhatsApp message
            let waMsg = `{{ __('messages.wa_order_intro') }}\n\n`;
            group.items.forEach((it, idx) => {
                waMsg += `${idx + 1}- ${it.name} ({{ __('messages.wa_price') }} ${it.price} {{ __('messages.sar') }})\n`;
            });
            waMsg += `\n{{ __('messages.wa_total') }} ${group.total} {{ __('messages.sar') }}`;
            
            const waUrl = `https://wa.me/${group.store_phone || "966500000000"}?text=${encodeURIComponent(waMsg)}`;

            const groupHtml = `
                <div class="store-group" data-aos="fade-up">
                    <div class="store-header">
                        <h3><i class="fa-solid fa-store"></i> ${group.store_name}</h3>
                        <span style="color:var(--text-muted); font-size:14px;">{{ __('messages.separate_order') }}</span>
                    </div>
                    
                    <div class="items-list">
                        ${itemsHtml}
                    </div>

                    <div class="store-summary">
                        <div>
                            <strong style="color:var(--text-dark);">{{ __('messages.store_total') }}</strong>
                            <span style="color:var(--primary); font-size:20px; font-weight:bold; margin-right:10px;">${group.total} {{ __('messages.sar') }}</span>
                        </div>
                        <a href="#" onclick="completeOrder(${storeId}, '${waUrl}')" class="whatsapp-order-btn">
                            <i class="fa-brands fa-whatsapp fa-xl"></i> {{ __('messages.complete_order_store') }}
                        </a>
                    </div>
                </div>
            `;
            contentDiv.innerHTML += groupHtml;
        }

        updateCartBadge();
    }

    function removeFromCart(index) {
        let cart = getCart();
        cart.splice(index, 1);
        localStorage.setItem('manzili_cart', JSON.stringify(cart));
        renderCart();
    }

    // Attempt to log sale intention if user logged in
    async function completeOrder(storeId, waUrl) {
        const token = localStorage.getItem('auth_token');
        const cart = getCart();
        const groupItems = cart.filter(c => c.store_id == storeId);

        if(token) {
            // log each item as a pending sale
            for(const item of groupItems) {
                try {
                    await fetch('/api/sales/initiate', {
                        method: 'POST',
                        headers: {
                            'Authorization': `Bearer ${token}`,
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ product_id: item.id })
                    });
                } catch(e) {}
            }
        }
        
        // Remove these items from cart since they are ordered
        const newCart = cart.filter(c => c.store_id != storeId);
        localStorage.setItem('manzili_cart', JSON.stringify(newCart));

        window.open(waUrl, '_blank');
        renderCart();
    }
</script>
@endpush
