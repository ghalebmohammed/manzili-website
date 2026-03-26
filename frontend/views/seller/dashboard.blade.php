@extends('layouts.app')

@section('title', __('messages.seller_dashboard'))

@push('styles')
<style>
    .dashboard-container {
        display: flex;
        min-height: calc(100vh - 70px);
        background: var(--bg-light);
    }
    .sidebar {
        width: 250px;
        background: var(--white);
        box-shadow: 2px 0 5px rgba(0,0,0,0.05);
        padding: 30px 0;
        position: sticky;
        top: 70px;
        height: calc(100vh - 70px);
        overflow-y: auto;
    }
    .sidebar-menu {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    
    @media print {
        aside, .navbar, #kyc-alert { display: none !important; }
        .main-content { padding: 0 !important; width: 100% !important; }
        .dashboard-container { display: block !important; }
        .btn-primary, .btn-secondary, .btn-sm, button { display: none !important; }
        select { border: none; appearance: none; -webkit-appearance: none; -moz-appearance: none; }
    }
    .sidebar-menu li {
        margin-bottom: 5px;
    }
    .sidebar-menu a {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 25px;
        color: var(--text-muted);
        text-decoration: none;
        font-weight: 500;
        border-right: 4px solid transparent;
        transition: all 0.3s;
    }
    .sidebar-menu a:hover, .sidebar-menu a.active {
        color: var(--primary);
        background: rgba(140, 163, 121,0.05);
        border-right-color: var(--primary);
    }
    .sidebar-menu a i {
        transition: transform 0.3s ease;
    }
    .sidebar-menu a:hover i, .sidebar-menu a.active i {
        transform: scale(1.2) rotate(-5deg);
    }
    .main-content {
        flex: 1;
        padding: 40px;
        overflow-y: auto;
    }
    
    /* Stats Cards */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }
    .stat-card {
        background: var(--white);
        border-radius: 10px;
        padding: 20px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        display: flex;
        align-items: center;
        gap: 20px;
    }
    .stat-icon {
        width: 50px;
        height: 50px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        color: white;
    }
    .stat-info h4 {
        margin: 0;
        font-size: 14px;
        color: var(--text-muted);
        font-weight: 500;
    }
    .stat-info p {
        margin: 5px 0 0;
        font-size: 24px;
        font-weight: 700;
        color: var(--text-dark);
    }

    /* Tables */
    .table-container {
        background: var(--white);
        border-radius: 10px;
        padding: 20px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        overflow-x: auto;
    }
    table {
        width: 100%;
        border-collapse: collapse;
    }
    th, td {
        padding: 15px;
        text-align: right;
        border-bottom: 1px solid #eee;
    }
    th {
        color: var(--text-muted);
        font-weight: 600;
    }
    .status-badge {
        padding: 5px 10px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 500;
    }
    .status-pending { background: #fff3cd; color: #856404; }
    .status-confirmed, .status-active { background: #d4edda; color: #155724; }
    .status-cancelled, .status-hidden { background: #f8d7da; color: #721c24; }

    /* Forms */
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
        padding: 12px;
        border: 1px solid #ddd;
        border-radius: 5px;
        font-family: inherit;
        font-size: 15px;
        box-sizing: border-box;
    }
    .form-control:focus {
        border-color: var(--primary);
        outline: none;
    }
    .btn-sm {
        padding: 6px 12px;
        font-size: 13px;
        border-radius: 4px;
        cursor: pointer;
        border: none;
        color: white;
    }
    
    .tab-content { display: none; }
    .tab-content.active { display: block; }

    #kyc-alert {
        background: #fff3cd;
        color: #856404;
        padding: 15px 20px;
        border-radius: 8px;
        margin-bottom: 20px;
        display: none;
        border: 1px solid #ffeeba;
    }
    #map-container {
        height: 250px;
        width: 100%;
        border-radius: 8px;
        border: 1px solid #ddd;
        margin-top: 10px;
    }

    /* Products Grid */
    .products-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 25px;
        margin-top: 20px;
    }
    .product-card {
        background: var(--white);
        border-radius: 12px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.04);
        overflow: hidden;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        display: flex;
        flex-direction: column;
        border: 1px solid #f5f5f5;
    }
    .product-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.08);
    }
    .product-image-wrapper {
        position: relative;
        height: 220px;
        background: #f8f9fa;
        overflow: hidden;
    }
    .product-card img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s ease;
    }
    .product-card:hover img {
        transform: scale(1.05);
    }
    .product-status-badge {
        position: absolute;
        top: 15px;
        right: 15px;
        padding: 6px 14px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        box-shadow: 0 4px 10px rgba(0,0,0,0.15);
        z-index: 10;
        backdrop-filter: blur(4px);
    }
    .product-details {
        padding: 20px;
        flex: 1;
        display: flex;
        flex-direction: column;
    }
    .product-title {
        font-size: 18px;
        font-weight: 700;
        color: var(--text-dark);
        margin: 0 0 10px 0;
        line-height: 1.4;
    }
    .product-category {
        font-size: 12px;
        color: var(--primary);
        background: rgba(140, 163, 121, 0.1);
        padding: 5px 12px;
        border-radius: 20px;
        display: inline-block;
        margin-bottom: 12px;
        align-self: flex-start;
        font-weight: 600;
    }
    .product-desc {
        font-size: 14px;
        color: var(--text-muted);
        margin-bottom: 20px;
        line-height: 1.6;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        flex: 1;
    }
    .product-meta {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        padding-bottom: 15px;
        border-bottom: 1px solid #f0f0f0;
    }
    .product-price {
        font-size: 22px;
        font-weight: 800;
        color: var(--primary);
    }
    .product-actions {
        display: flex;
        gap: 12px;
    }
    .product-actions button {
        flex: 1;
        padding: 10px;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        font-weight: 600;
        font-size: 14px;
        transition: all 0.2s;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }
    .btn-edit {
        background: #f0f7f4;
        color: #17a2b8;
    }
    .btn-edit:hover {
        background: #17a2b8;
        color: white;
    }
    .btn-delete {
        background: #fdf4f4;
        color: #dc3545;
    }
    .btn-delete:hover {
        background: #dc3545;
        color: white;
    }
</style>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
@endpush

@section('content')
<div class="dashboard-container">
    <aside class="sidebar">
        <div style="border-bottom: 1px solid #eee; margin-bottom: 20px;">
            <div id="sidebar-store-cover" style="height: 100px; background: #eee; background-size: cover; background-position: center; border-radius: 8px 8px 0 0;"></div>
            <div style="padding: 0 25px 20px; text-align: center; position: relative; margin-top: -40px;">
                <img id="sidebar-store-logo" src="https://via.placeholder.com/80?text=Logo" style="width: 80px; height: 80px; border-radius: 50%; border: 4px solid white; background: white; object-fit: cover; margin-bottom: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                <p style="margin:0; font-weight:700; color:var(--text-dark); font-size:18px;" id="sidebar-store-name">{{ __('messages.my_store') }}</p>
                <span style="font-size:12px; color:var(--primary);" id="sidebar-store-status">{{ __('messages.loading') }}</span>
            </div>
        </div>
        <ul class="sidebar-menu">
            <li><a href="#" class="nav-link active" data-target="dashboard-tab"><i class="fa-solid fa-chart-line"></i> {{ __('messages.statistics') }}</a></li>
            <li><a href="#" class="nav-link" data-target="products-tab"><i class="fa-solid fa-box-open"></i> {{ __('messages.products') }}</a></li>
            <li><a href="#" class="nav-link" data-target="orders-tab"><i class="fa-solid fa-clipboard-list"></i> {{ __('messages.orders') }}</a></li>
            <li><a href="#" class="nav-link" data-target="settings-tab"><i class="fa-solid fa-gear"></i> {{ __('messages.store_settings') }}</a></li>
            <li><a href="#" class="nav-link" onclick="logout(); return false;" style="color: #dc3545;"><i class="fa-solid fa-arrow-right-from-bracket" style="color: #dc3545;"></i> {{ __('messages.logout') }}</a></li>
        </ul>
    </aside>

    <main class="main-content">
        <div id="kyc-alert">
            <i class="fa-solid fa-circle-exclamation"></i> <strong>{{ __('messages.alert') }}</strong> {{ __('messages.kyc_alert_msg') }}
        </div>

        <!-- Dashboard Tab -->
        <div id="dashboard-tab" class="tab-content active">
            <h2 style="margin-top:0; color:var(--primary); margin-bottom: 25px;">{{ __('messages.overview') }}</h2>
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon" style="background: var(--primary);">
                        <i class="fa-solid fa-boxes-stacked"></i>
                    </div>
                    <div class="stat-info">
                        <h4>{{ __('messages.total_products') }}</h4>
                        <p id="stat-products">0</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon" style="background: var(--secondary);">
                        <i class="fa-solid fa-clock-rotate-left"></i>
                    </div>
                    <div class="stat-info">
                        <h4>{{ __('messages.pending_orders') }}</h4>
                        <p id="stat-pending">0</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon" style="background: #4CAF50;">
                        <i class="fa-solid fa-check-double"></i>
                    </div>
                    <div class="stat-info">
                        <h4>{{ __('messages.confirmed_sales') }}</h4>
                        <p id="stat-sales">0</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon" style="background: #9C27B0;">
                        <i class="fa-solid fa-eye"></i>
                    </div>
                    <div class="stat-info">
                        <h4>{{ __('messages.store_views') }}</h4>
                        <p id="stat-views">0</p>
                    </div>
                </div>
            </div>
            
            <div class="table-container" style="margin-top:30px;">
                <h3 style="margin-top:0; margin-bottom: 20px;">{{ __('messages.recent_orders') }}</h3>
                <table id="recent-orders-table">
                    <thead>
                        <tr>
                            <th>{{ __('messages.number_abbr') }}</th>
                            <th>{{ __('messages.product') }}</th>
                            <th>{{ __('messages.customer_phone') }}</th>
                            <th>{{ __('messages.date') }}</th>
                            <th>{{ __('messages.status') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td colspan="5" style="text-align:center;">{{ __('messages.loading') }}</td></tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Products Tab -->
        <div id="products-tab" class="tab-content">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:25px; background:var(--white); padding:20px; border-radius:12px; box-shadow:0 2px 10px rgba(0,0,0,0.02);">
                <div>
                    <h2 style="margin:0; color:var(--text-dark); font-size: 22px;">{{ __('messages.products') }}</h2>
                    <p style="margin:5px 0 0; color:var(--text-muted); font-size:14px;">إدارة منتجاتك، تعديل الأسعار، ومتابعة المخزون</p>
                </div>
                <button class="btn-primary" id="btn-add-product" onclick="showProductModal()" style="padding:12px 25px; border-radius:8px; font-weight:600; font-size:15px; box-shadow:0 4px 10px rgba(140, 163, 121, 0.3);"><i class="fa-solid fa-plus"></i> {{ __('messages.new_product') }}</button>
            </div>
            
            <div id="products-container" class="products-grid">
                <div style="grid-column: 1 / -1; text-align: center; padding: 40px;">
                    <i class="fa-solid fa-spinner fa-spin fa-3x" style="color:var(--primary);"></i>
                    <p style="margin-top:15px; color:var(--text-muted);">{{ __('messages.loading') }}</p>
                </div>
            </div>
        </div>

        <!-- Orders (Sales) Tab -->
        <div id="orders-tab" class="tab-content">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:25px;">
                <h2 style="margin-top:0; color:var(--primary); margin-bottom:0;">{{ __('messages.orders_sales_history') }}</h2>
                <button class="btn-secondary" onclick="window.print()"><i class="fa-solid fa-print"></i> {{ __('messages.generate_report') }}</button>
            </div>
            <div class="table-container">
                <table id="all-orders-table">
                    <thead>
                        <tr>
                            <th>{{ __('messages.order_number') }}</th>
                            <th>{{ __('messages.product') }}</th>
                            <th>{{ __('messages.price') }}</th>
                            <th>{{ __('messages.customer_contact') }}</th>
                            <th>{{ __('messages.sale_date') }}</th>
                            <th>{{ __('messages.status') }}</th>
                            <th>{{ __('messages.action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td colspan="7" style="text-align:center;">{{ __('messages.loading') }}</td></tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Settings Tab -->
        <div id="settings-tab" class="tab-content">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px;">
                
                <!-- Store Profile Form -->
                <div class="table-container">
                    <h3 style="margin-top:0; border-bottom:1px solid #eee; padding-bottom:15px; margin-bottom:20px; color:var(--primary);"><i class="fa-solid fa-store"></i> {{ __('messages.basic_store_info') }}</h3>
                    <p style="font-size:14px; color:var(--text-muted); margin-bottom: 25px;">{{ __('messages.update_store_info_desc') }}</p>
                    
                    <form id="store-profile-form">
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                            <div class="form-group" style="background:#f4fdfb; padding:15px; border-radius:8px; border:2px dashed var(--primary); text-align:center;">
                                <label style="color:var(--primary); font-weight:bold; margin-bottom:10px; cursor:pointer;" for="store-logo">
                                    <i class="fa-solid fa-camera fa-2x"></i><br>{{ __('messages.logo_image') }}
                                </label>
                                <input type="file" id="store-logo" name="logo" class="form-control" accept="image/*" style="display:none;" onchange="document.getElementById('store-logo-preview').innerText = this.files[0] ? this.files[0].name : '{{ __('messages.no_image_selected') }}'">
                                <div id="store-logo-preview" style="margin-top:10px; font-size:12px; color:var(--text-muted);">{{ __('messages.no_image_selected') }}</div>
                            </div>
                            
                            <div class="form-group" style="background:#fdf4f4; padding:15px; border-radius:8px; border:2px dashed #dc3545; text-align:center;">
                                <label style="color:#dc3545; font-weight:bold; margin-bottom:10px; cursor:pointer;" for="store-cover">
                                    <i class="fa-solid fa-image fa-2x"></i><br>{{ __('messages.cover_image') }}
                                </label>
                                <input type="file" id="store-cover" name="cover_image" class="form-control" accept="image/*" style="display:none;" onchange="document.getElementById('store-cover-preview').innerText = this.files[0] ? this.files[0].name : '{{ __('messages.no_cover_selected') }}'">
                                <div id="store-cover-preview" style="margin-top:10px; font-size:12px; color:var(--text-muted);">{{ __('messages.no_cover_selected') }}</div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>{{ __('messages.store_name_visible') }}</label>
                            <input type="text" id="store-name" class="form-control" required>
                        </div>
                        
                        <div class="form-group">
                            <label>{{ __('messages.contact_whatsapp') }}</label>
                            <input type="text" id="store-whatsapp" class="form-control" placeholder="77xxxxxxx" required>
                        </div>

                        <div class="form-group">
                            <label>{{ __('messages.store_desc_brief') }}</label>
                            <textarea id="store-desc" class="form-control" rows="4" placeholder="{{ __('messages.write_brief_products') }}"></textarea>
                        </div>

                        <button type="submit" id="profile-submit-btn" class="btn-primary" style="width:100%; padding: 12px; font-size: 16px;">
                            <i class="fa-solid fa-save"></i> {{ __('messages.save_store_settings') }}
                        </button>
                        <div id="profile-save-msg" style="text-align:center; margin-top:10px; display:none; font-weight:bold; font-size:14px;"></div>
                    </form>
                </div>

                <!-- Store Verification / KYC Form -->
                <div class="table-container">
                    <h3 style="margin-top:0; border-bottom:1px solid #eee; padding-bottom:15px; margin-bottom:20px; color:var(--primary);"><i class="fa-solid fa-shield-halved"></i> {{ __('messages.account_verification') }}</h3>
                    
                    <div id="kyc-status-badge" style="margin-bottom:20px; padding:20px; border-radius:10px; text-align:center; font-weight:bold; display:none; font-size: 16px; border: 1px solid #ddd; box-shadow: 0 4px 6px rgba(0,0,0,0.02);">
                        <!-- JS injected message -->
                    </div>

                    <form id="store-kyc-form">
                        <p style="font-size:14px; color:var(--text-muted); margin-bottom: 20px;">{{ __('messages.kyc_desc') }}</p>

                        <div class="form-group">
                            <label>إسم المتجر</label>
                            <input type="text" id="kyc-store-name" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label>نوع المتجر</label>
                            <select id="kyc-business-type" class="form-control" required>
                                <option value="مشروع منزلي">{{ __('messages.home_business') }}</option>
                                <option value="تاجر">{{ __('messages.merchant') }}</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>نشاط المتجر</label>
                            <select id="store-activity" class="form-control" required>
                                <option value="أسرة منتجة">{{ __('messages.productive_family') }}</option>
                                <option value="مخبوزات وحلويات">{{ __('messages.bakery_sweets') }}</option>
                                <option value="أزياء">{{ __('messages.fashion_embroidery') }}</option>
                                <option value="عطور">{{ __('messages.perfumes_incense') }}</option>
                                <option value="أخرى">{{ __('messages.other') }}</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>أرقام التواصل بالمتجر</label>
                            <input type="text" id="kyc-contact-info" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label>موقع المتجر</label>
                            <div id="map-container"></div>
                            <input type="hidden" id="latitude">
                            <input type="hidden" id="longitude">
                        </div>

                        <div class="form-group">
                            <label>وصف الموقع</label>
                            <input type="text" id="kyc-location-desc" class="form-control" placeholder="{{ __('messages.example_address') }}" required>
                        </div>

                        <div style="display:grid; grid-template-columns: 1fr 1fr; gap:15px;">
                            <div class="form-group" style="background:#fffafa; padding: 15px; border-radius: 8px; border: 1px solid #f5c6cb;">
                                <label style="color:var(--text-dark);"><i class="fa-solid fa-id-card"></i> صورة الهوية الامامية</label>
                                <input type="file" id="store-identity-front" class="form-control" accept="image/*" required style="margin-top:10px;">
                            </div>

                            <div class="form-group" style="background:#fffafa; padding: 15px; border-radius: 8px; border: 1px solid #f5c6cb;">
                                <label style="color:var(--text-dark);"><i class="fa-solid fa-id-card"></i> صورة الهوية الخلفية</label>
                                <input type="file" id="store-identity-back" class="form-control" accept="image/*" required style="margin-top:10px;">
                            </div>
                        </div>

                        <button type="submit" id="kyc-submit-btn" class="btn-secondary" style="width:100%; padding: 12px; font-size: 16px; margin-top: 10px;">
                            <i class="fa-solid fa-paper-plane"></i> {{ __('messages.submit_verification') }}
                        </button>
                        <div id="kyc-save-msg" style="text-align:center; margin-top:10px; display:none; font-weight:bold; font-size:14px;"></div>
                    </form>
                </div>

            </div>
        </div>

    </main>
</div>

<!-- Add/Edit Product Modal (Simple pure JS approach) -->
<!-- Add/Edit Product Modal -->
<div id="product-modal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.6); z-index:9999; align-items:center; justify-content:center; backdrop-filter: blur(4px);">
    <div style="background:var(--white); width:550px; max-width:95%; max-height: 90vh; overflow-y: auto; border-radius:15px; padding:35px; position:relative; box-shadow: 0 10px 40px rgba(0,0,0,0.2);">
        <span onclick="closeProductModal()" style="position:absolute; top:20px; left:20px; cursor:pointer; font-size:24px; color:#aaa; transition: 0.3s;"><i class="fa-solid fa-xmark"></i></span>
        <h3 id="modal-title" style="margin-top:0; color:var(--primary); margin-bottom:25px; font-size: 22px; border-bottom: 2px solid #eee; padding-bottom: 10px;"><i class="fa-solid fa-box-open"></i> {{ __('messages.add_new_product') }}</h3>
        
        <form id="product-form">
            <input type="hidden" id="prod-id">
            <div class="form-group">
                <label>{{ __('messages.product_name') }}</label>
                <input type="text" id="prod-name" class="form-control" placeholder="{{ __('messages.example_product_name') }}" required>
            </div>
            
            <div style="display:flex; gap:15px;">
                <div class="form-group" style="flex:1;">
                    <label>{{ __('messages.price_sar') }}</label>
                    <input type="number" id="prod-price" class="form-control" step="0.01" min="0" placeholder="{{ __('messages.example_price') ?? '150.00' }}" required>
                </div>
                <div class="form-group" style="flex:1;">
                    <label>{{ __('messages.category') }}</label>
                    <select id="prod-category" class="form-control" required>
                        <option value="">{{ __('messages.select_category') }}</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label>{{ __('messages.engaging_product_desc') }}</label>
                <textarea id="prod-desc" class="form-control" rows="3" placeholder="{{ __('messages.write_product_details') }}"></textarea>
            </div>
            
            <div class="form-group" style="background:#f4fdfb; padding:20px; border-radius:8px; border:2px dashed var(--primary); margin-bottom: 20px;">
                <label style="color:var(--primary); font-weight:bold; margin-bottom:5px;"><i class="fa-solid fa-images"></i> {{ __('messages.product_images_upload') }}</label>
                <p style="font-size:12px; color:var(--text-muted); margin-top:0; margin-bottom:15px;">{{ __('messages.good_pictures_increase') }}</p>
                <input type="file" id="prod-images" name="images[]" class="form-control" accept="image/*" multiple>
            </div>
            
            <div class="form-group">
                <label>{{ __('messages.status') }}</label>
                <select id="prod-status" class="form-control">
                    <option value="active">{{ __('messages.active_public') }}</option>
                    <option value="hidden">{{ __('messages.hidden_not_showing') }}</option>
                </select>
            </div>
            
            <div style="display:flex; gap:10px; margin-top:20px;">
                <button type="submit" id="prod-submit-btn" class="btn-primary" style="flex:1; padding: 15px; font-size: 16px;">
                    <i class="fa-solid fa-download"></i> <span id="prod-btn-text">{{ __('messages.save_product') }}</span>
                </button>
                <button type="button" onclick="closeProductModal()" class="btn-secondary" style="flex:1; padding: 15px; font-size: 16px; background:#6c757d; border-color: #6c757d;">
                    <i class="fa-solid fa-times"></i> <span id="cancel-btn-text">{{ __('messages.cancel_addition') }}</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const token = localStorage.getItem('auth_token');
    if (!token) {
        window.location.href = '/login';
    }

    const headers = {
        'Authorization': `Bearer ${token}`,
        'Accept': 'application/json',
        'Content-Type': 'application/json'
    };

    // Tab Navigation
    document.querySelectorAll('.nav-link').forEach(link => {
        link.addEventListener('click', function(e) {
            if(this.getAttribute('onclick')) return; // skip logout
            e.preventDefault();
            
            // Remove active classes
            document.querySelectorAll('.nav-link').forEach(l => l.classList.remove('active'));
            document.querySelectorAll('.tab-content').forEach(t => t.classList.remove('active'));
            
            // Add active class
            this.classList.add('active');
            const target = this.getAttribute('data-target');
            document.getElementById(target).classList.add('active');

            // Load specifics if needed
            if(target === 'products-tab') loadProducts();
            if(target === 'orders-tab') loadOrders(true);
            if(target === 'settings-tab') {
                if (typeof initMap === 'function') initMap();
            }
        });
    });

    // Initial Load
    document.addEventListener('DOMContentLoaded', async () => {
        await fetchCategories();
        await loadDashboardData();
        loadOrders(false); // loads recent orders for dashboard tab
    });

    window.appCategories = {};
    async function fetchCategories() {
        try {
            const res = await fetch('/api/categories');
            const categories = await res.json();
            const select = document.getElementById('prod-category');
            const isEnglish = document.documentElement.lang === 'en';
            categories.forEach(c => {
                const name = isEnglish ? (c.name_en || c.name_ar) : c.name_ar;
                window.appCategories[c.id] = name;
                select.innerHTML += `<option value="${c.id}">${name}</option>`;
            });
        } catch(e) {}
    }

    async function loadDashboardData() {
        try {
            const res = await fetch('/api/seller/dashboard/stats', { headers });
            if(res.status === 401 || res.status === 403) { logout(); return; }
            const data = await res.json();
            
            document.getElementById('stat-products').innerText = data.products_count || 0;
            document.getElementById('stat-pending').innerText = data.pending_sales || 0;
            document.getElementById('stat-sales').innerText = data.total_sales || 0;
            document.getElementById('stat-views').innerText = data.total_views || 0;

            if(data.kyc_status === 'pending' || data.kyc_status === 'rejected') {
                document.getElementById('kyc-alert').style.display = 'block';
                if(data.kyc_status === 'pending') {
                    document.getElementById('kyc-alert').innerHTML = '<i class="fa-solid fa-clock"></i> <strong>{{ __('messages.alert') }}</strong> {{ __('messages.documents_under_review') }}';
                }
            }

            // Load Store settings info
            loadStoreDetails();
        } catch(e) { console.error('Error fetching dashboard stats', e); }
    }

    async function loadStoreDetails() {
        try {
            const res = await fetch('/api/seller/store', { headers });
            const store = await res.json();
            
            document.getElementById('sidebar-store-name').innerText = store.name || '{{ __('messages.my_store') }}';
            
            const statusBadge = document.getElementById('sidebar-store-status');
            if (store.status === 'active') {
                statusBadge.innerText = '{{ __('messages.active') }}';
                statusBadge.style.color = 'var(--primary)';
            } else if (store.status === 'pending') {
                statusBadge.innerText = '{{ __('messages.pending_approval') }}';
                statusBadge.style.color = 'orange';
            } else {
                statusBadge.innerText = '{{ __('messages.inactive') }}';
                statusBadge.style.color = 'red';
            }
            window.storeStatus = store.status; // Save for modal check
            
            if(store.logo) {
                document.getElementById('sidebar-store-logo').src = store.logo;
            } else {
                document.getElementById('sidebar-store-logo').src = 'https://via.placeholder.com/80?text=Logo';
            }

            if(store.cover_image) {
                document.getElementById('sidebar-store-cover').style.backgroundImage = `url('${store.cover_image}')`;
            } else {
                document.getElementById('sidebar-store-cover').style.backgroundImage = 'none';
                document.getElementById('sidebar-store-cover').style.background = '#eee';
            }

            
            // Populate Profile Form
            document.getElementById('store-name').value = store.name || '';
            document.getElementById('store-whatsapp').value = store.whatsapp_number || '';
            document.getElementById('store-desc').value = store.description || '';
            if (store.logo) {
                document.getElementById('store-logo-preview').innerHTML = `<img src="${store.logo}" style="max-height: 80px; border-radius: 8px; margin-top: 10px;">`;
            }
            if (store.cover_image) {
                document.getElementById('store-cover-preview').innerHTML = `<img src="${store.cover_image}" style="max-height: 80px; border-radius: 8px; margin-top: 10px; width: 100%; object-fit: cover;">`;
            }

            // Fill KYC
            document.getElementById('kyc-store-name').value = store.name || '';
            document.getElementById('kyc-contact-info').value = store.contact_info || store.whatsapp_number || '';
            document.getElementById('kyc-location-desc').value = store.location_description || store.location || '';
            document.getElementById('latitude').value = store.latitude || '';
            document.getElementById('longitude').value = store.longitude || '';

            if (store.business_type) {
                document.getElementById('kyc-business-type').value = store.business_type;
            }
            if (store.store_activity) {
                document.getElementById('store-activity').value = store.store_activity;
            }
            
            const kBadge = document.getElementById('kyc-status-badge');
            const kForm = document.getElementById('store-kyc-form');
            const btnAddProd = document.getElementById('btn-add-product');
            
            if(store.kyc_status === 'approved') {
                kBadge.style.display = 'block';
                kBadge.style.background = '#d4edda'; kBadge.style.color = '#155724'; kBadge.style.border = '1px solid #c3e6cb';
                kBadge.innerHTML = '<i class="fa-solid fa-check-circle" style="font-size:24px; vertical-align:middle; margin-left:10px;"></i> {{ __('messages.account_verified_success') }}';
                kForm.style.display = 'none';
                if(btnAddProd) btnAddProd.style.display = 'inline-block';
            } else if(store.kyc_status === 'pending' && store.identity_front) {
                // If they have uploaded documents, it is truly under review.
                kBadge.style.display = 'block';
                kBadge.style.background = '#fff3cd'; kBadge.style.color = '#856404'; kBadge.style.border = '1px solid #ffeeba';
                kBadge.innerHTML = '<i class="fa-solid fa-clock" style="font-size:24px; vertical-align:middle; margin-left:10px;"></i> {{ __('messages.verification_pending_msg') }}';
                kForm.style.display = 'none';
                if(btnAddProd) btnAddProd.style.display = 'none';
            } else {
                // If they haven't uploaded documents, show the KYC form so they can!
                kBadge.style.display = 'none';
                kForm.style.display = 'block';
                if(btnAddProd) btnAddProd.style.display = 'none';
                // Leaflet requires a timeout sometimes when revealing a hidden container
                setTimeout(() => typeof initMap === 'function' && initMap(), 200);
            }

        } catch(e) { console.error('Error loading store details', e); }
    }

    // Profile Form Submit
    document.getElementById('store-profile-form').addEventListener('submit', async (e) => {
        e.preventDefault();
        const submitBtn = document.getElementById('profile-submit-btn');
        const msgBox = document.getElementById('profile-save-msg');
        
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> {{ __('messages.saving') }}';
        msgBox.style.display = 'none';

        const formData = new FormData();
        formData.append('name', document.getElementById('store-name').value);
        formData.append('whatsapp_number', document.getElementById('store-whatsapp').value);
        formData.append('description', document.getElementById('store-desc').value);

        const logoFile = document.getElementById('store-logo');
        if (logoFile.files.length > 0) {
            formData.append('logo', logoFile.files[0]);
        }

        const coverFile = document.getElementById('store-cover');
        if (coverFile.files.length > 0) {
            formData.append('cover_image', coverFile.files[0]);
        }

        try {
            const res = await fetch('/api/seller/store/profile', { 
                method: 'POST',
                headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }, 
                body: formData 
            }); 
            
            const data = await res.json();
            
            if(res.ok) {
                msgBox.style.display = 'block';
                msgBox.style.color = 'green';
                msgBox.innerText = data.message || '{{ __('messages.saved_successfully') }}';
                loadStoreDetails();
            } else {
                msgBox.style.display = 'block';
                msgBox.style.color = 'red';
                msgBox.innerHTML = data.message || '{{ __('messages.check_fields_error') }}';
            }
        } catch(e) {
            msgBox.style.display = 'block';
            msgBox.style.color = 'red';
            msgBox.innerText = '{{ __('messages.connection_lost') }}';
        }
        
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fa-solid fa-save"></i> حفظ إعدادات المتجر';
    });

    // KYC Form Submit
    document.getElementById('store-kyc-form').addEventListener('submit', async (e) => {
        e.preventDefault();
        const submitBtn = document.getElementById('kyc-submit-btn');
        const msgBox = document.getElementById('kyc-save-msg');
        
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> {{ __('messages.submitting') }}';
        msgBox.style.display = 'none';

        const formData = new FormData();
        formData.append('name', document.getElementById('kyc-store-name').value);
        formData.append('business_type', document.getElementById('kyc-business-type').value);
        formData.append('store_activity', document.getElementById('store-activity').value);
        formData.append('contact_info', document.getElementById('kyc-contact-info').value);
        formData.append('location_description', document.getElementById('kyc-location-desc').value);
        
        let lat = document.getElementById('latitude').value;
        let lng = document.getElementById('longitude').value;
        if(lat) formData.append('latitude', lat);
        if(lng) formData.append('longitude', lng);

        const idFront = document.getElementById('store-identity-front');
        if (idFront.files.length > 0) {
            formData.append('identity_front', idFront.files[0]);
        }

        const idBack = document.getElementById('store-identity-back');
        if (idBack.files.length > 0) {
            formData.append('identity_back', idBack.files[0]);
        }

        try {
            const res = await fetch('/api/seller/store/kyc', { 
                method: 'POST',
                headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }, 
                body: formData 
            }); 
            
            const data = await res.json();
            
            if(res.ok) {
                msgBox.style.display = 'block';
                msgBox.style.color = 'green';
                msgBox.innerText = data.message || '{{ __('messages.submitted_successfully') }}';
                loadStoreDetails();
            } else {
                msgBox.style.display = 'block';
                msgBox.style.color = 'red';
                msgBox.innerHTML = data.message || '{{ __('messages.fill_all_fields_image') }}';
            }
        } catch(e) {
            msgBox.style.display = 'block';
            msgBox.style.color = 'red';
            msgBox.innerText = '{{ __('messages.connection_lost') }}';
        }
        
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fa-solid fa-paper-plane"></i> إرسال للتوثيق';
    });


    // PRODUCTS LOGIC
    async function loadProducts() {
        const container = document.getElementById('products-container');
        container.innerHTML = `
            <div style="grid-column: 1 / -1; text-align: center; padding: 50px;">
                <i class="fa-solid fa-spinner fa-spin fa-3x" style="color:var(--primary);"></i>
                <p style="margin-top:15px; font-size:16px; color:var(--text-muted);">{{ __('messages.loading') }}</p>
            </div>`;
        
        try {
            const res = await fetch('/api/seller/products', { headers });
            const data = await res.json();
            
            if(!data.data || data.data.length === 0) {
                container.innerHTML = `
                    <div style="grid-column: 1 / -1; text-align: center; padding: 60px; background:var(--white); border-radius:12px; box-shadow:0 4px 10px rgba(0,0,0,0.02); border: 1px solid #f0f0f0;">
                        <i class="fa-solid fa-box-open fa-4x" style="color:#ddd; margin-bottom:20px;"></i>
                        <h3 style="color:var(--text-dark); margin-bottom:10px;">{{ __('messages.no_products_added_yet') }}</h3>
                        <p style="color:var(--text-muted); margin-bottom:20px;">ابدأ بإضافة أول منتج لمتجرك الآن</p>
                        <button class="btn-primary" onclick="showProductModal()" style="padding:10px 20px; border-radius:8px;"><i class="fa-solid fa-plus"></i> {{ __('messages.new_product') }}</button>
                    </div>`;
                return;
            }

            let html = '';
            data.data.forEach(p => {
                let statusBadge = p.status === 'active' ? 'background: rgba(40, 167, 69, 0.9); color: white;' : 'background: rgba(108, 117, 125, 0.9); color: white;';
                let statusText = p.status === 'active' ? '{{ __('messages.active') }}' : '{{ __('messages.hidden_not_showing') }}';
                
                if (p.status === 'deleted_by_admin') {
                    statusBadge = 'background: rgba(220, 53, 69, 0.9); color: white;';
                    statusText = '<i class="fa-solid fa-ban"></i> محذوف من الإدارة';
                }
                
                let imgHtml = '';
                if (p.images && p.images.length > 0) {
                    let imgSrc = typeof p.images[0] === 'object' ? p.images[0].image_path : p.images[0];
                    imgHtml = `<img src="${imgSrc}" alt="${p.name}">`;
                } else {
                    imgHtml = `<div style="width: 100%; height: 100%; background: #f8f9fa; display: flex; flex-direction:column; align-items: center; justify-content: center; color:#ccc;">
                        <i class="fa-solid fa-image fa-3x" style="margin-bottom:10px;"></i>
                        <span>لا توجد صورة</span>
                    </div>`;
                }

                const categoryName = p.category ? (document.documentElement.lang === 'en' ? (p.category.name_en || p.category.name_ar) : p.category.name_ar) : (window.appCategories[p.category_id] || 'غير محدد');
                
                let actions = '';
                if (p.status !== 'deleted_by_admin') {
                    actions = `
                        <button onclick="editProduct(${p.id})" class="btn-edit" title="{{ __('messages.edit_product') }}">
                            <i class="fa-solid fa-pen"></i> تعديل
                        </button>
                        <button onclick="deleteProduct(${p.id})" class="btn-delete" title="{{ __('messages.confirm_delete_product') }}">
                            <i class="fa-solid fa-trash"></i> حذف
                        </button>
                    `;
                } else {
                    actions = `<div style="text-align:center; width:100%; padding:10px; background:#fdf4f4; color:#dc3545; border-radius:8px; font-weight:bold; font-size:13px;">يرجى التواصل مع الدعم</div>`;
                }

                html += `
                    <div class="product-card">
                        <div class="product-image-wrapper">
                            <div class="product-status-badge" style="${statusBadge}">${statusText}</div>
                            ${imgHtml}
                        </div>
                        <div class="product-details">
                            <div class="product-category"><i class="fa-solid fa-tags"></i> ${categoryName}</div>
                            <h3 class="product-title">${p.name}</h3>
                            <div class="product-desc">${p.description ? p.description : '<em style="color:#aaa;">لا يوجد وصف</em>'}</div>
                            
                            <div class="product-meta">
                                <div class="product-price">${parseFloat(p.price).toFixed(2)} <span style="font-size:14px; color:var(--text-muted); font-weight:normal;">{{ __('messages.sar') }}</span></div>
                                <div style="font-size:12px; color:var(--text-muted);"><i class="fa-regular fa-clock"></i> ${new Date(p.created_at).toLocaleDateString(document.documentElement.lang === 'en' ? 'en-US' : 'ar-EG')}</div>
                            </div>
                            
                            <div class="product-actions">
                                ${actions}
                            </div>
                        </div>
                    </div>
                `;
            });
            container.innerHTML = html;
        } catch(e) {}
    }

    /* Product Modal logic */
    function showProductModal() {
        if (window.storeStatus !== 'active') {
            alert('عذراً، لا يُسمح بإضافة منتجات قبل رفع وثائق التوثيق وقبول الإدارة لحسابك.');
            return;
        }
        document.getElementById('product-form').reset();
        document.getElementById('prod-id').value = '';
        document.getElementById('modal-title').innerHTML = '<i class="fa-solid fa-box-open"></i> {{ __('messages.add_new_product') }}';
        document.getElementById('prod-btn-text').innerText = '{{ __('messages.save_product') }}';
        document.getElementById('cancel-btn-text').innerText = '{{ __('messages.cancel_addition') }}';
        document.getElementById('product-modal').style.display = 'flex';
    }
    function closeProductModal() {
        document.getElementById('product-modal').style.display = 'none';
    }

    document.getElementById('product-form').addEventListener('submit', async (e) => {
        e.preventDefault();
        const id = document.getElementById('prod-id').value;
        const submitBtn = document.getElementById('prod-submit-btn');
        const originalHtml = submitBtn.innerHTML;
        
        // Show loading state
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> {{ __('messages.saving') }}...';

        const form = new FormData();
        form.append('name', document.getElementById('prod-name').value);
        form.append('description', document.getElementById('prod-desc').value);
        form.append('price', document.getElementById('prod-price').value);
        form.append('status', document.getElementById('prod-status').value);
        form.append('category_id', document.getElementById('prod-category').value);
        
        const fileInput = document.getElementById('prod-images');
        if(fileInput.files.length > 0) {
            for(let i=0; i<fileInput.files.length; i++) {
                form.append('images[]', fileInput.files[i]);
            }
        }

        if(id) {
            // Laravel needs _method=PUT for file uploads over PUT
            form.append('_method', 'PUT');
        }

        const url = id ? `/api/seller/products/${id}` : '/api/seller/products';
        const method = 'POST'; // We use POST for both, relying on _method for updates

        try {
            const res = await fetch(url, { 
                method, 
                body: form,
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            });
            if(res.ok) {
                closeProductModal();
                loadProducts();
                loadDashboardData(); // update stats
            } else { alert('{{ __('messages.check_fields_error') }}'); }
        } catch(e) {
            console.error(e);
            alert('حدث خطأ أثناء الاتصال بالخادم. يرجى المحاولة لاحقاً.');
        } finally {
            // Restore button original state
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalHtml;
        }
    });

    async function deleteProduct(id) {
        if(confirm('{{ __('messages.confirm_delete_product') }}')) {
            fetch(`/api/seller/products/${id}`, { method: 'DELETE', headers })
                .then(() => loadProducts());
        }
    }

    async function editProduct(id) {
        try {
            const res = await fetch(`/api/seller/products/${id}`, { headers });
            const p = await res.json();
            document.getElementById('prod-id').value = p.id;
            document.getElementById('prod-name').value = p.name;
            document.getElementById('prod-desc').value = p.description || '';
            document.getElementById('prod-price').value = p.price;
            document.getElementById('prod-status').value = p.status;
            document.getElementById('prod-category').value = p.category_id || '';
            document.getElementById('prod-images').value = '';
            
            document.getElementById('modal-title').innerHTML = '<i class="fa-solid fa-pen"></i> {{ __('messages.edit_product') }}';
            document.getElementById('prod-btn-text').innerText = '{{ __('messages.save_changes') }}';
            document.getElementById('cancel-btn-text').innerText = '{{ __('messages.cancel_edit') }}';
            document.getElementById('product-modal').style.display = 'flex';
        } catch(e) {}
    }

    // ORDERS LOGIC
    async function loadOrders(fullList = false) {
        const tbodyDashboard = document.querySelector('#recent-orders-table tbody');
        const tbodyAll = document.querySelector('#all-orders-table tbody');
        
        try {
            const res = await fetch('/api/seller/sales', { headers });
            const data = await res.json();
            
            if(!data.data || data.data.length === 0) {
                tbodyDashboard.innerHTML = '<tr><td colspan="5" style="text-align:center;">{{ __('messages.no_orders_yet') }}</td></tr>';
                if(tbodyAll) tbodyAll.innerHTML = '<tr><td colspan="6" style="text-align:center;">{{ __('messages.no_orders_yet') }}</td></tr>';
                return;
            }

            let htmlDash = '';
            let htmlAll = '';

            data.data.forEach((o, i) => {
                const sBadge = o.status === 'pending' ? 'status-pending' : (o.status === 'confirmed' ? 'status-confirmed' : 'status-cancelled');
                const sText = o.status === 'pending' ? '{{ __('messages.pending') }}' : (o.status === 'confirmed' ? '{{ __('messages.confirmed') }}' : '{{ __('messages.cancelled') }}');
                const pName = o.product ? o.product.name : '{{ __('messages.deleted_product') }}';
                const date = new Date(o.created_at).toLocaleDateString(document.documentElement.lang === 'ar' ? 'ar-SA' : 'en-US');
                const pPrice = o.product ? o.product.price : '0.00';
                const customerPhone = (o.customer && o.customer.phone) ? o.customer.phone : (o.customer_contact || '{{ __('messages.unavailable') }}');

                if(i < 5) { // Only top 5 for dashboard
                    htmlDash += `
                        <tr>
                            <td>#${o.id}</td>
                            <td>${pName}</td>
                            <td dir="ltr">${customerPhone}</td>
                            <td>${date}</td>
                            <td><span class="status-badge ${sBadge}">${sText}</span></td>
                        </tr>
                    `;
                }

                htmlAll += `
                    <tr>
                        <td>#${o.id}</td>
                        <td>${pName}</td>
                        <td style="color:var(--primary); font-weight:bold;">${pPrice} {{ __('messages.sar') }}</td>
                        <td dir="ltr">${customerPhone}</td>
                        <td>${date}</td>
                        <td><span class="status-badge ${sBadge}">${sText}</span></td>
                        <td>
                            ${o.status === 'pending' ? `
                                <select onchange="updateOrderStatus(${o.id}, this.value)" style="padding:5px; border-radius:4px; border:1px solid #ddd;">
                                    <option value="">{{ __('messages.update_status') }}</option>
                                    <option value="confirmed">{{ __('messages.confirm_order') }}</option>
                                    <option value="cancelled">{{ __('messages.cancel_order') }}</option>
                                </select>
                            ` : '-'}
                        </td>
                    </tr>
                `;
            });
            tbodyDashboard.innerHTML = htmlDash;
            if(tbodyAll) tbodyAll.innerHTML = htmlAll;
        } catch(e) {}
    }

    async function updateOrderStatus(id, newStatus) {
        if(!newStatus) return;
        try {
            const res = await fetch(`/api/seller/sales/${id}/status`, { 
                method: 'PUT', headers, body: JSON.stringify({status: newStatus}) 
            });
            if(res.ok) {
                loadOrders(true);
                loadDashboardData(); // update stats
            }
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
        } catch(e) { console.error('Logout error:', e); }
        finally {
            localStorage.removeItem('auth_token');
            localStorage.removeItem('user');
            window.location.href = '/login';
        }
    }

    // Leaflet Map Initialization
    let mapInitialized = false;
    let map, marker;
    function initMap() {
        if(mapInitialized) {
            setTimeout(() => map.invalidateSize(), 100);
            return;
        }
        mapInitialized = true;
        
        // Default to Sanaa, Yemen
        let initialLat = document.getElementById('latitude').value || 15.3694;
        let initialLng = document.getElementById('longitude').value || 44.1910;
        
        map = L.map('map-container').setView([initialLat, initialLng], 12);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        marker = L.marker([initialLat, initialLng], {draggable: true}).addTo(map);

        function updateInputs(lat, lng) {
            document.getElementById('latitude').value = lat;
            document.getElementById('longitude').value = lng;
        }

        marker.on('dragend', function(e) {
            var position = marker.getLatLng();
            updateInputs(position.lat, position.lng);
        });

        map.on('click', function(e) {
            marker.setLatLng(e.latlng);
            updateInputs(e.latlng.lat, e.latlng.lng);
        });
    }

</script>
@endpush
