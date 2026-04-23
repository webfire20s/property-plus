<?php
require '../includes/auth_check.php';
require '../config/db.php';
include '../includes/navbar.php';

// Logic remains untouched
$stmt = $pdo->query("SELECT * FROM memberships");
$plans = $stmt->fetchAll();
?>



<style>
    .header-section {
        padding: 100px 0 60px;
        text-align: center;
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
        margin-bottom: 60px;
    }

    .pricing-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 25px;
        padding: 50px 35px;
        text-align: center;
        transition: all 0.4s ease;
        height: 100%;
        display: flex;
        flex-direction: column;
        position: relative;
    }

    .pricing-card:hover {
        transform: translateY(-10px);
        border-color: #2eca6a;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.08);
    }

    /* Featured/Most Popular Plan Styling */
    .featured-plan {
        background: #1e1e1e; /* Dark theme contrast */
        color: white;
        border: none;
        transform: scale(1.05);
        z-index: 2;
    }
    
    .featured-plan:hover {
        transform: scale(1.05) translateY(-5px);
    }

    .featured-plan .plan-name { color: #2eca6a; }
    .featured-plan .plan-price { color: white; }
    .featured-plan .feature-item { border-color: rgba(255,255,255,0.1); }
    .featured-plan .btn-buy { background: #2eca6a; border: none; }

    .plan-name {
        font-size: 0.85rem;
        font-weight: 800;
        color: #2eca6a;
        text-transform: uppercase;
        letter-spacing: 2px;
        margin-bottom: 15px;
        display: block;
    }

    .plan-price {
        font-size: 3rem;
        font-weight: 700;
        color: #000;
        margin-bottom: 30px;
    }

    .plan-price span {
        font-size: 1rem;
        color: #94a3b8;
    }

    .feature-list {
        list-style: none;
        padding: 0;
        margin: 0 0 40px 0;
        text-align: left;
        flex-grow: 1;
    }

    .feature-item {
        padding: 12px 0;
        border-bottom: 1px solid #f1f5f9;
        font-size: 0.95rem;
        display: flex;
        align-items: center;
    }

    .feature-item i {
        color: #2eca6a;
        margin-right: 12px;
    }

    .btn-buy {
        background-color: #000;
        color: #fff !important;
        border-radius: 10px;
        padding: 15px;
        font-weight: 700;
        text-decoration: none;
        transition: 0.3s;
        text-transform: uppercase;
        font-size: 0.85rem;
    }

    .popular-tag {
        position: absolute;
        top: -15px;
        left: 50%;
        transform: translateX(-50%);
        background: #2eca6a;
        color: #fff;
        padding: 5px 20px;
        border-radius: 20px;
        font-size: 0.7rem;
        font-weight: 800;
        text-transform: uppercase;
        box-shadow: 0 4px 10px rgba(46, 202, 106, 0.3);
    }
</style>

<div class="header-section" data-aos="fade-down">
    <div class="container">
        <span class="badge px-3 py-2 rounded-pill fw-bold mb-3" style="background: rgba(46, 202, 106, 0.1); color: #2eca6a;">MEMBERSHIP</span>
        <h1 class="fw-bold mb-3" style="font-size: 2.5rem; color: #000;">Elevate Your Presence</h1>
        <p class="text-secondary mx-auto" style="max-width: 600px;">
            Choose a plan that fits your goals. From individual sellers to professional agencies, we have you covered.
        </p>
    </div>
</div>

<div class="container pb-5">
    <div class="row g-5 justify-content-center align-items-center">
         
        <?php 
        $count = 0;
        $features_map = [
            'listing' => [
                'Browse properties',
                'Add property (text only)',
                'No images allowed',
                'No videos',
                'Limited visibility',
                'Document verification'
            ],
            'basic' => [
                'Browse properties',
                'Add property',
                'Upload up to 5 properties',
                'Contact users',
                'Medium visibility',
                'Document verification'
            ],
            'silver' => [
                'Browse properties',
                'Add property',
                'Upload up to 10 properties',
                'Contact users',
                'Medium+ visibility',
                'Document verification'
            ],
            'gold' => [
                'Browse properties',
                'Add property',
                'Upload up to 20 properties',
                'Contact users',
                'High visibility',
                'Document verification'
            ],
            'platinum' => [
                'Browse properties',
                'Add property',
                'Unlimited properties',
                'Video upload enabled',
                'Premium visibility',
                'Document verification'
            ]
        ];
        foreach ($plans as $plan): 
            $count++;
            $is_featured = ($count == 2); 
        ?>
            <div class="col-lg-4 col-md-6" data-aos="zoom-in" data-aos-delay="<?php echo $count * 100; ?>">
                <div class="pricing-card shadow-sm <?php echo $is_featured ? 'featured-plan' : ''; ?>">
                    <?php
                    $stmt = $pdo->prepare("
                        SELECT m.name 
                        FROM user_memberships um
                        JOIN memberships m ON um.membership_id = m.id
                        WHERE um.user_id=? AND um.status='active'
                        ORDER BY um.id DESC LIMIT 1
                    ");
                    $stmt->execute([$_SESSION['user_id']]);
                    $current = $stmt->fetch();

                    $current_plan = $current['name'] ?? 'Listing';
                    ?>
                    <?php if(strtolower($plan['name']) == 'platinum'): ?>
                        <span class="badge bg-dark text-warning mb-2">🔥 Most Premium</span>
                    <?php elseif(strtolower($plan['name']) == 'gold'): ?>
                        <span class="badge bg-warning text-dark mb-2">⭐ Most Popular</span>
                    <?php elseif(strtolower($plan['name']) == 'listing'): ?>
                        <span class="badge bg-secondary mb-2">Free Plan</span>
                    <?php endif; ?>
                        <?php if ($current_plan == $plan['name']): ?>
                            <div class="badge bg-success mb-3">Your Current Plan</div>
                        <?php endif; ?>

                        <span class="plan-name"><?php echo htmlspecialchars($plan['name']); ?></span>
                        
                        <div class="plan-price">
                            ₹<?php echo number_format($plan['price']); ?><span>/year</span>
                        </div>
                        
                    <div class="feature-list">
                        <?php
                        $plan_key = strtolower($plan['name']);
                        $plan_features = $features_map[$plan_key] ?? [];
                        ?>

                        <ul class="list-unstyled small mb-4">
                            <?php foreach ($plan_features as $f): ?>
                                <li class="mb-2">
                                    <i class="fa-solid fa-check text-success me-2"></i>
                                    <?= $f ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>

                    <?php if ($plan['price'] == 0): ?>
                        
                        <a href="dashboard.php" class="btn-buy text-center" style="background:#2eca6a;">
                            Current Free Plan
                        </a>

                    <?php else: ?>

                        <a href='buy_plan.php?id=<?php echo $plan['id']; ?>' class="btn-buy text-center">
                            Select Plan <i class="bi bi-chevron-right ms-2"></i>
                        </a>

                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php 
// 2. Include the footer (Handles AOS and JS scripts)
include('../includes/footer.php'); 
?>
