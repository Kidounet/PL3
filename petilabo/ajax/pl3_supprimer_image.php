<?php
define("_CHEMIN_BASE_URL", "../../");
define("_CHEMIN_BASE_RESSOURCES", "../");
require_once(_CHEMIN_BASE_URL."petilabo/pl3_init.php");

/* Initialisations */
$html = "";
$ajax_media_maj = false;
$ajax_media_valide = pl3_ajax_init::Init_media();

/* Traitement des paramètres */
if ($ajax_media_valide) {
	$media = pl3_ajax_init::Get_media();
	if ($media != null) {
		$source_page = pl3_ajax_init::Get_source_page();
		$fiche_media = pl3_ajax_init::Get_fiche_media();
		$fiche_media->retirer_image($media->get_id());
		$source_page->enregistrer_xml();
		$html = "Image ".$media->get_id()." supprimée.";
	}
}

/* Retour JSON de la requête AJAX */
echo json_encode(array("valide" => $ajax_media_valide, "html" => $html));
