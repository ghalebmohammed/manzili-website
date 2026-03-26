@extends('layouts.app')

@section('title', __('messages.buyer_dashboard'))

@push('styles')
<style>
    .dashboard-container {
        display: flex;
        min-height: calc(100vh - 70px);
        background: linear-gradient(135deg, rgba(140, 163, 121,0.05) 0%, rgba(212, 163, 115,0.08) 100%);
        position: relative;
    }
    
    /* Decorative Background Elements */
    .dashboard-container::before {
        content: '';
        position: absolute;
        width: 300px;
        height: 300px;
        background: var(--primary);
        opacity: 0.1;
        border-radius: 50%;
        top: 50px;
        right: 100px;
        filter: blur(60px);
        pointer-events: none;
    }
    .dashboard-container::after {
        content: '';
        position: absolute;
        width: 400px;
        height: 400px;
        background: var(--secondary);
        opacity: 0.1;
        border-radius: 50%;
        bottom: 50px;
        left: 50px;
        filter: blur(80px);
        pointer-events: none;
    }

    .sidebar {
        width: 280px;
        background: rgba(255, 255, 255, 0.85);
        backdrop-filter: blur(15px);
        box-shadow: 2px 0 15px rgba(0,0,0,0.03);
        padding: 30px 20px;
        position: sticky;
        top: 70px;
        height: calc(100vh - 70px);
        overflow-y: auto;
        border-left: 1px solid rgba(255,255,255,0.5);
        z-index: 10;
    }
    
    .sidebar-user {
        text-align: center;
        padding-bottom: 25px;
        border-bottom: 1px dashed #e1e5eb;
        margin-bottom: 25px;
    }
    .sidebar-user .avatar {
        width: 80px;
        height: 80px;
        background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 30px;
        margin: 0 auto 15px;
        box-shadow: 0 5px 15px rgba(212, 163, 115,0.3);
    }
    .sidebar-user p {
        margin: 0;
        font-weight: 800;
        color: var(--text-dark);
        font-size: 20px;
    }
    .sidebar-user span {
        font-size: 13px;
        color: var(--primary);
        background: rgba(140, 163, 121, 0.1);
        padding: 4px 12px;
        border-radius: 50px;
        display: inline-block;
        margin-top: 8px;
    }

    .sidebar-menu {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    .sidebar-menu li { margin-bottom: 8px; }
    .sidebar-menu a {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 15px 20px;
        color: var(--text-muted);
        text-decoration: none;
        font-weight: 600;
        border-radius: 10px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
    }
    .sidebar-menu a i {
        font-size: 18px;
        width: 24px;
        text-align: center;
        transition: transform 0.3s;
    }
    .sidebar-menu a:hover {
        color: var(--primary);
        background: rgba(255,255,255,0.9);
        box-shadow: 0 4px 10px rgba(0,0,0,0.03);
        transform: translateX(-5px);
    }
    .sidebar-menu a.active {
        color: white;
        background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
        box-shadow: 0 5px 15px rgba(212, 163, 115,0.2);
    }
    .sidebar-menu a:hover i, .sidebar-menu a.active i {
        color: white;
        transform: scale(1.2) rotate(-5deg);
    }
    .sidebar-menu a:hover i {
        color: var(--primary);
    }
    .sidebar-menu a[onclick="logout(); return false;"]:hover i {
        color: #dc3545;
    }

    .main-content {
        flex: 1;
        padding: 40px;
        overflow-y: auto;
        z-index: 10;
    }
    
    .dashboard-header {
        margin-bottom: 30px;
    }
    .dashboard-header h2 {
        margin: 0;
        color: var(--text-dark);
        font-size: 28px;
        font-weight: 800;
    }
    .dashboard-header p {
        margin: 5px 0 0;
        color: var(--text-muted);
    }

    /* Modern Glassmorphic Container */
    .glass-container {
        background: rgba(255, 255, 255, 0.85);
        backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.8);
        border-radius: 20px;
        padding: 25px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.03);
    }
    
    /* Table Styling */
    .table-container {
        overflow-x: auto;
    }
    table { width: 100%; border-collapse: separate; border-spacing: 0 10px; margin-top: -10px; }
    th { 
        padding: 15px 20px; 
        text-align: right; 
        color: var(--text-muted); 
        font-weight: 600; 
        font-size: 14px;
        border: none;
    }
    td { 
        padding: 18px 20px; 
        background: white; 
        color: var(--text-dark);
        font-weight: 500;
        transition: all 0.3s;
    }
    tr td:first-child { border-top-right-radius: 12px; border-bottom-right-radius: 12px; }
    tr td:last-child { border-top-left-radius: 12px; border-bottom-left-radius: 12px; }
    
    tbody tr {
        box-shadow: 0 2px 10px rgba(0,0,0,0.02);
        cursor: default;
        transition: transform 0.3s, box-shadow 0.3s;
    }
    tbody tr:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.06);
    }
    tbody tr:hover td {
        background: #fafbfe;
    }
    
    .status-badge {
        padding: 6px 15px;
        border-radius: 30px;
        font-size: 13px;
        font-weight: 700;
        display: inline-block;
        text-align: center;
        box-shadow: 0 2px 5px rgba(0,0,0,0.05);
    }
    .status-pending { background: linear-gradient(135deg, #FFC107 0%, #FF9800 100%); color: white; }
    .status-confirmed { background: linear-gradient(135deg, #25D366 0%, #1da851 100%); color: white; }
    .status-cancelled { background: linear-gradient(135deg, #ff4d4d 0%, #e60000 100%); color: white; }

    /* Grid for Products/Stores */
    .grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 25px;
    }

    .item-card {
        background: white;
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 5px 15px rgba(0,0,0,0.03);
        border: 1px solid #f1f1f1;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        display: flex;
        flex-direction: column;
        position: relative;
    }
    .item-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 15px 35px rgba(0,0,0,0.08);
    }
    .item-img-container {
        height: 180px;
        background: #f8f9fa;
        position: relative;
        overflow: hidden;
    }
    .item-img-container img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s;
    }
    .item-card:hover .item-img-container img {
        transform: scale(1.05);
    }
    .item-details {
        padding: 20px;
        flex-grow: 1;
        display: flex;
        flex-direction: column;
    }
    .item-title {
        font-size: 18px;
        font-weight: 800;
        color: var(--text-dark);
        text-decoration: none;
        margin-bottom: 5px;
        transition: color 0.3s;
        display: block;
    }
    .item-title:hover {
        color: var(--primary);
    }
    .item-price {
        color: var(--secondary);
        font-weight: 800;
        font-size: 18px;
        margin-bottom: 12px;
    }
    .item-store-link {
        font-size: 13px;
        color: var(--text-muted);
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        margin-bottom: 15px;
        background: #f4f6f9;
        padding: 5px 10px;
        border-radius: 5px;
        transition: background 0.3s;
    }
    .item-store-link:hover {
        background: #e9ecef;
        color: var(--text-dark);
    }
    
    .remove-btn {
        background: rgba(220,53,69,0.1);
        color: #dc3545;
        border: none;
        width: 100%;
        padding: 10px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 14px;
        cursor: pointer;
        transition: all 0.3s;
        margin-top: auto;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }
    .remove-btn:hover {
        background: #dc3545;
        color: white;
    }

    .tab-content { display: none; animation: fadeIn 0.4s ease-out forwards; }
    .tab-content.active { display: block; }
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .empty-state {
        text-align: center;
        padding: 60px 20px;
    }
    .empty-state i {
        font-size: 60px;
        color: #e1e5eb;
        margin-bottom: 20px;
    }
    .empty-state h3 {
        color: var(--text-dark);
        margin: 0 0 10px;
    }
    .empty-state p {
        color: var(--text-muted);
        margin: 0;
    }
</style>
@endpush

@section('content')
<div class="dashboard-container">
    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-user">
            <div class="avatar">
                <i class="fa-solid fa-user"></i>
            </div>
            <p id="sidebar-user-name">{{ __('messages.dear_customer') }}</p>
            <span>{{ __('messages.buyer_account') }}</span>
        </div>
        <ul class="sidebar-menu">
            <li><a href="#" class="nav-link active" data-target="orders-tab"><i class="fa-solid fa-bag-shopping"></i> {{ __('messages.my_orders') }}</a></li>
            <li><a href="#" class="nav-link" data-target="favorites-tab"><i class="fa-solid fa-heart"></i> {{ __('messages.favorite_products') }}</a></li>
            <li><a href="#" class="nav-link" data-target="following-tab"><i class="fa-solid fa-store"></i> {{ __('messages.followed_stores') }}</a></li>
            <li style="margin-top: 30px;"><a href="#" class="nav-link" onclick="logout(); return false;" style="color: #dc3545;"><i class="fa-solid fa-arrow-right-from-bracket" style="color: #dc3545;"></i> {{ __('messages.logout') }}</a></li>
        </ul>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        <!-- Orders Tab -->
        <div id="orders-tab" class="tab-content active">
            <div class="dashboard-header">
                <h2>{{ __('messages.order_history') }}</h2>
                <p>{{ __('messages.track_past_orders') }}</p>
            </div>
            
            <div class="glass-container">
                <div class="table-container">
                    <table id="orders-table">
                        <thead>
                            <tr>
                                <th>{{ __('messages.order_number') }}</th>
                                <th>{{ __('messages.product_image') }}</th>
                                <th>{{ __('messages.product') }}</th>
                                <th>{{ __('messages.store') }}</th>
                                <th>{{ __('messages.price') }}</th>
                                <th>{{ __('messages.order_date') }}</th>
                                <th>{{ __('messages.order_status') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr><td colspan="7" style="text-align:center; padding:40px;"><i class="fa-solid fa-circle-notch fa-spin fa-2x" style="color:var(--primary);"></i></td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Favorites Tab -->
        <div id="favorites-tab" class="tab-content">
            <div class="dashboard-header">
                <h2>{{ __('messages.favorite_products') }}</h2>
                <p>{{ __('messages.best_saved_products') }}</p>
            </div>
            <div class="glass-container" style="background: transparent; border: none; box-shadow: none; padding: 0;">
                <div class="grid" id="favorites-grid">
                    <div style="grid-column: 1/-1; text-align:center; padding:40px;"><i class="fa-solid fa-circle-notch fa-spin fa-2x" style="color:var(--primary);"></i></div>
                </div>
            </div>
        </div>

        <!-- Following Tab -->
        <div id="following-tab" class="tab-content">
            <div class="dashboard-header">
                <h2>{{ __('messages.followed_stores') }}</h2>
                <p>{{ __('messages.favorite_stores_list') }}</p>
            </div>
            <div class="glass-container" style="background: transparent; border: none; box-shadow: none; padding: 0;">
                <div class="grid" id="following-grid">
                    <div style="grid-column: 1/-1; text-align:center; padding:40px;"><i class="fa-solid fa-circle-notch fa-spin fa-2x" style="color:var(--primary);"></i></div>
                </div>
            </div>
        </div>
    </main>
</div>
@endsection

@push('scripts')
<script>
    const token = localStorage.getItem('auth_token');
    const userStr = localStorage.getItem('user');

    if (!token) {
        window.location.href = '/login';
    } else if(userStr) {
        try {
            const userObj = JSON.parse(userStr);
            document.getElementById('sidebar-user-name').innerText = userObj.name || '{{ __('messages.dear_customer') }}';
        } catch(e){}
    }

    const headers = {
        'Authorization': `Bearer ${token}`,
        'Accept': 'application/json'
    };

    // Tabs logic
    document.querySelectorAll('.nav-link').forEach(link => {
        link.addEventListener('click', function(e) {
            if(this.getAttribute('onclick')) return;
            e.preventDefault();
            
            document.querySelectorAll('.nav-link').forEach(l => l.classList.remove('active'));
            document.querySelectorAll('.tab-content').forEach(t => t.classList.remove('active'));
            
            this.classList.add('active');
            document.getElementById(this.getAttribute('data-target')).classList.add('active');
        });
    });

    document.addEventListener('DOMContentLoaded', () => {
        loadOrders();
        loadFavorites();
        loadFollowedStores();
    });

    async function loadOrders() {
        const tbody = document.querySelector('#orders-table tbody');
        try {
            const res = await fetch('/api/customer/orders', { headers });
            if(res.status === 401 || res.status === 403) { logout(); return; }
            const data = await res.json();
            
            if(!data.data || data.data.length === 0) {
                tbody.innerHTML = `<tr><td colspan="7">
                    <div class="empty-state">
                        <i class="fa-solid fa-clipboard-check"></i>
                        <h3>{{ __('messages.no_orders_yet') }}</h3>
                        <p>{{ __('messages.start_shopping_explore') }}</p>
                        <a href="/products" class="btn-primary" style="display:inline-block; margin-top:20px;">{{ __('messages.browse_products') }}</a>
                    </div>
                </td></tr>`;
                return;
            }

            let html = '';
            data.data.forEach(o => {
                const img = o.product && o.product.images && o.product.images.length > 0 ? `<img src="${o.product.images[0]}" style="width:40px; height:40px; border-radius:8px; object-fit:cover;">` : `<div style="width:40px; height:40px; border-radius:8px; background:#eee; display:flex; align-items:center; justify-content:center;"><i class="fa-solid fa-box" style="color:#aaa;"></i></div>`;
                const pName = o.product ? `<a href="/products/${o.product.slug}" style="color:var(--text-dark); text-decoration:none; font-weight:700;">${o.product.name}</a>` : '<span style="color:#999;">{{ __('messages.deleted_product') }}</span>';
                const sName = o.store ? `<a href="/stores/${o.store.slug}" style="color:var(--primary); text-decoration:none;">${o.store.name}</a>` : '<span style="color:#999;">{{ __('messages.deleted_store') }}</span>';
                const price = o.product ? `<span style="font-weight:700;">${o.product.price} {{ __('messages.sar') }}</span>` : '-';
                const date = new Date(o.created_at).toLocaleDateString('ar-SA');
                
                const badgeClass = o.status === 'pending' ? 'status-pending' : (o.status === 'confirmed' ? 'status-confirmed' : 'status-cancelled');
                const badgeText = o.status === 'pending' ? `<i class="fa-regular fa-clock"></i> {{ __('messages.pending') }}` : (o.status === 'confirmed' ? `<i class="fa-solid fa-check"></i> {{ __('messages.confirmed') }}` : `<i class="fa-solid fa-xmark"></i> {{ __('messages.cancelled') }}`);

                html += `
                    <tr>
                        <td><strong>#${o.id}</strong></td>
                        <td>${img}</td>
                        <td>${pName}</td>
                        <td>${sName}</td>
                        <td>${price}</td>
                        <td style="color:var(--text-muted);">${date}</td>
                        <td><span class="status-badge ${badgeClass}">${badgeText}</span></td>
                    </tr>
                `;
            });
            tbody.innerHTML = html;
        } catch(e) {
            tbody.innerHTML = '<tr><td colspan="7" style="text-align:center; color:red; padding:30px;">{{ __('messages.server_connection_error') }}</td></tr>';
        }
    }

    async function loadFavorites() {
        const favGrid = document.getElementById('favorites-grid');
        try {
            const resFav = await fetch('/api/favorites/products', { headers });
            const dataFav = await resFav.json();
            
            if(!dataFav.data || dataFav.data.length === 0) {
                favGrid.innerHTML = `
                    <div style="grid-column: 1/-1;" class="glass-container empty-state">
                        <i class="fa-solid fa-heart-crack"></i>
                        <h3>{{ __('messages.favorites_empty') }}</h3>
                        <p>{{ __('messages.add_products_to_return') }}</p>
                    </div>`;
                return;
            }

            let favHtml = '';
            dataFav.data.forEach(p => {
                const img = (p.images && p.images.length > 0) ? `<img src="${p.images[0]}" alt="${p.name}">` : `<div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;color:#ddd;"><i class="fa-solid fa-box fa-3x"></i></div>`;
                favHtml += `
                    <div class="item-card">
                        <div class="item-img-container">
                            ${img}
                        </div>
                        <div class="item-details">
                            <a href="/products/${p.slug}" class="item-title">${p.name}</a>
                            <div class="item-price">${p.price} {{ __('messages.sar') }}</div>
                            <a href="/stores/${p.store.slug}" class="item-store-link"><i class="fa-solid fa-store"></i> ${p.store.name}</a>
                            <button class="remove-btn" onclick="removeFavorite(${p.id})"><i class="fa-solid fa-heart-crack"></i> {{ __('messages.remove_from_favorites') }}</button>
                        </div>
                    </div>
                `;
            });
            favGrid.innerHTML = favHtml;
        } catch(e) {
            favGrid.innerHTML = '<div style="grid-column: 1/-1; color:red;" class="glass-container">{{ __('messages.loading_favorites_error') }}</div>';
        }
    }

    async function loadFollowedStores() {
        const foldGrid = document.getElementById('following-grid');
        try {
            const resFold = await fetch('/api/followers/stores', { headers });
            const dataFold = await resFold.json();
            
            if(!dataFold.data || dataFold.data.length === 0) {
                foldGrid.innerHTML = `
                    <div style="grid-column: 1/-1;" class="glass-container empty-state">
                        <i class="fa-solid fa-store-slash"></i>
                        <h3>{{ __('messages.not_following_stores') }}</h3>
                        <p>{{ __('messages.discover_and_follow_stores') }}</p>
                        <a href="/stores" class="btn-primary" style="display:inline-block; margin-top:20px;">{{ __('messages.explore_stores') }}</a>
                    </div>`;
                return;
            }

            let foldHtml = '';
            dataFold.data.forEach(f => {
                if(!f.store) return;
                const s = f.store;
                const coverStyle = s.cover ? `background: url(${s.cover}) center/cover;` : `background: linear-gradient(135deg, var(--secondary) 0%, rgba(212, 163, 115,0.8) 100%);`;
                const logo = s.logo ? `<img src="${s.logo}" style="width:100%;height:100%;object-fit:cover;border-radius:50%;">` : `<i class="fa-solid fa-store"></i>`;
                
                foldHtml += `
                    <div class="item-card">
                        <div style="${coverStyle} height: 120px; position:relative;">
                            <div style="width: 70px; height: 70px; border-radius: 50%; background: white; border: 4px solid white; position:absolute; bottom:-35px; right:20px; display:flex; align-items:center; justify-content:center; color:#ddd; font-size:24px; box-shadow:0 4px 10px rgba(0,0,0,0.1);">
                                ${logo}
                            </div>
                        </div>
                        <div class="item-details" style="padding-top: 45px;">
                            <a href="/stores/${s.slug}" class="item-title" style="font-size: 20px;">${s.name} ${s.kyc_status === 'approved' ? `<i class="fa-solid fa-circle-check" style="color:#25D366; font-size:14px;" title="{{ __('messages.verified_in_manzili') }}"></i>` : ''}</a>
                            <p style="color:var(--text-muted); font-size: 14px; margin-bottom: 20px;">${s.store_type || '{{ __('messages.miscellaneous') }}'}</p>
                            <button class="remove-btn" onclick="unfollowStore(${s.id})"><i class="fa-solid fa-user-minus"></i> {{ __('messages.unfollow') }}</button>
                        </div>
                    </div>
                `;
            });
            foldGrid.innerHTML = foldHtml || `<div style="grid-column: 1/-1; text-align:center;">{{ __('messages.data_error') }}</div>`;
        } catch(e) {
            foldGrid.innerHTML = `<div style="grid-column: 1/-1; color:red;" class="glass-container">{{ __('messages.loading_followed_error') }}</div>`;
        }
    }

    window.removeFavorite = async (id) => {
        try {
            await fetch(`/api/favorites/products/${id}`, { method: 'POST', headers });
            location.reload();
        } catch(e) {}
    }

    window.unfollowStore = async (id) => {
        try {
            await fetch(`/api/followers/stores/${id}`, { method: 'POST', headers });
            location.reload();
        } catch(e) {}
    }

    async function logout() {
        if(!confirm('{{ __('messages.confirm_logout') }}')) return;
        try {
            await fetch('/api/logout', { 
                method: 'POST', 
                headers: {
                    'Authorization': `Bearer ${localStorage.getItem('auth_token')}`,
                    'Accept': 'application/json'
                }
            });
        } catch(e) {}
        finally {
            localStorage.removeItem('auth_token');
            localStorage.removeItem('user');
            window.location.href = '/login';
        }
    }
</script>
@endpush
