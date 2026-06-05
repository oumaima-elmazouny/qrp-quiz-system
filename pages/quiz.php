<?php
require_once '../config.php';
redirect_if_not_logged_in();

$query = "SELECT q.*, c.nom_chapitre, m.nom_matiere 
          FROM quiz q 
          LEFT JOIN chapitres c ON q.id_chapitre = c.id_chapitre 
          LEFT JOIN matieres m ON c.id_matiere = m.id_matiere
          ORDER BY m.nom_matiere ASC, q.titre ASC";

try {
    $all_quiz = $pdo->query($query)->fetchAll();
} catch (Exception $e) {
    $all_quiz = $pdo->query("SELECT * FROM quiz ORDER BY id_quiz DESC")->fetchAll();
}

$title = "Mes Quiz de Révision";
include '../includes/header.php';
include '../includes/navbar.php';
?>

<style>
    body { background-color: #f8f9fa; }
    .quiz-card { border: none; border-radius: 20px; transition: all 0.3s ease; overflow: hidden; background: white; }
    .quiz-card:hover { transform: translateY(-10px); box-shadow: 0 20px 40px rgba(0,0,0,0.08) !important; }
    .img-container { height: 180px; overflow: hidden; background: #f0f0f0; position: relative; }
    .img-container img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.5s; }
    .quiz-card:hover .img-container img { transform: scale(1.1); }
    .badge-top { position: absolute; top: 15px; left: 15px; z-index: 10; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px; }
    .fw-800 { font-weight: 800; }
</style>

<div class="container py-5">
    <div class="row mb-5 align-items-center">
        <div class="col-md-8">
            <h2 class="fw-800 text-dark"><i class="bi bi-journal-check text-primary me-2"></i> Quiz Disponibles</h2>
            <p class="text-muted">Sélectionnez un quiz pour tester vos connaissances et suivre votre progression.</p>
        </div>
        <div class="col-md-4 text-md-end">
            <span class="badge bg-white text-primary border px-3 py-2 rounded-pill shadow-sm">
                <?= count($all_quiz) ?> Quiz accessibles
            </span>
        </div>
    </div>

    <div class="row g-4">
        <?php if (count($all_quiz) > 0): ?>
            <?php foreach ($all_quiz as $q): ?>
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 shadow-sm quiz-card position-relative">
                    
                    <?php if(!empty($q['nom_matiere'])): ?>
                        <span class="badge bg-primary badge-top rounded-pill px-3 shadow-sm">
                            <?= e($q['nom_matiere']) ?>
                        </span>
                    <?php endif; ?>

                    <div class="img-container">
                        <?php 
                        $imagePath = !empty($q['image_path']) ? "../images/".e($q['image_path']) : "../assets/img/default-quiz.jpg";
                        ?>
                        <img src="<?= $imagePath ?>" alt="<?= e($q['titre']) ?>">
                    </div>
                    
                    <div class="card-body p-4">
                        <h5 class="card-title fw-bold text-dark mb-2"><?= e($q['titre']) ?></h5>
                        <p class="card-text text-muted small mb-4">
                            <?php 
                                $desc = e($q['description']);
                                echo (strlen($desc) > 85) ? substr($desc, 0, 82) . '...' : $desc;
                            ?>
                        </p>
                        
                        <div class="d-flex flex-column gap-2 border-top pt-3">
                            <div class="d-flex justify-content-between align-items-center small">
                                <span class="text-muted"><i class="bi bi-folder2-open me-1"></i> Chapitre</span>
                                <span class="fw-bold text-dark"><?= e($q['nom_chapitre'] ?? 'Général') ?></span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center small">
                                <span class="text-muted"><i class="bi bi-stopwatch me-1"></i> Durée</span>
                                <?php if($q['temps_limite'] > 0): ?>
                                    <span class="fw-bold text-danger"><?= intval($q['temps_limite']) ?> minutes</span>
                                <?php else: ?>
                                    <span class="fw-bold text-success">Illimité</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card-footer bg-white border-0 p-4 pt-0">
                        <a href="passer_quiz.php?id=<?= $q['id_quiz'] ?>" class="btn btn-primary w-100 py-2 rounded-pill shadow-sm fw-bold">
                            Démarrer le Quiz <i class="bi bi-arrow-right ms-2"></i>
                        </a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12 text-center py-5">
                <div class="bg-white rounded-4 p-5 shadow-sm border">
                    <i class="bi bi-search text-muted" style="font-size: 3rem;"></i>
                    <h4 class="mt-3 fw-bold">Aucun quiz trouvé</h4>
                    <p class="text-muted">Revenez plus tard pour voir les nouveaux tests mis en ligne.</p>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include '../includes/footer.php'; ?>