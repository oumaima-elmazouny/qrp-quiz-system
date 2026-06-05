<?php
require_once '../config.php';
redirect_if_not_logged_in();

if (!has_role('enseignant') && !has_role('admin')) {
    header('Location: dashbord.php');
    exit();
}

$id_quiz = $_GET['id'] ?? null;
if (!$id_quiz) { header('Location: gerer_quiz.php'); exit(); }

$message = "";

if (isset($_GET['delete_q'])) {
    $id_q = intval($_GET['delete_q']);
    $pdo->prepare("DELETE FROM reponses WHERE id_question = ?")->execute([$id_q]);
    $pdo->prepare("DELETE FROM questions WHERE id_question = ?")->execute([$id_q]);
    header("Location: modifier_quiz.php?id=$id_quiz&msg=q_deleted");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $pdo->beginTransaction();

        $stmt = $pdo->prepare("UPDATE quiz SET titre = ?, description = ?, temps_limite = ? WHERE id_quiz = ?");
        $stmt->execute([trim($_POST['titre']), trim($_POST['description']), intval($_POST['temps']) * 60, $id_quiz]);

        if (isset($_POST['questions'])) {
            foreach ($_POST['questions'] as $id_q => $data_q) {
                
                if (strpos($id_q, 'newq_') === 0) {
                    $stmtInsQ = $pdo->prepare("INSERT INTO questions (id_quiz, texte_question, type_question) VALUES (?, ?, ?)");
                    $stmtInsQ->execute([$id_quiz, trim($data_q['texte']), $data_q['type']]);
                    $actual_id_q = $pdo->lastInsertId();
                } else {
                    $actual_id_q = $id_q;
                    $img_q_path = $data_q['old_image'] ?? null;
                    if (!empty($_FILES['img_questions']['name'][$id_q])) {
                        $img_q_path = "q_" . time() . "_" . basename($_FILES['img_questions']['name'][$id_q]);
                        move_uploaded_file($_FILES['img_questions']['tmp_name'][$id_q], "../images/" . $img_q_path);
                    }
                    $stmtUpQ = $pdo->prepare("UPDATE questions SET texte_question = ?, image_path = ?, type_question = ? WHERE id_question = ?");
                    $stmtUpQ->execute([trim($data_q['texte']), $img_q_path, $data_q['type'], $actual_id_q]);
                }

                if (isset($data_q['reponses'])) {
                    foreach ($data_q['reponses'] as $id_r => $data_r) {
                        $img_r_path = $data_r['old_image'] ?? null;
                        if (!empty($_FILES['img_reponses']['name'][$id_q][$id_r])) {
                            $img_r_path = "rep_" . time() . "_" . basename($_FILES['img_reponses']['name'][$id_q][$id_r]);
                            move_uploaded_file($_FILES['img_reponses']['tmp_name'][$id_q][$id_r], "../images/" . $img_r_path);
                        }

                        $is_correct = 0;
                        if ($data_q['type'] === 'unique') {
                            if (isset($_POST['correctes_unique'][$id_q]) && $_POST['correctes_unique'][$id_q] == $id_r) $is_correct = 1;
                        } else {
                            if (isset($_POST['correctes_multiple'][$id_q]) && in_array($id_r, $_POST['correctes_multiple'][$id_q])) $is_correct = 1;
                        }

                        if (strpos($id_r, 'newr_') === 0) {
                            $pdo->prepare("INSERT INTO reponses (id_question, texte_reponse, is_correct, image_reponse) VALUES (?, ?, ?, ?)")
                                ->execute([$actual_id_q, trim($data_r['texte']), $is_correct, $img_r_path]);
                        } else {
                            $pdo->prepare("UPDATE reponses SET texte_reponse = ?, image_reponse = ?, is_correct = ? WHERE id_reponse = ?")
                                ->execute([trim($data_r['texte']), $img_r_path, $is_correct, $id_r]);
                        }
                    }
                }
            }
        }

        if (!empty($_POST['delete_reponses'])) {
            foreach ($_POST['delete_reponses'] as $id_rep_del) {
                $pdo->prepare("DELETE FROM reponses WHERE id_reponse = ?")->execute([$id_rep_del]);
            }
        }

        $pdo->commit();
        header("Location: modifier_quiz.php?id=$id_quiz&msg=updated");
        exit();
    } catch (Exception $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        $message = "<div class='alert alert-danger'>Erreur : " . $e->getMessage() . "</div>";
    }
}

$quiz = $pdo->prepare("SELECT * FROM quiz WHERE id_quiz = ?");
$quiz->execute([$id_quiz]);
$qInfo = $quiz->fetch();

$questions = $pdo->prepare("SELECT * FROM questions WHERE id_quiz = ?");
$questions->execute([$id_quiz]);
$all_questions = $questions->fetchAll();

$title = "Modifier Quiz";
include '../includes/header.php';
include '../includes/navbar.php';
?>

<div class="container py-5">
    <div class="card border-0 shadow-lg rounded-4 p-4">
        <h2 class="fw-bold text-primary mb-4 border-bottom pb-2">🛠 Modifier le Quiz</h2>
        
        <?php if(isset($_GET['msg'])) echo "<div class='alert alert-success'>Mise à jour réussie !</div>"; ?>
        <?= $message ?>

        <form method="POST" enctype="multipart/form-data">
            <div class="row g-3 mb-5 bg-light p-4 rounded-4 shadow-sm">
                <div class="col-md-9">
                    <label class="form-label fw-bold">Titre du Quiz</label>
                    <input type="text" name="titre" class="form-control" value="<?= e($qInfo['titre']) ?>" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold">Temps (min)</label>
                    <input type="number" name="temps" class="form-control" value="<?= $qInfo['temps_limite'] / 60 ?>">
                </div>
                <div class="col-12">
                    <label class="form-label fw-bold">Description</label>
                    <textarea name="description" class="form-control" rows="2"><?= e($qInfo['description']) ?></textarea>
                </div>
            </div>

            <div id="questions_master_container">
                <?php foreach ($all_questions as $q): ?>
                    <div class="border rounded-4 p-4 mb-5 bg-white shadow-sm position-relative">
                        <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-2">
                            <h5 class="fw-bold text-secondary mb-0">Question ID: <?= $q['id_question'] ?></h5>
                            <a href="?id=<?= $id_quiz ?>&delete_q=<?= $q['id_question'] ?>" class="btn btn-sm btn-danger rounded-pill" onclick="return confirm('Supprimer ?')">Supprimer</a>
                        </div>
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Énoncé</label>
                                <textarea name="questions[<?= $q['id_question'] ?>][texte]" class="form-control" rows="2" required><?= e($q['texte_question']) ?></textarea>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-bold">Type</label>
                                <select name="questions[<?= $q['id_question'] ?>][type]" class="form-select type-selector" data-q-id="<?= $q['id_question'] ?>">
                                    <option value="unique" <?= $q['type_question'] == 'unique' ? 'selected' : '' ?>>Unique</option>
                                    <option value="multiple" <?= $q['type_question'] == 'multiple' ? 'selected' : '' ?>>Multiple</option>
                                </select>
                            </div>
                            <div class="col-md-3 text-center">
                                <?php if ($q['image_path']): ?>
                                    <img src="../images/<?= $q['image_path'] ?>" class="img-thumbnail mb-2" style="height: 70px;">
                                <?php endif; ?>
                                <input type="file" name="img_questions[<?= $q['id_question'] ?>]" class="form-control form-control-sm">
                                <input type="hidden" name="questions[<?= $q['id_question'] ?>][old_image]" value="<?= $q['image_path'] ?>">
                            </div>
                        </div>

                        <div class="mt-4">
                            <div class="d-flex justify-content-between mb-2">
                                <h6 class="fw-bold">Réponses</h6>
                                <button type="button" class="btn btn-sm btn-outline-success" onclick="addNewReponse(<?= $q['id_question'] ?>)">+ Réponse</button>
                            </div>
                            <div id="container_q_<?= $q['id_question'] ?>">
                                <?php
                                $stmtR = $pdo->prepare("SELECT * FROM reponses WHERE id_question = ?");
                                $stmtR->execute([$q['id_question']]);
                                foreach ($stmtR->fetchAll() as $r):
                                    $inputType = ($q['type_question'] == 'unique') ? 'radio' : 'checkbox';
                                    $inputName = ($q['type_question'] == 'unique') ? "correctes_unique[{$q['id_question']}]" : "correctes_multiple[{$q['id_question']}][]";
                                ?>
                                    <div class="row g-2 mb-2 align-items-center bg-light p-2 rounded">
                                        <div class="col-auto">
                                            <input type="<?= $inputType ?>" name="<?= $inputName ?>" value="<?= $r['id_reponse'] ?>" <?= $r['is_correct'] ? 'checked' : '' ?> class="answer-check">
                                        </div>
                                        <div class="col">
                                            <input type="text" name="questions[<?= $q['id_question'] ?>][reponses][<?= $r['id_reponse'] ?>][texte]" class="form-control form-control-sm" value="<?= e($r['texte_reponse']) ?>">
                                        </div>
                                        <div class="col-md-3">
                                            <input type="file" name="img_reponses[<?= $q['id_question'] ?>][<?= $r['id_reponse'] ?>]" class="form-control form-control-sm">
                                            <input type="hidden" name="questions[<?= $q['id_question'] ?>][reponses][<?= $r['id_reponse'] ?>][old_image]" value="<?= $r['image_reponse'] ?>">
                                        </div>
                                        <div class="col-auto">
                                            <button type="button" class="btn btn-sm text-danger" onclick="deleteRep(this, <?= $r['id_reponse'] ?>)"><i class="bi bi-trash"></i></button>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div id="delete_pool"></div>

            <div class="d-flex justify-content-between mt-5">
                <button type="button" class="btn btn-dark rounded-pill px-4" onclick="addNewQuestion()">+ Ajouter une Question</button>
                <button type="submit" class="btn btn-primary rounded-pill px-5 shadow-lg fw-bold text-uppercase">Enregistrer tout le Quiz</button>
            </div>
        </form>
    </div>
</div>

<script>
let newQCount = 0;
let newRCount = 0;

function addNewQuestion() {
    const container = document.getElementById('questions_master_container');
    const qId = 'newq_' + newQCount++;
    const div = document.createElement('div');
    div.className = "border-primary border rounded-4 p-4 mb-5 bg-white shadow";
    div.innerHTML = `
        <h5 class="text-primary fw-bold mb-4">Nouvelle Question</h5>
        <div class="row g-3">
            <div class="col-md-8">
                <textarea name="questions[${qId}][texte]" class="form-control" placeholder="Énoncé..." required></textarea>
            </div>
            <div class="col-md-4">
                <select name="questions[${qId}][type]" class="form-select type-selector" data-q-id="${qId}">
                    <option value="unique">Unique</option>
                    <option value="multiple">Multiple</option>
                </select>
            </div>
        </div>
        <div class="mt-4">
            <button type="button" class="btn btn-sm btn-outline-success mb-2" onclick="addNewReponse('${qId}')">+ Réponse</button>
            <div id="container_q_${qId}"></div>
        </div>
    `;
    container.appendChild(div);
    addNewReponse(qId);
    addNewReponse(qId);
}

function addNewReponse(qId) {
    const container = document.getElementById('container_q_' + qId);
    const rId = 'newr_' + newRCount++;
    const type = document.querySelector(`[name="questions[${qId}][type]"]`).value;
    const inputType = (type === 'unique') ? 'radio' : 'checkbox';
    const inputName = (type === 'unique') ? `correctes_unique[${qId}]` : `correctes_multiple[${qId}][]`;

    const div = document.createElement('div');
    div.className = "row g-2 mb-2 align-items-center bg-light p-2 rounded";
    div.innerHTML = `
        <div class="col-auto"><input type="${inputType}" name="${inputName}" value="${rId}" class="answer-check"></div>
        <div class="col"><input type="text" name="questions[${qId}][reponses][${rId}][texte]" class="form-control form-control-sm" placeholder="Réponse..." required></div>
        <div class="col-md-3"><input type="file" name="img_reponses[${qId}][${rId}]" class="form-control form-control-sm"></div>
        <div class="col-auto"><button type="button" class="btn btn-sm text-danger" onclick="this.closest('.row').remove()"><i class="bi bi-trash"></i></button></div>
    `;
    container.appendChild(div);
}

function deleteRep(btn, id) {
    if(confirm('Supprimer cette réponse ?')) {
        const pool = document.getElementById('delete_pool');
        const input = document.createElement('input');
        input.type = 'hidden'; input.name = 'delete_reponses[]'; input.value = id;
        pool.appendChild(input);
        btn.closest('.row').remove();
    }
}

document.addEventListener('change', function(e) {
    if (e.target.classList.contains('type-selector')) {
        const qId = e.target.getAttribute('data-q-id');
        const type = e.target.value;
        const radios = document.querySelectorAll(`#container_q_${qId} .answer-check`);
        radios.forEach(r => {
            r.type = (type === 'unique') ? 'radio' : 'checkbox';
            r.name = (type === 'unique') ? `correctes_unique[${qId}]` : `correctes_multiple[${qId}][]`;
        });
    }
});
</script>

<?php include '../includes/footer.php'; ?>