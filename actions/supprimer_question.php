<?php
require_once '../config.php';
redirect_if_not_logged_in();

if (!has_role('enseignant') && !has_role('admin')) {
    header('Location: ../pages/dashbord.php');
    exit();
}

$id_question = isset($_GET['id']) ? intval($_GET['id']) : 0;
$id_quiz = isset($_GET['id_quiz']) ? intval($_GET['id_quiz']) : 0;

if ($id_question > 0) {
    try {
        $pdo->beginTransaction();

        $stmt = $pdo->prepare("SELECT image_path FROM questions WHERE id_question = ?");
        $stmt->execute([$id_question]);
        $question = $stmt->fetch();

        if ($question && !empty($question['image_path'])) {
            $file_path = "../images/" . $question['image_path'];
            if (file_exists($file_path)) {
                unlink($file_path);
            }
        }

        $stmtR = $pdo->prepare("DELETE FROM reponses WHERE id_question = ?");
        $stmtR->execute([$id_question]);

        $stmtQ = $pdo->prepare("DELETE FROM questions WHERE id_question = ?");
        $stmtQ->execute([$id_question]);

        $pdo->commit();
        
        header("Location: ../pages/gerer_quiz.php?msg=deleted");
        exit();

    } catch (Exception $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        die("Erreur lors de la suppression : " . $e->getMessage());
    }
} else {
    header("Location: ../pages/gerer_quiz.php");
    exit();
}