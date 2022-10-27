<?php
	/*
	
	
		Utilisation : travail.php
		Role: affichage de la page
	
	
	*/
	/*
		Regarde si les infos requises pour rentrer sur la page
	*/
	function changeTp( $connexion, $id){
		if(isset($id["id"]) && !empty($id["id"])){
			$i=mysqli_real_escape_string($connexion,$id["id"]);
			$req='SELECT travaux.id as id, typ, pdf, users.nom as monnom, users.prenom as monprenom, travaux.nom as nom, matiere, anonyme, travaux.année, pseudo, travaux.université, travaux.filiere, document,image , text FROM travaux JOIN users ON users.id=travaux.author WHERE travaux.id='.$i.';';
			$result=mysqli_query($connexion,$req);
			$td=mysqli_fetch_assoc($result);
			if($td){
				return $td;
			}
			else{
				header( "Location: accueil.php");
				exit;
			}
		}
		else{
			header( "Location: accueil.php");
			exit;
		}
		
	}
	/*
		Affichage en fonction du travail donnée
	*/
	function afficheTD($travail,$connexion){
		if( !$travail["pdf"] && !$travail["image"] && empty($travail["text"])){
			header( "Location: accueil.php");
			exit;
		}
		else{
			/* Caractere interdit pour fichier */
			$interdit=array("<",">","|"," ",":","*","?",'"',"\\","/");
			/* nom du repertoir ou seront mis les fichiers */
			$rep=str_replace($interdit,"-",substr($travail["id"].$travail["nom"],0,6));
			echo '<h1>'.$travail["nom"].'</h1>';
			if($travail["image"]){
			if($img = opendir("images_projet/".$rep)) {
					$compteur=1;
					while($d = readdir($img)){
						if($d != '.' && $d != '..'){
							echo '<input type="radio" style="display:none;" name="radio" id="ap'.$compteur.'" ';
							if ($compteur==1) {
								echo 'checked';
							}
							echo '>';
							echo '<img src="images_projet/'.$rep."/".$d.'" alt="Erreur d\'affichage" class="defil">';
							$compteur++;
						}
					}
					closedir($img);
					if($compteur!=2) {
						echo '<div class="buttons">';
						for($i=1;$i<$compteur;$i++) {
							echo '<label for="ap'.$i.'"> '.$i.' </label>';
							echo '<style> #ap'.$i.':checked + * { display:block; } </style>';
						}
						echo '</div>';
					}
					else {
						echo '<style> .defil { display:block; } </style>';
					}
				}
 			}
			echo '<div class="mesdoc">';
			/* Contenu */
			if($travail["text"]!=null){
				echo '<h3><label for="contenu"> Contenu : </label></h3>
					<textarea name="contenu" id="contenu" rows="30" spellcheck="false" readonly="1">'. $travail["text"].'</textarea>';
			}
			/* Lien vers les documents ( dans un zip ) */
			if($travail["document"]){
				$rep2='documents/'.$rep.'.zip';
				echo "<div>";
				download($rep2,"Télecharger les documents");
				echo "</div>";
			}
			if($travail["pdf"]){
				if($doss = opendir("pdf/".$rep)) {
					$compter=1;
					while($d = readdir($doss)){
						if($d != '.' && $d != '..'){
							echo "<div>";
							download ("pdf/".$rep."/".$d,"Regarder le pdf numéro ".$compter);
							echo "</div>";
							$compter++;
						}
					}
					closedir($doss);
				}
			}
			echo "</div>";
			echo '<table class="table"><tr><td>';
			/* Auteur */
			echo " Auteur : ";
			if($travail["author"]){
				echo $travail["pseudo"];
			}
			else {
				echo $travail["monprenom"]." ".$travail["monnom"];
			}
			echo "</td>";
			echo'<td>';
			/* Type (TD ou TP) */
			echo " Type : ";
			if($travail["typ"]){
				echo "TP";
			}
			else{ echo "TD";}
			echo '</td></tr>';
			echo '<tr><td>';
			/* Filière */
			echo " Filière : ";
			echo retrouveFil($travail["filiere"],$connexion);
			echo '</td><td>';
			/* Année */
			echo " Année : ";
			echo $travail["année"];
			echo '</td></tr><tr><td>';
			/* Université */
			echo " Université : ";
			echo retrouveUniv($travail["université"],$connexion);
			echo '</td><td>';
			/* Matière */
			echo " Matière : ";
			echo retrouveMat($travail["matiere"],$connexion);
			echo '</td></tr></table>';
			
			
		}
	}
	/*
		Donne nom université a partir de son id
	*/
	function retrouveUniv($id,$connexion){
		$req ='SELECT nom FROM universités WHERE id='.$id.";"; 
		$result=mysqli_query($connexion,$req);
		$td=mysqli_fetch_assoc($result);
		return $td["nom"];
	}
	/*
		Donne nom matiere a partir de son id
	*/
	function retrouveMat($id, $connexion){
		$req ='SELECT nom FROM matières WHERE id='.$id.";"; 
		$result=mysqli_query($connexion,$req);
		$td=mysqli_fetch_assoc($result);
		return $td["nom"];
	}
	/*
		Donne nom filiere a partir de son id
	*/
	function retrouveFil($id, $connexion){
		$req ='SELECT nom FROM filières WHERE id='.$id.";"; 
		$result=mysqli_query($connexion,$req);
		$td=mysqli_fetch_assoc($result);
		return $td["nom"];
	}
	/*
		Creer lien de téléchargement
	*/
	function download ($chemin,$objet) {

		if(file_exists($chemin)){
			echo '<a href="'.htmlspecialchars($chemin).'" target="_blank"> '.htmlspecialchars($objet).' </a>';	
		}
	}
	
	//header('Content-type: application/pdf');
	//header('Content-Disposition: attachment; filename="'.$fichier.'"');
	//readfile($chemin);
	
			
?>