<?php
function is_following($dbh, $user_id, $user_id_target) {
    $stmt = $dbh->prepare("SELECT COUNT(*) FROM follows WHERE user_id = ? AND user_id_target = ?");
    $stmt->execute([$user_id, $user_id_target]);
    return $stmt->fetchColumn() > 0;
}

function follow_user($dbh, $user_id, $user_id_target) {
    $stmt = $dbh->prepare("INSERT INTO follows (user_id, user_id_target) VALUES (?, ?)");
    return $stmt->execute([$user_id, $user_id_target]);
}

function unfollow_user($dbh, $user_id, $user_id_target) {
    $stmt = $dbh->prepare("DELETE FROM follows WHERE user_id = ? AND user_id_target = ?");
    return $stmt->execute([$user_id, $user_id_target]);
}
//
?>
