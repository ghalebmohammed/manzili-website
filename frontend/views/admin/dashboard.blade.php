@extends('layouts.app')

@section('title', __('messages.admin_dashboard_title'))

@push('styles')
<style>
    .dashboard-container {
        display: flex;
        gap: 20px;
        margin-top: 30px;
        margin-bottom: 50px;
    }
    .sidebar {
        width: 250px;
        background: var(--white);
        border-radius: 10px;
        padding: 20px 0;
        box-shadow: 0 4px 10px rgba(0,0,0,0.05);
        height: fit-content;
    }
    .sidebar-menu {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    .sidebar-menu li {
        padding: 0;
    }
    .sidebar-menu a {
        display: block;
        padding: 15px 20px;
        color: var(--text-dark);
        text-decoration: none;
        font-weight: 500;
        border-right: 4px solid transparent;
        transition: all 0.3s;
    }
    .sidebar-menu a.active, .sidebar-menu a:hover {
        background-color: var(--bg-light);
        color: var(--primary);
        border-right-color: var(--primary);
    }
    .sidebar-menu i {
        width: 25px;
        text-align: center;
        margin-left: 10px;
        transition: transform 0.3s ease;
    }
    .sidebar-menu a:hover i, .sidebar-menu a.active i {
        transform: scale(1.2) rotate(-5deg);
    }
    
    .dashboard-content {
        flex: 1;
    }
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }
    .stat-card {
        background: var(--white);
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.05);
        display: flex;
        align-items: center;
        gap: 15px;
    }
    .stat-icon {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        display: flex;
        justify-content: center;
        align-items: center;
        font-size: 20px;
    }
    .stat-info h4 {
        margin: 0 0 5px 0;
        color: var(--text-muted);
        font-size: 14px;
        font-weight: normal;
    }
    .stat-info p {
        margin: 0;
        font-size: 24px;
        font-weight: bold;
        color: var(--text-dark);
    }
    
    .content-section {
        background: var(--white);
        border-radius: 10px;
        padding: 25px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.05);
        margin-bottom: 25px;
        display: none;
    }
    .content-section.active {
        display: block;
    }
    .section-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        border-bottom: 2px solid #eee;
        padding-bottom: 15px;
    }
    .data-table {
        width: 100%;
        border-collapse: collapse;
    }
    .data-table th, .data-table td {
        padding: 12px 15px;
        text-align: right;
        border-bottom: 1px solid #eee;
    }
    .data-table th {
        background-color: #f8f9fa;
        color: var(--text-dark);
        font-weight: 600;
    }
    .status-badge {
        padding: 5px 10px;
        border-radius: 15px;
        font-size: 12px;
        font-weight: bold;
    }
    .status-pending { background: #fff3cd; color: #856404; }
    .status-approved { background: #d4edda; color: #155724; }
    .status-rejected { background: #f8d7da; color: #721c24; }
    
    .action-btn {
        padding: 5px 10px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-family: inherit;
        font-size: 12px;
        color: white;
    }
    .btn-approve { background: var(--primary); }
    .btn-reject { background: #dc3545; }

    @keyframes pulse-icon {
        0% { transform: scale(1); }
        50% { transform: scale(1.2); color: var(--primary); }
        100% { transform: scale(1); }
    }
    .contact-link-active {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.3s ease;
    }
    .contact-link-active:hover i {
        animation: pulse-icon 1s infinite;
        color: var(--primary) !important;
    }
    .contact-link-active:hover {
        transform: translateX(-5px);
        color: var(--primary) !important;
    }
</style>
@endpush

@section('content')
<div class="container">
    <div class="dashboard-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <h3 style="padding: 0 20px; color: var(--primary); margin-bottom: 15px;">{{ __('messages.central_administration') }}</h3>
            <ul class="sidebar-menu">
                <li><a href="#" class="nav-link active" data-target="overview"><i class="fa-solid fa-chart-pie"></i> {{ __('messages.overview') }}</a></li>
                <li><a href="#" class="nav-link" data-target="creation-requests"><i class="fa-solid fa-user-plus"></i> {{ __('messages.store_creation_requests') }}</a></li>
                <li><a href="#" class="nav-link" data-target="verification-requests"><i class="fa-solid fa-file-signature"></i> {{ __('messages.verification_requests') }}</a></li>
                <li><a href="#" class="nav-link" data-target="verified-stores"><i class="fa-solid fa-store"></i> {{ __('messages.verified_stores') }}</a></li>
                <li><a href="#" class="nav-link" data-target="all-stores-data"><i class="fa-solid fa-server"></i> بيانات المتاجر</a></li>
                <li><a href="#" class="nav-link" data-target="featured-ads"><i class="fa-solid fa-star"></i> الإعلانات المميزة</a></li>
                <li><a href="#" class="nav-link" data-target="notifications"><i class="fa-solid fa-paper-plane"></i> {{ __('messages.send_notifications') }}</a></li>
                <li><a href="#" onclick="logoutUser(); return false;" style="color: #dc3545;"><i class="fa-solid fa-arrow-right-from-bracket" style="color: #dc3545;"></i> {{ __('messages.logout') }}</a></li>
            </ul>
        </aside>

        <!-- Main Content -->
        <div class="dashboard-content">
            <!-- Overview Section -->
            <div id="overview" class="content-section active" data-aos="fade-up">
                <div class="section-header">
                    <h2 style="margin: 0;"><i class="fa-solid fa-chart-pie"></i> {{ __('messages.overview') }}</h2>
                </div>
                
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon" style="background: rgba(140, 163, 121, 0.1); color: var(--primary);">
                            <i class="fa-solid fa-users"></i>
                        </div>
                        <div class="stat-info">
                            <h4>{{ __('messages.total_users') }}</h4>
                            <p id="stat-users">0</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon" style="background: rgba(212, 163, 115, 0.1); color: var(--secondary);">
                            <i class="fa-solid fa-store"></i>
                        </div>
                        <div class="stat-info">
                            <h4>{{ __('messages.total_stores') }}</h4>
                            <p id="stat-stores">0</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon" style="background: rgba(255, 193, 7, 0.1); color: #ffc107;">
                            <i class="fa-solid fa-file-signature"></i>
                        </div>
                        <div class="stat-info">
                            <h4>{{ __('messages.pending_kyc_requests') }}</h4>
                            <p id="stat-pending-kyc">0</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon" style="background: rgba(23, 162, 184, 0.1); color: #17a2b8;">
                            <i class="fa-solid fa-box-open"></i>
                        </div>
                        <div class="stat-info">
                            <h4>{{ __('messages.displayed_products') }}</h4>
                            <p id="stat-products">0</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Store Creation Requests Section -->
            <div id="creation-requests" class="content-section" data-aos="fade-up">
                <div class="section-header">
                    <h2 style="margin: 0;"><i class="fa-solid fa-user-plus"></i> {{ __('messages.store_creation_requests') }}</h2>
                </div>
                <div style="overflow-x: auto;">
                    <table class="data-table" id="creation-requests-table">
                        <thead>
                            <tr>
                                <th>{{ __('messages.store_name') }}</th>
                                <th>{{ __('messages.owner_name') }}</th>
                                <th>{{ __('messages.creation_date') }}</th>
                                <th>{{ __('messages.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr><td colspan="4" style="text-align:center;">{{ __('messages.loading_data') }}</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Verification Requests Section -->
            <div id="verification-requests" class="content-section" data-aos="fade-up">
                <div class="section-header">
                    <h2 style="margin: 0;"><i class="fa-solid fa-file-signature"></i> {{ __('messages.verification_requests') }}</h2>
                </div>
                <div style="overflow-x: auto;">
                    <table class="data-table" id="requests-table">
                        <thead>
                            <tr>
                                <th>{{ __('messages.store_name') }}</th>
                                <th>{{ __('messages.owner_name') }}</th>
                                <th>{{ __('messages.creation_date') }}</th>
                                <th>{{ __('messages.kyc_status') }}</th>
                                <th>{{ __('messages.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- JS will populate rows -->
                            <tr><td colspan="5" style="text-align:center;">{{ __('messages.loading_data') }}</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Verified Stores Section -->
            <div id="verified-stores" class="content-section" data-aos="fade-up">
                <div class="section-header">
                    <h2 style="margin: 0;"><i class="fa-solid fa-store"></i> {{ __('messages.verified_stores') }}</h2>
                    <input type="text" id="store-search" placeholder="{{ __('messages.search_store_name') }}" style="padding: 8px 15px; border: 1px solid #ccc; border-radius: 5px; width: 250px;">
                </div>
                <div style="overflow-x: auto;">
                    <table class="data-table" id="verified-stores-table">
                        <thead>
                            <tr>
                                <th>{{ __('messages.store_name') }}</th>
                                <th>{{ __('messages.owner_name') }}</th>
                                <th>{{ __('messages.creation_date') }}</th>
                                <th>{{ __('messages.account_status') }}</th>
                                <th>{{ __('messages.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- JS will populate rows -->
                            <tr><td colspan="5" style="text-align:center;">{{ __('messages.loading_data') }}</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- All Stores Data Section -->
            <div id="all-stores-data" class="content-section" data-aos="fade-up">
                <div class="section-header">
                    <h2 style="margin: 0;"><i class="fa-solid fa-server"></i> بيانات المتاجر</h2>
                    <input type="text" id="all-store-search" placeholder="ابحث باسم المتجر أو المالك..." style="padding: 8px 15px; border: 1px solid #ccc; border-radius: 5px; width: 250px;">
                </div>
                <div style="overflow-x: auto;">
                    <table class="data-table" id="all-stores-table">
                        <thead>
                            <tr>
                                <th>المتجر</th>
                                <th>المالك & التواصل</th>
                                <th>بيانات النشاط</th>
                                <th>حالة التوثيق</th>
                                <th>صور التوثيق</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- JS will populate rows -->
                            <tr><td colspan="6" style="text-align:center;">{{ __('messages.loading_data') }}</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Featured Ads Section -->
            <div id="featured-ads" class="content-section" data-aos="fade-up">
                <div class="section-header">
                    <h2 style="margin: 0;"><i class="fa-solid fa-star"></i> إدارة الإعلانات المميزة</h2>
                    <button class="btn-primary" onclick="openAddFeaturedModal()">إضافة إعلان جديد</button>
                </div>
                
                <div style="overflow-x: auto;">
                    <table class="data-table" id="featured-items-table">
                        <thead>
                            <tr>
                                <th>النوع</th>
                                <th>الاسم</th>
                                <th>تاريخ الانتهاء</th>
                                <th>الحالة</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr><td colspan="5" style="text-align:center;">{{ __('messages.loading_data') }}</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Notifications Section -->
            <div id="notifications" class="content-section" data-aos="fade-up">
                <div class="section-header">
                    <h2 style="margin: 0;"><i class="fa-solid fa-paper-plane"></i> {{ __('messages.send_notifications_users') }}</h2>
                </div>
                <form id="send-notification-form" style="max-width: 600px;">
                    <div style="margin-bottom: 15px;">
                        <label style="display: block; margin-bottom: 5px;">{{ __('messages.notification_title') }}</label>
                        <input type="text" id="notif-title" required style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px;">
                    </div>
                    <div style="margin-bottom: 15px;">
                        <label style="display: block; margin-bottom: 5px;">{{ __('messages.notification_body') }}</label>
                        <textarea id="notif-body" required rows="4" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px;"></textarea>
                    </div>
                    <div style="margin-bottom: 15px;">
                        <label style="display: block; margin-bottom: 5px;">{{ __('messages.redirect_link_optional') }}</label>
                        <input type="url" id="notif-url" placeholder="https://..." style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px;">
                    </div>
                    <div style="margin-bottom: 20px;">
                        <label style="display: block; margin-bottom: 5px;">{{ __('messages.target_audience') }}</label>
                        <select id="notif-target" required style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px;">
                            <option value="all">{{ __('messages.all_users') }}</option>
                            <option value="customer">{{ __('messages.customers_only') }}</option>
                            <option value="seller">{{ __('messages.stores_sellers_only') }}</option>
                        </select>
                    </div>
                    <button type="submit" class="btn-primary"><i class="fa-solid fa-paper-plane"></i> {{ __('messages.send_notification_now') }}</button>
                    <p id="notif-status" style="margin-top: 10px; display: none;"></p>
                </form>
            </div>

        </div>
    </div>
</div>

<!-- Renew Featured Ad Modal -->
<div id="renew-featured-modal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:9999; align-items:center; justify-content:center;">
    <div style="background:var(--white); width:400px; max-width:95%; border-radius:10px; padding:30px; position:relative;">
        <span onclick="document.getElementById('renew-featured-modal').style.display='none'" style="position:absolute; top:20px; left:20px; cursor:pointer; font-size:20px; color:#999;"><i class="fa-solid fa-xmark"></i></span>
        <h3 style="margin-top:0; color:var(--primary); margin-bottom:20px;"><i class="fa-solid fa-clock-rotate-left"></i> تجديد الإعلان</h3>
        
        <form id="renew-featured-form">
            <input type="hidden" id="renew-feat-id">
            <div style="margin-bottom: 20px;">
                <label style="display:block; margin-bottom:5px;">إضافة مدة جديدة</label>
                <div style="display:flex; gap: 10px;">
                    <input type="number" id="renew-duration-val" required min="1" style="flex:2; padding:10px; border:1px solid #ddd; border-radius:5px;" placeholder="أدخل المدة">
                    <select id="renew-duration-type" style="flex:1; padding:10px; border:1px solid #ddd; border-radius:5px;">
                        <option value="days">أيام</option>
                        <option value="hours">ساعات</option>
                    </select>
                </div>
            </div>
            <button type="submit" class="btn-primary" style="width:100%;">تجديد الإعلان</button>
        </form>
    </div>
</div>

<!-- Products Modal -->
<div id="admin-products-modal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:9999; align-items:center; justify-content:center;">
    <div style="background:var(--white); width:800px; max-width:95%; max-height:80vh; border-radius:10px; padding:30px; position:relative; display:flex; flex-direction:column;">
        <span onclick="document.getElementById('admin-products-modal').style.display='none'" style="position:absolute; top:20px; left:20px; cursor:pointer; font-size:20px; color:#999;"><i class="fa-solid fa-xmark"></i></span>
        <h3 id="modal-store-name" style="margin-top:0; color:var(--primary); margin-bottom:20px;">{{ __('messages.products') }}</h3>
        
        <div style="flex:1; overflow-y:auto; border: 1px solid #eee; border-radius:5px;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>{{ __('messages.product') }}</th>
                        <th>{{ __('messages.price') }}</th>
                        <th>{{ __('messages.status') }}</th>
                        <th>{{ __('messages.actions') }}</th>
                    </tr>
                </thead>
                <tbody id="modal-products-body">
                    <!-- Populated by JS -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Featured Ad Modal -->
<div id="add-featured-modal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:9999; align-items:center; justify-content:center;">
    <div style="background:var(--white); width:500px; max-width:95%; border-radius:10px; padding:30px; position:relative;">
        <span onclick="document.getElementById('add-featured-modal').style.display='none'" style="position:absolute; top:20px; left:20px; cursor:pointer; font-size:20px; color:#999;"><i class="fa-solid fa-xmark"></i></span>
        <h3 style="margin-top:0; color:var(--primary); margin-bottom:20px;"><i class="fa-solid fa-plus-circle"></i> إضافة إعلان مميز</h3>
        
        <form id="featured-ad-form">
            <div style="margin-bottom: 15px;">
                <label style="display:block; margin-bottom:5px;">نوع الإعلان</label>
                <select id="feat-type" required style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px;">
                    <option value="store">متجر</option>
                    <option value="product">منتج</option>
                </select>
            </div>
            <div style="margin-bottom: 15px;">
                <label style="display:block; margin-bottom:5px;">رقم المعرف (ID المتجر أو المنتج)</label>
                <div style="display:flex; gap:10px; align-items:center;">
                    <input type="number" id="feat-id" required min="1" style="flex:1; padding:10px; border:1px solid #ddd; border-radius:5px;" placeholder="رقم المتجر أو المنتج">
                    <a href="/stores" target="_blank" id="feat-browse-btn" class="btn-secondary" style="padding:10px 15px; border-radius:5px;" title="تصفح للبحث"><i class="fa-solid fa-up-right-from-square"></i></a>
                </div>
            </div>
            <div style="margin-bottom: 20px;">
                <label style="display:block; margin-bottom:5px;">مدة الظهور</label>
                <div style="display:flex; gap: 10px;">
                    <input type="number" id="feat-duration-val" required min="1" style="flex:2; padding:10px; border:1px solid #ddd; border-radius:5px;" placeholder="أدخل المدة">
                    <select id="feat-duration-type" style="flex:1; padding:10px; border:1px solid #ddd; border-radius:5px;">
                        <option value="days">أيام</option>
                        <option value="hours">ساعات</option>
                    </select>
                </div>
            </div>
            <button type="submit" class="btn-primary" style="width:100%;">إعتماد الإعلان</button>
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

    // Navigation Logic
    document.querySelectorAll('.sidebar-menu .nav-link').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            // Remove active classes
            document.querySelectorAll('.sidebar-menu .nav-link').forEach(l => l.classList.remove('active'));
            document.querySelectorAll('.content-section').forEach(s => s.classList.remove('active'));
            
            // Add active to current
            this.classList.add('active');
            const target = this.getAttribute('data-target');
            document.getElementById(target).classList.add('active');
            
            if (target === 'verification-requests' || target === 'verified-stores' || target === 'all-stores-data') fetchStores();
            if (target === 'featured-ads') fetchFeaturedAds();
        });
    });

    const headers = {
        'Authorization': `Bearer ${token}`,
        'Accept': 'application/json',
        'Content-Type': 'application/json'
    };

    document.addEventListener('DOMContentLoaded', async () => {
        await loadStats();

        // Check if coming from selection page
        const urlParams = new URLSearchParams(window.location.search);
        const fType = urlParams.get('featured_type');
        const fId = urlParams.get('featured_id');
        
        if(fType && fId) {
            showSection('featured-ads');
            openAddFeaturedModal();
            document.getElementById('feat-type').value = fType;
            document.getElementById('feat-id').value = fId;
            updateBrowseLink();
            
            // Cleanup URL
            const newUrl = window.location.protocol + "//" + window.location.host + window.location.pathname;
            window.history.pushState({path:newUrl},'',newUrl);
        }
    });

    async function loadStats() {
        try {
            const res = await fetch('/api/admin/dashboard/stats', { headers });
            if (res.status === 403) {
                alert('{{ __('messages.unauthorized_access') }}');
                window.location.href = '/';
                return;
            }
            const data = await res.json();
            document.getElementById('stat-users').innerText = data.users || 0;
            document.getElementById('stat-stores').innerText = data.stores || 0;
            document.getElementById('stat-products').innerText = data.products || 0;
            document.getElementById('stat-pending-kyc').innerText = data.pending_kyc_count || 0;
        } catch (e) {}
    }

    let allStores = [];
    function fetchStores() {
        fetch('/api/admin/stores', { headers })
            .then(res => res.json())
            .then(data => {
                allStores = data.data || [];
                renderCreationRequests();
                renderVerificationRequests();
                renderVerifiedStores(document.getElementById('store-search') ? document.getElementById('store-search').value : '');
                renderAllStoresData(document.getElementById('all-store-search') ? document.getElementById('all-store-search').value : '');
            })
            .catch(e => console.error(e));
    }

    function renderCreationRequests() {
        const tbody = document.querySelector('#creation-requests-table tbody');
        if(!tbody) return;
        tbody.innerHTML = '';
        const requests = allStores.filter(s => s.status === 'pending');

        if (requests.length === 0) {
            tbody.innerHTML = `<tr><td colspan="4" style="text-align:center;">{{ __('messages.no_stores_currently') }}</td></tr>`;
            return;
        }

        requests.forEach(store => {
            const ownerName = store.user ? store.user.name : '{{ __('messages.unknown') }}';
            const created = new Date(store.created_at).toLocaleDateString(document.documentElement.lang === 'ar' ? 'ar-SA' : 'en-US');
            
            // Custom approve store creation
            let actions = `
                <button class="action-btn btn-approve" onclick="approveStoreCreation(${store.id})"><i class="fa-solid fa-check"></i> {{ __('messages.approve_creation') }}</button>
            `;

            tbody.innerHTML += `
                <tr>
                    <td><strong>${store.name}</strong></td>
                    <td>${ownerName}</td>
                    <td>${created}</td>
                    <td>${actions}</td>
                </tr>
            `;
        });
    }

    function renderVerificationRequests() {
        const tbody = document.querySelector('#requests-table tbody');
        if(!tbody) return;
        tbody.innerHTML = '';
        // Only accounts that are pending KYC verification and have uploaded their documents
        const requests = allStores.filter(s => s.identity_front && (s.kyc_status === 'pending' || s.kyc_status === 'rejected'));

        if (requests.length === 0) {
            tbody.innerHTML = `<tr><td colspan="5" style="text-align:center;">{{ __('messages.no_stores_currently') }}</td></tr>`;
            return;
        }

        requests.forEach(store => {
            const ownerName = store.user ? store.user.name : '{{ __('messages.unknown') }}';
            const created = new Date(store.created_at).toLocaleDateString(document.documentElement.lang === 'ar' ? 'ar-SA' : 'en-US');
            
            let statusClass = store.kyc_status === 'rejected' ? 'status-rejected' : 'status-pending';
            let statusText = store.kyc_status === 'rejected' ? '{{ __('messages.rejected') }}' : '{{ __('messages.under_review') }}';

            let actions = ``;
            if (store.kyc_status === 'pending') {
                actions += `
                    <button class="action-btn" style="background:#007bff;" onclick="viewKycModal(${store.id})"><i class="fa-solid fa-eye"></i> {{ __('messages.view_kyc_data') }}</button><br><br>
                    <button class="action-btn btn-approve" onclick="updateKyc(${store.id}, 'approved')"><i class="fa-solid fa-check"></i> {{ __('messages.verify') }}</button>
                    <button class="action-btn btn-reject" onclick="updateKyc(${store.id}, 'rejected')"><i class="fa-solid fa-xmark"></i> {{ __('messages.reject_verification') }}</button>
                `;
            } else if (store.kyc_status === 'rejected') {
                actions += `<button class="action-btn btn-approve" onclick="updateKyc(${store.id}, 'approved')">{{ __('messages.reverify') }}</button>`;
            }

            tbody.innerHTML += `
                <tr>
                    <td><strong>${store.name}</strong></td>
                    <td>${ownerName}</td>
                    <td>${created}</td>
                    <td><span class="status-badge ${statusClass}">${statusText}</span></td>
                    <td>${actions}</td>
                </tr>
            `;
        });
    }

    function renderVerifiedStores(searchTerm = '') {
        const tbody = document.querySelector('#verified-stores-table tbody');
        if(!tbody) return;
        tbody.innerHTML = '';
        let verifiedStores = allStores.filter(s => s.kyc_status === 'approved');

        if (searchTerm) {
            verifiedStores = verifiedStores.filter(s => s.name.toLowerCase().includes(searchTerm.toLowerCase()));
        }

        if (verifiedStores.length === 0) {
            tbody.innerHTML = `<tr><td colspan="5" style="text-align:center;">{{ __('messages.no_stores_currently') }}</td></tr>`;
            return;
        }

        verifiedStores.forEach(store => {
            const ownerName = store.user ? store.user.name : '{{ __('messages.unknown') }}';
            const created = new Date(store.created_at).toLocaleDateString(document.documentElement.lang === 'ar' ? 'ar-SA' : 'en-US');
            
            let accountStatus = store.status === 'active' ? '<span style="color:green;">{{ __('messages.active_account') }}</span>' : '<span style="color:red;">{{ __('messages.suspended') }}</span>';
            let verifyBadge = '<i class="fa-solid fa-circle-check" style="color:#25D366; margin-right:5px;" title="{{ __('messages.verified') }}"></i>';

            let toggleBtnStyle = store.status === 'active' ? 'background:#dc3545;' : 'background:#28a745;';
            let toggleBtnIcon = store.status === 'active' ? 'fa-ban' : 'fa-check';
            let toggleBtnText = store.status === 'active' ? '{{ __('messages.disable_account') }}' : '{{ __('messages.enable_account') }}';

            let actions = `
                <button class="action-btn" style="background:#17a2b8;" onclick="viewStoreProducts(${store.id}, '${store.name}')"><i class="fa-solid fa-box"></i> {{ __('messages.products') }}</button>
                <button class="action-btn" style="${toggleBtnStyle}" onclick="toggleStoreStatus(${store.id})"><i class="fa-solid ${toggleBtnIcon}"></i> ${toggleBtnText}</button>
                <br><button class="action-btn btn-reject" style="margin-top:5px;" onclick="updateKyc(${store.id}, 'rejected')">{{ __('messages.cancel_verification') }}</button>
            `;

            tbody.innerHTML += `
                <tr>
                    <td><strong>${store.name}</strong> ${verifyBadge}</td>
                    <td>${ownerName}</td>
                    <td>${created}</td>
                    <td>${accountStatus}</td>
                    <td>${actions}</td>
                </tr>
            `;
        });
    }

    document.addEventListener('DOMContentLoaded', () => {
        const searchInput = document.getElementById('store-search');
        if (searchInput) {
            searchInput.addEventListener('input', function(e) {
                renderVerifiedStores(e.target.value);
            });
        }
        const allSearchInput = document.getElementById('all-store-search');
        if (allSearchInput) {
            allSearchInput.addEventListener('input', function(e) {
                renderAllStoresData(e.target.value);
            });
        }
    });

    function renderAllStoresData(searchTerm = '') {
        const tbody = document.querySelector('#all-stores-table tbody');
        if(!tbody) return;
        tbody.innerHTML = '';
        let storesList = [...allStores];

        if (searchTerm) {
            const lowerSearchTerm = searchTerm.toLowerCase();
            storesList = storesList.filter(s => 
                (s.name && s.name.toLowerCase().includes(lowerSearchTerm)) || 
                (s.user && s.user.name && s.user.name.toLowerCase().includes(lowerSearchTerm)) ||
                (s.id && s.id.toString().includes(lowerSearchTerm)) ||
                (s.contact_info && s.contact_info.toLowerCase().includes(lowerSearchTerm)) ||
                (s.user && s.user.phone && s.user.phone.toLowerCase().includes(lowerSearchTerm))
            );
        }

        if (storesList.length === 0) {
            tbody.innerHTML = `<tr><td colspan="6" style="text-align:center;">لا يوجد متاجر مطابقة</td></tr>`;
            return;
        }

        storesList.forEach(store => {
            const ownerName = store.user ? store.user.name : '{{ __('messages.unknown') }}';
            const ownerEmail = store.user ? store.user.email : 'غير متوفر';
            const phone = store.contact_info || (store.user ? store.user.phone : 'غير متوفر');
            const phoneLink = phone !== 'غير متوفر' ? `<a href="tel:${phone}" dir="ltr" class="contact-link-active" style="text-decoration:none; color:var(--text-dark);"><i class="fa-solid fa-phone" style="color:#999; transition: color 0.3s;"></i> ${phone}</a>` : `<i class="fa-solid fa-phone" style="color:#999;"></i> ${phone}`;
            const emailLink = ownerEmail !== 'غير متوفر' ? `<a href="mailto:${ownerEmail}" class="contact-link-active" style="text-decoration:none; color:var(--primary);"><i class="fa-solid fa-envelope" style="color:#999; transition: color 0.3s;"></i> ${ownerEmail}</a>` : `<i class="fa-solid fa-envelope" style="color:#999;"></i> ${ownerEmail}`;
            const created = new Date(store.created_at).toLocaleDateString(document.documentElement.lang === 'ar' ? 'ar-SA' : 'en-US');
            
            let statusBadge = store.status === 'active' ? '<span class="status-badge status-approved">مفعل</span>' : '<span class="status-badge status-rejected">موقوف/غير مفعل</span>';
            let kycBadge = '';
            if(store.kyc_status === 'approved') kycBadge = '<span class="status-badge status-approved">موثق</span>';
            else if(store.kyc_status === 'rejected') kycBadge = '<span class="status-badge status-rejected">مرفوض</span>';
            else kycBadge = '<span class="status-badge status-pending">قيد المراجعة</span>';

            let kycImages = ``;
            if (store.identity_front) {
                kycImages += `<a href="${store.identity_front}" target="_blank" style="display:inline-block; margin:2px;"><img src="${store.identity_front}" style="width:40px; height:40px; border-radius:5px; object-fit:cover; border:1px solid #ddd;" title="الهوية (الأمام)"></a>`;
            }
            if (store.identity_back) {
                kycImages += `<a href="${store.identity_back}" target="_blank" style="display:inline-block; margin:2px;"><img src="${store.identity_back}" style="width:40px; height:40px; border-radius:5px; object-fit:cover; border:1px solid #ddd;" title="الهوية (الخلف)"></a>`;
            }
            if (store.commercial_register) {
                kycImages += `<a href="${store.commercial_register}" target="_blank" style="display:inline-block; margin:2px;"><i class="fa-solid fa-file-pdf fa-2x" style="color:#dc3545;" title="السجل التجاري"></i></a>`;
            }
            if (store.freelance_document) {
                kycImages += `<a href="${store.freelance_document}" target="_blank" style="display:inline-block; margin:2px;"><i class="fa-solid fa-file-pdf fa-2x" style="color:#007bff;" title="وثيقة العمل الحر"></i></a>`;
            }
            if (!kycImages) kycImages = '<span style="color:#999; font-size:12px;">لا توجد مرفقات</span>';

            let toggleBtnStyle = store.status === 'active' ? 'background:#dc3545;' : 'background:#28a745;';
            let toggleBtnIcon = store.status === 'active' ? 'fa-ban' : 'fa-check';
            let toggleBtnText = store.status === 'active' ? 'إيقاف' : 'تفعيل';

            let actions = `
                <button class="action-btn" style="background:#007bff; margin-bottom:5px;" onclick="viewKycModal(${store.id})"><i class="fa-solid fa-eye"></i> التفاصيل الكاملة</button><br>
                <button class="action-btn" style="${toggleBtnStyle}; margin-bottom:5px;" onclick="toggleStoreStatus(${store.id})"><i class="fa-solid ${toggleBtnIcon}"></i> ${toggleBtnText}</button><br>
                <button class="action-btn" style="background:#dc3545;" onclick="deleteStore(${store.id})"><i class="fa-solid fa-trash"></i> حذف المتجر نهائياً</button>
            `;

            tbody.innerHTML += `
                <tr>
                    <td>
                        <strong>${store.name}</strong><br>
                        <span style="font-size:11px; color:#666;">ID: ${store.id} | ${created}</span>
                    </td>
                    <td>
                        <div style="margin-bottom: 5px;"><i class="fa-solid fa-user" style="color:#999;"></i> ${ownerName}</div>
                        <div style="margin-bottom: 5px;">${emailLink}</div>
                        <div>${phoneLink}</div>
                    </td>
                    <td>
                        <span style="font-size:12px; background:#eee; padding:2px 5px; border-radius:3px;">${store.store_type || 'غير محدد'}</span><br>
                        <span style="font-size:12px; color:#555;">${store.store_activity || 'لا يوجد وصف نشاط'}</span>
                    </td>
                    <td>
                        ${statusBadge}<br>
                        <div style="margin-top:5px;">${kycBadge}</div>
                    </td>
                    <td>${kycImages}</td>
                    <td>${actions}</td>
                </tr>
            `;
        });
    }

    async function toggleStoreStatus(id) {
        if (!confirm('{{ __('messages.confirm_disable_store') }}')) return;
        try {
            const res = await fetch(`/api/admin/store/${id}/status`, {
                method: 'POST',
                headers
            });
            const data = await res.json();
            alert(data.message);
            fetchStores();
        } catch (e) {}
    }

    async function deleteStore(id) {
        if (!confirm('تحذير: هل أنت متأكد من حذف المتجر وتفاصيله نهائياً؟ هذا الإجراء لا يمكن التراجع عنه!')) return;
        try {
            const res = await fetch(`/api/admin/stores/${id}`, {
                method: 'DELETE',
                headers
            });
            const data = await res.json();
            if (res.ok) {
                alert(data.message);
                fetchStores();
                loadStats();
            } else {
                alert(data.error || 'حدث خطأ أثناء محاولة الحذف');
            }
        } catch (e) {
            console.error(e);
            alert('تعذر الاتصال بالخادم.');
        }
    }

    async function approveStoreCreation(id) {
        if (!confirm('{{ __('messages.confirm_approve_creation') }}')) return;
        try {
            const res = await fetch(`/api/admin/store/${id}/status`, {
                method: 'POST',
                headers
            });
            const data = await res.json();
            alert(data.message);
            fetchStores();
        } catch (e) {}
    }

    function viewKycModal(storeId) {
        const store = allStores.find(s => s.id === storeId);
        if (!store) return;
        
        const modal = document.createElement('div');
        modal.id = 'kyc-modal';
        modal.style = 'position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.6); z-index:9999; display:flex; align-items:center; justify-content:center; backdrop-filter: blur(4px);';
        
        let mapLink = store.latitude && store.longitude 
            ? `<a href="https://www.google.com/maps/search/?api=1&query=${store.latitude},${store.longitude}" target="_blank" style="color:var(--primary); text-decoration:underline;">${store.location_description || '{{ __('messages.location_map') }}'}</a>`
            : '{{ __('messages.unknown') }}';

        modal.innerHTML = `
            <div style="background:var(--white); width:600px; max-width:95%; max-height:80vh; overflow-y:auto; border-radius:15px; padding:35px; position:relative;">
                <span onclick="document.body.removeChild(this.parentNode.parentNode);" style="position:absolute; top:20px; left:20px; cursor:pointer; font-size:24px; color:#aaa;"><i class="fa-solid fa-xmark"></i></span>
                <h3 style="margin-top:0; color:var(--primary); margin-bottom:20px;">{{ __('messages.view_kyc_data') }} - ${store.name}</h3>
                
                <p><strong>{{ __('messages.owner_name') }}:</strong> ${store.user ? store.user.name : '{{ __('messages.unknown') }}'}</p>
                <p><strong>{{ __('messages.contact_whatsapp') }}:</strong> ${store.contact_info || (store.user ? store.user.phone : '{{ __('messages.unknown') }}')}</p>
                <p><strong>{{ __('messages.business_type') }}:</strong> ${store.store_type || '{{ __('messages.unknown') }}'}</p>
                <p><strong>{{ __('messages.store_activity') }}:</strong> ${store.store_activity || '{{ __('messages.unknown') }}'}</p>
                <p><strong>{{ __('messages.location_map') }}:</strong> ${mapLink}</p>
                
                <div style="display:flex; gap:15px; margin-top:20px;">
                    <div style="flex:1;">
                        <h4 style="margin-bottom:10px;">{{ __('messages.id_front') }}</h4>
                        ${store.identity_front ? `
                        <div style="position:relative; display:block; width:100%;">
                            <img src="${store.identity_front}" style="width:100%; border-radius:8px;">
                            <a href="${store.identity_front}" download="id_front_${store.id}" title="تحميل الصورة" style="position:absolute; bottom:10px; left:10px; background:rgba(0,0,0,0.6); color:#fff; width:40px; height:40px; border-radius:50%; display:flex; align-items:center; justify-content:center; text-decoration:none; font-size:18px; transition:0.3s; z-index: 10;" onmouseover="this.style.background='var(--primary)'" onmouseout="this.style.background='rgba(0,0,0,0.6)'"><i class="fa-solid fa-download"></i></a>
                        </div>
                        ` : '{{ __('messages.no_image_selected') }}'}
                    </div>
                    <div style="flex:1;">
                        <h4 style="margin-bottom:10px;">{{ __('messages.id_back') }}</h4>
                        ${store.identity_back ? `
                        <div style="position:relative; display:block; width:100%;">
                            <img src="${store.identity_back}" style="width:100%; border-radius:8px;">
                            <a href="${store.identity_back}" download="id_back_${store.id}" title="تحميل الصورة" style="position:absolute; bottom:10px; left:10px; background:rgba(0,0,0,0.6); color:#fff; width:40px; height:40px; border-radius:50%; display:flex; align-items:center; justify-content:center; text-decoration:none; font-size:18px; transition:0.3s; z-index: 10;" onmouseover="this.style.background='var(--primary)'" onmouseout="this.style.background='rgba(0,0,0,0.6)'"><i class="fa-solid fa-download"></i></a>
                        </div>
                        ` : '{{ __('messages.no_image_selected') }}'}
                    </div>
                </div>
            </div>
        `;
        document.body.appendChild(modal);
    }

    async function updateKyc(id, status) {
        if (!confirm('{{ __('messages.confirm_change_kyc') }}')) return;
        try {
            const res = await fetch(`/api/admin/stores/${id}/kyc`, {
                method: 'PUT',
                headers,
                body: JSON.stringify({ status })
            });
            if (res.ok) {
                alert('{{ __('messages.updated_successfully') }}');
                fetchStores();
                loadStats();
            } else {
                alert('{{ __('messages.update_error') }}');
            }
        } catch (e) {}
    }

    async function logoutUser() {
        if(confirm('{{ __('messages.confirm_logout') }}')) {
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
    }

    // Admin new functions
    async function toggleStoreStatus(id) {
        if(!confirm('{{ __('messages.confirm_toggle_status') }}')) return;
        try {
            const res = await fetch(`/api/admin/stores/${id}/toggle-status`, { method: 'PUT', headers });
            if(res.ok) fetchStores();
        } catch(e) {}
    }



    async function viewStoreProducts(id, storeName) {
        document.getElementById('admin-products-modal').style.display = 'flex';
        document.getElementById('modal-store-name').innerText = `{{ __('messages.store_products') }} ${storeName}`;
        const tbody = document.getElementById('modal-products-body');
        tbody.innerHTML = '<tr><td colspan="4" style="text-align:center;">{{ __('messages.loading_data') }}</td></tr>';
        
        try {
            const res = await fetch(`/api/admin/stores/${id}/products`, { headers });
            const data = await res.json();
            if(!data.data || data.data.length === 0) {
                tbody.innerHTML = '<tr><td colspan="4" style="text-align:center;">{{ __('messages.no_products_for_store') }}</td></tr>';
                return;
            }

            let html = '';
            data.data.forEach(p => {
                let statusBadge = p.status === 'active' ? `<span style="color:green">{{ __('messages.available_approved') }}</span>` : `<span style="color:orange">${p.status}</span>`;
                html += `
                    <tr>
                        <td>${p.name}</td>
                        <td>${p.price}</td>
                        <td>${statusBadge}</td>
                        <td>
                            ${p.status !== 'active' ? `<button class="action-btn" style="background:#25D366; margin-bottom:5px;" onclick="approveProduct(${p.id}, ${id}, '${storeName}')"><i class="fa-solid fa-check"></i> {{ __('messages.approve_product_publish') }}</button>` : ''}
                            <button class="action-btn btn-reject" onclick="deleteProduct(${p.id}, ${id}, '${storeName}')"><i class="fa-solid fa-trash"></i> {{ __('messages.delete') }}</button>
                        </td>
                    </tr>
                `;
            });
            tbody.innerHTML = html;
        } catch(e) {}
    }

    async function approveProduct(prodId, storeId, storeName) {
        if(!confirm('{{ __('messages.confirm_approve_publish') }}')) return;
        try {
            const res = await fetch(`/api/admin/products/${prodId}/approve`, { method: 'PUT', headers });
            if(res.ok) {
                alert('{{ __('messages.approved_success') }}');
                viewStoreProducts(storeId, storeName);
            }
        } catch(e) {}
    }

    async function deleteProduct(prodId, storeId, storeName) {
        if(!confirm('{{ __('messages.confirm_force_delete') }}')) return;
        try {
            const res = await fetch(`/api/admin/products/${prodId}`, { method: 'DELETE', headers });
            if(res.ok) {
                alert('{{ __('messages.deleted_success') }}');
                viewStoreProducts(storeId, storeName);
            }
        } catch(e) {}
    }

    document.getElementById('send-notification-form').addEventListener('submit', async function(e) {
        e.preventDefault();
        const statusEl = document.getElementById('notif-status');
        const btn = this.querySelector('button');
        
        btn.disabled = true;
        btn.innerText = '{{ __('messages.sending') }}';
        statusEl.style.display = 'none';

        const payload = {
            title: document.getElementById('notif-title').value,
            message: document.getElementById('notif-body').value,
            url: document.getElementById('notif-url').value,
            target_role: document.getElementById('notif-target').value
        };

        try {
            const res = await fetch('/api/admin/notifications/send', {
                method: 'POST',
                headers,
                body: JSON.stringify(payload)
            });
            const data = await res.json();

            if(res.ok) {
                statusEl.style.display = 'block';
                statusEl.style.color = 'green';
                statusEl.innerText = data.message;
                this.reset();
            } else {
                statusEl.style.display = 'block';
                statusEl.style.color = 'red';
                statusEl.innerText = data.message || '{{ __('messages.send_error') }}';
            }
        } catch(err) {
            statusEl.style.display = 'block';
            statusEl.style.color = 'red';
            statusEl.innerText = '{{ __('messages.connection_error') }}';
        } finally {
            btn.disabled = false;
            btn.innerHTML = '<i class="fa-solid fa-paper-plane"></i> {{ __('messages.send_notification_now') }}';
        }
    });

    // --- Featured Ads Logic --- //
    async function fetchFeaturedAds() {
        const tbody = document.querySelector('#featured-items-table tbody');
        tbody.innerHTML = '<tr><td colspan="5" style="text-align:center;">جاري التحميل...</td></tr>';
        try {
            const res = await fetch('/api/admin/featured', { headers });
            const data = await res.json();
            
            if(!data.data || data.data.length === 0) {
                tbody.innerHTML = '<tr><td colspan="5" style="text-align:center;">لاتوجد إعلانات مميزة قيد العرض حالياً</td></tr>';
                return;
            }
            
            let html = '';
            data.data.forEach(ad => {
                const typeIcon = ad.item_type === 'store' ? '<i class="fa-solid fa-store" style="color:var(--secondary)"></i> متجر' : '<i class="fa-solid fa-box" style="color:var(--primary)"></i> منتج';
                const statusBadge = ad.is_active 
                    ? '<span class="status-badge status-approved">نشط (يظهر الآن)</span>' 
                    : '<span class="status-badge status-rejected">منتهي</span>';

                html += `
                    <tr>
                        <td>${typeIcon}</td>
                        <td style="font-weight:bold;">${ad.name}</td>
                        <td dir="ltr" style="text-align:right;">${ad.end_at}</td>
                        <td>${statusBadge}</td>
                        <td>
                            <button class="action-btn btn-approve" onclick="openRenewFeaturedModal(${ad.id})" style="margin-bottom: 5px;"><i class="fa-solid fa-clock-rotate-left"></i> تجديد</button>
                            <button class="action-btn btn-reject" onclick="deleteFeatured(${ad.id})"><i class="fa-solid fa-trash"></i> إزالة</button>
                        </td>
                    </tr>
                `;
            });
            tbody.innerHTML = html;
        } catch(e) {
            tbody.innerHTML = '<tr><td colspan="5" style="text-align:center;color:red;">خطأ بالاتصال</td></tr>';
        }
    }

    function openAddFeaturedModal() {
        document.getElementById('add-featured-modal').style.display = 'flex';
        document.getElementById('featured-ad-form').reset();
        document.getElementById('feat-id').value = '';
        updateBrowseLink();
    }

    function updateBrowseLink() {
        const type = document.getElementById('feat-type').value;
        const btn = document.getElementById('feat-browse-btn');
        if(type === 'store') {
            btn.href = '/stores?select_mode=featured';
        } else {
            btn.href = '/products?select_mode=featured';
        }
    }

    document.getElementById('feat-type').addEventListener('change', () => {
        document.getElementById('feat-id').value = '';
        updateBrowseLink();
    });

    document.getElementById('featured-ad-form').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const featId = document.getElementById('feat-id').value;
        if(!featId) {
            alert('يرجى إدخال رقم المعرف (ID).');
            return;
        }
        
        const durationVal = parseInt(document.getElementById('feat-duration-val').value);
        const durationType = document.getElementById('feat-duration-type').value;
        const totalHours = durationType === 'days' ? (durationVal * 24) : durationVal;

        const payload = {
            item_type: document.getElementById('feat-type').value,
            item_id: featId,
            duration_hours: totalHours
        };
        
        const btn = this.querySelector('button[type="submit"]');
        btn.disabled = true;
        btn.innerText = 'جاري الإضافة...';
        
        try {
            const res = await fetch('/api/admin/featured', {
                method: 'POST',
                headers,
                body: JSON.stringify(payload)
            });
            const data = await res.json();
            if(res.ok) {
                document.getElementById('add-featured-modal').style.display = 'none';
                alert(data.message);
                fetchFeaturedAds();
            } else {
                alert(data.message || 'حدث خطأ أثناء الإضافة');
            }
        } catch(e) { 
            alert('خطأ في الاتصال بالشبكة'); 
        } finally {
            btn.disabled = false;
            btn.innerText = 'إعتماد الإعلان';
        }
    });

    async function deleteFeatured(id) {
        if(!confirm('هل أنت متأكد من إزالة هذا الإعلان؟')) return;
        try {
            const res = await fetch(`/api/admin/featured/${id}`, { method: 'DELETE', headers });
            const data = await res.json();
            if(res.ok) {
                fetchFeaturedAds();
            } else {
                alert(data.message);
            }
        } catch(e) {}
    }

    // Renew Featured Ad Logic
    function openRenewFeaturedModal(id) {
        document.getElementById('renew-feat-id').value = id;
        document.getElementById('renew-duration-val').value = '';
        document.getElementById('renew-featured-modal').style.display = 'flex';
    }

    document.getElementById('renew-featured-form').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const id = document.getElementById('renew-feat-id').value;
        const durationVal = parseInt(document.getElementById('renew-duration-val').value);
        const durationType = document.getElementById('renew-duration-type').value;
        const totalHours = durationType === 'days' ? (durationVal * 24) : durationVal;

        const btn = this.querySelector('button[type="submit"]');
        btn.disabled = true;
        btn.innerText = 'جاري التجديد...';
        
        try {
            const res = await fetch(`/api/admin/featured/${id}/renew`, {
                method: 'PUT',
                headers,
                body: JSON.stringify({ duration_hours: totalHours })
            });
            const data = await res.json();
            if(res.ok) {
                document.getElementById('renew-featured-modal').style.display = 'none';
                alert(data.message);
                fetchFeaturedAds();
            } else {
                alert(data.error || data.message || 'حدث خطأ أثناء التجديد');
            }
        } catch(e) { 
            alert('خطأ في الاتصال بالشبكة'); 
        } finally {
            btn.disabled = false;
            btn.innerText = 'تجديد الإعلان';
        }
    });
</script>
@endpush
