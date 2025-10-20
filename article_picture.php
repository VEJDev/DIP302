<?php
    require 'db.php';

    $id = $_GET['id'];
    $stmt = $conn->prepare("SELECT picture FROM raksts WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($image);
    $stmt->fetch();

    header("Content-Type: image/jpeg");

    if ($image == NULL) {
        $imagePath = "assets/none.jpg";
        readfile($imagePath);
    } else {
        echo $image;
    }
?>