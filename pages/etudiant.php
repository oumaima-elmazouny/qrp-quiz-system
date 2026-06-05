<?php
require_once '../config.php';
redirect_if_not_logged_in();

if ($_SESSION['user_role'] != 'etudiant') {
    header('Location: dashbord.php');
    exit();
}

$stmt = $pdo->query("
    SELECT q.*, c.nom_chapitre, m.nom_matiere 
    FROM quiz q
    LEFT JOIN chapitres c ON q.id_chapitre = c.id_chapitre
    LEFT JOIN matieres m ON c.id_matiere = m.id_matiere
    ORDER BY q.id_quiz DESC
");
$quizList = $stmt->fetchAll();

$title = "Catalogue des Quiz";
include '../includes/header.php';
include '../includes/navbar.php';
?>

<div class="container py-5">

    <div class="row mb-5 animate-in">
        <div class="col-md-8 text-center text-md-start">
            <h2 class="fw-bold mb-2">📚 Catalogue des Quiz</h2>
            <p class="text-muted fs-5">Sélectionnez un sujet et commencez votre entraînement.</p>
        </div>
        <div class="col-md-4 d-flex align-items-center justify-content-center justify-content-md-end mt-3 mt-md-0">
            <span class="badge bg-white text-dark border px-3 py-2 rounded-pill shadow-sm">
                <i class="bi bi-collection-play me-2 text-primary"></i>
                <?= count($quizList) ?> Quiz disponibles
            </span>
        </div>
    </div>

    <?php if (count($quizList) > 0): ?>
        <div class="row g-4">
            <?php foreach ($quizList as $quiz): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden quiz-card-hover">
                        <div style="height: 6px; background: linear-gradient(90deg, #4f46e5, #0ea5e9);"></div>
                        
                        <div class="card-body p-4 d-flex flex-column">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <?php if (!empty($quiz['nom_matiere'])): ?>
                                    <span class="badge bg-primary bg-opacity-10 text-primary fw-bold px-3 py-2 rounded-pill">
                                        <?= e($quiz['nom_matiere']) ?>
                                    </span>
                                <?php else: ?>
                                    <span></span> <?php endif; ?>
                                
                                <div class="text-muted bg-light px-2 py-1 rounded small">
                                    <i class="bi bi-clock-history me-1"></i> 
                                    <span class="fw-bold"><?= round($quiz['temps_limite'] / 60) ?> min</span>
                                </div>
                            </div>

                            <h4 class="fw-bold mb-2 text-dark"><?= e($quiz['titre']) ?></h4>

                            <p class="text-muted small mb-4">
                                <i class="bi bi-journal-text me-1"></i>
                                <?= !empty($quiz['nom_chapitre']) ? e($quiz['nom_chapitre']) : "Général" ?>
                            </p>

                            <div class="mt-auto">
                                <a href="passer_quiz.php?id=<?= $quiz['id_quiz'] ?>" 
                                   class="btn btn-primary w-100 rounded-pill py-2 fw-bold shadow-sm">
                                    <i class="bi bi-play-circle-fill me-2"></i> Relever le défi
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

    <?php else: ?>
        <div class="row justify-content-center mt-5">
            <div class="col-md-6 text-center p-5 bg-light rounded-5 border border-dashed">
                <i class="bi bi-inbox fs-1 text-muted mb-3 d-block"></i>
                <h5 class="fw-bold">Aucun quiz disponible</h5>
                <p class="text-muted">Revenez plus tard, vos professeurs préparent de nouveaux défis !</p>
                <a href="dashbord.php" class="btn btn-outline-secondary btn-sm rounded-pill px-4">Retour au Dashboard</a>
            </div>
        </div>
    <?php endif; ?>

</div>

<style>
    .quiz-card-hover {
        transition: all 0.3s ease;
        background: #fff;
    }
    .quiz-card-hover:hover {
        transform: translateY(-8px);
        box-shadow: 0 1rem 3rem rgba(0,0,0,.08) !important;
    }
    .animate-in {
        animation: fadeInUp 0.6s ease-out;
    }
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .text-primary { color: #4f46e5 !important; }
    .btn-primary { background-color: #4f46e5; border: none; }
    .btn-primary:hover { background-color: #4338ca; }
</style>

<?php include '../includes/footer.php'; ?>