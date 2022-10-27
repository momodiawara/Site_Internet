<?php
	/*
	
	
		Utilisation: partout
		Role: operation de base
	
	
	*/
	ini_set('display_errors','off');
	/*
		création <select>
	*/
	
	function selector ($table,$co,$tout) {
		$req='SELECT nom,id FROM '.$table.';';
		$result=mysqli_query($co,$req);
		if($result) {
			echo '<select name="'.$table.'" id="'.$table.'"> ';
			if($tout) { 
				echo '<option value="all"> Tout </option>'; 
			}
			while($ligne=mysqli_fetch_assoc($result)) {
				echo '<option value="'.$ligne["id"].'"';
				if((isset($_COOKIE[$table]) && $_COOKIE[$table]==$ligne["id"]) || (isset($_SESSION[$table]) && $_SESSION[$table]==$ligne["id"])) {
					echo ' selected';
				}
				echo ' > '.$ligne["nom"].' </option>';
			}
			echo '</select>';
		}
		else {
			erreur("Erreur inconnu",1);
		}
	}
	/*
		Connection base de donnée
	*/
	function connexion () {
		//$connexion = mysqli_connect ("pams.script.univ-paris-diderot.fr", "dthauv22", "w7X(g41W", "dthauv22" ) ;
		//$connexion = mysqli_connect("localhost","root","","projet");
		$connexion = mysqli_connect("mysql55-174.perso","stsitesbaso","Babouchou12","stsitesbaso");
		if (!$connexion) {
			erreur("Le site est indisponible pour le moment",0); 
			return 0;
		}

		mysqli_set_charset($connexion, "utf8");
		return $connexion;
		
	}
	$f=1;	
	
	/*
		Message d'erreur
	*/
	function erreur ($mess,$anim) {
		global $f;
		if($f) {
			if($anim) echo '<div class="erreur anime"> '.htmlspecialchars($mess).' </div>';
			else echo '<div class="erreur"> '.htmlspecialchars($mess).' </div>';
			$f=0;
		}
	}
	/*
		Message de succes
	*/
	function reussi ($mess,$anim) {
		global $f;
		if($f) {
			if ($anim) 
				echo '<div class="valide anime"> '.htmlspecialchars($mess).' </div>';
			else 
				echo '<div class="valide"> '.htmlspecialchars($mess).' </div>';
			$f=0;
		}
	}
	
	/*
		Verifie si connecter
	*/
	function connecter ($connexion,$tab) {
		if(isset($tab["pd"]) && isset($tab["psw"]) && isset($tab["université"]) && isset($tab["année"]) && isset($tab["fi"]) && isset($tab["nom"]) && isset($tab["prenom"])
		&& !empty($tab["pd"]) && !empty($tab["psw"]) && !empty($tab["université"]) && !empty($tab["année"]) && !empty($tab["fi"]) && !empty($tab["nom"]) && !empty($tab["prenom"])) {
			$pseudo=mysqli_escape_string($connexion,$tab["pd"]);
			$pass=$tab["psw"];
			$univ=mysqli_escape_string($connexion,$tab["université"]);
			$ann=mysqli_escape_string($connexion,$tab["année"]);
			$fil=mysqli_escape_string($connexion,$tab["fi"]);
			$nom=mysqli_escape_string($connexion,$tab["nom"]);
			$prenom=mysqli_escape_string($connexion,$tab["prenom"]);
			$req='SELECT password FROM users WHERE pseudo="'.$pseudo.'" AND activation="1" AND université="'.$univ.'" AND année="'.$ann.'" AND filiere="'.$fil.'"  AND nom="'.$nom.'"  AND prenom="'.$prenom.'";';
			$result=mysqli_query($connexion,$req);
			while($comptes=mysqli_fetch_assoc($result)) {
				if(password_verify($pass,$comptes["password"])) {
					return 1;
				}		
			}
		}
		return 0;
	}
	/*
		Informations de connection
	*/
	function ajout(&$session,$comptes,$pass) {
		$session["pd"]=$comptes["pseudo"];
		$session["id"]=$comptes["id"];
		$session["psw"]=$pass;
		$session["université"]=$comptes["université"];
		$session["fi"]=$comptes["filiere"];
		$session["année"]=$comptes["année"];
		$session["anonyme"]=$comptes["anonyme"];
		$session["nom"]=$comptes["nom"];
		$session["prenom"]=$comptes["prenom"];
	}
	/*
		Change clé d'activation
	*/
	function update ($connexion,$pseudo) {
		if($connexion) {
			/* clé d'activation */
			$activation=mysqli_real_escape_string($connexion,md5(microtime(TRUE)*100000));
			$pseudo=mysqli_real_escape_string($connexion,$pseudo);
			$req='UPDATE users SET code_activation="'.$activation.'" WHERE pseudo="'.$pseudo.'";';
			$modif=mysqli_query($connexion,$req);
		}
	}
	
	function existe ($info,$table,$connexion) {
		$req='SELECT id FROM '.mysqli_escape_string($connexion,$table).' WHERE id="'.mysqli_escape_string($connexion,$info).'";';
		$result=mysqli_query($connexion,$req);
		return mysqli_fetch_assoc($result);
	}
?>