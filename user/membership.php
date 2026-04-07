<?php
require '../includes/auth_check.php';
require '../config/db.php';
include '../includes/navbar.php';

// Logic remains untouched
$stmt = $pdo->query("SELECT * FROM memberships");
$plans = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Membership Plans | Property Plus</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --slate-50: #f8fafc;
            --slate-200: #e2e8f0;
            --slate-900: #0f172a;
            --blue-600: #2563eb;
        }

        body { 
            background-color: var(--slate-50); 
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: var(--slate-900);
        }

        .header-section {
            padding: 60px 0;
            text-align: center;
        }

        .pricing-card {
            background: #ffffff;
            border: 1px solid var(--slate-200);
            border-radius: 24px;
            padding: 40px 30px;
            text-align: center;
            transition: all 0.3s ease;
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .pricing-card:hover {
            transform: translateY(-10px);
            border-color: var(--blue-600);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.05);
        }

        .plan-name {
            font-size: 1.25rem;
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 20px;
        }

        .plan-price {
            font-size: 3rem;
            font-weight: 800;
            color: var(--slate-900);
            margin-bottom: 20px;
        }

        .plan-price span {
            font-size: 1rem;
            color: #94a3b8;
            font-weight: 400;
        }

        .feature-list {
            list-style: none;
            padding: 0;
            margin: 0 0 30px 0;
            text-align: left;
            flex-grow: 1;
        }

        .feature-item {
            padding: 12px 0;
            border-bottom: 1px solid #f1f5f9;
            color: #475569;
            font-size: 0.95rem;
            display: flex;
            align-items: center;
        }

        .feature-item i {
            color: var(--blue-600);
            margin-right: 12px;
        }

        .btn-buy {
            background-color: var(--slate-900);
            color: white !important;
            border-radius: 12px;
            padding: 14px;
            font-weight: 700;
            text-decoration: none;
            transition: 0.2s;
        }

        .btn-buy:hover {
            background-color: var(--blue-600);
        }

        /* Highlight the most popular plan if needed */
        .featured-plan {
            border: 2px solid var(--blue-600);
            position: relative;
        }
        
        .popular-badge {
            position: absolute;
            top: -15px;
            left: 50%;
            transform: translateX(-50%);
            background: var(--blue-600);
            color: white;
            padding: 4px 15px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 700;
        }
    </style>
</head>
<body>

<div class="header-section">
    <div class="container">
        <h1 class="fw-bold mb-3">Choose Your Plan</h1>
        <p class="text-secondary mx-auto" style="max-width: 600px;">
            Unlock premium features and increase your property listing capacity with our tailored membership tiers.
        </p>
    </div>
</div>

<div class="container pb-5">
    <div class="row g-4 justify-content-center">
        <?php foreach ($plans as $plan): ?>
            <div class="col-lg-4 col-md-6">
                <div class="pricing-card shadow-sm">
                    <div class="plan-name"><?php echo htmlspecialchars($plan['name']); ?></div>
                    
                    <div class="plan-price">
                        ₹<?php echo number_format($plan['price']); ?><span>/year</span>
                    </div>

                    <div class="feature-list">
                        <div class="feature-item">
                            <i class="fa-solid fa-circle-check"></i>
                            Up to <b><?php echo $plan['property_limit']; ?></b> Property Listings
                        </div>
                        <div class="feature-item">
                            <i class="fa-solid fa-circle-check"></i>
                            Verified Partner Badge
                        </div>
                        <div class="feature-item">
                            <i class="fa-solid fa-circle-check"></i>
                            Direct Lead Management
                        </div>
                        <div class="feature-item">
                            <i class="fa-solid fa-circle-check"></i>
                            24/7 Priority Support
                        </div>
                    </div>

                    <a href='buy_plan.php?id=<?php echo $plan['id']; ?>' class="btn-buy">
                        Get Started
                    </a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>