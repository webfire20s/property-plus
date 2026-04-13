<?php
require 'auth.php';
require '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $id = $_POST['doc_id'] ?? null;
    $status = $_POST['status'] ?? null;
    $reason = $_POST['reason'] ?? null;

    if (!$id || $status !== 'rejected') {
        die("Invalid request");
    }

    // UPDATE DOCUMENT WITH REASON
    $stmt = $pdo->prepare("UPDATE property_documents SET status='rejected', rejection_reason=? WHERE id=?");
    $stmt->execute([$reason, $id]);

} else {
    // VERIFY CASE (GET)
    $id = $_GET['id'] ?? null;
    $status = $_GET['status'] ?? null;

    if (!$id || $status !== 'verified') {
        die("Invalid request");
    }

    $stmt = $pdo->prepare("UPDATE property_documents SET status='verified', rejection_reason=NULL WHERE id=?");
    $stmt->execute([$id]);
}

// GET PROPERTY ID
$prop = $pdo->prepare("SELECT property_id FROM property_documents WHERE id=?");
$prop->execute([$id]);
$property_id = $prop->fetchColumn();

// CHECK ALL DOCUMENTS
$check = $pdo->prepare("SELECT status FROM property_documents WHERE property_id=?");
$check->execute([$property_id]);

$allVerified = true;
$anyRejected = false;

foreach ($check as $row) {
    if ($row['status'] != 'verified') {
        $allVerified = false;
    }
    if ($row['status'] == 'rejected') {
        $anyRejected = true;
    }
}

// UPDATE PROPERTY STATUS
if ($allVerified) {
    $pdo->prepare("UPDATE properties SET status='approved' WHERE id=?")
        ->execute([$property_id]);
} elseif ($anyRejected) {
    $pdo->prepare("UPDATE properties SET status='rejected' WHERE id=?")
        ->execute([$property_id]);
} else {
    $pdo->prepare("UPDATE properties SET status='pending' WHERE id=?")
        ->execute([$property_id]);
}

header("Location: properties.php");
exit;