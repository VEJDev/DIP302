<?php
    // This file should only be ran once and shouldn't be available to others

    // Connection
    $con = mysqli_connect("127.0.0.1", "root", "");
    if (!$con) {
        echo("Failed to connect to mysql<br>");
        die(mysqli_connect_error());
    }
    echo("connected to mysql<br>");

    // Creating & selecting the database
    $sql = 'CREATE DATABASE IF NOT EXISTS prese;';
    if(mysqli_query($con, $sql)) {
        echo("database created/already exists<br>");
    } else {
        echo("Failed to create database<br>");
    }
    $db = mysqli_select_db($con, 'prese');
    if(!$db) {
        die(mysqli_error());
    }

    // Creating tables
    $sql = "CREATE TABLE IF NOT EXISTS profils (
        id INT PRIMARY KEY AUTO_INCREMENT,
        level INT NOT NULL,
        email VARCHAR(128) NOT NULL,
        password VARCHAR(128) NOT NULL,
        username VARCHAR(16) NOT NULL,
        picture MEDIUMBLOB,
        created DATETIME NOT NULL,
        pw_reset_token TEXT(128) UNIQUE
    );";
    if(!mysqli_query($con, $sql)) {
        echo("couldn't create table<br>");
    } else {
        echo("table created/already exists<br>");
    }

    $sql = "CREATE TABLE IF NOT EXISTS raksts (
        id INT PRIMARY KEY AUTO_INCREMENT,
        title VARCHAR(256) NOT NULL,
        text VARCHAR(16384) NOT NULL,
        picture MEDIUMBLOB NOT NULL,
        created DATETIME NOT NULL,
        last_access DATETIME DEFAULT '1970-01-01 00:00:00',
        views INT DEFAULT 0
    );";
    if(!mysqli_query($con, $sql)) {
        echo("couldn't create table<br>");
    } else {
        echo("table created/already exists<br>");
    }

    $sql = "CREATE TABLE IF NOT EXISTS komentars (
        id INT PRIMARY KEY AUTO_INCREMENT,
        article_id INT NOT NULL,
        profile_id INT NOT NULL,
        text VARCHAR(2000) NOT NULL,
        created DATETIME NOT NULL,
        FOREIGN KEY (profile_id) REFERENCES profils(id) ON DELETE CASCADE
    );";
    if(!mysqli_query($con, $sql)) {
        echo("couldn't create table");
    } else {
        echo("table created/already exists");
    }

    mysqli_close($con);

?>
