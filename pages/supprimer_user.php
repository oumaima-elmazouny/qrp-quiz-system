<?php
require_once '../config.php';

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: dashbord.php');
    exit();
}

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id_a_supprimer = $_GET['id'];

    if ($id_a_supprimer == $_SESSION['user_id']) {
        header('Location: gerer_users.php?error=self_delete');
        exit();
    }

    try {
        $stmt = $pdo->prepare("DELETE FROM users WHERE id_user = ?");
        $stmt->execute([$id_a_supprimer]);

        header('Location: gerer_users.php?success=deleted');
        exit();
    } catch (PDOException $e) {
        die("Erreur lors de la suppression : " . $e->getMessage());
    }
} else {
    header('Location: gerer_users.php');
    exit();
}