# QRP - Système de Quiz Révision

## 📌 1. Description du Projet
QRP est une application web de révision interactive. Elle permet de créer un environnement d'apprentissage où les étudiants peuvent tester leurs connaissances, voir leur progression et figurer dans un classement général.

---

## ⚙️ 2. Installation Technique
* **Serveur local :** XAMPP, WAMP, Laragon (PHP 8.2+ recommandé)
* **Base de données :** MySQL
* **Emplacement :** Dossier `Quiz_app_IL` dans votre répertoire `htdocs` ou équivalent

### 🚀 Procédure d'installation
1. Importez le fichier SQL fourni dans **phpMyAdmin**.
2. Vérifiez les accès à la base de données dans le fichier `config.php` (*Host, DB Name, User, Pass*).
3. Accédez à l'application via : `http://localhost/Quiz_app_IL/`

---

## 📂 3. Structure des Dossiers
```text
Quiz_app_IL/
│
├── actions/      --> Scripts logiques de traitement (submit_quiz.php, etc.)
├── includes/     --> Éléments de mise en page (header.php, navbar.php, footer.php)
├── pages/        --> Pages de l'interface (dashbord.php, gerer_users.php, etc.)
├── css/          --> Feuilles de style (Bootstrap, styles personnalisés)
├── images/       --> Ressources graphiques et icônes du projet
├── js/           --> Scripts clients (Animations, validation de formulaire)
├── sessions/     --> Stockage local des fichiers de session (générés par le serveur)
├── sql/          --> Script d'export de la base de données (quiz_revision_db.sql)
│
├── config.php    --> Configuration PDO et démarrage des sessions PHP
└── index.php     --> Point d'entrée du projet (Page de connexion)
