<?php
require_once '../config.php';
redirect_if_not_logged_in();

if (!has_role('enseignant') && !has_role('admin')) {
    header('Location: dashbord.php'); 
    exit();
}

$id_user = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT COUNT(*) FROM quiz WHERE id_createur = ?");
$stmt->execute([$id_user]);
$nb_quiz = $stmt->fetchColumn();

$title = "Espace Enseignant";
include '../includes/header.php';
include '../includes/navbar.php';
?>
  
<style>
    .dash-card {
        border: none; 
        border-radius: 20px;
        transition: transform 0.3s, box-shadow 0.3s; 
        background: var(--card-bg);
    }
    .dash-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
    }
    .icon-circle {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 20px;
        font-size: 1.5rem;
    }
</style>

<div class="container py-5 fade-in">
    <div class="row mb-5">
        <div class="col-12">
            <h2 class="fw-bold">👨‍🏫 Bienvenue, <?= e($_SESSION['user_nom']) ?></h2>
            <p class="text-muted">Gérez vos quiz et suivez les résultats de vos étudiants.</p>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-md-4">
            <div class="card shadow-sm dash-card h-100 p-4 text-center">
                <div class="icon-circle bg-success text-white mx-auto">
                    <i class="bi bi-plus-lg"></i>
                </div>
                <h4>Créer un Quiz</h4>
                <p class="text-muted small">Ajoutez de nouvelles questions et configurez le temps limite.</p>
                <a href="create_quiz.php" class="btn btn-success rounded-pill mt-auto">
                    Nouveau Quiz
                </a>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm dash-card h-100 p-4 text-center">
                <div class="icon-circle bg-primary text-white mx-auto">
                    <i class="bi bi-list-check"></i>
                </div>
                <h4>Gérer les Quiz</h4>
                <p class="text-muted small">Vous avez actuellement <strong><?= $nb_quiz ?></strong> quiz créés.</p>
                <a href="gerer_quiz.php" class="btn btn-primary rounded-pill mt-auto">
                    Voir les Quiz
                </a>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm dash-card h-100 p-4 text-center">
                <div class="icon-circle bg-dark text-white mx-auto">
                    <i class="bi bi-graph-up"></i>
                </div>
                <h4>Résultats</h4>
                <p class="text-muted small">Consultez les notes et les performances des étudiants.</p>
                <a href="statistiques.php" class="btn btn-dark rounded-pill mt-auto">
                    Voir Stats
                </a>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>