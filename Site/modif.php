<!DOCTYPE html>
<html>
	<head>
		<title> Modifier son mot de passe </title>
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
			session_start();
			if (error($_GET["error"])) {
					unset($_GET["error"]);
			}
			$connexion=connexion();
			if($connexion) {
				vrai($connexion,$_GET);
			}
			else {
				header("Location:accueil.php");
			}
		?>
	</head>
	<body>
		<form method="post" action="oubli.php">
			<h3> Changer de mot de passe </h3>
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
			<input type="hidden" name="code" value="<?php echo $_GET["code_activation"]; ?>">
			<input type="hidden" name="id" value="<?php echo $_GET["id"]; ?>">
			<input type="submit" value="Valider">
		</form>
		<?php mysqli_close($connexion); ?>
	</body>
</html>