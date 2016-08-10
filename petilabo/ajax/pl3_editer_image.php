<?php
define("_CHEMIN_BASE_URL", "../../");
require_once(_CHEMIN_BASE_URL."petilabo/pl3_init.php");

/* Initialisations */
$html = "";
$ajax_media_valide = pl3_ajax_init::Init_media();

/* Traitement des paramètres */
if ($ajax_media_valide) {
	$media = pl3_ajax_init::Get_media();
	$media_id = $media->get_id();
	$editeur_media = new pl3_admin_editeur_image($media, "editeur_type_media", $media_id);
	$html = $editeur_media->editer();
}

/* Retour JSON de la requête AJAX */
echo json_encode(array("valide" => $ajax_media_valide, "html" => $html));
