<?php
require_once '../config.php';
redirect_if_not_logged_in();

$user_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("
    SELECT t.*, q.titre 
    FROM tentatives t 
    JOIN quiz q ON t.id_quiz = q.id_quiz 
    WHERE t.id_user = ? 
    ORDER BY t.date_tentative DESC
");
$stmt->execute([$user_id]);
$mes_scores = $stmt->fetchAll();

$title = "Ma Progression";
include '../includes/header.php';
include '../includes/navbar.php';
?>

<div class="container page-wrapper mt-5">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-5 gap-3 animate-in">
        <div>
            <h2 class="fw-800 text-dark mb-1">🚀 Ma Progression</h2>
            <p class="text-muted mb-0">Retrouvez l'historique de vos performances et vos scores.</p>
        </div>
        <div class="bg-white shadow-sm rounded-4 px-4 py-3 border">
            <span class="text-muted small fw-bold text-uppercase d-block">Total tentatives</span>
            <span class="h4 fw-800 text-primary mb-0"><?= count($mes_scores) ?></span>
        </div>
    </div>

    <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
        <div class="list-group list-group-flush">
            <?php if (count($mes_scores) > 0): ?>
                <?php foreach ($mes_scores as $s): ?>
                    <div class="list-group-item p-4 d-flex justify-content-between align-items-center list-item-hover">
                        <div class="d-flex align-items-center">
                            <div class="icon-circle-sm bg-light text-primary me-3">
                                <i class="bi bi-patch-check"></i>
                            </div>
                            <div>
                                <h6 class="mb-1 fw-bold text-dark"><?= e($s['titre']) ?></h6>
                                <small class="text-muted">
                                    <i class="bi bi-calendar3 me-1"></i> 
                                    <?= date('d/m/Y à H:i', strtotime($s['date_tentative'])) ?>
                                </small>
                            </div>
                        </div>
                        <div class="text-end">
                            <span class="badge <?= ($s['score'] >= 10) ? 'bg-success' : 'bg-warning text-dark' ?> rounded-pill fs-6 px-4 py-2 shadow-sm">
                                <?= $s['score'] ?> <small>pts</small>
                            </span>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="p-5 text-center">
                    <div class="mb-4">
                        <i class="bi bi-clipboard-x text-muted display-1 opacity-25"></i>
                    </div>
                    <h4 class="fw-bold">Aucun résultat pour le moment</h4>
                    <p class="text-muted">C'est le moment idéal pour tester tes connaissances !</p>
                    <a href="liste_quiz.php" class="btn btn-primary rounded-pill px-5 py-2 mt-3 shadow-sm fw-bold">
                        <i class="bi bi-play-fill me-1"></i> Voir les quiz disponibles
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
    .fw-800 { font-weight: 800; }
    .icon-circle-sm {
        width: 40px; height: 40px;
        display: flex; align-items: center; justify-content: center;
        border-radius: 12px;
        font-size: 1.2rem;
    }
    .list-item-hover { transition: background 0.2s ease; }
    .list-item-hover:hover { background-color: #fbfbfe; }
    
    .animate-in {
        animation: fadeIn 0.5s ease-out;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>

<?php include '../includes/footer.php'; ?>