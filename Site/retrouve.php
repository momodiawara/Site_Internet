<?php
	/*
	
	
		Utilisation : oubli.php,modif.php
		Role: Gerer la perte de mot de passe
	
	
	*/
	/*
		Si mot de passe perdu
	*/
	function perdu ($connexion,$mail,$pseudo) {
		sleep(1);
		$mail=mysqli_real_escape_string($connexion,$mail);
		$pseudo=mysqli_real_escape_string($connexion,$pseudo);
		$req='SELECT id,code_activation FROM users WHERE email="'.$mail.'" AND pseudo="'.$pseudo.'" AND activation="1";';
		$result=mysqli_query($connexion,$req);
		$compte=mysqli_fetch_assoc($result);
		if($compte) {
			$destinataire = str_replace(array("\n","\r",PHP_EOL),' ',htmlspecialchars($mail)); //evite l'envoie à un mail etranger
			$sujet = "Changer de mot de passe" ;
			$entete = "From: inscription@st-sites.com";
			$message = 'Pour changer le mot de compte, veuillez cliquer sur le lien ci dessous
ou le copier/coller dans votre navigateur internet.
https://st-sites.com/projet/modif.php?id='.$compte["id"].'&code_activation='.urlencode($compte["code_activation"]).'
Si ce mail ne vous concerne pas, supprimer le.
---------------
Ceci est un mail automatique, Merci de ne pas y répondre.';
			/* Envoie du mail */
			if(!mail($destinataire, $sujet, $message, $entete)) {
				erreur("Echec de l'envoie du mail",1);
			}
			else {
				reussi("Un email à été envoyé à ".$mail,1);
			}
					
		}
		else {
			erreur("Aucun compte correspondant",1);
		}
		
	}
	/*
		Regarde si les infos requises pour rentrer sur la page
	*/
	function vrai ($connexion,$get) {
		if(isset($get["id"]) && isset($get["code_activation"]) && !empty($get["id"]) && !empty($get["code_activation"])) {
			$id=mysqli_real_escape_string($connexion,$get["id"]);
			$act=mysqli_real_escape_string($connexion,$get["code_activation"]);
			$req='SELECT id FROM users WHERE id="'.$id.'" AND code_activation="'.$act.'";';
			$result=mysqli_query($connexion,$req);
			$compte=mysqli_fetch_assoc($result);
			if(!$compte) {	
				header("Location:accueil.php");
				exit;
			}
		}
		else {
			header("Location:accueil.php");
			exit;
		}
	}
	/*
		si informations correctent modifie le mot de passe
	*/
	function retrouve($connexion,$post) {
		if($post["pss1"]==$post["pss2"]) {
			$id=mysqli_real_escape_string($connexion,$post["id"]);
			$act=mysqli_real_escape_string($connexion,$post["code"]);
			$pss=mysqli_real_escape_string($connexion,$post["pss1"]);
			$req='SELECT email,pseudo FROM users WHERE id="'.$id.'" AND code_activation="'.$act.'";';
			$result=mysqli_query($connexion,$req);
			$compte=mysqli_fetch_assoc($result);
			sleep(1);
			if($compte) {
				$req='SELECT password FROM users WHERE email="'.$compte["email"].'";';
				$result2=mysqli_query($connexion,$req);
				while($pris=mysqli_fetch_assoc($result2)) {
					if(password_verify($pss,$pris["password"])) {
						header('Location:modif.php?id='.urlencode($post["id"]).'&code_activation='.urlencode($post["code"]).'&error=2');
						exit;
					}
				}
				$req='UPDATE users SET password="'.password_hash($pss, PASSWORD_DEFAULT).'" WHERE id="'.$id.'" AND code_activation="'.$act.'";';
				$result3=mysqli_query($connexion,$req);
				update ($connexion,$compte["pseudo"]);
				reussi("Mot de passe changé",1);
			}
		}
		else {
			header('Location:modif.php?id='.urlencode($post["id"]).'&code_activation='.urlencode($post["code"]).'&error=1');
			exit;
		}
	}
	/*
		erreur de modification de compte
	*/
	function error ($i) {
		if(isset($i)) {
			if($i==1) {
				erreur("Mots de passe différents",1);
			}
			else if($i==2) {
				erreur("Le compte existe déjà",1);
			}
			return 1;
		}
		return 0;
	}
?>