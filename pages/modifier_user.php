<?php
require_once '../config.php';

if ($_SESSION['user_role'] !== 'admin') {
    header('Location: dashbord.php');
    exit();
}

$user = null;
$error = null;

if (isset($_GET['id'])) {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id_user = ?");
    $stmt->execute([$_GET['id']]);
    $user = $stmt->fetch();
}

if (!$user) {
    header('Location: gerer_users.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom_complet']);
    $login = trim($_POST['login']);
    $role = $_POST['role'];
    $id = $_POST['id_user'];

    try {
        $stmt = $pdo->prepare("UPDATE users SET nom_complet = ?, login = ?, role = ? WHERE id_user = ?");
        $stmt->execute([$nom, $login, $role, $id]);
        header('Location: gerer_users.php?success=updated');
        exit();
    } catch (PDOException $e) {
        $error = "Erreur lors de la mise à jour : " . $e->getMessage();
    }
}

$title = "Modifier l'utilisateur";
include '../includes/header.php';
include '../includes/navbar.php';
?>

<div class="container py-5">
    <div class="card border-0 shadow-sm mx-auto rounded-4" style="max-width: 600px;">
        <div class="card-body p-5">
            <h2 class="fw-bold mb-4">✏️ Modifier l'utilisateur</h2>
            
            <?php if ($error): ?>
                <div class="alert alert-danger"><?= $error ?></div>
            <?php endif; ?>

            <form method="POST">
                <input type="hidden" name="id_user" value="<?= $user['id_user'] ?>">
                
                <div class="mb-3">
                    <label class="form-label text-muted small fw-bold">NOM COMPLET</label>
                    <input type="text" name="nom_complet" class="form-control rounded-3" value="<?= e($user['nom_complet']) ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label text-muted small fw-bold">IDENTIFIANT (LOGIN)</label>
                    <input type="text" name="login" class="form-control rounded-3" value="<?= e($user['login']) ?>" required>
                </div>

                <div class="mb-4">
                    <label class="form-label text-muted small fw-bold">RÔLE</label>
                    <select name="role" class="form-select rounded-3">
                        <option value="etudiant" <?= $user['role'] === 'etudiant' ? 'selected' : '' ?>>Étudiant</option>
                        <option value="enseignant" <?= $user['role'] === 'enseignant' ? 'selected' : '' ?>>Enseignant</option>
                        <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Administrateur</option>
                    </select>
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary rounded-pill py-2 fw-bold shadow-sm">Enregistrer les modifications</button>
                    <a href="gerer_users.php" class="btn btn-light rounded-pill">Annuler</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>