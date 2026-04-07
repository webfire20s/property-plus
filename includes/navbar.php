<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
    :root {
        --nav-bg: #ffffff;
        --slate-900: #0f172a;
        --blue-600: #2563eb;
    }

    .navbar {
        background: var(--nav-bg);
        border-bottom: 1px solid #e2e8f0;
        padding: 15px 0;
        transition: all 0.3s ease;
    }

    .navbar-brand {
        font-weight: 800;
        color: var(--slate-900) !important;
        letter-spacing: -0.5px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .nav-link {
        color: #64748b !important;
        font-weight: 600;
        font-size: 0.95rem;
        padding: 8px 16px !important;
        transition: 0.2s;
    }

    .nav-link:hover {
        color: var(--slate-900) !important;
    }

    .nav-link.active-link {
        color: var(--blue-600) !important;
    }

    .btn-nav-action {
        background: var(--slate-900);
        color: white !important;
        border-radius: 10px;
        font-weight: 600;
        padding: 8px 20px !important;
        margin-left: 10px;
    }

    .btn-nav-outline {
        border: 1.5px solid var(--slate-900);
        color: var(--slate-900) !important;
        border-radius: 10px;
        font-weight: 600;
        padding: 8px 20px !important;
    }

    .logout-link {
        color: #ef4444 !important;
    }

    /* Mobile adjustments */
    @media (max-width: 991px) {
        .navbar-collapse {
            background: #fff;
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0 10px 15px rgba(0,0,0,0.1);
            margin-top: 15px;
        }
        .btn-nav-action, .btn-nav-outline {
            margin-left: 0;
            margin-top: 10px;
            text-align: center;
        }
    }
</style>

<nav class="navbar navbar-expand-lg sticky-top">
    <div class="container">
        <a class="navbar-brand" href="/realestate/index.php">
            <div style="background: var(--slate-900); color:white; width:32px; height:32px; border-radius:8px; display:flex; align-items:center; justify-content:center;">
                <i class="fa-solid fa-house-chimney" style="font-size: 0.9rem;"></i>
            </div>
            PropertyPlus
        </a>

        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
            <i class="fa-solid fa-bars-staggered"></i>
        </button>

        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav ms-auto align-items-center">
                <li class="nav-item">
                    <a class="nav-link" href="../index.php"><i class="fa-solid fa-magnifying-glass me-1 small"></i> Browse</a>
                </li>

                <?php if(isset($_SESSION['user_id'])): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="../user/dashboard.php">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../user/my_properties.php">My Properties</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../user/requests.php">Requests</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../user/membership.php">Membership</a>
                    </li>
                    <li class="nav-item ms-lg-3">
                        <a class="nav-link btn-nav-action" href="../user/add_property.php">
                            <i class="fa-solid fa-plus me-1"></i> Add Property
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link logout-link ms-lg-2" href="../auth/logout.php">
                            <i class="fa-solid fa-arrow-right-from-bracket"></i>
                        </a>
                    </li>

                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link btn-nav-outline" href="../auth/login.php">Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link btn-nav-action" href="../auth/register.php">Register</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>