<?php
require_once '../config.php';
redirect_if_not_logged_in();

$score = $_SESSION['dernier_score'] ?? null;
$justes = $_SESSION['reponses_justes'] ?? 0;
$total = $_SESSION['total_questions'] ?? 0;

if ($score === null) {
    header('Location: dashbord.php');
    exit();
}

if ($score >= 16) {
    $color = "text-success"; $bg_light = "bg-success"; $icon = "bi-trophy-fill";
    $message = "Incroyable ! Tu es un expert 🏆";
} elseif ($score >= 10) {
    $color = "text-primary"; $bg_light = "bg-primary"; $icon = "bi-star-fill";
    $message = "Bien joué ! Tu as la moyenne 👍";
} else {
    $color = "text-danger"; $bg_light = "bg-danger"; $icon = "bi-exclamation-triangle-fill";
    $message = "Continue de réviser, tu vas y arriver ! 💪";
}

$title = "Résultat du Quiz";
include '../includes/header.php';
include '../includes/navbar.php';
?>

<div class="container py-5 text-center">
    <div class="card shadow-lg border-0 p-5 mx-auto rounded-5" style="max-width: 600px;">
        <i class="bi <?= $icon ?> <?= $color ?> display-1 mb-4"></i>
        <h2 class="fw-bold"><?= $_SESSION['latest_quiz_title'] ?></h2>
        
        <div class="my-4">
            <div class="d-inline-block p-4 rounded-circle border border-5 shadow-sm" style="width: 160px; height: 160px;">
                <h1 class="display-4 fw-bold mb-0 <?= $color ?>"><?= round($score) ?></h1>
                <small class="text-muted fw-bold">/ 20</small>
            </div>
        </div>

        <h4 class="<?= $color ?> fw-bold mb-4"><?= $message ?></h4>
        <p class="text-muted">Tu as trouvé <strong><?= $justes ?></strong> bonnes réponses sur <strong><?= $total ?></strong>.</p>

        <div class="progress rounded-pill mb-5" style="height: 15px;">
            <div class="progress-bar progress-bar-striped progress-bar-animated <?= $bg_light ?>" 
                 style="width: <?= ($score / 20) * 100 ?>%"></div>
        </div>

        <div class="row g-3">
            <div class="col-6"><a href="liste_quiz.php" class="btn btn-primary w-100 rounded-pill py-2">Autres Quiz</a></div>
            <div class="col-6"><a href="dashbord.php" class="btn btn-outline-dark w-100 rounded-pill py-2">Tableau de bord</a></div>
        </div>
    </div>
</div>

<?php 
unset($_SESSION['dernier_score']); 
unset($_SESSION['reponses_justes']); 
unset($_SESSION['total_questions']); 
unset($_SESSION['latest_quiz_title']); 
include '../includes/footer.php'; 
?>