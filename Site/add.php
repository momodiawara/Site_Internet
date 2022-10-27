<!DOCTYPE html>
<html>
	<head>
		<?php 
		session_start();
		require_once("bases.php"); 
		$connexion=connexion(); 
		if($connexion) {
			if(!connecter($connexion,$_SESSION)) {
				header("Location:accueil.php?re=add.php#contenu");
				exit;
			}	
		}
		else {
			header("Location:accueil.php?re=add.php#contenu");
			exit;
		}
		?>
		<title> Deposer un Travail </title>
		<link rel="stylesheet" type="text/css" href="./css/stylesheet.css">
		<link rel="stylesheet" type="text/css" href="./css/stylesheetadd.css"> 		
		<link rel="stylesheet" type="text/css" href="./css/speciaux.css"> 
		<link rel="stylesheet" type="text/css" href="./css/erreurs.css"> 
		<link rel="stylesheet" type="text/css" href="./css/speciauxadd.css"> 		
		<meta charset="utf-8" name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0">
		<!--
			name="viewport" -> affichage par rapport à l'ecran de l'appareil - 'zoom initial' (pour eviter d'avoir des textes tres petit sur telephone par exemple)
			content="width=device-width -> largeur de la fenetre par rapport a l'ecran de l'appareil (ici prend tout l'ecran)
					initial-scale=1.0 -> niveau de zoom initial (1 -> voit toute la page)
					minimum-scale=1.0 -> zoom minimal (empeche dezoom ici)
		-->
	</head>
	<body>
		<?php
		if(isset($_GET["mes"])) {
			reussi("Nouveau Travail Ajouté",1);
			unset($_GET["mes"]);
		}
		if($connexion) {
			//ajout du document
			if(isset($_POST["titre"]) && !empty($_POST["titre"]) && isset($_POST["contenu"]) && isset($_POST["type"]) && isset($_POST["matières"]) && isset($_FILES["doc"]) && isset($_FILES["img"]) && isset($_FILES["pdf"])) {
				require_once("ajout.php");
				add($connexion,$_POST,$_FILES,$_SESSION);
			}
			//connexion
			require_once("connection.php");
			if(connex($connexion,$_POST,$_SESSION)) {
				reussi("connexion reussite",1);
				unset($_POST["psw"]);
				unset($_POST["pseudo"]);
				if(isset($_SESSION["re"]) && file_exists($_SESSION["re"])) {
					$redi=$_SESSION["re"];
					unset($_SESSION["re"]);
					header("Location:".$redi."#contenu");
					exit;
				}
			}
		
		}
		?>
		<header>
			<!-- barre de navigation haute -->
			<nav class="nav">
				<!-- bar de recherche haute : disparait sur mobile -->
				<div class="petit">
					<form action="accueil.php#contenu" method="GET">
						<input type="text" placeholder="Rechercher"  name="search" <?php if(isset($_GET["search"])) { echo 'value="'.htmlspecialchars($_GET["search"]).'"'; } ?>>
						<input type="submit" value="OK">
					</form>
				</div>
				<!-- formulaire de connection/mes infos -->
				<?php infos($connexion,$_SESSION); ?>
			</nav>
			<div class="imgs">
			</div>
			<!-- bar de navigation avec accueil -->
			<nav style="position: relative; top: -5px;"> <!-- evite espacement -->
				<a href="accueil.php"> accueil </a>
				<div class="petit2 button">
					<!-- bar de recherche:apparait sur mobile -->
					<form action="accueil.php#contenu" method="GET">
						<input type="text" placeholder="Rechercher" name="search" style="border-style:hidden;" <?php if(isset($_GET["search"])) { echo 'value="'.htmlspecialchars($_GET["search"]).'"'; } ?>>
						<input type="submit" value="OK">
					</form>
				</div>
			</nav>
		</header>
		<main id="contenu">
			<!-- bar de navigation basse : information de recherche -->
			<nav>
				<form method="POST" action="accueil.php#contenu">
					<div>
						<div class="type">
							<label for="td" class="pointer"> TD </label>
							<input type="checkbox" name="TD" id="td" class="button" <?php if( !isset($_COOKIE["td"])) { echo "checked"; } ?> >
							<label for="td" class="pointer"><img alt="~" src="images/valide.png"></label>
						</div>
						<div class="type">
							<label for="tp" class="pointer"> TP </label>
							<input type="checkbox" id="tp" name="TP" class="button" <?php if( !isset($_COOKIE["tp"])) { echo "checked"; } ?> > 
							<label for="tp" class="pointer"><img alt="~" src="images/valide.png"></label>
						</div>
					</div>
					<div class="select">
						<label for="matières"> Matières : </label>
						<?php selector("matières",$connexion,1); ?>
					</div>
					<div class="select">
						<label for="filières"> Filières : </label>
						<?php selector("filières",$connexion,1); ?>
					</div>
					<div class="select">
						<label for="op3"> Année : </label>
						<input name="ann" type="number" min="0" max="8" value=<?php if(!isset($_COOKIE["annee"])) { echo "0";} else { echo $_COOKIE["annee"]; } ?> >
					</div>
					<div class="select">
						<label for="universités"> Universités : </label>
						<?php selector("universités",$connexion,1);  ?>
					</div>
					<input type="submit" value="VOIR" style="font-weight:bold">
					<input type="hidden" name="verif" value="1">
				</form>
			</nav>
			<article>
				<form method="post" action="" enctype="multipart/form-data">
					<label for="titre"> Nom du travail : </label>
					<input type="text" id="titre" name="titre" placeholder="Nom du travail" <?php if(isset($_POST["titre"])) { echo "value=".htmlentities($_POST["titre"]); } ?> required>
					<label for="contenu"> Contenu : </label>
					<textarea name="contenu" id="contenu" rows="30" spellcheck="false"><?php if(isset($_POST["contenu"])) { echo htmlentities($_POST["contenu"]); } else { echo "Ecrire le contenu ici (laisser vide si pas de texte)"; }?></textarea>
					<div>
						<label for="td2" class="pointer"> TD </label>
						<input type="radio" name="type" id="td2" value="0" checked>
						<label for="tp2" class="pointer"> TP </label>
						<input type="radio" id="tp2" name="type" value="1"> 
					<div>
						<label for="matières"> Matière : </label>
						<?php selector ("matières",$connexion,0); ?>
					</div>				
					<table>
						<tr>
							<td><label for="pdf"> Pdfs : </label></td>
							<td><label for="img"> Images : </label></td>
							<td><label for="doc"> Documents : </label></td>
						</tr>
						<tr>
							<td><input type="file" name="pdf[]" id="pdf" multiple="multiple"></td>
							<td><input type="file" name="img[]" id="img" multiple="multiple"></td>
							<td><input type="file" name="doc[]" id="doc" multiple="multiple"></td>
						</tr>
						<tr>
							<td colspan="3">( choix multiple possible )</td>
						</tr>
					</table>
					<input type="submit" value="creer" class="final">
				</form>
			</article>
		</main>
		<?php mysqli_close($connexion); ?>

	</body>
</html>
