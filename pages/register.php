<?php
require_once '../config.php';

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = trim($_POST['login']);
    $nom = trim($_POST['nom_complet']);
    $pass = $_POST['password'];
    $role = $_POST['role'] ?? 'etudiant';

    $roles_autorises = ['etudiant', 'enseignant'];
    if (!in_array($role, $roles_autorises)) {
        $role = 'etudiant'; 
    }

    if (!empty($login) && !empty($pass) && !empty($nom)) {
        $hashed_password = password_hash($pass, PASSWORD_DEFAULT);

        try {
            $check = $pdo->prepare("SELECT id_user FROM users WHERE login = ?");
            $check->execute([$login]);

            if ($check->rowCount() > 0) {
                $message = "<div class='alert alert-danger shadow-sm'>Ce login est déjà utilisé. Veuillez en choisir un autre.</div>";
            } else {
                $stmt = $pdo->prepare("INSERT INTO users (login, password, nom_complet, role) VALUES (?, ?, ?, ?)");
                $stmt->execute([$login, $hashed_password, $nom, $role]);
                
                $message = "<div class='alert alert-success shadow-sm'>
                                <strong>Succès !</strong> Votre compte a été créé. 🎉<br>
                                Redirection vers la connexion dans 3 secondes...
                                <a href='../index.php' class='alert-link'>Cliquer ici sinon</a>
                            </div>";
                
                echo "<script>setTimeout(() => { window.location.href = '../index.php'; }, 3000);</script>";
            }
        } catch (PDOException $e) {
            $message = "<div class='alert alert-danger'>Erreur technique : " . htmlspecialchars($e->getMessage()) . "</div>";
        }
    } else {
        $message = "<div class='alert alert-warning shadow-sm'>Veuillez remplir tous les champs obligatoires.</div>";
    }
}

$title = "Créer un compte";
include '../includes/header.php';
?>

<style>
    body { background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%); min-height: 100vh; }
    .card { border-radius: 20px; }
    .form-control, .form-select { padding: 12px 20px; border: 1px solid #e0e0e0; }
    .form-control:focus { box-shadow: 0 0 0 0.25 margin rgba(13, 110, 253, 0.1); border-color: #0d6efd; }
    .btn-register { padding: 12px; font-weight: bold; transition: all 0.3s; }
    .btn-register:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(13, 110, 253, 0.3); }
</style>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow-lg border-0">
                <div class="card-body p-5">
                    <div class="text-center mb-4">
                        <h2 class="fw-bold text-primary">Rejoignez-nous</h2>
                        <p class="text-muted">Créez votre profil en quelques secondes</p>
                    </div>

                    <?= $message ?>

                    <form method="POST" class="mt-4">
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-uppercase">Nom complet</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-0"><i class="bi bi-person"></i></span>
                                <input type="text" name="nom_complet" class="form-control bg-light border-0" placeholder="Ex: Jean Dupont" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold text-uppercase">Identifiant</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-0"><i class="bi bi-at"></i></span>
                                <input type="text" name="login" class="form-control bg-light border-0" placeholder="Ex: jdupont" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold text-uppercase">Mot de passe</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-0"><i class="bi bi-lock"></i></span>
                                <input type="password" name="password" class="form-control bg-light border-0" placeholder="••••••••" required>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label small fw-bold text-uppercase">Votre profil</label>
                            <select name="role" class="form-select bg-light border-0 shadow-none">
                                <option value="etudiant">🎓 Étudiant (passer des quiz)</option>
                                <option value="enseignant">👨‍🏫 Enseignant (créer des quiz)</option>
                            </select>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-register rounded-pill shadow">
                                Créer mon compte
                            </button>
                        </div>
                        
                        <div class="mt-4 text-center">
                            <p class="mb-0 text-muted small">Déjà inscrit ?</p>
                            <a href="../index.php" class="fw-bold text-decoration-none">Se connecter à l'espace membre</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>