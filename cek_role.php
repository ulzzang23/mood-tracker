<?php
session_start();

function cekRole($roles = []) {
    if (!isset($_SESSION['user'])) {
        header("Location: login.php");
        exit;
    }

    if (!in_array($_SESSION['user']['role'], $roles)) {
        echo "❌ Akses ditolak untuk role: " . $_SESSION['user']['role'];
        exit;
    }
}
