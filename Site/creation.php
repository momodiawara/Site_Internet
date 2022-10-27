<?php
	/*
	
	
		Utilisation: nouveau.php
		Role: Gestion de la page et creation du compte
		
	
	*/
	/*
		Verifie informations formulaire et envoie erreur
	*/
	function correct ($tab) {
		/* mot de passe invalide */
		if(!preg_match("/^.{8,255}$/",$tab["pss1"])) {
			erreur("Mot de passe invalide",1);
			return 0;
		}
		/* prenom invalide */
		if (!preg_match("/^[\w -_]{1,30}$/",$tab["prenom"])) {
			erreur("Prenom invalide",1);
			return 0;
		}
		/* Nom invalide */
		if (!preg_match("/^[\w -_]{1,30}$/",$tab["nom"])) {
			erreur("Nom invalide",1);
			return 0;
		}
		/* Pseudo invalide */
		if (!preg_match("/^[\w -_]{1,30}$/",$tab["pseudo"])) {
			erreur("Pseudo invalide",1);
			return 0;
		}
		/* Email invalide */
		if(!preg_match("/^[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,4}$/",$tab["email"]) ||  strlen($tab["email"])>255 ) {
			erreur("Email invalide",1);
			return 0;
		}
		/* Année invalide */
		if($tab["ann"]<1 && $tab["ann"]>8) {
			erreur("Année invalide",1);
			return 0;
		}
		/* mot de passe différent */
		if($tab["pss1"]!=$tab["pss2"]) {
			erreur("Les mots de passe sont différents",1);
			return 0;
		}
		return 1;
	}
	/*
		Creer un compte et test si correct
	*/
	function creation($connexion,$tab) {
		if(correct ($tab)) {
			foreach($tab as $t) {
				$t=mysqli_real_escape_string($connexion,$t); //evite insertion sql
			}
			sleep(1); 
			/* verifi si université existe bien */
			if(!existe($tab["universités"],"universités",$connexion)) { 
				erreur("Université inconnue",1);
				return;
			}
			/* verifi si filière existe bien */
			if(!existe($tab["filières"],"filières",$connexion)) { 
				erreur("Filière inconnue",1);
				return;
			}
			/* verifie si le pseudo existe */
			$req='SELECT id FROM users WHERE pseudo="'.$tab["pseudo"].'";';
			$psexist=mysqli_query($connexion,$req);
			if($pseudo=mysqli_fetch_assoc($psexist)) {
				erreur("Le pseudo ".$tab["pseudo"]." est déjà pris",1);
				return;
			}
			/* verifie si mail+mot de passe existe */
			$req='SELECT password FROM users WHERE email="'.$tab["email"].'";';
			$comptexiste=mysqli_query($connexion,$req);
			//regarde tout les comptes à cette adresse email
			while($compte=mysqli_fetch_assoc($comptexiste)) {
				if (password_verify($tab["pss1"],$compte["password"])) {
					erreur("Le compte existe déjà",1);
					return;
				}
			}
			/* Regarde si anonymat */
			$anonyme=0;
			if(isset($tab["co"])) {
				$anonyme=1;
			}
			/* clé d'activation */
			$activation=mysqli_escape_string($connexion,md5(microtime(TRUE)*100000));
			/* creation du compte*/
			$req='INSERT INTO users (nom,prenom,pseudo,password,email,anonyme,filiere,université,année,code_activation) VALUES ("'.$tab["nom"].'","'.$tab["prenom"].'","'.$tab["pseudo"].'","'.password_hash($tab["pss1"], PASSWORD_DEFAULT).'","'.$tab["email"].'","'.$anonyme.'","'.$tab["filières"].'","'.$tab["universités"].'","'.$tab["ann"].'","'.$activation.'");';
			$creer=mysqli_query($connexion,$req);
			if($creer) {
				$req='SELECT id FROM users WHERE pseudo="'.$tab["pseudo"].'";';
				$ids=mysqli_query($connexion,$req);
				$id=mysqli_fetch_assoc($ids);
				if($id) {
					/* mail à envoyé */
					$destinataire = str_replace(array("\n","\r",PHP_EOL),' ',htmlspecialchars($tab["email"])); //eviter envoi à autre email
					$sujet = "Activer votre compte" ;
					$entete = "From: inscription@st-sites.com";
					$message = 'Pour activer votre compte, veuillez cliquer sur le lien ci dessous
ou le copier/coller dans votre navigateur internet.
https://st-sites.com/projet/accueil.php?id='.$id["id"].'&code_activation='.urlencode($activation).'
Si ce mail ne vous concerne pas, supprimer le.
---------------
Ceci est un mail automatique, Merci de ne pas y répondre.';
					/* Envoie du mail */
					if(!mail($destinataire, $sujet, $message, $entete)) {
						erreur("Echec de l'envoie du mail",1);
					}
					else {
						reussi("Compte créé: un email à été envoyé à ".$tab["email"],1);
					}
				}
				else {
					/* si id non trouvé */
					erreur("Erreur inconnu",1);
				}
				
			}
			else {
				/* echec de la creation du compte */
				erreur("La creation du compte à échoué",1);
			}
		}
	}
	/*
		Permet de garder informations
	*/
	function cocher ($tab) {
		/*
			Regarde l'email
		*/
		if (isset($tab["email"])) {
			$_SESSION["email"]=$tab["email"];
		}
		/*
			Regarde le pseudo
		*/
		if (isset($tab["pseudo"])) {
			$_SESSION["pseudo"]=$tab["pseudo"];
		}
		/*
			Regarde le nom
		*/
		if (isset($tab["nom"])) {
			$_SESSION["nom"]=$tab["nom"];
		}
		/*
			Regarde le prenom
		*/
		if (isset($tab["prenom"])) {
			$_SESSION["prenom"]=$tab["prenom"];
		}
		/*
			Regarde l'anonymat
		*/
		if (isset($tab["co"])) {
			$_SESSION["co"]=$tab["co"];
		}
		else {
			if(isset($_SESSION["co"])) {
				unset($_SESSION["co"]);
			}
		}
		/*
			Regarde la matiere
		*/
		if (isset($tab["matières"])) {
			$_SESSION["matières"]=$tab["matières"];
		}
		/*
			Regarde la filière
		*/
		if (isset($tab["filières"])) {
			$_SESSION["filières"]=$tab["filières"];
		}
		/*
			Regarde l'année
		*/
		if (isset($tab["ann"])) {
			$tab["ann"]=(int) $tab["ann"];
			if ( $tab["ann"] > 0 && $tab["ann"] < 9 ) {
				$_SESSION["annee"]=$tab["ann"];	
			}
			else {
				unset($_SESSION["annee"]);
			}
		}
		/*
			Regarde l'université
		*/
		if (isset($tab["universités"])) {
			$_SESSION["universités"]=$tab["universités"];
		}
	}
?>