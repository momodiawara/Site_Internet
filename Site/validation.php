<?php
	/*
	
	
	Utilisation : accueil.php
	Role: Validation de la creation d'un compte
	
	
	*/
	/* Activation du compte */
	function validation($connexion,$id,$act) {
		$id=mysqli_escape_string($connexion,$id); 
		$act=mysqli_escape_string($connexion,$act); 
		$req='SELECT activation,pseudo,code_activation FROM users WHERE id="'.$id.'";';
		$result=mysqli_query($connexion,$req);
		$result2=mysqli_fetch_assoc($result);
		if(!$result2) {
			erreur("Le compte n'existe pas",1);
		}
		else if($result2 && $result2["activation"]) {
			erreur("Le compte est déjà activé",1);
		}
		else if ($act!=$result2["code_activation"]) {
			erreur("Code d'activation érroné",1);
		}
		else {
			$req='UPDATE users SET activation="1" WHERE id="'.$id.'" AND code_activation="'.$act.'";';
			$result=mysqli_query($connexion,$req);
			reussi("Le compte de ".$result2["pseudo"]." à été activé",1);
			update($connexion,$result2["pseudo"]);
		}
	}	
?>