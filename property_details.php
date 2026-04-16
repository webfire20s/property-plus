<?php
require 'config/db.php';
include 'includes/navbar.php';

$id = $_GET['id'] ?? null;

$stmt = $pdo->prepare("SELECT * FROM properties WHERE id=? AND status='approved'");
$stmt->execute([$id]);
$property = $stmt->fetch();

if (!$property) {
    die("Property not found");
}

// Get images
$imgStmt = $pdo->prepare("SELECT image_path FROM property_images WHERE property_id=?");
$imgStmt->execute([$id]);
$images = $imgStmt->fetchAll(PDO::FETCH_COLUMN);

// Get documents
$docStmt = $pdo->prepare("SELECT * FROM property_documents WHERE property_id=?");
$docStmt->execute([$id]);
$docs = $docStmt->fetchAll();
?>

<style>
    body {
        background: #f7f7f7;
        font-family: 'Poppins', sans-serif;
    }

    .property-header {
        margin-top: 120px;
        margin-bottom: 40px;
    }

    .title-single {
        font-weight: 700;
        font-size: 2.5rem;
        color: #000;
        border-left: 5px solid #2eca6a;
        padding-left: 20px;
    }

    .price-box {
        background-color: #2eca6a;
        padding: .6rem 1.5rem;
        color: #fff;
        border-radius: 50px;
        display: inline-block;
        font-weight: 700;
        font-size: 1.2rem;
    }

    /* Carousel Styling */
    .carousel-item img {
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    }

    .property-card {
        background: #fff;
        border: 1px solid #ebebeb;
        padding: 30px;
        border-radius: 12px;
        margin-bottom: 30px;
    }

    .summary-list {
        padding: 0;
        list-style: none;
    }

    .summary-list li {
        padding: 12px 0;
        border-bottom: 1px solid #f2f2f2;
        display: flex;
        justify-content: space-between;
    }

    .summary-list li strong {
        color: #000;
        font-weight: 600;
    }

    .summary-list li span {
        color: #555;
    }

    .section-title {
        font-weight: 700;
        color: #000;
        margin-bottom: 20px;
        position: relative;
    }

    .section-title::after {
        content: '';
        position: absolute;
        width: 50px;
        height: 3px;
        background-color: #2eca6a;
        bottom: -8px;
        left: 0;
    }

    .doc-link {
        color: #000;
        text-decoration: none;
        font-weight: 600;
        display: flex;
        align-items: center;
        padding: 10px;
        background: #f8f9fa;
        border-radius: 8px;
        transition: 0.3s;
        border: 1px solid transparent;
    }

    .doc-link:hover {
        background: #fff;
        border-color: #2eca6a;
        color: #2eca6a;
        transform: translateX(5px);
    }
</style>

<div class="container property-header">
    <div class="row align-items-end mb-4">
        <div class="col-md-8">
            <h1 class="title-single"><?= htmlspecialchars($property['title']) ?></h1>
            <p class="text-muted ms-4 ps-1"><i class="bi bi-geo-alt-fill me-2 text-success"></i><?= htmlspecialchars($property['city']) ?></p>
        </div>
        <div class="col-md-4 text-md-end">
            <div class="price-box shadow-sm">
                ₹<?= number_format($property['price']) ?>
            </div>
        </div>
    </div>

    <div class="row mb-5">
        <div class="col-12">
            <div id="carouselMain" class="carousel slide" data-bs-ride="carousel">
                <div class="carousel-inner shadow-sm rounded-4">
                    <?php foreach ($images as $i => $img): ?>
                        <div class="carousel-item <?= $i == 0 ? 'active' : '' ?>">
                            <img src="uploads/<?= $img ?>" class="d-block w-100" style="height:550px; object-fit:trim;" alt="Property Image">
                        </div>
                    <?php endforeach; ?>
                </div>
                <button class="carousel-control-prev" type="button" data-bs-target="#carouselMain" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#carouselMain" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                </button>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-7 col-lg-8">
            <div class="property-card shadow-sm">
                <h4 class="section-title">Property Description</h4>
                <p class="text-secondary mt-4 lh-lg">
                    <?= nl2br(htmlspecialchars($property['description'])) ?>
                </p>
            </div>

            <?php if ($docs): ?>
            <div class="property-card shadow-sm">
                <h4 class="section-title">Attached Documents</h4>
                <div class="row mt-4 g-3">
                    <?php foreach ($docs as $d): ?>
                        <div class="col-md-6">
                            <a href="uploads/docs/<?= $d['file_path'] ?>" target="_blank" class="doc-link">
                                <i class="bi bi-file-earmark-pdf-fill text-danger me-2 fs-4"></i>
                                <?= strtoupper($d['document_type']) ?>
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <div class="col-md-5 col-lg-4">
            <div class="property-card shadow-sm">
                <h4 class="section-title">Quick Summary</h4>
                <ul class="summary-list mt-4">
                    <li>
                        <strong>Property Type:</strong>
                        <span><?= $property['property_type'] ?></span>
                    </li>
                    <li>
                        <strong>Purpose:</strong>
                        <span><?= $property['purpose'] ?></span>
                    </li>
                    <li>
                        <strong>Area:</strong>
                        <span><?= $property['area'] ?> sq.ft</span>
                    </li>
                    <li>
                        <strong>Location:</strong>
                        <span><?= htmlspecialchars($property['city']) ?></span>
                    </li>
                </ul>
            </div>

            <div class="property-card shadow-sm bg-light">
                <h5 class="fw-bold mb-3">Address</h5>
                <p class="text-muted small">
                    <i class="bi bi-pin-map-fill me-1 text-success"></i> 
                    <?= htmlspecialchars($property['address']) ?>
                </p>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>