<?php
require_once '../config.php';
redirect_if_not_logged_in();

if (isset($_GET['id']) && (has_role('admin') || has_role('enseignant'))) {
    $id = intval($_GET['id']);
    $user_id = $_SESSION['user_id'];
    
    try {
        if (!has_role('admin')) {
            $check = $pdo->prepare("SELECT id_createur FROM quiz WHERE id_quiz = ?");
            $check->execute([$id]);
            $quiz = $check->fetch();
            
            if (!$quiz || $quiz['id_createur'] != $user_id) {
                header('Location: gerer_quiz.php?msg=denied');
                exit();
            }
        }

        $stmtImg = $pdo->prepare("SELECT image_path FROM questions WHERE id_quiz = ? AND image_path IS NOT NULL");
        $stmtImg->execute([$id]);
        $images = $stmtImg->fetchAll(PDO::FETCH_COLUMN);

        $pdo->beginTransaction();

        $stmt1 = $pdo->prepare("DELETE FROM reponses WHERE id_question IN (SELECT id_question FROM questions WHERE id_quiz = ?)");
        $stmt1->execute([$id]);

        $stmt2 = $pdo->prepare("DELETE FROM questions WHERE id_quiz = ?");
        $stmt2->execute([$id]);

        $stmt3 = $pdo->prepare("DELETE FROM tentatives WHERE id_quiz = ?");
        $stmt3->execute([$id]);

        $stmt4 = $pdo->prepare("DELETE FROM quiz WHERE id_quiz = ?");
        $stmt4->execute([$id]);

        $pdo->commit();

        foreach ($images as $img) {
            $file_path = "../images/" . $img;
            if (file_exists($file_path)) {
                unlink($file_path);
            }
        }
        
        header('Location: gerer_quiz.php?msg=success_delete');
        exit();
        
    } catch (PDOException $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        header('Location: gerer_quiz.php?msg=error_delete');
        exit();
    }
}

header('Location: gerer_quiz.php');
exit();