<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>
<style>
    :root {
        --sidebar-dark: #0f172a; /* Deep Charcoal */
        --sidebar-hover: #1e293b; 
        --accent-blue: #3b82f6;
        --text-muted: #94a3b8;
    }

    .sidebar-bg { 
        background: var(--sidebar-dark); 
        min-height: 100vh; 
        box-shadow: 4px 0 10px rgba(0,0,0,0.05);
        z-index: 1000;
    }
    
    .nav-link-admin {
        color: var(--text-muted);
        padding: 12px 18px;
        display: flex;
        align-items: center;
        text-decoration: none;
        font-weight: 600;
        font-size: 0.9rem;
        transition: all 0.3s ease;
        border-radius: 12px;
        margin: 4px 15px;
    }

    .nav-link-admin i {
        width: 20px;
        font-size: 1.1rem;
        transition: transform 0.3s ease;
    }

    .nav-link-admin:hover {
        color: #f8fafc !important;
        background: var(--sidebar-hover);
    }

    .nav-link-admin:hover i {
        transform: translateX(3px);
    }

    .nav-link-admin.active {
        color: white !important;
        background: var(--accent-blue);
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
    }

    .nav-link-admin.active i {
        color: white;
    }

    /* Mobile Toggle Bar */
    .mobile-header {
        background: var(--sidebar-dark);
        padding: 12px 20px;
        display: none;
        z-index: 1050;
    }

    .brand-logo {
        letter-spacing: -0.5px;
        background: linear-gradient(to right, #fff, #94a3b8);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    @media (max-width: 768px) {
        .mobile-header { display: flex; align-items: center; justify-content: space-between; }
        .sidebar-desktop { display: none; }
    }
</style>

<div class="mobile-header sticky-top d-md-none shadow-lg">
    <div class="d-flex align-items-center">
        <div class="bg-primary rounded-2 me-2" style="width: 30px; height: 30px; display: grid; place-items: center;">
            <i class="fa-solid fa-house-chimney text-white" style="font-size: 0.8rem;"></i>
        </div>
        <h5 class="text-white mb-0 fw-800 brand-logo">PropertyPlus</h5>
    </div>
    <button class="btn btn-dark border-secondary btn-sm" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileSidebar">
        <i class="fa-solid fa-bars-staggered"></i>
    </button>
</div>

<div class="col-md-2 d-none d-md-block sidebar-bg px-0 position-fixed">
    <div class="p-4 mb-3">
        <div class="d-flex align-items-center mb-1">
            <div class="bg-primary rounded-3 me-2 shadow-sm" style="width: 35px; height: 35px; display: grid; place-items: center;">
                <i class="fa-solid fa-house-chimney text-white"></i>
            </div>
            <h4 class="fw-800 text-white mb-0 brand-logo">PropertyPlus</h4>
        </div>
        <div class="ps-1">
            <span class="badge bg-secondary text-uppercase py-1 px-2" style="font-size: 0.55rem; letter-spacing: 1px; background-color: #334155 !important;">
                Admin Management
            </span>
        </div>
    </div>
    
    <div class="mt-2">
        <?php renderLinks($current_page); ?>
    </div>
</div>

<div class="offcanvas offcanvas-start sidebar-bg text-white border-0" tabindex="-1" id="mobileSidebar" style="width: 280px;">
    <div class="offcanvas-header border-bottom border-secondary mx-3 px-0">
        <h5 class="offcanvas-title fw-800 brand-logo">Control Panel</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body px-0 pt-4">
        <?php renderLinks($current_page); ?>
    </div>
</div>

<?php
function renderLinks($current) {
    $links = [
        'dashboard.php' => ['icon' => 'fa-chart-pie', 'label' => 'Overview'],
        'users.php' => ['icon' => 'fa-user-group', 'label' => 'User Management'],
        'properties.php' => ['icon' => 'fa-city', 'label' => 'Property Listings'],
        'leads.php' => ['icon' => 'fa-comment-dots', 'label' => 'Inquiry Leads'],
        'payment_verifications.php' => ['icon' => 'fa-shield', 'label' => 'Verifications'],
        'payments.php' => ['icon' => 'fa-receipt', 'label' => 'Payment History'],
    ];

    foreach ($links as $file => $data) {
        $active = ($current == $file) ? 'active' : '';
        echo "
        <a href='$file' class='nav-link-admin $active'>
            <i class='fa-solid {$data['icon']} me-3'></i> 
            <span>{$data['label']}</span>
        </a>";
    }

    echo "
    <div class='mt-4 pt-4 border-top border-secondary mx-4 opacity-25'></div>
    <a href='logout.php' class='nav-link-admin text-danger'>
        <i class='fa-solid fa-right-from-bracket me-3'></i> 
        <span>Sign Out</span>
    </a>";
}
?>