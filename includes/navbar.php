<style>
    .navbar-custom {
        background: rgba(255, 255, 255, 0.95) !important;
        backdrop-filter: blur(10px);
        border-bottom: 2px solid rgba(0,0,0,0.05);
        padding: 0.7rem 0;
    }
    .navbar-brand {
        font-weight: 800;
        color: #0d6efd !important;
        font-size: 1.4rem;
        letter-spacing: -0.5px;
    }
    .nav-link {
        font-weight: 600;
        color: #495057 !important;
        padding: 0.5rem 1rem !important;
        margin: 0 3px;
        border-radius: 8px;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
    }
    .nav-link:hover, .nav-link.active {
        color: #0d6efd !important;
        background: rgba(13, 110, 253, 0.08);
    }
    .nav-link i { margin-right: 6px; font-size: 1.1rem; }
    .user-info-badge {
        background: #f8f9fa;
        border: 1px solid #e9ecef;
        padding: 6px 15px;
        border-radius: 50px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .role-dot { width: 8px; height: 8px; border-radius: 50%; }
    .dot-admin { background: #dc3545; } 
    .dot-enseignant { background: #fd7e14; } 
    .dot-etudiant { background: #198754; } 

    .btn-logout-custom {
        font-weight: 700;
        border-radius: 50px;
        padding: 6px 20px;
        transition: all 0.3s ease;
    }
    @media (max-width: 991px) {
        .navbar-collapse { margin-top: 15px; padding-bottom: 10px; }
        .user-info-badge { margin-bottom: 10px; width: fit-content; }
    }
</style>

<?php 
    $current_page = basename($_SERVER['PHP_SELF']); 
    $dashboard_file = "dashbord.php"; 
    $url = defined('BASE_URL') ? BASE_URL : '/Quiz_app_IL';
    $role = $_SESSION['user_role'] ?? 'etudiant';
?>

<nav class="navbar navbar-expand-lg navbar-light sticky-top navbar-custom">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="<?= $url ?>/pages/<?= $dashboard_file ?>">
            <i class="bi bi-mortarboard-fill me-2"></i>
            <?= defined('SITE_NAME') ? SITE_NAME : "QUIZ REVISION" ?>
        </a>

        <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                
                <li class="nav-item">
                    <a class="nav-link <?= $current_page == $dashboard_file ? 'active' : '' ?>" href="<?= $url ?>/pages/<?= $dashboard_file ?>">
                        <i class="bi bi-house-door"></i> Accueil
                    </a>
                </li>

                <?php if ($role === 'etudiant'): ?>
                    <li class="nav-item">
                        <a class="nav-link <?= $current_page == 'liste_quiz.php' ? 'active' : '' ?>" href="<?= $url ?>/pages/liste_quiz.php">
                            <i class="bi bi-card-checklist"></i> Mes Quiz
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $current_page == 'progression.php' ? 'active' : '' ?>" href="<?= $url ?>/pages/progression.php">
                            <i class="bi bi-graph-up"></i> Ma Progression
                        </a>
                    </li>
                <?php endif; ?>

                <?php if ($role === 'enseignant'): ?>
                    <li class="nav-item">
                        <a class="nav-link <?= $current_page == 'create_quiz.php' ? 'active' : '' ?>" href="<?= $url ?>/pages/create_quiz.php">
                            <i class="bi bi-plus-circle"></i> Créer Quiz
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $current_page == 'gerer_quiz.php' ? 'active' : '' ?>" href="<?= $url ?>/pages/gerer_quiz.php">
                            <i class="bi bi-collection-play"></i> Gérer Mes Quiz
                        </a>
                    </li>
                <?php endif; ?>

                <?php if ($role === 'admin'): ?>
                    <li class="nav-item">
                        <a class="nav-link <?= $current_page == 'gerer_users.php' ? 'active' : '' ?>" href="<?= $url ?>/pages/gerer_users.php">
                            <i class="bi bi-people"></i> Utilisateurs
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $current_page == 'gerer_quiz.php' ? 'active' : '' ?>" href="<?= $url ?>/pages/gerer_quiz.php">
                            <i class="bi bi-shield-check"></i> Modérer Quiz
                        </a>
                    </li>
                <?php endif; ?>

                <li class="nav-item">
                    <a class="nav-link <?= $current_page == 'leaderboard.php' ? 'active' : '' ?>" href="<?= $url ?>/pages/leaderboard.php">
                        <i class="bi bi-trophy"></i> Classement
                    </a>
                </li>
            </ul>

            <div class="d-lg-flex align-items-center">
                <?php if(isset($_SESSION['user_nom'])): ?>
                <div class="user-info-badge me-lg-3 mb-3 mb-lg-0">
                    <span class="role-dot dot-<?= $role ?>"></span>
                    <span class="small fw-bold text-dark">
                        <?= e($_SESSION['user_nom']) ?> 
                        <span class="text-muted fw-normal" style="font-size: 0.75rem;">
                            (<?= strtoupper(e($role)) ?>)
                        </span>
                    </span>
                </div>
                <?php endif; ?>
                
                <a href="<?= $url ?>/pages/logout.php" class="btn btn-outline-danger btn-logout-custom btn-sm">
                    <i class="bi bi-box-arrow-right"></i> Quitter
                </a>
            </div>
        </div>
    </div>
</nav>