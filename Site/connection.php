<?php 
/*
	
	
	Utilisation : accueil.php
	Role: Verifier connection
	
	
*/
/*
	Fonction de connexion
*/
function connex ($connexion,&$tab,&$session) {
	sleep(1);
	if(isset($tab["pseudo"]) && isset($tab["psw"]) && !empty($tab["pseudo"]) && !empty($tab["psw"]) ) {
		$email=mysqli_escape_string($connexion,$tab["pseudo"]);	
		$pass=$tab["psw"];
		unset($tab["psw"]);
		unset($tab["pseudo"]);
		$req='SELECT * FROM users WHERE email="'.$email.'" OR pseudo="'.$email.'";';
		$result=mysqli_query($connexion,$req);
		$comptes=mysqli_fetch_assoc($result);
		if(!$comptes) {
			erreur("Pseudo/Email érroné",1);
			return false;
		}
		if(password_verify($pass,$comptes["password"])) {
			if($comptes["activation"]) {
				ajout($session,$comptes,$pass);
				return true;
			}
			else {
				erreur("Compte non activé",1);
			}
		}	
		
		while($comptes=mysqli_fetch_assoc($result)) {
			if(password_verify($pass,$comptes["password"])) {
				if($comptes["activation"]) {
					ajout($session,$comptes,$pass);
					return true;
				}
				else {
					erreur("Compte non activé",1);
				}
			}	
			
		}
		erreur("Mot de passe érroné",1);
	}
	return false;
}
/*
	Affichage page d'accueil si connecter
*/
function infos($connexion,$infos) {
	echo '<div class="divtitre"> <label for="connexion" class="pointer titre"> ';
	if($connexion) {
		if(connecter ($connexion,$infos)) {
			if($infos["anonyme"]) {
				echo htmlspecialchars($infos["pd"]);
			}
			else {
				echo htmlspecialchars($infos["prenom"])." ".htmlspecialchars($infos["nom"]);
			}
			echo ' </label>
					<form method="POST" action="accueil.php" class="menu">
						<input type="hidden" name="verif" value="1">
						<input type="checkbox" class="button" id="connexion"> 
						<div class="menu2">
							<input type="reset" value="x" class="pointer reset" style="border-radius: 10px;">
							<a href="mestravaux.php#contenu" class="pointer"> Mes TD/TP </a>
							<a href="transit.php?r=2" class="pointer"> Ma Filière </a>
							<a href="transit.php?r=3" class="pointer"> Mon Université </a>
							<label for="tdfil" class="white"> Mes matières </label>
							<div class="titre" style="white-space:normal;">
				'.matieres($connexion,$infos["fi"],$infos["année"],$infos["université"]).'
							</div>
							<a href="add.php#contenu" class="pointer"> Deposer un TD/TP </a>
							<a href="deconnection.php" class="pointer"> Deconnexion </a>
						';
		}
		else {
			nonco();
		}
	}
	else {
		nonco();
	}
	echo "</div>
					</form>
					
				</div>";
}
/*
	Affichage par defaut de la barre de connection
*/

function nonco() {
	echo ' Se connecter </label>
					<form method="POST" action="" class="menu" >
						<input type="checkbox" class="button" id="connexion">
						<div>
							<input type="reset" value="x" class="reset pointer">
							<label for="id1" class="white"> Pseudo/Email : </label>
							<input id="id1" type="text" name="pseudo" placeholder="Pseudo/Email" required>
							<label for="id2" class="white"> Mot de passe : </label>
							<input id="id2" type="password" name="psw" pattern="^.{8,256}$" placeholder="Mot de passe" required>
							<input type="submit" value="Connexion">
							<div class="white">
								<a href="nouveau.php" class="mini pointer"> Creer un compte </a>
								<a href="oubli.php" class="mini pointer"> Mot de passe oublié </a>
							</div>
						';
}
/*
	<select> avec toutes les matieres d'une personne 
*/
function matieres($connexion,$fi,$an,$uni) {
	$fi=mysqli_escape_string($connexion,$fi);	
	$an=mysqli_escape_string($connexion,$an);	
	$req='SELECT matiere AS id, matières.nom AS nom FROM travaux JOIN matières ON matières.id=matiere WHERE filiere="'.$fi.'" AND année="'.$an.'" AND université="'.$uni.'";';
	$result=mysqli_query($connexion,$req);
	$retour="";
	$ens=array();
	while($s=mysqli_fetch_assoc($result)) {
		if(!in_array($s["id"],$ens)) {
			$ens[]=$s["id"];
			$retour=$retour.'<option value="'.$s["id"].'" '; 
			if((isset($_COOKIE["matières"]) && $_COOKIE["matières"]==$s["id"]) || (isset($_SESSION["matières"]) && $_SESSION["matières"]==$s["id"])) {
					$retour=$retour.' selected';
			}
			$retour=$retour.' >'.$s["nom"].'</option>';
		}
	}
	if(empty($retour)) {
		return 'Aucune matière';
	}
	else {
		return '<select class="divtitre" name="matières" id="tdfil">'.$retour.'</select> <input class="pointer" type="submit" value="<-">';
	}
	

}
?>