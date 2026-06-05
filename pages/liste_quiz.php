<?php
require_once '../config.php';
redirect_if_not_logged_in();

if ($_SESSION['user_role'] !== 'etudiant' && $_SESSION['user_role'] !== 'admin') {
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
$quizzes = $stmt->fetchAll();

$title = "Liste des Quiz";
include '../includes/header.php';
include '../includes/navbar.php';
?>

<div class="container page-wrapper py-5">
    
    <div class="d-flex align-items-center mb-5 animate-in">
        <div class="icon-box bg-primary text-white me-3 shadow">
            <i class="bi bi-journal-text fs-3"></i>
        </div>
        <div>
            <h2 class="fw-800 mb-0">Quiz Disponibles</h2>
            <p class="text-muted mb-0">Testez vos connaissances et gagnez des points !</p>
        </div>
    </div>

    <div class="row g-4">
        <?php if (count($quizzes) > 0): ?>
            <?php foreach ($quizzes as $quiz): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden quiz-hover-effect">
                        <div class="card-body p-4 d-flex flex-column">
                            
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <?php if (!empty($quiz['nom_matiere'])): ?>
                                    <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill small fw-bold">
                                        <?= e($quiz['nom_matiere']) ?>
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-light text-muted px-3 py-2 rounded-pill small">Général</span>
                                <?php endif; ?>
                                
                                <small class="text-muted fw-bold">
                                    <i class="bi bi-clock me-1"></i> <?= round($quiz['temps_limite'] / 60) ?> min
                                </small>
                            </div>

                            <h5 class="fw-bold text-dark mb-2"><?= e($quiz['titre']) ?></h5>
                            
                            <p class="text-muted small mb-4">
                                <i class="bi bi-layers me-1"></i> 
                                <?= !empty($quiz['nom_chapitre']) ? e($quiz['nom_chapitre']) : 'Révisions générales' ?>
                            </p>

                            <div class="mt-auto">
                                <a href="passer_quiz.php?id=<?= $quiz['id_quiz'] ?>" 
                                   class="btn btn-outline-primary w-100 rounded-pill py-2 fw-bold transition-all">
                                    <i class="bi bi-play-fill me-1"></i> Lancer le quiz
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12 text-center py-5">
                <div class="bg-light rounded-5 p-5 border border-dashed">
                    <i class="bi bi-emoji-frown fs-1 text-muted mb-3"></i>
                    <h4 class="fw-bold">Aucun quiz pour le moment</h4>
                    <p class="text-muted">Repassez plus tard, vos enseignants préparent du nouveau contenu !</p>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
    .fw-800 { font-weight: 800; }
    .icon-box {
        width: 60px; height: 60px;
        display: flex; align-items: center; justify-content: center;
        border-radius: 18px;
    }
    
    .quiz-hover-effect { transition: all 0.3s ease; }
    .quiz-hover-effect:hover {
        transform: translateY(-10px);
        box-shadow: 0 15px 30px rgba(79, 70, 229, 0.15) !important;
    }
    
    .quiz-hover-effect:hover .btn-outline-primary {
        background-color: #4f46e5; 
        color: white;
        border-color: #4f46e5;
    }

    .animate-in { animation: fadeIn 0.6s ease-out; }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateX(-20px); }
        to { opacity: 1; transform: translateX(0); }
    }
</style>

<?php include '../includes/footer.php'; ?>