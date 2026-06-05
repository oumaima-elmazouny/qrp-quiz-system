<?php
require_once '../config.php';
redirect_if_not_logged_in();

if (!has_role('enseignant') && !has_role('admin')) {
    header('Location: dashbord.php');
    exit();
}

$message = "";
$current_quiz_id = $_SESSION['current_quiz_id'] ?? null;

if (isset($_GET['action']) && $_GET['action'] === 'finish_now') {
    unset($_SESSION['current_quiz_id'], $_SESSION['current_quiz_titre']);
    header('Location: gerer_quiz.php?success=quiz_cree');
    exit();
} 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $pdo->beginTransaction();

        if (!$current_quiz_id) {
            $stmt = $pdo->prepare("INSERT INTO quiz (titre, description, temps_limite, id_chapitre, id_createur) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([
                trim($_POST['titre']), 
                trim($_POST['description'] ?? ''), 
                intval($_POST['temps']) * 60, 
                intval($_POST['id_chapitre']), 
                $_SESSION['user_id']
            ]);
            $current_quiz_id = $pdo->lastInsertId();
            $_SESSION['current_quiz_id'] = $current_quiz_id;
            $_SESSION['current_quiz_titre'] = $_POST['titre'];
        }

        $img_q = null;
        if (!empty($_FILES['image_question']['name'])) {
            $img_q = "q_" . time() . "_" . basename($_FILES['image_question']['name']);
            if (!is_dir("../images")) mkdir("../images", 0777, true);
            move_uploaded_file($_FILES['image_question']['tmp_name'], "../images/" . $img_q);
        }

        $stmtQ = $pdo->prepare("INSERT INTO questions (id_quiz, texte_question, image_path, type_question) VALUES (?, ?, ?, ?)");
        $stmtQ->execute([$current_quiz_id, trim($_POST['texte_question']), $img_q, $_POST['type_question']]);
        $id_question = $pdo->lastInsertId();

        if (isset($_POST['reponses'])) {
            foreach ($_POST['reponses'] as $index => $texte) {
                $img_r = null;
                if (!empty($_FILES['img_reponses']['name'][$index])) {
                    $img_r = "rep_" . time() . "_" . basename($_FILES['img_reponses']['name'][$index]);
                    move_uploaded_file($_FILES['img_reponses']['tmp_name'][$index], "../images/" . $img_r);
                }

                $is_correct = 0;
                if ($_POST['type_question'] === 'unique') {
                    if (isset($_POST['correcte_unique']) && $_POST['correcte_unique'] == $index) $is_correct = 1;
                } else {
                    if (isset($_POST['correctes']) && in_array($index, $_POST['correctes'])) $is_correct = 1;
                }

                if (!empty(trim($texte)) || !empty($img_r)) {
                    $stmtR = $pdo->prepare("INSERT INTO reponses (id_question, texte_reponse, is_correct, image_reponse) VALUES (?, ?, ?, ?)");
                    $stmtR->execute([$id_question, trim($texte), $is_correct, $img_r]);
                }
            }
        }

        $pdo->commit();
        $message = "<div class='alert alert-success shadow-sm'>Question ajoutée au quiz !</div>";
    } catch (Exception $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        $message = "<div class='alert alert-danger'>Erreur : " . $e->getMessage() . "</div>";
    }
}

$title = "Création de Quiz";
include '../includes/header.php';
include '../includes/navbar.php';
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-9">
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="card-header bg-primary py-3 text-white">
                    <h3 class="mb-0 text-center fw-bold"><?= $current_quiz_id ? "➕ Ajouter une Question" : "🚀 Créer un nouveau Quiz" ?></h3>
                </div>
                <div class="card-body p-5">
                    <?= $message ?>

                    <form method="POST" enctype="multipart/form-data" id="quizForm">
                        
                        <?php if (!$current_quiz_id): ?>
                            <div class="row g-3 mb-4">
                                <div class="col-12">
                                    <label class="form-label fw-bold">Titre du Quiz</label>
                                    <input type="text" name="titre" class="form-control" required placeholder="Ex: Algorithmique Avancée">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Matière</label>
                                    <select id="matiere_select" class="form-select" required>
                                        <option value="">-- Choisir Matière --</option>
                                        <?php 
                                        $mats = $pdo->query("SELECT * FROM matieres")->fetchAll();
                                        foreach($mats as $m) echo "<option value='{$m['id_matiere']}'>{$m['nom_matiere']}</option>";
                                        ?>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Chapitre</label>
                                    <select name="id_chapitre" id="chapitre_select" class="form-select" required disabled>
                                        <option value="">Sélectionnez d'abord la matière</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Temps (min)</label>
                                    <input type="number" name="temps" class="form-control" value="15" required>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-info d-flex justify-content-between align-items-center mb-4">
                                <span>Quiz en cours : <strong><?= e($_SESSION['current_quiz_titre']) ?></strong></span>
                                <span class="badge bg-primary">ID: #<?= $current_quiz_id ?></span>
                            </div>
                        <?php endif; ?>

                        <div class="bg-light p-4 rounded-4 border">
                            <h4 class="mb-4 text-primary fw-bold">Détails de la Question</h4>
                            
                            <div class="mb-3">
                                <label class="form-label fw-bold">Énoncé de la question</label>
                                <textarea name="texte_question" class="form-control" rows="3" placeholder="Écrivez votre question ici..." required></textarea>
                            </div>

                            <div class="row mb-4">
                                <div class="col-md-7">
                                    <label class="form-label fw-bold"><i class="bi bi-image me-2"></i>Image d'illustration (optionnel)</label>
                                    <input type="file" name="image_question" class="form-control" accept="image/*">
                                </div>
                                <div class="col-md-5">
                                    <label class="form-label fw-bold">Type de réponse</label>
                                    <select name="type_question" id="type_question" class="form-select bg-white">
                                        <option value="unique">Choix Unique (Radio)</option>
                                        <option value="multiple">Choix Multiple (Checkbox)</option>
                                    </select>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="mb-0 fw-bold text-dark">Options de Réponses</h5>
                                <button type="button" class="btn btn-outline-primary btn-sm rounded-pill" onclick="addReponse()">
                                    <i class="bi bi-plus-lg"></i> Ajouter un choix
                                </button>
                            </div>

                            <div id="reponses_container">
                                </div>
                        </div>

                        <div class="mt-5 d-flex justify-content-between align-items-center">
                            <button type="reset" class="btn btn-light rounded-pill px-4">Vider</button>
                            
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary rounded-pill px-5 shadow">
                                    Enregistrer la Question
                                </button>
                                
                                <?php if ($current_quiz_id): ?>
                                    <a href="?action=finish_now" class="btn btn-success rounded-pill px-5 shadow" onclick="return confirm('Finaliser et publier ce quiz ?')">
                                        TERMINER LE QUIZ
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let reponseCount = 0;

function addReponse() {
    const typeQ = document.getElementById('type_question').value;
    const inputType = (typeQ === 'unique') ? 'radio' : 'checkbox';
    const inputName = (typeQ === 'unique') ? 'correcte_unique' : 'correctes[]';
    
    const container = document.getElementById('reponses_container');
    const div = document.createElement('div');
    div.className = "row g-2 mb-3 align-items-center bg-white p-3 rounded shadow-sm border";
    div.id = `rep_row_${reponseCount}`;
    
    div.innerHTML = `
        <div class="col-auto">
            <div class="text-center">
                <input class="form-check-input" type="${inputType}" name="${inputName}" value="${reponseCount}">
                <label class="d-block small text-muted mt-1">Correct</label>
            </div>
        </div>
        <div class="col">
            <input type="text" name="reponses[]" class="form-control" placeholder="Texte de la réponse" required>
        </div>
        <div class="col-md-4">
            <input type="file" name="img_reponses[${reponseCount}]" class="form-control form-control-sm" accept="image/*">
        </div>
        <div class="col-auto">
            <button type="button" class="btn btn-outline-danger btn-sm" onclick="removeReponse(${reponseCount})">
                <i class="bi bi-trash"></i>
            </button>
        </div>
    `;
    container.appendChild(div);
    reponseCount++;
}

function removeReponse(id) {
    const row = document.getElementById(`rep_row_${id}`);
    if (row) row.remove();
}

document.getElementById('type_question').addEventListener('change', function() {
    const type = this.value;
    const inputs = document.querySelectorAll('#reponses_container input[type="radio"], #reponses_container input[type="checkbox"]');
    
    inputs.forEach(input => {
        if (type === 'unique') {
            input.type = 'radio';
            input.name = 'correcte_unique';
        } else {
            input.type = 'checkbox';
            input.name = 'correctes[]';
        }
    });
});

document.getElementById('matiere_select')?.addEventListener('change', function () {
    const id = this.value;
    const select = document.getElementById('chapitre_select');
    if (!id) {
        select.disabled = true;
        return;
    }
    select.disabled = false;
    select.innerHTML = '<option>Chargement...</option>';
    fetch('../actions/get_chapitres.php?id_matiere=' + id)
        .then(r => r.json())
        .then(data => {
            select.innerHTML = '<option value="">-- Choisir Chapitre --</option>';
            data.forEach(ch => {
                let o = document.createElement('option');
                o.value = ch.id_chapitre;
                o.textContent = ch.nom_chapitre;
                select.appendChild(o);
            });
        });
});

window.onload = () => { addReponse(); addReponse(); };
</script>

<?php include '../includes/footer.php'; ?>