<?php
require_once '../config.php';
redirect_if_not_logged_in();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../pages/dashbord.php');
    exit();
}
  
$quiz_id = intval($_POST['quiz_id'] ?? 0);
$user_id = $_SESSION['user_id'];

if ($quiz_id <= 0) {
    header('Location: ../pages/dashbord.php');
    exit();
}

try {
    $stmt = $pdo->prepare("
        SELECT q.id_question, r.id_reponse 
        FROM questions q
        JOIN reponses r ON q.id_question = r.id_question
        WHERE q.id_quiz = ? AND r.is_correct = 1
    ");
    $stmt->execute([$quiz_id]);
    $results = $stmt->fetchAll(PDO::FETCH_GROUP); 

    $reponses_justes = 0;
    $total_questions = count($results);

    foreach ($results as $question_id => $correct_reponses) {
        $correct_ids = array_column($correct_reponses, 'id_reponse');
        $user_answers = $_POST['reponse'][$question_id] ?? [];
        
        if (!is_array($user_answers)) { $user_answers = [$user_answers]; }

        $user_answers = array_map('intval', $user_answers);
        $correct_ids = array_map('intval', $correct_ids);
        
        sort($correct_ids);
        sort($user_answers);

        if (!empty($user_answers) && $user_answers == $correct_ids) {
            $reponses_justes++;
        }
    }

    if ($total_questions > 0) {
        $score_sur_20 = ($reponses_justes / $total_questions) * 20;
    } else {
        $score_sur_20 = 0;
    }
    $score_sur_20 = round($score_sur_20, 2); 

    $stmtInsert = $pdo->prepare("INSERT INTO scores (id_user, id_quiz, score, date_tentative) VALUES (?, ?, ?, NOW())");
    $stmtInsert->execute([$user_id, $quiz_id, $score_sur_20]);

    $stmtTitle = $pdo->prepare("SELECT titre FROM quiz WHERE id_quiz = ?");
    $stmtTitle->execute([$quiz_id]);
    $quiz_info = $stmtTitle->fetch();

    $_SESSION['dernier_score'] = $score_sur_20; 
    $_SESSION['reponses_justes'] = $reponses_justes;
    $_SESSION['total_questions'] = $total_questions;
    $_SESSION['latest_quiz_title'] = $quiz_info['titre'] ?? 'Quiz';

    header('Location: ../pages/resultat_quiz.php');
    exit();

} catch (PDOException $e) {
    die("Erreur : " . $e->getMessage());
}