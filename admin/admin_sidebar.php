<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>
<style>
    :root {
        --sidebar-dark: #0f172a;
        --accent-blue: #2563eb;
    }
    .sidebar-bg { background: var(--sidebar-dark); min-height: 100vh; }
    
    .nav-link-admin {
        color: #94a3b8;
        padding: 12px 20px;
        display: block;
        text-decoration: none;
        font-weight: 600;
        transition: 0.2s;
        border-radius: 8px;
        margin: 4px 15px;
    }
    .nav-link-admin:hover, .nav-link-admin.active {
        color: white !important;
        background: rgba(255,255,255,0.1);
    }
    .nav-link-admin.active { border-left: 4px solid var(--accent-blue); }

    /* Mobile Toggle Bar */
    .mobile-header {
        background: var(--sidebar-dark);
        padding: 15px;
        display: none;
    }

    @media (max-width: 768px) {
        .mobile-header { display: flex; align-items: center; justify-content: space-between; }
        .sidebar-desktop { display: none; }
    }
</style>

<div class="mobile-header sticky-top d-md-none shadow">
    <h5 class="text-white mb-0 fw-bold">PropertyPlus</h5>
    <button class="btn btn-outline-light btn-sm" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileSidebar">
        <i class="fa-solid fa-bars"></i>
    </button>
</div>

<div class="col-md-2 d-none d-md-block sidebar-bg px-0 position-fixed">
    <div class="p-4">
        <h5 class="fw-bold text-white mb-0">PropertyPlus</h5>
        <small class="text-muted text-uppercase" style="font-size: 0.6rem;">Admin Console</small>
    </div>
    <?php renderLinks($current_page); ?>
</div>

<div class="offcanvas offcanvas-start sidebar-bg text-white" tabindex="-1" id="mobileSidebar" style="width: 280px;">
    <div class="offcanvas-header border-bottom border-secondary">
        <h5 class="offcanvas-title fw-bold">Menu</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body px-0">
        <?php renderLinks($current_page); ?>
    </div>
</div>

<?php
function renderLinks($current) {
    $links = [
        'dashboard.php' => ['icon' => 'fa-chart-line', 'label' => 'Dashboard'],
        'users.php' => ['icon' => 'fa-users', 'label' => 'Users'],
        'properties.php' => ['icon' => 'fa-building', 'label' => 'Properties'],
        'leads.php' => ['icon' => 'fa-envelope-open-text', 'label' => 'Leads'],
        'payments.php' => ['icon' => 'fa-indian-rupee-sign', 'label' => 'Payments'],
    ];

    foreach ($links as $file => $data) {
        $active = ($current == $file) ? 'active' : '';
        echo "<a href='$file' class='nav-link-admin $active'><i class='fa-solid {$data['icon']} me-2'></i> {$data['label']}</a>";
    }
    echo "<hr class='mx-4 opacity-10'><a href='logout.php' class='nav-link-admin text-danger'><i class='fa-solid fa-power-off me-2'></i> Logout</a>";
}
?>