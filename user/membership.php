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
    <title>Membership Plans | PropertyPlus</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --brand-gold: #fbbf24;
            --brand-blue: #2563eb;
            --slate-50: #f8fafc;
            --slate-100: #f1f5f9;
            --slate-200: #e2e8f0;
            --slate-900: #0f172a;
        }

        body { 
            background-color: var(--slate-50); 
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: var(--slate-900);
        }

        .header-section {
            padding: 80px 0 60px;
            text-align: center;
            background: white;
            border-bottom: 1px solid var(--slate-200);
            margin-bottom: 60px;
        }

        .pricing-card {
            background: #ffffff;
            border: 1px solid var(--slate-200);
            border-radius: 32px;
            padding: 50px 35px;
            text-align: center;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            height: 100%;
            display: flex;
            flex-direction: column;
            position: relative;
        }

        .pricing-card:hover {
            transform: translateY(-15px);
            border-color: var(--brand-gold);
            box-shadow: 0 30px 60px -12px rgba(15, 23, 42, 0.12);
        }

        /* Styling for the middle/featured plan */
        .featured-plan {
            background: var(--slate-900);
            color: white;
            border: none;
        }
        
        .featured-plan .plan-name { color: var(--brand-gold); }
        .featured-plan .plan-price { color: white; }
        .featured-plan .feature-item { color: rgba(255,255,255,0.8); border-color: rgba(255,255,255,0.1); }
        .featured-plan .btn-buy { background: var(--brand-gold); color: var(--slate-900) !important; }

        .plan-name {
            font-size: 0.85rem;
            font-weight: 800;
            color: var(--brand-blue);
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 15px;
            display: block;
        }

        .plan-price {
            font-size: 3.5rem;
            font-weight: 800;
            color: var(--slate-900);
            margin-bottom: 30px;
            letter-spacing: -2px;
        }

        .plan-price span {
            font-size: 1rem;
            color: #94a3b8;
            font-weight: 600;
            letter-spacing: 0;
        }

        .feature-list {
            list-style: none;
            padding: 0;
            margin: 0 0 40px 0;
            text-align: left;
            flex-grow: 1;
        }

        .feature-item {
            padding: 15px 0;
            border-bottom: 1px solid #f1f5f9;
            font-size: 1rem;
            display: flex;
            align-items: center;
            font-weight: 500;
        }

        .feature-item i {
            color: var(--brand-gold);
            margin-right: 15px;
            font-size: 1.1rem;
        }

        .btn-buy {
            background-color: var(--slate-900);
            color: white !important;
            border-radius: 18px;
            padding: 18px;
            font-weight: 800;
            text-decoration: none;
            transition: 0.3s;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-size: 0.9rem;
        }

        .btn-buy:hover {
            transform: scale(1.02);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }

        .popular-tag {
            position: absolute;
            top: 25px;
            right: 25px;
            background: var(--brand-gold);
            color: var(--slate-900);
            padding: 5px 15px;
            border-radius: 10px;
            font-size: 0.7rem;
            font-weight: 800;
            text-transform: uppercase;
        }
    </style>
</head>
<body>

<div class="header-section">
    <div class="container">
        <span class="badge bg-primary-subtle text-primary px-3 py-2 rounded-pill fw-bold mb-3">PRICING PLANS</span>
        <h1 class="fw-extrabold mb-3" style="font-weight: 800; font-size: 3rem; letter-spacing: -1.5px;">Elevate Your Presence</h1>
        <p class="text-secondary mx-auto fw-medium" style="max-width: 600px; font-size: 1.1rem;">
            Choose a plan that fits your goals. From individual sellers to professional agencies, we have you covered.
        </p>
    </div>
</div>

<div class="container pb-5">
    <div class="row g-4 justify-content-center">
        <?php 
        $count = 0;
        foreach ($plans as $plan): 
            $count++;
            // Make the 2nd plan "Featured" automatically
            $is_featured = ($count == 2); 
        ?>
            <div class="col-lg-4 col-md-6">
                <div class="pricing-card shadow-sm <?php echo $is_featured ? 'featured-plan' : ''; ?>">
                    <?php if($is_featured): ?>
                        <div class="popular-tag">Most Popular</div>
                    <?php endif; ?>

                    <span class="plan-name"><?php echo htmlspecialchars($plan['name']); ?></span>
                    
                    <div class="plan-price">
                        ₹<?php echo number_format($plan['price']); ?><span>/year</span>
                    </div>

                    <div class="feature-list">
                        <div class="feature-item">
                            <i class="fa-solid fa-square-check"></i>
                            <span><b><?php echo $plan['property_limit']; ?></b> Property Listings</span>
                        </div>
                        <div class="feature-item">
                            <i class="fa-solid fa-square-check"></i>
                            <span>Verified Partner Badge</span>
                        </div>
                        <div class="feature-item">
                            <i class="fa-solid fa-square-check"></i>
                            <span>Lead Access Dashboard</span>
                        </div>
                        <div class="feature-item">
                            <i class="fa-solid fa-square-check"></i>
                            <span>Direct WhatsApp Integration</span>
                        </div>
                        <div class="feature-item">
                            <i class="fa-solid fa-square-check"></i>
                            <span>24/7 Priority Support</span>
                        </div>
                    </div>

                    <a href='buy_plan.php?id=<?php echo $plan['id']; ?>' class="btn-buy text-center">
                        Select This Plan
                    </a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>