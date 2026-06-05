<?php
ob_start(); 
require_once 'config.php'; 
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!empty($email) && !empty($password)) {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            
            $_SESSION['user_id'] = $user['id_user'];
            $_SESSION['user_nom'] = $user['nom_complet'];
            $_SESSION['user_role'] = $user['role']; 

            session_write_close(); /
            header('Location: pages/dashbord.php');
            echo '<script>window.location.href="pages/dashbord.php";</script>';
            exit(); 
        } else {
            $error = "Email ou mot de passe incorrect.";
        }
    } else {
        $error = "Veuillez remplir tous les champs.";
    }
}

$title = "Connexion";
include 'includes/header.php'; 
?>

<div class="container d-flex align-items-center justify-content-center" style="min-height: 100vh;">
    <div class="card border-0 shadow-lg rounded-4 overflow-hidden" style="max-width: 450px; width: 100%;">
        <div class="card-body p-5">
            <div class="text-center mb-4">
                <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3 shadow" style="width: 60px; height: 60px;">
                    <i class="bi bi-person-lock fs-3"></i>
                </div>
                <h2 class="fw-bold">Connexion</h2>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-danger border-0 rounded-3 small"><?= $error ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="mb-3">
                    <label class="form-label small fw-bold">Email</label>
                    <input type="email" name="email" class="form-control rounded-pill bg-light" required>
                </div>
                <div class="mb-4">
                    <label class="form-label small fw-bold">Mot de passe</label>
                    <input type="password" name="password" class="form-control rounded-pill bg-light" required>
                </div>
                <button type="submit" class="btn btn-primary w-100 rounded-pill py-2 fw-bold shadow-sm">Se connecter</button>
            </form>
            <div class="text-center mt-4">
                <a href="register.php" class="small text-decoration-none">Créer un compte</a>
            </div>
        </div>
    </div>
</div>

<?php 
include 'includes/footer.php'; 
ob_end_flush(); 
?>