<?php
define("_CHEMIN_BASE_URL", "../../");
define("_CHEMIN_BASE_RESSOURCES", "../");
require_once(_CHEMIN_BASE_URL."petilabo/pl3_init.php");

/* Initialisations */
$html = "";
$bloc_id = null;
$ajax_objet_valide = pl3_ajax_init::Init_objet();
if ($ajax_objet_valide) {
	$objet = pl3_ajax_init::Get_objet();
	if ($objet != null) {
		$source_page = pl3_ajax_init::Get_source_page();
		$contenu = pl3_ajax_init::Get_contenu();
		$bloc = pl3_ajax_init::Get_bloc();
		$bloc_id = $contenu->get_id()."-".$bloc->get_id();
		$bloc->retirer_objet($objet->get_id());
		$source_page->enregistrer_xml();
		$html .= $bloc->afficher(_MODE_ADMIN_OBJETS);
	}
}

/* Retour JSON de la requête AJAX */
echo json_encode(array("valide" => $ajax_objet_valide, "bloc_id" => $bloc_id, "html" => $html));
