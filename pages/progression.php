<?php
require_once '../config.php';
redirect_if_not_logged_in();

if (!has_role('etudiant') && !has_role('admin')) {
    header('Location: dashbord.php');
    exit();
}

$user_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT COUNT(*) as total, SUM(score) as somme FROM tentatives WHERE id_user = ?");
$stmt->execute([$user_id]);
$stats = $stmt->fetch();

$total = $stats['total'] ?? 0;
$sum = $stats['somme'] ?? 0;
$avg = ($total > 0) ? round($sum / $total, 2) : 0;

$stmtHist = $pdo->prepare("
    SELECT t.*, q.titre 
    FROM tentatives t 
    JOIN quiz q ON t.id_quiz = q.id_quiz 
    WHERE t.id_user = ? 
    ORDER BY t.date_tentative DESC 
    LIMIT 8
");
$stmtHist->execute([$user_id]);
$historique = $stmtHist->fetchAll();

$title = "Ma Progression";
include '../includes/header.php';
include '../includes/navbar.php';
?>

<style>
    body { background-color: #f4f7fe; }
    .stat-card { border: none; border-radius: 20px; transition: all 0.3s ease; }
    .stat-card:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important; }
    .progress-table-card { border-radius: 20px; border: none; }
    .badge-score { font-size: 0.9rem; padding: 8px 12px; border-radius: 10px; }
    .fw-800 { font-weight: 800; }
</style>

<div class="container py-5">
    <div class="mb-5">
        <h2 class="fw-800 text-dark mb-1 text-center text-md-start">📈 Ma Progression</h2>
        <p class="text-muted text-center text-md-start">Suivez vos performances et votre évolution en temps réel.</p>
    </div>

    <div class="row g-4 text-center mb-5">
        <div class="col-md-4">
            <div class="card shadow-sm stat-card p-4 bg-white">
                <div class="icon-box mb-3 text-primary fs-1"><i class="bi bi-trophy"></i></div>
                <p class="text-uppercase small fw-bold text-muted mb-1">Quiz Terminés</p>
                <h2 class="display-5 fw-800 text-dark"><?= $total ?></h2>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm stat-card p-4 bg-white">
                <div class="icon-box mb-3 text-success fs-1"><i class="bi bi-star"></i></div>
                <p class="text-uppercase small fw-bold text-muted mb-1">Points Cumulés</p>
                <h2 class="display-5 fw-800 text-dark"><?= $sum ?></h2>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm stat-card p-4 bg-white">
                <?php 
                    $avgColor = ($avg >= 15) ? 'text-success' : (($avg >= 10) ? 'text-primary' : 'text-danger');
                ?>
                <div class="icon-box mb-3 <?= $avgColor ?> fs-1"><i class="bi bi-graph-up-arrow"></i></div>
                <p class="text-uppercase small fw-bold text-muted mb-1">Moyenne Générale</p>
                <h2 class="display-5 fw-800 <?= $avgColor ?>"><?= $avg ?><small class="fs-6">/20</small></h2>
            </div>
        </div>
    </div>

    <div class="card shadow-sm progress-table-card">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="fw-800 mb-0 text-dark"><i class="bi bi-clock-history me-2 text-primary"></i>Dernières tentatives</h4>
                <?php if ($total > 8): ?>
                    <span class="text-muted small">Affichage des 8 derniers résultats</span>
                <?php endif; ?>
            </div>
            
            <?php if (count($historique) > 0): ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle border-top">
                        <thead class="bg-light">
                            <tr>
                                <th class="border-0 py-3">Quiz</th>
                                <th class="border-0 py-3 text-center">Date</th>
                                <th class="border-0 py-3 text-center">Score</th>
                                <th class="border-0 py-3 text-end">Statut</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($historique as $h): ?>
                            <tr>
                                <td class="fw-bold py-3 text-dark"><?= e($h['titre']) ?></td>
                                <td class="text-muted text-center"><?= date('d/m/Y à H:i', strtotime($h['date_tentative'])) ?></td>
                                <td class="text-center">
                                    <span class="badge bg-light text-dark border badge-score fw-bold">
                                        <?= $h['score'] ?> <small>/ 20</small>
                                    </span>
                                </td>
                                <td class="text-end">
                                    <?php if ($h['score'] >= 10): ?>
                                        <span class="badge bg-success bg-opacity-10 text-success px-3 py-2 rounded-pill">
                                            <i class="bi bi-check-circle-fill me-1"></i> Réussi
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-danger bg-opacity-10 text-danger px-3 py-2 rounded-pill">
                                            <i class="bi bi-exclamation-triangle-fill me-1"></i> À revoir
                                        </span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="text-center py-5">
                    <img src="../images/empty-state.png" class="mb-3" style="width: 150px; opacity: 0.5;">
                    <p class="text-muted fs-5">Tu n'as pas encore relevé de défi !</p>
                    <a href="liste_quiz.php" class="btn btn-primary btn-lg rounded-pill px-5 shadow-sm">
                        Découvrir les Quiz
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>