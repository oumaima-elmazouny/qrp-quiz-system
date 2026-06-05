-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 30, 2026 at 08:30 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `quiz_revision_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `chapitres`
--

CREATE TABLE `chapitres` (
  `id_chapitre` int(11) NOT NULL,
  `nom_chapitre` varchar(100) NOT NULL,
  `id_matiere` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `chapitres`
--

INSERT INTO `chapitres` (`id_chapitre`, `nom_chapitre`, `id_matiere`) VALUES
(1, 'Variables et Boucles', 1),
(2, 'Introduction HTML/CSS', 2),
(3, 'Modèle OSI', 3);

-- --------------------------------------------------------

--
-- Table structure for table `matieres`
--

CREATE TABLE `matieres` (
  `id_matiere` int(11) NOT NULL,
  `nom_matiere` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `matieres`
--

INSERT INTO `matieres` (`id_matiere`, `nom_matiere`) VALUES
(1, 'Algorithmique'),
(2, 'Développement Web'),
(3, 'Réseaux');

-- --------------------------------------------------------

--
-- Table structure for table `questions`
--

CREATE TABLE `questions` (
  `id_question` int(11) NOT NULL,
  `texte_question` text NOT NULL,
  `id_quiz` int(11) NOT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `type_question` enum('unique','multiple') DEFAULT 'unique'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `questions`
--

INSERT INTO `questions` (`id_question`, `texte_question`, `id_quiz`, `image_path`, `type_question`) VALUES
(1, 'Que signifie HTML ?', 1, '', 'unique'),
(2, 'Quelle propriété CSS change la couleur de fond ?', 1, '', 'unique'),
(3, 'Quelle balise est utilisée pour un lien hypertexte ?', 1, '', 'unique');

-- --------------------------------------------------------

--
-- Table structure for table `quiz`
--

CREATE TABLE `quiz` (
  `id_quiz` int(11) NOT NULL,
  `titre` varchar(150) NOT NULL,
  `description` text DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `temps_limite` int(11) NOT NULL COMMENT 'Temps en secondes',
  `id_chapitre` int(11) NOT NULL,
  `id_createur` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `quiz`
--

INSERT INTO `quiz` (`id_quiz`, `titre`, `description`, `image_path`, `temps_limite`, `id_chapitre`, `id_createur`) VALUES
(1, 'Bases du Web', 'Testez vos connaissances sur HTML et CSS.', NULL, 300, 2, 2),
(2, 'Réseaux : Niveau 1', 'Quiz sur le modèle OSI et ses couches.', NULL, 600, 3, 2);

-- --------------------------------------------------------

--
-- Table structure for table `reponses`
--

CREATE TABLE `reponses` (
  `id_reponse` int(11) NOT NULL,
  `texte_reponse` text NOT NULL,
  `is_correct` tinyint(1) NOT NULL DEFAULT 0,
  `id_question` int(11) NOT NULL,
  `image_reponse` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reponses`
--

INSERT INTO `reponses` (`id_reponse`, `texte_reponse`, `is_correct`, `id_question`, `image_reponse`) VALUES
(1, 'HyperText Markup Language', 1, 1, ''),
(2, 'HighText Machine Language', 0, 1, ''),
(3, 'Hyperlinks and Text Markup Language', 0, 1, ''),
(4, 'color', 0, 2, ''),
(5, 'background-color', 1, 2, ''),
(6, 'font-weight', 0, 2, ''),
(7, '<link>', 0, 3, ''),
(8, '<a>', 1, 3, ''),
(9, '<href>', 0, 3, '');

-- --------------------------------------------------------

--
-- Table structure for table `scores`
--

CREATE TABLE `scores` (
  `id_score` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `id_quiz` int(11) NOT NULL,
  `score` int(11) NOT NULL,
  `date_tentative` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `scores`
--

INSERT INTO `scores` (`id_score`, `id_user`, `id_quiz`, `score`, `date_tentative`) VALUES
(1, 2, 1, 100, '2026-04-29 16:06:40'),
(2, 59, 1, 20, '2026-04-29 16:23:00'),
(3, 59, 1, 20, '2026-04-29 17:09:41');

-- --------------------------------------------------------

--
-- Table structure for table `tentatives`
--

CREATE TABLE `tentatives` (
  `id_tentative` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `id_quiz` int(11) NOT NULL,
  `score` int(11) NOT NULL,
  `date_tentative` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tentatives`
--

INSERT INTO `tentatives` (`id_tentative`, `id_user`, `id_quiz`, `score`, `date_tentative`) VALUES
(1, 3, 1, 3, '2026-04-26 22:32:09'),
(2, 4, 1, 2, '2026-04-26 22:32:09'),
(3, 3, 2, 8, '2026-04-26 22:32:09');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id_user` int(11) NOT NULL,
  `login` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nom_complet` varchar(100) NOT NULL,
  `role` enum('admin','enseignant','etudiant') NOT NULL DEFAULT 'etudiant'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id_user`, `login`, `password`, `nom_complet`, `role`) VALUES
(1, 'ENSIASD', '$2y$10$w8.3.zN1H75b.Cg1O3fCiuM1b.KkL/UqT4S8nQpZ/3vYvQG5UeZ7.', 'Administrateur Principal', 'admin'),
(2, 'prof_dupont', '$2y$10$e0MYzXyjpJS7Pd0RVvMuneGjA.w1wT2g.oF2GjCjK3A3B1Q6C.2F2', 'Jean Dupont', 'enseignant'),
(3, 'etu_martin', '$2y$10$e0MYzXyjpJS7Pd0RVvMuneGjA.w1wT2g.oF2GjCjK3A3B1Q6C.2F2', 'Sophie Martin', 'etudiant'),
(4, 'etu_dubois', '$2y$10$e0MYzXyjpJS7Pd0RVvMuneGjA.w1wT2g.oF2GjCjK3A3B1Q6C.2F2', 'Paul Dubois', 'etudiant'),
(58, 'PROFESSEUR', '$2y$10$Pb5.cmTCHWmHhFeJxpwqOOO86C4g0NwqyIvKBpJSI5o6HewCCMzhO', 'PROFESSEUR', 'enseignant'),
(59, 'user', '$2y$10$TeIFUYDV5ZcRbINHqBBeb.mf.6D0LYiDQBnG2MimCV5td6J4ZksYq', 'etudiant', 'etudiant');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `chapitres`
--
ALTER TABLE `chapitres`
  ADD PRIMARY KEY (`id_chapitre`),
  ADD KEY `id_matiere` (`id_matiere`);

--
-- Indexes for table `matieres`
--
ALTER TABLE `matieres`
  ADD PRIMARY KEY (`id_matiere`);

--
-- Indexes for table `questions`
--
ALTER TABLE `questions`
  ADD PRIMARY KEY (`id_question`),
  ADD KEY `id_quiz` (`id_quiz`);

--
-- Indexes for table `quiz`
--
ALTER TABLE `quiz`
  ADD PRIMARY KEY (`id_quiz`),
  ADD KEY `id_chapitre` (`id_chapitre`),
  ADD KEY `id_createur` (`id_createur`);

--
-- Indexes for table `reponses`
--
ALTER TABLE `reponses`
  ADD PRIMARY KEY (`id_reponse`),
  ADD KEY `id_question` (`id_question`);

--
-- Indexes for table `scores`
--
ALTER TABLE `scores`
  ADD PRIMARY KEY (`id_score`),
  ADD KEY `id_user` (`id_user`),
  ADD KEY `id_quiz` (`id_quiz`);

--
-- Indexes for table `tentatives`
--
ALTER TABLE `tentatives`
  ADD PRIMARY KEY (`id_tentative`),
  ADD KEY `id_user` (`id_user`),
  ADD KEY `id_quiz` (`id_quiz`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id_user`),
  ADD UNIQUE KEY `login` (`login`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `chapitres`
--
ALTER TABLE `chapitres`
  MODIFY `id_chapitre` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `matieres`
--
ALTER TABLE `matieres`
  MODIFY `id_matiere` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `questions`
--
ALTER TABLE `questions`
  MODIFY `id_question` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=79;

--
-- AUTO_INCREMENT for table `quiz`
--
ALTER TABLE `quiz`
  MODIFY `id_quiz` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `reponses`
--
ALTER TABLE `reponses`
  MODIFY `id_reponse` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=235;

--
-- AUTO_INCREMENT for table `scores`
--
ALTER TABLE `scores`
  MODIFY `id_score` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `tentatives`
--
ALTER TABLE `tentatives`
  MODIFY `id_tentative` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=60;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `chapitres`
--
ALTER TABLE `chapitres`
  ADD CONSTRAINT `chapitres_ibfk_1` FOREIGN KEY (`id_matiere`) REFERENCES `matieres` (`id_matiere`) ON DELETE CASCADE;

--
-- Constraints for table `questions`
--
ALTER TABLE `questions`
  ADD CONSTRAINT `questions_ibfk_1` FOREIGN KEY (`id_quiz`) REFERENCES `quiz` (`id_quiz`) ON DELETE CASCADE;

--
-- Constraints for table `quiz`
--
ALTER TABLE `quiz`
  ADD CONSTRAINT `quiz_ibfk_1` FOREIGN KEY (`id_chapitre`) REFERENCES `chapitres` (`id_chapitre`) ON DELETE CASCADE,
  ADD CONSTRAINT `quiz_ibfk_2` FOREIGN KEY (`id_createur`) REFERENCES `users` (`id_user`);

--
-- Constraints for table `reponses`
--
ALTER TABLE `reponses`
  ADD CONSTRAINT `reponses_ibfk_1` FOREIGN KEY (`id_question`) REFERENCES `questions` (`id_question`) ON DELETE CASCADE;

--
-- Constraints for table `scores`
--
ALTER TABLE `scores`
  ADD CONSTRAINT `fk_scores_user` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE CASCADE;

--
-- Constraints for table `tentatives`
--
ALTER TABLE `tentatives`
  ADD CONSTRAINT `tentatives_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE CASCADE,
  ADD CONSTRAINT `tentatives_ibfk_2` FOREIGN KEY (`id_quiz`) REFERENCES `quiz` (`id_quiz`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
