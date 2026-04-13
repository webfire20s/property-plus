<?php
require '../includes/auth_check.php';
require '../config/db.php';

$id = $_GET['id'];

// 1. Verify ownership (Existing Logic)
$stmt = $pdo->prepare("SELECT * FROM properties WHERE id=? AND user_id=?");
$stmt->execute([$id, $_SESSION['user_id']]);

if ($stmt->rowCount() == 0) {
    // Styled error message instead of 'die'
    include '../includes/navbar.php'; 
    echo "<div class='container py-5 text-center'><div class='alert alert-danger'>Unauthorized Access.</div></div>";
    exit;
}

// 2. Delete (Existing Logic)
$pdo->prepare("DELETE FROM properties WHERE id=?")->execute([$id]);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Property Deleted | EstateAgency</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background: #f8fafc; font-family: 'Poppins', sans-serif; }
        .delete-card {
            max-width: 500px;
            margin: 100px auto;
            background: white;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.05);
            text-align: center;
        }
        .icon-box {
            width: 80px;
            height: 80px;
            background: #fee2e2;
            color: #ef4444;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            border-radius: 50%;
            margin: 0 auto 20px;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="delete-card">
        <div class="icon-box">
            <i class="fa-solid fa-trash-can"></i>
        </div>
        <h3 class="fw-bold text-dark">Property Removed</h3>
        <p class="text-secondary mb-4">The listing has been successfully deleted from your account and the marketplace.</p>
        
        <a href="dashboard.php" class="btn btn-dark w-100 py-3" style="border-radius: 10px; font-weight: 600;">
            Return to Dashboard
        </a>
    </div>
</div>

<script>
    // Optional: Auto-redirect after 3 seconds
    setTimeout(function() {
        window.location.href = 'dashboard.php';
    }, 3000);
</script>

</body>
</html>