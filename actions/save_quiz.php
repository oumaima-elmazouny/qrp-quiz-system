<?php
require_once '../config.php';
redirect_if_not_logged_in();

if (!has_role('enseignant') && !has_role('admin')) {
    header('Location: ../pages/dashbord.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titre       = trim($_POST['titre'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $id_chapitre = intval($_POST['id_chapitre'] ?? 0);
    $temps_min   = intval($_POST['temps'] ?? 0);
    $id_createur = $_SESSION['user_id'];

    $enonce  = trim($_POST['question'] ?? '');
    $opt1    = trim($_POST['opt1'] ?? '');
    $opt2    = trim($_POST['opt2'] ?? '');
    $opt3    = trim($_POST['opt3'] ?? '');
    $opt4    = trim($_POST['opt4'] ?? '');
    $correct = intval($_POST['correct'] ?? 0);

    if (empty($titre) || $id_chapitre <= 0 || empty($enonce)) {
        header('Location: ../pages/create_quiz.php?msg=error_missing');
        exit();
    }

    try {
        $pdo->beginTransaction(); 

        $temps_secondes = $temps_min * 60;
        $stmtQuiz = $pdo->prepare("
            INSERT INTO quiz (titre, description, temps_limite, id_chapitre, id_createur) 
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmtQuiz->execute([$titre, $description, $temps_secondes, $id_chapitre, $id_createur]);
        
        $quiz_id = $pdo->lastInsertId();

        $stmtQuest = $pdo->prepare("INSERT INTO questions (id_quiz, enonce) VALUES (?, ?)");
        $stmtQuest->execute([$quiz_id, $enonce]);
        
        $question_id = $pdo->lastInsertId();

        $stmtRep = $pdo->prepare("INSERT INTO reponses (id_question, texte_reponse, is_correct) VALUES (?, ?, ?)");
        
        $stmtRep->execute([$question_id, $opt1, ($correct === 1 ? 1 : 0)]);
        $stmtRep->execute([$question_id, $opt2, ($correct === 2 ? 1 : 0)]);
        $stmtRep->execute([$question_id, $opt3, ($correct === 3 ? 1 : 0)]);
        $stmtRep->execute([$question_id, $opt4, ($correct === 4 ? 1 : 0)]);

        $pdo->commit(); 

        header('Location: ../pages/gerer_quiz.php?msg=updated');
        exit();

    } catch (Exception $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        die("Erreur lors de la sauvegarde : " . $e->getMessage());
    }
} else {
    header('Location: ../pages/create_quiz.php');
    exit();
}