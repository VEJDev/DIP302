<?php
    // Run this script outside of this folder to change permissions of a user
    $username = "asdf";
    $permission = 2; // 0 - guest, 1 - registered, 2 - administrator
    
    require 'db.php';

    $stmt2 = $conn->prepare("UPDATE profils SET level=? WHERE username=?");
    $stmt2->bind_param("is", $permission, $username);
    $stmt2->execute();
    echo("Success...");
?>
