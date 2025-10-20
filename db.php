<?php
    $conn = new mysqli('localhost', 'root', '', 'prese');

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
?>