<?php
require 'auth.php';
require '../config/db.php';

$id = $_GET['id'];

$pdo->prepare("UPDATE users SET status='active' WHERE id=?")->execute([$id]);

header("Location: users.php");