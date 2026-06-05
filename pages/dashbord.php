<?php
ob_start(); 
require_once '../config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

$user_role = $_SESSION['user_role'] ?? 'etudiant';
$user_nom  = $_SESSION['user_nom'] ?? 'Utilisateur';

if ($user_role === 'enseignant') {
    header('Location: dashboard_enseignant.php'); 
    exit();
}
 
$title = "Tableau de Bord";
include '../includes/header.php';
include '../includes/navbar.php';
?>

<div class="container py-5">
    <div class="row mb-5">
        <div class="col-12">
            <h2 class="fw-bold">Ravi de vous revoir, <?= e($user_nom) ?> ! 👋</h2>
            <?php 
                $badge_class = ($user_role === 'admin') ? 'bg-danger' : 'bg-primary';
            ?>
            <span class="badge <?= $badge_class ?> rounded-pill px-3 py-2"><?= ucfirst($user_role) ?></span>
        </div>
    </div>

    <div class="row g-4">
        
        <?php if ($user_role === 'etudiant'): ?>
            <div class="col-md-6">
                <div class="card border-0 shadow-sm rounded-4 text-white p-4 h-100" style="background: linear-gradient(135deg, #4f46e5, #0ea5e9);">
                    <h4 class="fw-bold">📝 Passer un Quiz</h4>
                    <p class="small opacity-75">Testez vos connaissances dès maintenant.</p>
                    <a href="liste_quiz.php" class="btn btn-light rounded-pill mt-auto fw-bold text-primary">Commencer</a>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card border-0 shadow-sm rounded-4 text-white p-4 h-100" style="background: linear-gradient(135deg, #10b981, #059669);">
                    <h4 class="fw-bold">📈 Ma Progression</h4>
                    <p class="small opacity-75">Suivez l'évolution de vos résultats.</p>
                    <a href="progression.php" class="btn btn-light rounded-pill mt-auto fw-bold text-success">Voir mes notes</a>
                </div>
            </div>

        <?php elseif ($user_role === 'admin'): ?>
            <div class="col-12">
                <div class="card border-0 shadow-lg rounded-4 text-white p-5" style="background: #1e293b;">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h3 class="fw-bold mb-3">⚙️ Panneau d'Administration</h3>
                            <p class="opacity-75">Bienvenue dans l'espace de gestion. Ici, vous pouvez contrôler l'ensemble de la plateforme QRP.</p>
                        </div>
                        <div class="col-md-4 text-md-end">
                             <i class="bi bi-shield-lock-fill" style="font-size: 4rem; opacity: 0.2;"></i>
                        </div>
                    </div>
                    <hr class="my-4 opacity-25">
                    <div class="d-flex flex-wrap gap-3">
                        <a href="gerer_quiz.php" class="btn btn-primary rounded-pill px-4 py-2 fw-bold">
                            <i class="bi bi-collection-play me-2"></i> Gérer les Quiz
                        </a>
                        <a href="gerer_users.php" class="btn btn-outline-light rounded-pill px-4 py-2 fw-bold">
                            <i class="bi bi-people me-2"></i> Utilisateurs
                        </a>
                        <a href="stats.php" class="btn btn-outline-info rounded-pill px-4 py-2 fw-bold">
                            <i class="bi bi-bar-chart me-2"></i> Statistiques
                        </a>
                    </div>
                </div>
            </div>
        <?php endif; ?>

    </div>
</div>

<?php 
include '../includes/footer.php'; 
ob_end_flush(); 
?>