<?php
require_once '../config.php';
redirect_if_not_logged_in();

if (!has_role('enseignant') && !has_role('admin')) {
    header('Location: dashbord.php');
    exit();
}

$stmt = $pdo->query("
    SELECT q.id_question, q.enonce, qz.titre as quiz_titre, qz.id_quiz
    FROM questions q
    JOIN quiz qz ON q.id_quiz = qz.id_quiz
    ORDER BY qz.id_quiz DESC, q.id_question ASC
");
$questions = $stmt->fetchAll();

$title = "Gestion des Questions";
include '../includes/header.php';
include '../includes/navbar.php';
?>

<div class="container page-wrapper mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold text-primary">Liste des Questions</h2>
        <a href="create_quiz.php" class="btn btn-primary rounded-pill">
            <i class="bi bi-plus-lg"></i> Nouvelle Question
        </a>
    </div>

    <div class="card shadow-sm border-0 rounded-4 overflow-hidden fade-in">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr class="text-muted small fw-bold text-uppercase">
                        <th class="ps-4 py-3">Quiz Parent</th>
                        <th class="py-3">Énoncé de la question</th>
                        <th class="py-3 text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($questions)): ?>
                        <tr>
                            <td colspan="3" class="text-center py-5 text-muted">
                                <i class="bi bi-folder-x display-4 d-block mb-3"></i>
                                Aucune question trouvée dans la base de données.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($questions as $q): ?>
                            <tr>
                                <td class="ps-4">
                                    <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2">
                                        <i class="bi bi-journal-text me-1"></i>
                                        <?= e($q['quiz_titre']) ?>
                                    </span>
                                </td>
                                <td class="fw-semibold text-dark">
                                    <?= e($q['enonce']) ?>
                                </td>
                                <td class="text-end pe-4">
                                    <div class="btn-group shadow-sm rounded-pill border bg-white">
                                        <a href="edit_question.php?id=<?= $q['id_question'] ?>" 
                                           class="btn btn-link btn-sm text-primary border-end px-3" 
                                           title="Modifier">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                        <a href="../actions/supprimer_question.php?id=<?= $q['id_question'] ?>&id_quiz=<?= $q['id_quiz'] ?>" 
                                           class="btn btn-link btn-sm text-danger px-3" 
                                           onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette question ainsi que ses réponses ?');"
                                           title="Supprimer">
                                            <i class="bi bi-trash3-fill"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>