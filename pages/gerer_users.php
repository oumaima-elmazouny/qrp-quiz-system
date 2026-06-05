<?php
require_once '../config.php';

if ($_SESSION['user_role'] !== 'admin') {
    header('Location: dashbord.php');
    exit();
}

$stmt = $pdo->query("SELECT id_user, login, nom_complet, role FROM users ORDER BY role, nom_complet");
$users = $stmt->fetchAll();

$title = "Gestion des Utilisateurs";
include '../includes/header.php';
include '../includes/navbar.php';
?>

<div class="container py-5">
    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show rounded-4 shadow-sm border-0 mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> L'opération a été effectuée avec succès !
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">👥 Gestion des Utilisateurs</h2>
        <a href="ajouter_user.php" class="btn btn-primary rounded-pill px-4 shadow-sm">
            <i class="bi bi-person-plus-fill me-2"></i> Ajouter un utilisateur
        </a>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="table-responsive p-3">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3">ID</th>
                        <th>Nom Complet</th>
                        <th>Login</th>
                        <th>Rôle</th>
                        <th class="text-end pe-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $u): ?>
                    <tr>
                        <td class="ps-3 text-muted">#<?= e($u['id_user']) ?></td>
                        <td><strong><?= e($u['nom_complet']) ?></strong></td>
                        <td><code class="text-dark bg-light px-2 py-1 rounded"><?= e($u['login']) ?></code></td>
                        <td>
                            <?php 
                                $badge_class = 'bg-primary';
                                if ($u['role'] === 'admin') $badge_class = 'bg-danger';
                                if ($u['role'] === 'enseignant') $badge_class = 'bg-warning text-dark';
                            ?>
                            <span class="badge rounded-pill <?= $badge_class ?> px-3">
                                <?= ucfirst($u['role']) ?>
                            </span>
                        </td>
                        <td class="text-end pe-3">
                            <a href="modifier_user.php?id=<?= $u['id_user'] ?>" class="btn btn-sm btn-outline-secondary rounded-circle" title="Modifier">
                                <i class="bi bi-pencil"></i>
                            </a>
                            
                            <?php if ($u['id_user'] != $_SESSION['user_id']): ?>
                                <a href="supprimer_user.php?id=<?= $u['id_user'] ?>" 
                                   class="btn btn-sm btn-outline-danger rounded-circle ms-1" 
                                   onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?')" 
                                   title="Supprimer">
                                    <i class="bi bi-trash"></i>
                                </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>