<?php
define("_CHEMIN_BASE_URL", "../../");
define("_CHEMIN_BASE_RESSOURCES", "../");
require_once(_CHEMIN_BASE_URL."petilabo/pl3_init.php");

/* Initialisations */
$ajax_objet_valide = pl3_ajax_init::Init_bloc();

/* Traitement des paramètres */
$html = "";
if ($ajax_objet_valide) {
	$ajax_objet_valide = false;
	$nom_classe = pl3_admin_post::Post("classe_objet");
	if (strlen($nom_classe) > 0) {
		$nom_constante_balise = $nom_classe."::NOM_BALISE";
		$nom_constante_fiche = $nom_classe."::NOM_FICHE";
		if ((@defined($nom_constante_balise)) && (@defined($nom_constante_fiche)))  {
			$bloc = pl3_ajax_init::Get_bloc();
			$id_bloc = $bloc->get_id();
			$id_contenu = $bloc->get_id_parent();
			$source_page = pl3_ajax_init::Get_source_page();
			$objet = $source_page->instancier_nouveau($nom_classe, $id_contenu, $id_bloc);
			$source_page->enregistrer_nouveau($objet, $id_contenu, $id_bloc);
			$html .= $bloc->afficher(_MODE_ADMIN_OBJETS);
			$ajax_objet_valide = true;
		}
	}
}

/* Retour JSON de la requête AJAX */
echo json_encode(array("valide" => $ajax_objet_valide, "html" => $html));
