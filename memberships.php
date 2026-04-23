<?php
require 'config/db.php';
include 'includes/navbar.php';

// Fetch plans (Logic strictly preserved)
$stmt = $pdo->query("SELECT * FROM memberships ORDER BY price ASC");
$plans = $stmt->fetchAll();
?>

<style>
    body {
        background: #f7f7f7;
        font-family: 'Poppins', sans-serif;
    }

    .pricing-container {
        padding: 120px 0 80px;
    }

    .intro-header {
        border-left: 5px solid #2eca6a;
        padding-left: 20px;
        margin-bottom: 50px;
    }

    .intro-header h2 {
        font-weight: 700;
        font-size: 2.5rem;
        color: #000;
        margin: 0;
    }

    .card-pricing {
        background: #fff;
        border: 1px solid #ebebeb;
        border-radius: 12px;
        padding: 45px 30px;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .card-pricing:hover {
        transform: translateY(-10px);
        border-color: #2eca6a;
        box-shadow: 0 15px 35px rgba(0,0,0,0.08);
    }

    .plan-name {
        font-weight: 700;
        letter-spacing: 2px;
        color: #000;
        font-size: 1.1rem;
        margin-bottom: 20px;
    }

    .plan-price {
        font-weight: 700;
        font-size: 3rem;
        color: #2eca6a;
        margin-bottom: 30px;
    }

    .plan-price span {
        font-size: 1rem;
        color: #888;
        font-weight: 400;
    }

    .feature-list {
        margin-bottom: 35px;
    }

    .feature-list li {
        padding: 10px 0;
        border-bottom: 1px solid #f2f2f2;
        font-size: 0.95rem;
        color: #555;
    }

    .feature-list li:last-child {
        border-bottom: none;
    }

    .btn-plan {
        font-weight: 700;
        text-transform: uppercase;
        padding: 14px 30px;
        letter-spacing: 1px;
        border-radius: 8px;
        transition: 0.3s;
        width: 100%;
        font-size: 0.85rem;
    }

    .btn-success-theme {
        background: #2eca6a;
        color: #fff;
        border: none;
    }

    .btn-success-theme:hover {
        background: #000;
        color: #fff;
    }

    .btn-dark-theme {
        background: #000;
        color: #fff;
        border: none;
    }

    .btn-dark-theme:hover {
        background: #2eca6a;
    }

    /* Highlight popular plan */
    .popular-badge {
        position: absolute;
        top: 20px;
        right: -35px;
        background: #2eca6a;
        color: #fff;
        padding: 5px 40px;
        transform: rotate(45deg);
        font-size: 0.7rem;
        font-weight: 700;
        text-transform: uppercase;
    }
</style>

<div class="container pricing-container">
    <div class="intro-header">
        <h2>Membership Plans</h2>
        <p class="text-muted">Choose the right plan to grow your real estate network</p>
    </div>

    <div class="row g-4">
        <?php foreach ($plans as $plan): ?>

            <?php
            $name = strtolower($plan['name']);

            // ✅ Features mapping (Untouched Logic)
            if ($name == 'listing') {
                $features = ["Browse properties", "Add property (text only)", "No images/videos", "Limited visibility", "Docs verification included"];
            } elseif ($name == 'basic') {
                $features = ["Add up to 5 properties", "Contact users", "Medium visibility", "Docs verification"];
            } elseif ($name == 'silver') {
                $features = ["Add up to 10 properties", "Contact users", "Better visibility", "Docs verification"];
            } elseif ($name == 'gold') {
                $features = ["Add up to 20 properties", "High visibility", "Priority exposure", "Docs verification"];
            } else { // platinum
                $features = ["Unlimited properties", "Video upload", "Premium visibility", "Docs verification"];
            }
            ?>

            <div class="col-lg-4 col-md-6" data-aos="fade-up">
                <div class="card-pricing text-center h-100">
                    
                    <?php if($name == 'gold'): ?>
                        <div class="popular-badge">Popular</div>
                    <?php endif; ?>

                    <h5 class="plan-name text-uppercase"><?= htmlspecialchars($plan['name']) ?></h5>

                    <h2 class="plan-price">
                        ₹<?= number_format($plan['price']) ?>
                        <span>/year</span>
                    </h2>

                    <ul class="list-unstyled feature-list text-start">
                        <?php foreach ($features as $f): ?>
                            <li>
                                <i class="bi bi-check2-circle text-success me-2"></i>
                                <?= $f ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>

                    <div class="mt-auto">
                        <?php if (!isset($_SESSION['user_id'])): ?>
                            <a href="auth/register.php" class="btn btn-plan btn-dark-theme">
                                Register to Continue
                            </a>
                        <?php else: ?>
                            <?php if ($plan['price'] == 0): ?>
                                <a href="user/dashboard.php" class="btn btn-plan btn-success-theme opacity-75 pe-none">
                                    Current Free Plan
                                </a>
                            <?php else: ?>
                                <a href="user/buy_plan.php?id=<?= $plan['id'] ?>" class="btn btn-plan btn-success-theme">
                                    Select Plan
                                </a>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>

                </div>
            </div>

        <?php endforeach; ?>
    </div>
</div>

<?php include('includes/footer.php'); ?>