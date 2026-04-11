<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Automatically define the root path
$base_url = "https://" . $_SERVER['HTTP_HOST'] . "/realestate/";
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
        padding: 12px 0;
        transition: all 0.3s ease;
    }

    .navbar-brand {
        display: flex;
        align-items: center;
        gap: 12px;
        text-decoration: none;
    }

    .nav-logo-img {
        height: 45px; /* Adjusted to make the 3D logo details visible */
        width: auto;
        object-fit: contain;
    }

    .brand-text {
        font-weight: 800;
        color: var(--slate-900);
        letter-spacing: -0.5px;
        font-size: 1.25rem;
        text-transform: uppercase;
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
        transition: 0.3s ease;
    }

    .btn-nav-action:hover {
        background: var(--blue-600);
        transform: translateY(-1px);
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
            display: block;
        }
        .nav-logo-img {
            height: 38px;
        }
    }
</style>

<nav class="navbar navbar-expand-lg sticky-top">    
    <div class="container">
        <a class="navbar-brand" href="<?php echo $base_url; ?>index.php">
            <img src="<?php echo $base_url; ?>assets/logo.png" alt="PropertyPlus Logo" class="nav-logo-img">
            <span class="brand-text">PropertyPlus</span>
        </a>

        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
            <i class="fa-solid fa-bars-staggered"></i>
        </button>

        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav ms-auto align-items-center">
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo $base_url; ?>index.php">
                        <i class="fa-solid fa-magnifying-glass me-1 small"></i> Browse
                    </a>
                </li>

                <?php if(isset($_SESSION['user_id'])): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo $base_url; ?>user/dashboard.php">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo $base_url; ?>user/my_properties.php">My Properties</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo $base_url; ?>user/requests.php">Requests</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo $base_url; ?>user/membership.php">Membership</a>
                    </li>
                    <li class="nav-item ms-lg-3">
                        <a class="nav-link btn-nav-action" href="<?php echo $base_url; ?>user/add_property.php">
                            <i class="fa-solid fa-plus me-1"></i> Add Property
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link logout-link ms-lg-2" href="<?php echo $base_url; ?>auth/logout.php">
                            <i class="fa-solid fa-arrow-right-from-bracket"></i>
                        </a>
                    </li>

                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link btn-nav-outline" href="<?php echo $base_url; ?>auth/login.php">Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link btn-nav-action" href="<?php echo $base_url; ?>auth/register.php">Register</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>