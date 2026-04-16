<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Automatic path detection for XAMPP vs cPanel
$protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http");
$host = $_SERVER['HTTP_HOST'];
if ($host == 'localhost') {
    $base_url = $protocol . "://" . $host . "/realestate/";
} else {
    $base_url = $protocol . "://" . $host . "/";
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>PropertyPlus | Elite Real Estate</title>
  
  <link href="<?php echo $base_url; ?>assets/logo.png" rel="icon">
  <link href="<?php echo $base_url; ?>assets/img/apple-touch-icon.png" rel="apple-touch-icon">

  <link href="https://fonts.googleapis.com" rel="preconnect">
  <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&family=Poppins:wght@300;400;500;600;700&family=Raleway:wght@300;400;500;600;700&display=swap" rel="stylesheet">

  <link href="<?php echo $base_url; ?>assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="<?php echo $base_url; ?>assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="<?php echo $base_url; ?>assets/vendor/aos/aos.css" rel="stylesheet">
  <link href="<?php echo $base_url; ?>assets/vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
  <link href="<?php echo $base_url; ?>assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">

  <link href="<?php echo $base_url; ?>assets/css/main.css" rel="stylesheet">

  <style>
    /* Custom style to integrate your Logo into the template's header */
    .header .logo img { max-height: 40px; margin-right: 10px; }
    .navmenu .btn-get-started {
        background: var(--accent-color, #2eca6a);
        padding: 8px 20px;
        margin-left: 15px;
        border-radius: 4px;
        color: #fff !important;
    }
    .logout-icon { color: #ef4444 !important; font-size: 1.2rem; }
  </style>
</head>

<body class="index-page">

  <header id="header" class="header d-flex align-items-center fixed-top">
    <div class="container-fluid container-xl position-relative d-flex align-items-center justify-content-between">

      <a href="<?php echo $base_url; ?>index.php" class="logo d-flex align-items-center">
        <img src="<?php echo $base_url; ?>assets/logo.png" alt="PropertyPlus">
        <h1 class="sitename">Property<span>Plus</span></h1>
      </a>

      <nav id="navmenu" class="navmenu">
        <ul>
          <li><a href="<?php echo $base_url; ?>index.php">Browse</a></li>
          
          <?php if(isset($_SESSION['user_id'])): ?>
            <li><a href="<?php echo $base_url; ?>user/dashboard.php">Dashboard</a></li>
            <li><a href="<?php echo $base_url; ?>user/my_properties.php">My Properties</a></li>
            <li><a href="<?php echo $base_url; ?>user/my_requests.php">My Requests</a></li>
            <li><a href="<?php echo $base_url; ?>user/requests.php">Requests</a></li>
            <li><a href="<?php echo $base_url; ?>user/membership.php">Membership</a></li>
            <li><a href="<?php echo $base_url; ?>user/add_property.php" class="btn-get-started">Add Property</a></li>
            <li>
                <a href="<?php echo $base_url; ?>auth/logout.php" class="logout-icon">
                    <i class="bi bi-box-arrow-right"></i>
                </a>
            </li>

          <?php else: ?>
            <li><a href="<?php echo $base_url; ?>auth/login.php">Login</a></li>
            <li><a href="<?php echo $base_url; ?>auth/register.php" class="btn-get-started">Register</a></li>
          <?php endif; ?>
        </ul>
        <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
      </nav>

    </div>
  </header>

  <main class="main">