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

---

### 📋 PARTIE 2 : À coller juste à la suite (en dessous de la Partie 1)

```markdown
---

## 🔑 4. Comptes de Test

| Rôle | Login | Mot de passe |
| :--- | :--- | :--- |
| **Administrateur** | `ENSIASD` | `ENSIASD2026` |
| **Professeur** | `PROFESSEUR` | `123456` |
| **Étudiant** | `user` | `user123` |

---

## ✨ 5. Fonctionnalités Clés
* **Gestion CRUD complète :** Contrôle total des utilisateurs par l'administrateur.
* **Calcul de score dynamique :** Les points sont automatiquement convertis en note sur 20 selon le nombre total de questions.
* **Classement en temps réel :** Seuls les étudiants ayant passé au moins un quiz apparaissent dans le Top 10.
* **Sécurité & Rôles :** Redirection automatique et sécurisée si un utilisateur tente d'accéder à une page non autorisée par son rôle.

---

## 🛠️ 6. Dépannage (FAQ)

> 💡 **Erreur "Base table or view not found"**  
> Vérifiez que vous avez bien importé toutes les tables requises (`users`, `quiz`, `questions`, `reponses`, `scores`).

> 💡 **Erreur 404**  
> Assurez-vous que les scripts de suppression et de modification sont bien présents à la racine ou dans le dossier spécifié.

> 💡 **Erreur Foreign Key (1452)**  
> Si vous tentez d'importer manuellement des données dans la table `scores`, l'identifiant `id_user` doit obligatoirement exister au préalable dans la table `users`.

---
*Projet final réalisé en 2026 - QRP Quiz Revision*
