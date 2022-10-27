<?php 
/*
	Utilisation: accueil.php
	Role: affichage des travaux et selection

*/
function options ($tab) {
	if(isset($tab["verif"])) {
		/*
			Regarde si TD coché
		*/
		if (!isset($tab["TD"])) {
			setcookie("td","1",time()+2419200);
			$_COOKIE["td"]=1;
		}
		else if (isset($_COOKIE["td"])) {
			setcookie("td");
			unset($_COOKIE["td"]);
		}
		/*
			Regarde si TP coché
		*/
		if (!isset($tab["TP"])) {
			setcookie("tp","1",time()+2419200);
			$_COOKIE["tp"]=1;
		}
		else if (isset($_COOKIE["tp"])) {
			setcookie("tp");
			unset($_COOKIE["tp"]);
		}
		/*
			Regarde la matiere
		*/
		if (isset($tab["matières"])) {
			setcookie("matières",$tab["matières"],time()+2419200);
			$_COOKIE["matières"]=$tab["matières"];
		}
		if (isset($tab["filières"])) {
			setcookie("filières",$tab["filières"],time()+2419200);
			$_COOKIE["filières"]=$tab["filières"];
		}
		/*
			Regarde l'année
		*/
		if (isset($tab["ann"])) {
			$tab["ann"]=(int) $tab["ann"];
			if ( $tab["ann"] > 0 && $tab["ann"] < 9 ) {
				setcookie("annee",$tab["ann"],time()+2419200);	
				$_COOKIE["annee"]=$tab["ann"];	
			}
			else {
				setcookie("annee");
				unset($_COOKIE["annee"]);
			}
		}
		/*
			Regarde l'université
		*/
		if (isset($tab["universités"])) {
			setcookie("universités",$tab["universités"],time()+2419200);
			$_COOKIE["universités"]=$tab["universités"];
		}
	}
}
/* 
	Trouve tout les travaux correspondant à la recherche
*/
function research ($co,$search) { 
	$mots=explode(" ",strtolower(trim($search))); 
	$mots[0]=mysqli_escape_string($co,$mots[0]); 
	$req='SELECT id FROM travaux where nom LIKE "%'.$mots[0].'%"'; 
	unset($mots[0]); 
	foreach ($mots as $s) { 
		$s=mysqli_escape_string($co,$s); 
		$req=$req.' UNION ALL SELECT id FROM travaux where nom LIKE "%'.$s.'%"';
		/* Un pour chaque mot */
	} 
	$req=$req.";";
	$result=mysqli_query($co,$req);
	if ($result) {
		$fin=array(); 
		/* compte le nombre de mot correct */
		while( $resultat=mysqli_fetch_assoc($result)) { 
			if (isset($fin[$resultat["id"]])) { 
				$fin[$resultat["id"]]++; 
			} 
			else{ 
				$fin[$resultat["id"]]=1; 
			}
		} 
		/* Trie les travaux par nb de nombres de mot correctent */
		asort($fin);
		return $fin; 
	}
	return array();
	
	
}
/*
	Critères de recherche
*/
function conditions ($connexion,$cook,$moi) {
	 $where="";
	 if(isset($cook["universités"]) && $cook["universités"]!="all") {
	 	$where="travaux.université=".mysqli_escape_string($connexion,$cook["universités"]);
	 }
	 if(isset($cook["matières"]) && $cook["matières"]!="all"){
		if(!empty($where)){
			$where=$where." AND";
		}
		$where=$where." matiere=".mysqli_escape_string($connexion,$cook["matières"]);
	 }
	 if(isset($cook["filières"]) && $cook["filières"]!="all"){
		if(!empty($where)){
			$where=$where." AND";
			}
		$where=$where." travaux.filiere=".mysqli_escape_string($connexion,$cook["filières"]);
		}	
	if(isset($cook["annee"]) && $cook["annee"]!=0){
		if(!empty($where)){
			$where=$where." AND";
			}
		$where=$where." travaux.année=".mysqli_escape_string($connexion,$cook["annee"]);
	 }
	 if(isset($cook["tp"]) && $cook["tp"]==1 && (!isset($cook["td"]) || !$cook["td"])){
		if(!empty($where)){
			$where=$where." AND";
		}
		$where=$where." travaux.typ=0";	
	}
	 if(isset($cook["td"]) && $cook["td"]==1 && (!isset($cook["tp"]) || !$cook["tp"])){
		if(!empty($where)){
			$where=$where." AND";
		}
		$where=$where." travaux.typ=1";	
	}
	 if(isset($moi) && $moi!=0){
		if(!empty($where)){
			$where=$where." AND";
		}
		$where=$where." author=".mysqli_escape_string($connexion,$moi);	
	}
	return $where;
}
/*
	fonction d'affichage des travaux
*/

function affi($connexion,$cook,$get,$moi){
	$where=conditions($connexion,$cook,$moi);
	//recherche
	if(isset($get["search"]) && !empty($get["search"])) {
		 $infos=research($connexion,$get["search"]);
	}
	 $req='SELECT author,travaux.nom as nom1,travaux.id,pseudo, matiere,travaux.université, travaux.filiere, pdf,typ,travaux.image, document,text,travaux.année FROM travaux JOIN users ON users.id=travaux.author ';
	 if(!empty($where)) {
	 	$req=$req." WHERE ".$where;
	}
	$req=$req.' ORDER BY travaux.nom;';
	 $result=mysqli_query($connexion, $req);
	 $array=array(array());
	 $nb=0;
	 /* fusionne info de recherche et conditions */
	 while($info=mysqli_fetch_assoc($result)){
		$array[$nb]=$info;
		if(isset($infos)) {
			if(isset($infos[$info["id"]])) {
				$array[$nb]["nb"]=$infos[$info["id"]];
			}
			else {
			     $array[$nb]=null;
			     $nb--;
			}
		}
		$nb++;
	}
	if(isset($infos)) {
		/* Trie les travaux par nb de nombres de mot correctent si recherche */
		usort($array,"cmp");
	}
	/* affiche */
	foreach($array as $s) {
		if($s!=null) {
		$req='SELECT nom FROM matières WHERE id='.$s["matiere"].';';
  		$result1= mysqli_query($connexion,$req);
		$m=mysqli_fetch_assoc($result1);
		$req='SELECT nom FROM universités WHERE id='.$s["université"].';';
		$result2=mysqli_query($connexion,$req);
		$u=mysqli_fetch_assoc($result2);
		$req='SELECT nom FROM filières WHERE id='.$s["filiere"].';';
		$result3=mysqli_query($connexion,$req);
		$f=mysqli_fetch_assoc($result3);
		$req='SELECT nom,prenom,pseudo,anonyme FROM users WHERE id='.$s["author"].';';
		$result4=mysqli_query($connexion,$req);
		$A=mysqli_fetch_assoc($result4);
		if($A["anonyme"]){
			$h=$A["pseudo"];
		}
		else{
			$h=$A["prenom"].' '.$A["nom"];
		}
		
		echo '<a href="travail.php?id='.$s["id"].'#contenu" class="article">';
		echo '<div class="typ"> ';
		if($s["typ"]) {
			echo '<img src="images/TP.png" alt="TP">'; 
		}	
		else {
			echo '<img src="images/TD.png" alt="TD">'; 
		}
		echo "</div>";
		echo '<div class="T"><h3>'.$s["nom1"].'</h3>'.'<div class="A">'.'<p>'."Université : ".$u["nom"].'</p>'.'<p>'."Matière : ".$m["nom"].'</p>'.'</div>'.'<div class="A"><p>'."Filière : ".$f["nom"].'</p>'.'<p>'."Année : ".$s["année"].'</p>'.'</div>';
		echo '<div class="D"><span ';
		if($s["text"]!=null) {
			echo 'style="background-color:green"';
		}
		echo '> texte </span><span ';
		
		if($s["document"]){
			echo 'style="background-color:green"';
		}
		echo '> documents </span><span ';
		if($s["image"]){
			echo 'style="background-color:green"';
		}
		echo '> images </span><span ';
		if($s["pdf"]){
			echo 'style="background-color:green"';
		}
		echo '> pdfs </span>';
		echo "</div>";
		echo '<div class="Z"> Auteur : '.$h; 
		echo"</div>";
		echo '</div>';
		echo '</a>';
	}
	}
	if(isset($infos) || isset($cook["filières"])) {
		if(!$nb) {
			erreur("Aucun élément ne correspond à votre requête",1);
		}
		else {
			if ($nb==1) reussi("1 résultat à la recherche",1);
			else reussi($nb." résultats à la recherche",1);
		}
	}
}
/*
	Fonction de trie du tableau
*/
function cmp($a, $b) {
    if ($a["nb"] == $b["nb"]) {
        return 0;
    }
    if ($a["nb"] > $b["nb"]){
       return -1;
    }
    return 1;
}

?>