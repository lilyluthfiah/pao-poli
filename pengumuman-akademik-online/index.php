<?php
session_start();

$user = $_SESSION['user'] ?? null;

if (!isset($user)) {
    header("Location: login.php");
    exit;
} else {
    header("Location: panel");
    exit;
}
