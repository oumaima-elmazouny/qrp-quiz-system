<?php
require_once '../config.php';
redirect_if_not_logged_in();

if (!has_role('enseignant') && !has_role('admin')) { 
    header('Location: dashbord.php'); 
    exit(); 
}

try {
    $total_quiz = $pdo->query("SELECT COUNT(*) FROM quiz")->fetchColumn() ?: 0;
    $total_questions = $pdo->query("SELECT COUNT(*) FROM questions")->fetchColumn() ?: 0;
    $total_users = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn() ?: 0; 
    
    $checkTable = $pdo->query("SHOW TABLES LIKE 'tentatives'");
    $total_tentatives = ($checkTable->rowCount() > 0) ? $pdo->query("SELECT COUNT(*) FROM tentatives")->fetchColumn() : 0;
    
    $moyenne_globale = 0;
    if ($total_tentatives > 0) {
        $moyenne_globale = $pdo->query("SELECT ROUND(AVG(score), 1) FROM tentatives")->fetchColumn();
    }

} catch (PDOException $e) {
    $total_quiz = $total_questions = $total_users = $total_tentatives = 0;
}

$title = "Statistiques Avancées";
include '../includes/header.php';
include '../includes/navbar.php';
?>

<style>
    .stat-card {
        border: none;
        border-radius: 24px; 
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        overflow: hidden;
        position: relative;
    }
    .stat-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 20px 40px rgba(0,0,0,0.15) !important;
    }
    .floating-icon {
        position: absolute;
        right: -5px;
        bottom: -15px;
        font-size: 5.5rem;
        opacity: 0.12;
        transform: rotate(-15deg);
        transition: all 0.4s;
    }
    .stat-card:hover .floating-icon {
        transform: rotate(0deg) scale(1.1);
        opacity: 0.2;
    }
    .bg-gradient-quiz { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
    .bg-gradient-quest { background: linear-gradient(135deg, #00b09b 0%, #96c93d 100%); }
    .bg-gradient-rep { background: linear-gradient(135deg, #ff9a9e 0%, #fecfef 100%); } /* Rose/Violet pour les participations */
    .bg-gradient-user { background: linear-gradient(135deg, #f6d365 0%, #fda085 100%); }
    
    .status-dot {
        height: 10px; width: 10px;
        background-color: #28a745;
        border-radius: 50%;
        display: inline-block;
        margin-right: 5px;
        animation: pulse 2s infinite;
    }
    @keyframes pulse {
        0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(40, 167, 69, 0.7); }
        70% { transform: scale(1); box-shadow: 0 0 0 10px rgba(40, 167, 69, 0); }
        100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(40, 167, 69, 0); }
    }
</style>

<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-5">
        <div>
            <h1 class="fw-bold text-dark">📊 Statistiques Système</h1>
            <p class="text-muted">Analyse globale de l'engagement et des ressources.</p>
        </div>
        <button onclick="window.location.reload()" class="btn btn-white shadow-sm rounded-pill px-4">
            <i class="bi bi-arrow-clockwise"></i> Actualiser
        </button>
    </div>

    <div class="row g-4">
        <div class="col-md-3">
            <div class="card stat-card bg-gradient-quiz text-white h-100 shadow">
                <div class="card-body p-4">
                    <h6 class="text-uppercase fw-bold opacity-75">Quiz créés</h6>
                    <h2 class="display-4 fw-bold mb-0"><?= $total_quiz ?></h2>
                    <i class="bi bi-journal-text floating-icon"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card bg-gradient-quest text-white h-100 shadow">
                <div class="card-body p-4">
                    <h6 class="text-uppercase fw-bold opacity-75">Questions</h6>
                    <h2 class="display-4 fw-bold mb-0"><?= $total_questions ?></h2>
                    <i class="bi bi-patch-question floating-icon"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card bg-gradient-rep text-white h-100 shadow">
                <div class="card-body p-4">
                    <h6 class="text-uppercase fw-bold opacity-75">Tentatives</h6>
                    <h2 class="display-4 fw-bold mb-0"><?= $total_tentatives ?></h2>
                    <i class="bi bi-lightning-charge floating-icon"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card bg-gradient-user text-white h-100 shadow">
                <div class="card-body p-4">
                    <h6 class="text-uppercase fw-bold opacity-75">Membres</h6>
                    <h2 class="display-4 fw-bold mb-0"><?= $total_users ?></h2>
                    <i class="bi bi-people floating-icon"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-5">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-4">Détails techniques</h5>
                    <div class="table-responsive">
                        <table class="table align-middle border-light">
                            <tbody>
                                <tr>
                                    <td><span class="status-dot"></span> Serveur de données</td>
                                    <td class="text-end fw-bold text-uppercase"><?= DB_NAME ?></td>
                                </tr>
                                <tr>
                                    <td><i class="bi bi-shield-lock me-2 text-primary"></i> Niveau d'accès</td>
                                    <td class="text-end fw-bold"><?= ucfirst(e($_SESSION['user_role'])) ?></td>
                                </tr>
                                <tr>
                                    <td><i class="bi bi-graph-up me-2 text-success"></i> Score Moyen Global</td>
                                    <td class="text-end fw-bold text-success"><?= $moyenne_globale ?> / 20</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 bg-primary text-white h-100">
                <div class="card-body p-4 d-flex flex-column justify-content-center">
                    <h5 class="fw-bold mb-4">Gestionnaire</h5>
                    <div class="d-grid gap-2">
                        <a href="gerer_quiz.php" class="btn btn-light rounded-pill">Éditer les contenus</a>
                        <a href="create_quiz.php" class="btn btn-light rounded-pill mb-2">Créer un nouveau Quiz</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>