<?php
require 'auth.php';
require '../config/db.php';

$id = $_GET['id'];

$pdo->prepare("UPDATE properties SET status='approved' WHERE id=?")
    ->execute([$id]);

header("Location: properties.php");