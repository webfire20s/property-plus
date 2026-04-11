
function hasActivePlan($pdo, $user_id) {
    $stmt = $pdo->prepare("
        SELECT * FROM user_memberships 
        WHERE user_id=? AND status='active' 
        AND expiry_date >= NOW()
        LIMIT 1
    ");
    $stmt->execute([$user_id]);
    return $stmt->fetch();
}