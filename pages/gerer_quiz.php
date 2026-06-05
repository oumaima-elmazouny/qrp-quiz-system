<?php
require_once '../config.php';
redirect_if_not_logged_in();

if ($_SESSION['user_role'] === 'etudiant') {
    header('Location: dashbord.php'); 
    exit();
}

$user_id = $_SESSION['user_id'];
$is_admin = ($_SESSION['user_role'] === 'admin');

if ($is_admin) {
    $stmt = $pdo->query("
        SELECT q.*, m.nom_matiere 
        FROM quiz q 
        LEFT JOIN chapitres c ON q.id_chapitre = c.id_chapitre 
        LEFT JOIN matieres m ON c.id_matiere = m.id_matiere 
        ORDER BY q.id_quiz DESC
    ");
} else {
    $stmt = $pdo->prepare("
        SELECT q.*, m.nom_matiere 
        FROM quiz q 
        LEFT JOIN chapitres c ON q.id_chapitre = c.id_chapitre 
        LEFT JOIN matieres m ON c.id_matiere = m.id_matiere 
        WHERE q.id_createur = ?
        ORDER BY q.id_quiz DESC
    ");
    $stmt->execute([$user_id]);
}
$quizzes = $stmt->fetchAll();

$title = "Gestion des Quiz";
include '../includes/header.php';
include '../includes/navbar.php';
?>

<div class="container page-wrapper mt-5">
    
    <?php if (isset($_GET['msg'])): ?>
        <div class="animate-in">
            <?php if ($_GET['msg'] == 'success_delete'): ?>
                <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-4 mb-4" role="alert">
                    <i class="bi bi-trash-fill me-2"></i><strong>Supprimé !</strong> Le quiz a été retiré avec succès.
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php elseif ($_GET['msg'] == 'updated'): ?>
                <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-4 mb-4" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i><strong>Mis à jour !</strong> Modifications enregistrées.
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php elseif ($_GET['msg'] == 'denied'): ?>
                <div class="alert alert-warning alert-dismissible fade show border-0 shadow-sm rounded-4 mb-4" role="alert">
                    <i class="bi bi-exclamation-octagon me-2"></i><strong>Accès refusé !</strong> Vous n'avez pas le droit de modifier ce quiz.
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-5 gap-3">
        <div>
            <h2 class="fw-800 text-dark mb-1">⚙️ Gestion des Quiz</h2>
            <p class="text-muted mb-0">Pilotez vos questionnaires et suivez vos contenus.</p>
        </div>
        <a href="create_quiz.php" class="btn btn-primary rounded-pill px-4 py-2 shadow-sm fw-bold">
            <i class="bi bi-plus-lg me-2"></i> Créer un nouveau quiz
        </a>
    </div>

    <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr class="text-muted small fw-bold">
                        <th class="ps-4 py-3">ID</th>
                        <th class="py-3">QUIZ / DISCIPLINE</th>
                        <th class="py-3 text-center">TEMPS</th>
                        <th class="py-3 text-end pe-4">ACTIONS</th>
                    </tr>
                </thead>
                <tbody class="border-top-0">
                    <?php if (count($quizzes) > 0): ?>
                        <?php foreach ($quizzes as $quiz): ?>
                            <tr>
                                <td class="ps-4">
                                    <span class="text-secondary small fw-bold">#<?= $quiz['id_quiz'] ?></span>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark"><?= e($quiz['titre']) ?></div>
                                    <span class="badge bg-light text-primary border-0 small px-0">
                                        <i class="bi bi-tag me-1"></i><?= $quiz['nom_matiere'] ? e($quiz['nom_matiere']) : 'Général' ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="badge rounded-pill bg-info bg-opacity-10 text-info px-3 py-2">
                                        <i class="bi bi-clock me-1"></i> <?= round($quiz['temps_limite'] / 60) ?> min
                                    </span>
                                </td>
                                <td class="text-end pe-4">
                                    <div class="btn-group shadow-sm rounded-pill overflow-hidden border">
                                        <a href="modifier_quiz.php?id=<?= $quiz['id_quiz'] ?>" 
                                           class="btn btn-white btn-sm px-3" 
                                           title="Paramètres du quiz">
                                            <i class="bi bi-gear text-primary"></i>
                                        </a>
                                        <a href="create_quiz.php?id_quiz=<?= $quiz['id_quiz'] ?>" 
                                           class="btn btn-white btn-sm px-3" 
                                           title="Gérer les questions">
                                            <i class="bi bi-list-check text-success"></i>
                                        </a>
                                        <a href="delete_quiz.php?id=<?= $quiz['id_quiz'] ?>" 
                                           class="btn btn-white btn-sm px-3" 
                                           onclick="return confirm('⚠️ Action irréversible ! Supprimer ce quiz et ses scores ?');"
                                           title="Supprimer">
                                            <i class="bi bi-trash3 text-danger"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="text-center py-5 text-muted">
                                <i class="bi bi-folder2-open fs-1 d-block mb-2"></i>
                                Aucun quiz trouvé. Commencez par en créer un !
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
    .fw-800 { font-weight: 800; }
    .table-hover tbody tr:hover { background-color: #fbfbfe; }
    .btn-white { background-color: #fff; border: none; }
    .btn-white:hover { background-color: #f8f9fa; }
    .btn-group .btn { border-right: 1px solid #eee; }
    .btn-group .btn:last-child { border-right: none; }
    .animate-in { animation: fadeIn 0.5s ease-in; }
    @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
</style>

<?php include '../includes/footer.php'; ?>