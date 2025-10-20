<?php
    session_start();
	require 'db.php';

    $stmt = $conn->prepare("DELETE FROM profils WHERE id = ?");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    session_unset();
    session_destroy();
    header("Location: index.php");
    exit;
?>