<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function e($string) {
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}

function is_logged_in() {
    return isset($_SESSION['user_id']);
}

function redirect_if_not_logged_in() {
    if (!is_logged_in()) {
        header('Location: ../index.php'); 
        exit();
    }
}

function has_role($role) {
    return (isset($_SESSION['user_role']) && $_SESSION['user_role'] === $role);
}

function input_int($data) {
    return intval($data);
}

function is_staff() {
    return (has_role('admin') || has_role('enseignant'));
}

function restrict_to_staff() {
    if (!is_staff()) {
        header('Location: dashbord.php?error=access_denied');
        exit();
    }
}
?>