<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($title) ? e($title) . ' — ' . (defined('SITE_NAME') ? SITE_NAME : "Quiz Révision") : (defined('SITE_NAME') ? SITE_NAME : "Quiz Révision") ?></title>

    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">

    <?php 
        $css_path = file_exists('../css/style.css') ? '../css/style.css' : 'css/style.css';
    ?>
    <link rel="stylesheet" href="<?= $css_path ?>">
    
    <style>
        :root {
            --primary:       #4f46e5;
            --primary-light: #e0e7ff;
            --secondary:     #0ea5e9;
            --success:       #10b981;
            --danger:        #ef4444;
            --warning:       #f59e0b;
            --dark:          #1e293b;
            --muted:         #64748b;
            --bg:            #f1f5f9;
            --card-bg:       #ffffff;
            --border:        #e2e8f0;
            --radius:        14px;
            --shadow:        0 4px 20px rgba(79,70,229,.08);
        }

        body {
            font-family: 'Nunito', sans-serif;
            background-color: var(--bg);
            color: var(--dark);
            margin: 0;
            padding: 0;
            overflow-x: hidden;
        }

        .card {
            border: 1px solid var(--border) !important;
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            background-color: var(--card-bg);
        }

        .btn {
            font-weight: 700;
            border-radius: 10px;
            transition: all .2s;
            padding: 0.6rem 1.2rem;
        }

        .form-control, .form-select {
            border: 1.5px solid var(--border);
            border-radius: 10px;
            padding: 0.7rem;
        }

        .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(79,70,229, 0.1);
        }

        .page-wrapper { 
            padding: 2rem 0; 
            min-height: 80vh; 
        }
    </style>
</head>
<body>