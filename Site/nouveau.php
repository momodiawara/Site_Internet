<!DOCTYPE html>
<html>
	<head>
		<title> Creer un compte </title>
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
			require_once("creation.php");
			session_start();
			$connexion=connexion();
			if($connexion) {
				if (isset($_POST["prenom"]) && (isset($_POST["nom"]) && isset($_POST["pseudo"]) && isset($_POST["email"]) && isset($_POST["pss1"]) && isset($_POST["pss2"]) && isset($_POST["ann"]) && isset($_POST["filières"]) && isset($_POST["universités"])) ) {
					creation($connexion,$_POST);
				}
			}
			cocher($_POST);
		?>
	</head>
	<body>
		<form method="post" action="">
			<h3> Creer un compte </h3>
			<div>
				<table>
					<tr> 
						<th> <label for="prenom"> Prenom : </label> </th>
						<th> <label for="nom"> Nom : </label> </th>
					</tr>
					<tr>
						<td> 
							<input type="text" id="prenom" name="prenom" placeholder="Prenom" <?php if(isset($_SESSION["prenom"])) { echo 'value="'.$_SESSION["prenom"].'"'; } ?> pattern="^([A-Za-zéè0-9\-_ ]|[\p{L}0-9\-_ ]){1,30}$" required>
							<span class="valid"></span> 
						</td>
						<td>
							<input type="text" id="nom" name="nom" placeholder="Nom" <?php if(isset($_SESSION["nom"])) { echo 'value="'.$_SESSION["nom"].'"'; } ?> pattern="^([A-Za-zéè0-9\-_ ]|[\p{L}0-9\-_ ]){1,30}$" required>
							<span class="valid"></span>
						</td>
					</tr>
				</table>
						
			</div>
			<div>
				<label for="pseudo"> Pseudo : </label>
				<div>
					<input type="text" id="pseudo" name="pseudo" placeholder="Pseudo" <?php if(isset($_SESSION["pseudo"])) { echo 'value="'.$_SESSION["pseudo"].'"'; } ?> pattern="^([A-Za-zéè0-9\-_ ]|[\p{L}0-9\-_ ]){1,30}$" required>
					<span class="valid"></span>
				</div>
			</div>
			<div>
				<label for="mail"> Email : </label>
				<div>
					<input type="email" id="mail" name="email" placeholder="Email" <?php if(isset($_SESSION["email"])) { echo 'value="'.$_SESSION["email"].'"'; } ?> title="l'email ne correspond pas" pattern="^[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,4}$" required>
					<span class="valid"></span>
				</div>
				<p class="mini"> Attention un email vous sera envoyé pour verifier votre compte </p>
			</div>
			<div>
				<table>
					<tr> 
						<th> <label for="pss1"> Mot de Passe : </label> </th>
						<th> <label for="pss2"> Verification : </label> </th>
					</tr>
					<tr>
						<td> 
							<input type="password" id="pss1" name="pss1" placeholder="8 caractères minimum" title="8 caractères minimum"  pattern="^.{8,256}$" required>
							<span class="valid"></span>
						</td>
						<td>
							<input type="password" id="pss2" name="pss2" placeholder="Retaper le mot de passe" title="8 caractères minimum" pattern="^.{8,256}$" required>
							<span class="valid"></span>
						</td>
					</tr>
				</table>
			</div>
			<div>
				<label for="universités"> Université : </label>
				<?php if($connexion) { selector("universités",$connexion,0); } ?>
			</div>
			<div>
				<table>
					<tr>
						<th> <label for="filières"> Filière : </label> </th>
						<th> <label for="an"> Année : </label> </th>
					</tr>
					<tr>
						<td>
							<?php if($connexion) { selector("filières",$connexion,0); } ?>
						</td>
						<td> 
							<input id="an" type="number" name="ann" min="1" max="8" value="<?php if(!isset($_SESSION["annee"])) { echo "1";} else { echo $_SESSION["annee"]; } ?>" size="3">
						</td>
					</tr>
				<table>
			</div>
			<div>
				<div class="mini">
					<input type="checkbox" id="inf1" name="co" <?php if(isset($_SESSION["co"])) { echo "checked"; } ?> >
					<label for="inf1"> Rester anonyme (identifier seulement par le pseudo) <label>
				</div>
			</div>
			<input type="hidden" name="creer" value="1">
			<input type="submit" value="Creer un compte">
			<a href="accueil.php" class="mini"> J'ai déjà un compte </a>
		<a href="oubli.php" class="mini"> Mot de passe oublié </a>
		</form>
		<?php mysqli_close($connexion); ?>
	</body>
</html>