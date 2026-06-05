<?php
require_once '../config.php';

if ($_SESSION['user_role'] !== 'admin') {
    header('Location: dashbord.php');
    exit();
}

$nb_users = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$nb_quiz = $pdo->query("SELECT COUNT(*) FROM quiz")->fetchColumn(); 

$title = "Statistiques Globales";
include '../includes/header.php';
include '../includes/navbar.php';
?>

<div class="container py-5">
    <h2 class="fw-bold mb-4">📊 Statistiques de la Plateforme</h2>
    
    <div class="row g-4 text-center">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm p-4 rounded-4">
                <h1 class="display-4 fw-bold text-primary"><?= $nb_users ?></h1>
                <p class="text-muted text-uppercase small fw-bold">Utilisateurs inscrits</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm p-4 rounded-4">
                <h1 class="display-4 fw-bold text-success"><?= $nb_quiz ?></h1>
                <p class="text-muted text-uppercase small fw-bold">Quiz créés</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm p-4 rounded-4">
                <h1 class="display-4 fw-bold text-info">--</h1>
                <p class="text-muted text-uppercase small fw-bold">Tentatives de Quiz</p>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>