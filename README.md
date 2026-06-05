# QRP - Système de Quiz Révision

## 📌 1. Description du Projet
QRP (Quiz Revision Platform) est une application web Full-Stack interactive. Elle permet de créer un environnement d'apprentissage multi-rôles (Admin, Enseignant, Étudiant) où les utilisateurs peuvent tester leurs connaissances, suivre leur progression et figurer dans un classement général.

---

## ⚙️ 2. Installation Technique (Local & En Ligne)
* **Serveur local :** XAMPP / WAMP (PHP 8.2+ recommandé)
* **Base de données :** MySQL/ MariaDB
* **Emplacement :** Dossier `Quiz_app_IL` dans votre répertoire `htdocs` ou équivalent
* 🌐 Lien du site en ligne : [quiz-oumaima-douae.infinityfreeapp.com]`

### 🚀 Procédure d'installation locale
1. Importez le fichier SQL fourni dans **phpMyAdmin**.
2. Vérifiez les accès à la base de données dans le fichier `config.php` (*Host, DB Name, User, Pass*).
3. Accédez à l'application via : `http://localhost/Quiz_app_IL/`

---

## 📂 3. Structure des Dossiers
```text
Quiz_app_IL/
│
├── actions/      --> Scripts de traitement logique (submit_quiz.php, etc.)
├── includes/     --> Composants réutilisables (header.php, navbar.php, footer.php)
├── pages/        --> Interface utilisateur (dashboard.php, gerer_users.php, etc.)
├── css/          --> Feuilles de style (Bootstrap & styles personnalisés)
├── images/       --> Ressources graphiques et icônes
├── js/           --> Scripts clients (Timer, animations, validations)
├── sql/          --> Script d'export final de la base de données
│
├── config.php    --> Configuration PDO, BASE_URL et gestion des sessions
└── index.php     --> Point d'entrée principal (Page de connexion)
```
---


## 🔑 4. Comptes de Test

| Rôle | Login | Mot de passe |
| :--- | :--- | :--- |
| **Administrateur** | `ENSIASD` | `ENSIASD2026` |
| **Professeur** | `ibrahim` | `ibrahim123` |
| **Étudiant** | `user` | `user123` |

---

## ✨ 5. Fonctionnalités Clés
* **Multi-accès :**  Interfaces dédiées et sécurisées pour l'Administrateur, le Professeur et l'Étudiant.
* **Correction Automatique :** Calcul instantané des scores convertis sur 20 selon le nombre de questions.
* **Dashboard Dynamique :**  Statistiques et gestion des ressources en temps réel.
* **Classement  :** Leaderboard interactif affichant le Top 10 des meilleurs étudiants.
* **Sécurité   :** Gestion stricte des accès par rôles et protection des sessions PHP.


---

## 🛠️ 6. Dépannage (FAQ)

> 💡 **Erreur 404**  
> Le fichier principal du tableau de bord a été renommé dashbord.php (sans "a"). Vérifiez bien l'orthographe dans vos liens et redirections PHP.

> 💡 **Redirection en boucle sur la page Login**  
> Si le login boucle sans vous connecter, assurez-vous qu'aucun espace ou retour à la ligne n'existe avant la fonction session_start() dans vos fichiers PHP.

> 💡 **Base de données / Erreur d'intégrité (1451)**  
> Pour vider la base ou réimporter proprement, désactivez temporairement la vérification des clés étrangères (Foreign Key Checks) dans phpMyAdmin lors de l'importation du fichier quiz_revision_db.sql.

---
*Projet final réalisé en 2026 - QRP Quiz Revision Platform *
