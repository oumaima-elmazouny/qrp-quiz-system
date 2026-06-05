<?php
require_once '../config.php';


if ($_SESSION['user_role'] !== 'admin') {
    header('Location: dashbord.php');
    exit();
}

$message = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom_complet']);
    $login = trim($_POST['login']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];

    try {
        $stmt = $pdo->prepare("INSERT INTO users (nom_complet, login, password, role) VALUES (?, ?, ?, ?)");
        $stmt->execute([$nom, $login, $password, $role]);
        header('Location: gerer_users.php?success=1');
        exit();
    } catch (PDOException $e) {
        $message = "Erreur : " . $e->getMessage();
    }
}

$title = "Ajouter un Utilisateur";
include '../includes/header.php';
include '../includes/navbar.php';
?>

<div class="container py-5">
    <div class="card border-0 shadow-sm mx-auto rounded-4" style="max-width: 600px;">
        <div class="card-body p-5">
            <h2 class="fw-bold mb-4">➕ Ajouter un utilisateur</h2>
            
            <?php if ($message): ?>
                <div class="alert alert-danger"><?= $message ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Nom Complet</label>
                    <input type="text" name="nom_complet" class="form-control" placeholder="ex: Jean Dupont" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Identifiant (Login)</label>
                    <input type="text" name="login" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Mot de passe par défaut</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <div class="mb-4">
                    <label class="form-label">Rôle</label>
                    <select name="role" class="form-select">
                        <option value="etudiant">Étudiant</option>
                        <option value="enseignant">Enseignant</option>
                        <option value="admin">Administrateur</option>
                    </select>
                </div>
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary rounded-pill py-2 fw-bold">Enregistrer l'utilisateur</button>
                    <a href="gerer_users.php" class="btn btn-light rounded-pill">Annuler</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>