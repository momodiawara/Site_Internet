<?php
/* 


	Utilisation: add.php
	Role: Ajout des nouveaux fichiers
	

*/
/* 
	ajoute le travail
*/
function add ($connexion,$infos,$files,$session) {
	/* Gestion des informations */
	$annee=(int) $session["année"];
	$nom=mysqli_real_escape_string($connexion,htmlentities($infos["titre"]));
	if(!empty($infos["contenu"]) && $infos["contenu"]!="Ecrire le contenu ici (laisser vide si pas de texte)") {
		$contenu=mysqli_real_escape_string($connexion,$infos["contenu"]);
	}
	$univ=mysqli_real_escape_string($connexion,$session["université"]);
	$matiere=mysqli_real_escape_string($connexion,$infos["matières"]);
	$filiere=mysqli_real_escape_string($connexion,$session["fi"]);
	$type=0;
	if($infos["type"]) {
		$type=1;
	}
	/* si vide */
	if(!isset($contenu) && empty($files['pdf']['name'][0]) && empty($files['docs']['name'][0]) && empty($files['img']['name'][0])) {
		erreur("Le travail doit contenir quelque chose!",1);
		return;
	}
	/* verifie année */
	if($annee<1 || $annee>8) {
		erreur("Année incorrecte",1);
		return;
	}
	/* verifi si université existe bien */
	if(!existe($univ,"universités",$connexion)) { 
		erreur("Université inconnue",1);
		return;
	}
	/* verifi si filière existe bien */
	if(!existe($filiere,"filières",$connexion)) { 
		erreur("Filière inconnue",1);
		return;
	}
	/* verifi si matière existe bien */
	if(!existe($matiere,"matières",$connexion)) {
		erreur("Matière inconnue",1);
		return;
	}
	/* regarde si fichiers correct */
	if(!empty($files['pdf']['name'][0])) {
		$pdfs=$files['pdf'];
		if(!verific("pdf",$pdfs,10*1000000,"","application/pdf",2)) {
			return;
		}
	}
	if(!empty($files['img']['name'][0])) {
		$images=$files['img'];
		if(!verific("image",$images,10*1000000,"image","",3)) {
			return;
		}
	}
	if(!empty($files['doc']['name'][0])) {
		$docs=$files['doc'];
		if(!verific("document",$docs,10*1000000,"","",5)) {
			return;
		}
	}
	/* insertion du travail */
	$part1='INSERT INTO travaux(nom,author,matiere,université,filiere,année,typ';
	$part2=' VALUES("'.$nom.'",'.$session["id"].','.$matiere.','.$univ.','.$filiere.','.$annee.','.$type;
	if(isset($pdfs)) {
		$part1=$part1.',pdf';
		$part2=$part2.',1';
	}
	if(isset($images)) {
		$part1=$part1.',image';
		$part2=$part2.',1';
	}
	if(isset($docs)) {
		$part1=$part1.',document';
		$part2=$part2.',1';
	}
	if(isset($contenu)) {
		$part1=$part1.',text';
		$part2=$part2.',"'.$contenu.'"';
	}
	$req=$part1.')'.$part2.');';
	$result=mysqli_query($connexion,$req);
	if(!$result) {
		erreur("une erreur inconnue est survenue",1);
		return;
	}
	/* id du dernier travail */
	$req="select MAX(id) AS id FROM travaux";
	$result=mysqli_query($connexion,$req);
	$id=mysqli_fetch_assoc($result);
	/* Caractere interdit pour fichier */
	$interdit=array("<",">","|"," ",":","*","?",'"',"\\","/");
	/* nom du repertoir ou seront mis les fichiers */
	$rep=str_replace($interdit,"-",substr($id["id"].$nom,0,6));
	if(isset($images)) {
		/* creer le repertoire */
		if(!create("images_projet/",$rep,$nom,$images,$interdit,$id["id"])) {
			return;
		}
	}
	
	if(isset($docs)) {
		/* test si le fichier zip existe deja */
		if(!file_exists("documents/".$rep.".zip")) {
			/* on fait appel à la classe ZipArchive */
			$zip = new ZipArchive();
			/* creation de l'archive */
			$zip->open("documents/".$rep.'.zip', ZipArchive::CREATE);
			/* ajout des fichiers */
			$nb=0;
			foreach($docs["tmp_name"] as $i) {
				if($zip->addFile($i, $docs["name"][$nb])==0) {
					erreur("2 fichiers identique",1);
					supprimedir("pdf/".$rep."/");
					$zip->close();
					unlink('documents/'.$rep.".zip");
					return;
				}
				
				$nb++;
			}
			$zip->close();
		}
		else {
			erreur("Les documents n'ont pas pu être envoyé",1);
			supprimedir("pdf/".$rep."/");
			return;
		}

	}
	if(isset($pdfs)) {
		/* creer le repertoire */
		if(!create("pdf/",$rep,$nom,$pdfs,$interdit,$id["id"])) {
			supprimedir("pdf/".$rep."/");
			unlink("documents/".$rep.".zip");
			return;
		}
	}
	header("Location:add.php?mes=1");
}
/*
	supprime le travail si erreur
*/
function supprime($connexion,$id) {
	$req='DELETE FROM travaux WHERE id="'.mysqli_real_escape_string($connexion,$id).'";';
	$result=mysqli_query($connexion,$req);
}
/* 
	supprime repertoire et son contenu si erreur
*/
function supprimedir($detruit) {
	if($dir=opendir($detruit)) {
		while($fichier = readdir($dir)) {
			if($fichier!="." && $fichier!="..") {
				unlink($detruit.'/'.$fichier);
			}
		}
		closedir($dir);	
		rmdir($detruit);
	}
}

/* Verification des fichiers */
function verific($nom,$file,$taille,$type,$ext,$nb) {
	if(sizeof($file["name"])>$nb) {
		erreur($nb." ".$nom."s maximum!",1);
		return;
	}
	foreach($file["name"] as $infos) {
		if(preg_match("#\..+\.#",$infos)) {
			erreur('opération annulé : fichier à double type interdit',1);
			return 0;
		}
	}
	foreach($file["size"] as $infos) {
		if($infos>$taille) {
			erreur("Taille limite de ".($taille/1000)."ko dépassé",1);
			return 0;
		}
	}
	if(!empty($ext)) {
		foreach($file["type"] as $infos) {
			if($infos!=$ext) {
				erreur("Les ".$nom."s doivent être des ".$nom."s!",1);
				return 0;
			}
		}
	}
	if(!empty($type)) {
		foreach($file["type"] as $infos) {
			if(!preg_match("#^$type/#",$infos)) {
				erreur("Type non respecté",1);
				return 0;
			}
		}
	}
	foreach($file["error"] as $infos) {
		if($infos>0) {
			erreur("Une erreur est survenue",1);
			return 0;
		}
	}
	return 1;
}
/* ajoute les documents au serveur */
function create($ou,$rep,$nom,$fic,$interdit,$id) {
	$nb=0;
	/* Creer le repertoire */
	if(mkdir($ou.$rep)) {
		foreach($fic["tmp_name"] as $i) {
				$monfic=str_replace($interdit,"-",substr(crypt($nom),0,6));
				while(file_exists("images_projet/".$rep."/".$monfic)) {
					$monfic=str_replace($interdit,"-",substr(crypt($nom),0,6));
				}
				/* ajoute le fichier ou annule tout */
				if(!move_uploaded_file($i,$ou.$rep."/".$monfic.".".pathinfo($fic["name"][$nb],PATHINFO_EXTENSION))) {
					erreur("Une erreur est survenue",1);
					supprime($connexion,$id);
					supprimedir($ou.$rep."/");
					return 0;
				}
				$nb++;
			}
			
		}
		else {
			erreur("Une erreur est survenue",1);
			supprime($connexion,$id);
			return 0;
		}
		return 1;
}