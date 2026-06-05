<?php
require_once '../config.php';
redirect_if_not_logged_in();

if ($_SESSION['user_role'] !== 'etudiant' && $_SESSION['user_role'] !== 'admin') {
    header('Location: dashbord.php');
    exit();
}

$id_quiz = isset($_GET['id']) ? intval($_GET['id']) : 0;

$stmt = $pdo->prepare("SELECT * FROM quiz WHERE id_quiz = ?");
$stmt->execute([$id_quiz]);
$quiz = $stmt->fetch();

if (!$quiz) { 
    die("Quiz non trouvé."); 
}

$stmt = $pdo->prepare("SELECT * FROM questions WHERE id_quiz = ?");
$stmt->execute([$id_quiz]);
$questions = $stmt->fetchAll();

$title = "Passage : " . e($quiz['titre']);
include '../includes/header.php';
include '../includes/navbar.php';
?>

<style>
    body { background-color: #f4f7fe; }
    .quiz-header { 
        background: rgba(255, 255, 255, 0.9); 
        backdrop-filter: blur(10px);
        padding: 20px; 
        border-radius: 15px; 
        box-shadow: 0 4px 15px rgba(0,0,0,0.05); 
        position: sticky; 
        top: 80px; 
        z-index: 1000; 
    }
    .question-card { 
        background: white; 
        border-radius: 20px; 
        border: none; 
        box-shadow: 0 5px 15px rgba(0,0,0,0.04); 
        margin-bottom: 30px; 
        padding: 30px; 
        transition: transform 0.3s ease;
    }
    .question-number { 
        background: #4f46e5; 
        color: white; 
        width: 35px; height: 35px; 
        display: inline-flex; 
        align-items: center; justify-content: center; 
        border-radius: 10px; 
        margin-right: 12px; 
        font-weight: bold;
    }
    .reponse-option { 
        border: 2px solid #f0f3f9; 
        border-radius: 15px; 
        padding: 18px; 
        transition: all 0.2s ease; 
        cursor: pointer; 
        display: flex; 
        align-items: center;
        height: 100%;
        font-weight: 500;
    }
    .reponse-option:hover { 
        border-color: #4f46e5; 
        background: #f8fbff; 
    }
    
    .btn-check:checked + .reponse-option { 
        border-color: #4f46e5; 
        background-color: #f0f0ff; 
        box-shadow: 0 4px 12px rgba(79, 70, 229, 0.1);
    }

    .timer-badge { 
        font-weight: 800; 
        font-size: 1.2rem; 
        padding: 10px 20px !important;
        border-radius: 12px !important;
    }
    .timer-low { 
        color: #ef4444 !important; 
        border-color: #ef4444 !important; 
        animation: blink 1s infinite; 
    }
    @keyframes blink { 50% { opacity: 0.5; } }
    .animate-in { animation: fadeInUp 0.5s ease-out forwards; }
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>

<div class="container py-4" style="max-width: 900px;">
    
    <div class="quiz-header d-flex justify-content-between align-items-center mb-5">
        <div>
            <h4 class="mb-0 fw-800 text-dark"><?= e($quiz['titre']) ?></h4>
            <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill">
                <?= count($questions) ?> Questions
            </span>
        </div>
        <div class="text-end">
            <div class="badge bg-white text-dark border p-2 shadow-sm timer-badge">
                <i class="bi bi-clock-fill me-2 text-primary"></i>
                <span id="timer-display">--:--</span>
            </div>
        </div>
    </div>

    <?php if (count($questions) === 0): ?>
        <div class="card border-0 shadow-sm rounded-4 p-5 text-center">
            <i class="bi bi-emoji-neutral display-1 text-muted mb-3"></i>
            <h4 class="fw-bold">Ce quiz est vide</h4>
            <p class="text-muted">L'enseignant n'a pas encore ajouté de questions.</p>
            <a href="liste_quiz.php" class="btn btn-primary rounded-pill px-4">Retourner à la liste</a>
        </div>
    <?php else: ?>

    <form id="quiz-form" action="../actions/submit_quiz.php" method="POST">
        <input type="hidden" name="quiz_id" value="<?= $id_quiz ?>">

        <?php foreach ($questions as $index => $q): ?>
            <div class="question-card animate-in" style="animation-delay: <?= $index * 0.1 ?>s">
                <h5 class="fw-bold text-dark mb-4">
                    <span class="question-number"><?= $index + 1 ?></span>
                    <?= e($q['texte_question'] ?? $q['enonce']) ?>
                </h5>

                <?php if (!empty($q['image_path'])): ?>
                    <div class="mb-4 text-center">
                        <img src="../images/<?= e($q['image_path']) ?>" class="img-fluid rounded-4 shadow-sm border" style="max-height: 300px;">
                    </div>
                <?php endif; ?>

                <?php
                $stmtR = $pdo->prepare("SELECT * FROM reponses WHERE id_question = ?");
                $stmtR->execute([$q['id_question']]);
                $reponses = $stmtR->fetchAll();
                $inputType = ($q['type_question'] === 'multiple') ? 'checkbox' : 'radio';
                ?>

                <div class="row g-3">
                    <?php foreach ($reponses as $r): ?>
                        <div class="col-md-6">
                            <input type="<?= $inputType ?>" 
                                   class="btn-check" 
                                   name="reponse[<?= $q['id_question'] ?>][]" 
                                   id="rep_<?= $r['id_reponse'] ?>" 
                                   value="<?= $r['id_reponse'] ?>" 
                                   <?= ($inputType === 'radio') ? 'required' : '' ?>>
                            
                            <label class="reponse-option h-100" for="rep_<?= $r['id_reponse'] ?>">
                                <?php if (!empty($r['image_reponse'])): ?>
                                    <img src="../images/<?= e($r['image_reponse']) ?>" class="rounded me-3" style="width: 50px; height: 50px; object-fit: cover;">
                                <?php endif; ?>
                                <span><?= e($r['texte_reponse']) ?></span>
                            </label>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>

        <div class="text-center mt-5 mb-5">
            <button type="submit" class="btn btn-primary btn-lg px-5 rounded-pill shadow-lg border-0 py-3 fw-800">
                <i class="bi bi-check-all me-2 fs-4"></i> Envoyer mes réponses
            </button>
        </div>
    </form>

    <?php endif; ?>
</div>

<script>
function initQuizTimer(totalSeconds) {
    let timer = totalSeconds;
    const display = document.querySelector('#timer-display');
    const badge = document.querySelector('.timer-badge');
    const form = document.getElementById('quiz-form');

    const countdown = setInterval(function () {
        let minutes = parseInt(timer / 60, 10);
        let seconds = parseInt(timer % 60, 10);

        display.textContent = (minutes < 10 ? "0" + minutes : minutes) + ":" + (seconds < 10 ? "0" + seconds : seconds);

        if (timer < 60) badge.classList.add('timer-low');

        if (--timer < 0) {
            clearInterval(countdown);
            const inputs = form.querySelectorAll('input[required]');
            inputs.forEach(input => input.removeAttribute('required'));
            
            alert("Temps écoulé ! Vos réponses vont être envoyées automatiquement.");
            form.submit();
        }
    }, 1000);
}

const timeInMinutes = <?= intval($quiz['temps_limite']) ?>;
if(timeInMinutes > 0) {
    initQuizTimer(timeInMinutes * 60); 
} else {
    document.querySelector('#timer-display').textContent = "Illimité";
}
</script>

<?php include '../includes/footer.php'; ?>