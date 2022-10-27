SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données :  `projet`
--

-- --------------------------------------------------------

--
-- Structure de la table `universités`
--

DROP TABLE IF EXISTS `universités`;
CREATE TABLE IF NOT EXISTS `universités` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(255) NOT NULL UNIQUE,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

INSERT INTO `universités` (`nom`) VALUES
('Paris Diderot');
INSERT INTO `universités` (`nom`) VALUES
('Paris Descartes');


-- --------------------------------------------------------

--
-- Structure de la table `filières`
--

DROP TABLE IF EXISTS `filières`;
CREATE TABLE IF NOT EXISTS `filières` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(255) NOT NULL UNIQUE,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

INSERT INTO `filières` (`nom`) VALUES
('Informatique');
INSERT INTO `filières` (`nom`) VALUES
('Mathematique');
INSERT INTO `filières` (`nom`) VALUES
('Math-Info');

-- --------------------------------------------------------

--
-- Structure de la table `matières`
--

DROP TABLE IF EXISTS `matières`;
CREATE TABLE IF NOT EXISTS `matières` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(255) NOT NULL UNIQUE,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

INSERT INTO `matières` (`nom`) VALUES
('Introduction à la programmation');
INSERT INTO `matières` (`nom`) VALUES
('Concept Informatique');
INSERT INTO `matières` (`nom`) VALUES
('Mathematique');
INSERT INTO `matières` (`nom`) VALUES
('Internet et Outil');

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `prenom` varchar(30) NOT NULL,
  `nom` varchar(30) NOT NULL,
  `pseudo` varchar(30) NOT NULL UNIQUE,
  `password` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `activation` tinyint(1) NOT NULL DEFAULT '0',
  `anonyme` tinyint(1) NOT NULL DEFAULT '0',
  `filiere` int(11) NOT NULL,
  `université` int(11) NOT NULL,
  `année` tinyint(4) NOT NULL,
  `code_activation` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `université` (`université`),
  KEY `filiere` (`filiere`),
  FOREIGN KEY (`filiere`) REFERENCES `filières`(`id`),
  FOREIGN KEY (`université`) REFERENCES `universités`(`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
INSERT INTO `users` (`prenom`,`nom`, `pseudo`,`password`, `email`,`anonyme`, `filiere`, `université`, `année`,`code_activation`) VALUES
('prenom', 'nom', 'pseudo','', 'test@gmail.com',1,2, 2, 2,1);
INSERT INTO `users` (`prenom`,`nom`, `pseudo`,`password`, `email`,`anonyme`, `filiere`, `université`, `année`,`code_activation`) VALUES
('prenom', 'nom', 'monpseudo','', 'test@gmail.com',0,3, 1, 5,1);
INSERT INTO `users` (`prenom`,`nom`,`pseudo`, `password`, `email`, `filiere`, `université`, `année`,`code_activation`,`activation`) VALUES
('Dao', 'THAUVIN', 'KillOrber','$2y$10$TOMeRjoc5u6B0PUiCaIHM.6TL5/eNp9mNCo8RH5XIzPiFpjnDZUkS', 'daothauvin@free.fr',1, 1, 1,1,1);

-- --------------------------------------------------------

--
-- Structure de la table `travaux`
--

DROP TABLE IF EXISTS `travaux`;
CREATE TABLE IF NOT EXISTS `travaux` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(30) NOT NULL,
  `author` int(11) NOT NULL,
  `typ` tinyint(1) NOT NULL DEFAULT '0',
  `matiere` int(11) NOT NULL,
  `université` int(11) NOT NULL,
  `filiere` int(11) NOT NULL,
  `pdf` tinyint(1) NOT NULL DEFAULT '0',
  `image` tinyint(1) NOT NULL DEFAULT '0',
  `document` tinyint(1) NOT NULL DEFAULT '0',
  `text` LONGTEXT,
  `année` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `matiere` (`matiere`),
  KEY `filiere` (`filiere`),
  KEY `université` (`université`),
  KEY `author` (`author`),
  FOREIGN KEY (`author`) REFERENCES `users`(`id`),
  FOREIGN KEY (`université`) REFERENCES `universités`(`id`),
  FOREIGN KEY (`filiere`) REFERENCES `filières`(`id`),
  FOREIGN KEY (`matiere`) REFERENCES `matières`(`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;

INSERT INTO `travaux` (`nom`, `matiere`, `université`,`filiere`,`text`,`année`, `author`,`typ`) VALUES
('TP1', 1, 2, 1,"Le TP1 n'est pas disponible",1,1,1);
INSERT INTO `travaux` (`nom`, `matiere`, `université`,`filiere`,`année`, `author`, `document`, `image`,`typ`) VALUES
('TP1', 3,1,2,2,2,1,1,1);
INSERT INTO `travaux` (`nom`, `matiere`, `université`,`text`,`filiere`,`année`, `author`) VALUES
('TD1', 2, 1,"Il y a 10 types de personnes, les gens qui connaissent le binaire et les autres",1,1,1);
INSERT INTO `travaux` (`nom`, `matiere`, `université`,`text`,`filiere`,`année`, `author`) VALUES
('Controle 1', 1, 1,"Pas de chance, c'est pas vrai",1,7,1);
INSERT INTO `travaux` (`nom`, `matiere`, `université`,`filiere`,`année`, `author`, `image`,`pdf`) VALUES
('TD-TP', 1, 1,1,3,1,1,1);

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;