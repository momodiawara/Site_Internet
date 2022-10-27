<!DOCTYPE html>
<html>
	<head>
		<title> Mot de Passe oublié </title>
		<link rel="stylesheet" type="text/css" href="./css/stylesheetnew.css"> 
		<link rel="stylesheet" type="text/css" href="./css/speciauxnew.css"> 
		<link rel="stylesheet" type="text/css" href="./css/erreurs.css"> 
		<meta charset="utf-8" name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0">
		<!--
			name="viewport" -> affichage par rapport à l'ecran de l'appareil - 'zoom initial' (pour eviter d'avoir des textes tres petit sur telephone par exemple)
			content="width=device-width -> largeur de la fenetre par rapport a l'ecran de l'appareil (ici prend tout l'ecran)
					initial-scale=1.0 -> niveau de zoom initial (1 -> voit toute la page)
					minimum-scale=1.0 -> zoom minimal (empeche dezoom ici)
		-->
		<?php 
		require_once("bases.php"); 
		require_once("retrouve.php"); 
		$connexion=connexion(); 
		if($connexion) {
			if(isset($_POST["email"]) && !empty($_POST["email"]) && isset($_POST["ps"]) && !empty($_POST["ps"])) {
				perdu($connexion,$_POST["email"],$_POST["ps"]);
			}	
			if(isset($_POST["pss1"]) && !empty($_POST["pss1"]) && isset($_POST["pss2"]) && !empty($_POST["pss2"]) && isset($_POST["id"]) && !empty($_POST["id"]) && isset($_POST["code"]) && !empty($_POST["code"])) {
				retrouve($connexion,$_POST);
			}
		}			
		?>
	</head>
	<body>
		<form method="post" action="">
			<h3> Mot de passe oublié </h3>
			<div>
				<label for="ps"> Pseudo : </label>
				<input type="text" id="ps" name="ps" placeholder="Pseudo" pattern="^([A-Za-zéè0-9\-_ ]|[\p{L}0-9\-]){1,30}$" required>
			</div>
			<div>
				<label for="mail"> Email : </label>
				<input type="email" id="mail" name="email" placeholder="Email" pattern="^[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,4}$" required>
				<span class="valid"></span>
				<p class="mini"> Attention un email vous sera envoyé pour debloquer votre compte </p>
			</div>
			<input type="submit" value="Envoyé">
			<a href="accueil.php" class="mini"> J'ai déjà un compte </a>
			<a href="nouveau.php" class="mini"> Creer un compte </a>
		</form>
	</body>
</html>