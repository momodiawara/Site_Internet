<?php
	require_once("bases.php");
	$connexion=connexion();
	session_start();
	if($connexion) {
		if(connecter($connexion,$_SESSION)) {
			if(isset($_GET["r"])){
				if($_GET["r"]==2){
					setcookie("filières",$_SESSION["fi"],time()+2419200);
				}
				if($_GET["r"]==3){
					setcookie("universités",$_SESSION["université"],time()+2419200);
				}
			}				
		}
	}
	header("Location:accueil.php#contenu");
?>