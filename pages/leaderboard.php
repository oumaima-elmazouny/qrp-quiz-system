<?php
require_once '../config.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

$stmt = $pdo->query("SELECT u.nom_complet, u.role, 
                            SUM(s.score) as total_points, 
                            COUNT(s.id_score) as quiz_faits 
                     FROM users u 
                     JOIN scores s ON u.id_user = s.id_user 
                     WHERE u.role = 'etudiant' 
                     GROUP BY u.id_user 
                     ORDER BY total_points DESC 
                     LIMIT 10");
$ranks = $stmt->fetchAll();

$title = "Classement Général";
include '../includes/header.php';
include '../includes/navbar.php';
?>

<div class="container page-wrapper py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            
            <div class="leaderboard-header text-center mb-5 animate-in">
                <div class="icon-circle bg-warning text-white mb-3 shadow-lg mx-auto">
                    <i class="bi bi-trophy-fill fs-1"></i>
                </div>
                <h1 class="fw-800 display-5">Temple de la Renommée</h1>
                <p class="text-muted fs-5">Félicitations aux meilleurs étudiants de la plateforme !</p>
            </div>

            <div class="card border-0 shadow-lg rounded-5 overflow-hidden">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr class="text-uppercase small fw-bold text-muted">
                                    <th class="ps-5 py-4" style="width: 150px;">Rang</th>
                                    <th class="py-4">Étudiant</th>
                                    <th class="py-4 text-center">Activité</th>
                                    <th class="pe-5 py-4 text-end">Score Total</th>
                                </tr>
                            </thead>
                            <tbody class="border-top-0">
                                <?php 
                                $i = 1; 
                                foreach ($ranks as $r): 
                                    $rowClass = ($i == 1) ? 'bg-gold-light' : '';
                                ?>
                                <tr class="<?= $rowClass ?>">
                                    <td class="ps-5">
                                        <?php if($i == 1): ?>
                                            <span class="rank-badge rank-1">1<sup>er</sup></span>
                                        <?php elseif($i == 2): ?>
                                            <span class="rank-badge rank-2">2<sup>e</sup></span>
                                        <?php elseif($i == 3): ?>
                                            <span class="rank-badge rank-3">3<sup>e</sup></span>
                                        <?php else: ?>
                                            <span class="ms-3 fw-bold text-muted">#<?= $i ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm me-3 bg-primary text-white rounded-circle d-flex align-items-center justify-content-center fw-bold shadow-sm">
                                                <?= strtoupper(substr(trim($r['nom_complet']), 0, 1)) ?>
                                            </div>
                                            <div>
                                                <div class="fw-bold text-dark"><?= e($r['nom_complet']) ?></div>
                                                <div class="text-muted x-small">Compétiteur Élite</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span class="text-dark fw-medium small">
                                            <i class="bi bi-check2-circle text-success me-1"></i>
                                            <?= $r['quiz_faits'] ?> quiz complétés
                                        </span>
                                    </td>
                                    <td class="pe-5 text-end">
                                        <span class="points-pill">
                                            <?= number_format($r['total_points'], 0, '.', ' ') ?> pts
                                        </span>
                                    </td>
                                </tr>
                                <?php $i++; endforeach; ?>

                                <?php if (empty($ranks)): ?>
                                <tr>
                                    <td colspan="4" class="text-center py-5 text-muted">
                                        <i class="bi bi-star fs-1 d-block mb-2"></i>
                                        Le classement est vide pour le moment. Soyez le premier !
                                    </td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="text-center mt-4">
                <div class="alert bg-white border-0 shadow-sm d-inline-block rounded-pill px-4">
                    <i class="bi bi-info-circle me-2 text-primary"></i> 
                    <span class="small text-muted">Le score est calculé sur le cumul de tous les points obtenus.</span>
                </div>
            </div>

        </div>
    </div>
</div>

<style>
    .fw-800 { font-weight: 800; }
    .icon-circle {
        width: 80px; height: 80px;
        display: flex; align-items: center; justify-content: center;
        border-radius: 50%;
        background: linear-gradient(135deg, #ffca28 0%, #ff8f00 100%);
    }
    
    .rank-badge {
        padding: 0.5rem 1rem;
        border-radius: 12px;
        font-weight: 800;
        font-size: 0.9rem;
        display: inline-block;
        text-align: center;
        min-width: 65px;
    }
    
    .rank-1 { background-color: #fff9db; color: #f59f00; border: 1px solid #ffe066; }
    .rank-2 { background-color: #f1f3f5; color: #495057; border: 1px solid #dee2e6; }
    .rank-3 { background-color: #fff4e6; color: #d9480f; border: 1px solid #ffd8a8; }
    
    .bg-gold-light { background-color: rgba(255, 249, 219, 0.4) !important; }
    
    .points-pill {
        background: #4f46e5;
        color: white;
        padding: 0.6rem 1.2rem;
        border-radius: 50px;
        font-weight: 700;
        box-shadow: 0 4px 10px rgba(79, 70, 229, 0.2);
    }

    .avatar-sm { width: 42px; height: 42px; font-size: 1.1rem; }
    .x-small { font-size: 0.75rem; }
    
    .animate-in { animation: fadeInDown 0.8s ease-out; }
    @keyframes fadeInDown {
        from { opacity: 0; transform: translateY(-20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .table-hover tbody tr:hover { background-color: #f8f9ff; }
</style>

<?php include '../includes/footer.php'; ?>