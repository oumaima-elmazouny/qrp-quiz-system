<?php
require_once '../config.php';
redirect_if_not_logged_in();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: dashbord.php');
    exit();
}

$id_quiz = intval($_POST['quiz_id'] ?? $_POST['id_quiz'] ?? 0);
$id_user = $_SESSION['user_id'];

if ($id_quiz <= 0) {
    header('Location: dashbord.php');
    exit();
}

try {
    $stmt = $pdo->prepare("
        SELECT q.id_question, r.id_reponse 
        FROM questions q 
        JOIN reponses r ON q.id_question = r.id_question 
        WHERE q.id_quiz = ? AND r.is_correct = 1
    ");
    $stmt->execute([$id_quiz]);
    
    $reponses_attendues = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $reponses_attendues[$row['id_question']][] = $row['id_reponse'];
    }

    $score = 0;
    $reponses_envoyees = $_POST['reponse'] ?? []; 

    foreach ($reponses_attendues as $id_q => $bonnes_ids) {
        if (isset($reponses_envoyees[$id_q])) {
            $choix_user = (array)$reponses_envoyees[$id_q]; 
            
            
            sort($bonnes_ids);
            sort($choix_user);
            
            if ($choix_user === $bonnes_ids) {
                $score++;
            }
        }
    }

    $total_questions = count($reponses_attendues);
    $score_final = ($total_questions > 0) ? round(($score / $total_questions) * 20, 2) : 0;

    $insert = $pdo->prepare("INSERT INTO tentatives (id_user, id_quiz, score, date_tentative) VALUES (?, ?, ?, NOW())");
    $insert->execute([$id_user, $id_quiz, $score_final]);

    $_SESSION['dernier_score'] = $score_final;
    $_SESSION['score_brut'] = $score; 
    $_SESSION['total_questions'] = $total_questions;

    header("Location: resultat_quiz.php");
    exit();

} catch (PDOException $e) {
    die("Erreur lors de la validation : " . $e->getMessage());
}