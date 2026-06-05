<?php
require_once '../config.php';

if (isset($_GET['id_matiere'])) {
    $id_matiere = intval($_GET['id_matiere']);
    $stmt = $pdo->prepare("SELECT id_chapitre, nom_chapitre FROM chapitres WHERE id_matiere = ?");
    $stmt->execute([$id_matiere]);
    $chapitres = $stmt->fetchAll(PDO::FETCH_ASSOC);

    header('Content-Type: application/json');
    echo json_encode($chapitres);
    exit();
}