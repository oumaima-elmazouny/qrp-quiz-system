<?php
require_once '../config.php';
redirect_if_not_logged_in();

$id_question = $_GET['id'] ?? null;
if (!$id_question) { header('Location: gerer_quiz.php'); exit(); }

$stmt = $pdo->prepare("SELECT q.*, qz.titre, qz.id_createur FROM questions q JOIN quiz qz ON q.id_quiz = qz.id_quiz WHERE q.id_question = ?");
$stmt->execute([$id_question]);
$question = $stmt->fetch();

if (!$question) { header('Location: gerer_quiz.php'); exit(); }

if (!has_role('admin') && $question['id_createur'] != $_SESSION['user_id']) {
    header('Location: gerer_quiz.php?msg=denied');
    exit();
}

$stmtR = $pdo->prepare("SELECT * FROM reponses WHERE id_question = ?");
$stmtR->execute([$id_question]);
$reponses = $stmtR->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $pdo->beginTransaction();

        $img_q = $_POST['old_image_question'];
        if (!empty($_FILES['image_question']['name'])) {
            $img_q = "q_" . time() . "_" . basename($_FILES['image_question']['name']);
            move_uploaded_file($_FILES['image_question']['tmp_name'], "../images/" . $img_q);
        }

        $stmtU = $pdo->prepare("UPDATE questions SET texte_question = ?, type_question = ?, image_path = ? WHERE id_question = ?");
        $stmtU->execute([$_POST['texte_question'], $_POST['type_question'], $img_q, $id_question]);

        $pdo->prepare("DELETE FROM reponses WHERE id_question = ?")->execute([$id_question]);

        if (isset($_POST['reponses'])) {
            foreach ($_POST['reponses'] as $index => $texte) {
                if (empty(trim($texte))) continue;
                
                $img_r = $_POST['old_img_reponses'][$index] ?? null;
                
                if (isset($_FILES['img_reponses']['name'][$index]) && !empty($_FILES['img_reponses']['name'][$index])) {
                    $img_r = "rep_" . time() . "_" . $index . "_" . basename($_FILES['img_reponses']['name'][$index]);
                    move_uploaded_file($_FILES['img_reponses']['tmp_name'][$index], "../images/" . $img_r);
                }

                $is_correct = (isset($_POST['correctes']) && in_array($index, (array)$_POST['correctes'])) ? 1 : 0;
                
                $stmtIns = $pdo->prepare("INSERT INTO reponses (texte_reponse, is_correct, id_question, image_reponse) VALUES (?, ?, ?, ?)");
                $stmtIns->execute([$texte, $is_correct, $id_question, $img_r]);
            }
        }

        $pdo->commit();
        header("Location: gerer_quiz.php?msg=updated");
        exit();
    } catch (Exception $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        die("Erreur : " . $e->getMessage());
    }
}

$title = "Modifier la Question";
include '../includes/header.php';
include '../includes/navbar.php';
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="mb-4">
                <a href="gerer_quiz.php" class="btn btn-sm btn-light rounded-pill">
                    <i class="bi bi-arrow-left"></i> Retour à la gestion
                </a>
                <h2 class="fw-bold mt-3">Modifier la Question</h2>
                <p class="text-muted">Quiz : <?= e($question['titre']) ?></p>
            </div>

            <form method="POST" enctype="multipart/form-data" class="card shadow-sm border-0 rounded-4">
                <div class="card-body p-4">
                    <div class="mb-4">
                        <label class="form-label fw-bold">Question</label>
                        <textarea name="texte_question" class="form-control rounded-3" rows="3" required><?= e($question['texte_question']) ?></textarea>
                        
                        <div class="mt-3 p-3 bg-light rounded-3">
                            <label class="form-label small fw-bold">Image actuelle</label>
                            <div class="d-flex align-items-center gap-3">
                                <?php if ($question['image_path']): ?>
                                    <img src="../images/<?= $question['image_path'] ?>" class="rounded" style="width: 50px; height: 50px; object-fit: cover;">
                                <?php endif; ?>
                                <input type="file" name="image_question" class="form-control form-control-sm">
                            </div>
                            <input type="hidden" name="old_image_question" value="<?= $question['image_path'] ?>">
                        </div>
                    </div>

                    <input type="hidden" name="type_question" id="type_question" value="<?= $question['type_question'] ?>">

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="fw-bold mb-0">Réponses</h5>
                        <button type="button" class="btn btn-sm btn-outline-primary rounded-pill" onclick="addNewReponse()">
                            + Ajouter une option
                        </button>
                    </div>

                    <div id="reponses_container">
                        <?php foreach ($reponses as $index => $rep): ?>
                        <div class="reponse-item card p-3 mb-3 border-light shadow-sm">
                            <div class="row align-items-center g-2">
                                <div class="col-auto">
                                    <input type="<?= ($question['type_question'] == 'multiple') ? 'checkbox' : 'radio' ?>" 
                                           name="correctes[]" value="<?= $index ?>" 
                                           class="form-check-input" 
                                           <?= $rep['is_correct'] ? 'checked' : '' ?>>
                                </div>
                                <div class="col">
                                    <input type="text" name="reponses[<?= $index ?>]" class="form-control form-control-sm mb-1" value="<?= e($rep['texte_reponse']) ?>" required>
                                    <div class="d-flex align-items-center gap-2">
                                        <input type="hidden" name="old_img_reponses[<?= $index ?>]" value="<?= $rep['image_reponse'] ?>">
                                        <input type="file" name="img_reponses[<?= $index ?>]" class="form-control form-control-sm border-0 bg-light" style="font-size: 0.7rem;">
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <button type="button" class="btn btn-link text-danger p-0" onclick="this.closest('.reponse-item').remove()">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="card-footer bg-white p-4 d-flex justify-content-end gap-2">
                    <button type="submit" class="btn btn-primary px-4 fw-bold">Enregistrer les modifications</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
let count = <?= count($reponses) ?>;
function addNewReponse() {
    const container = document.getElementById('reponses_container');
    const type = (document.getElementById('type_question').value === 'multiple') ? 'checkbox' : 'radio';
    const div = document.createElement('div');
    div.className = "reponse-item card p-3 mb-3 border-primary shadow-sm animate-in";
    div.innerHTML = `
        <div class="row align-items-center g-2">
            <div class="col-auto">
                <input type="${type}" name="correctes[]" value="${count}" class="form-check-input">
            </div>
            <div class="col">
                <input type="text" name="reponses[${count}]" class="form-control form-control-sm mb-1" placeholder="Réponse..." required>
                <input type="file" name="img_reponses[${count}]" class="form-control form-control-sm border-0 bg-light" style="font-size: 0.7rem;">
            </div>
            <div class="col-auto">
                <button type="button" class="btn btn-link text-danger p-0" onclick="this.closest('.reponse-item').remove()"><i class="bi bi-trash"></i></button>
            </div>
        </div>`;
    container.appendChild(div);
    count++;
}
</script>

<style>
    .reponse-item { border-left: 4px solid #dee2e6 !important; }
    .reponse-item:has(.form-check-input:checked) { border-left-color: #10b981 !important; background-color: #f0fdf4 !important; }
    .animate-in { animation: slideIn 0.3s ease-out; }
    @keyframes slideIn { from { opacity: 0; transform: translateX(-10px); } to { opacity: 1; transform: translateX(0); } }
</style>

<?php include '../includes/footer.php'; ?>