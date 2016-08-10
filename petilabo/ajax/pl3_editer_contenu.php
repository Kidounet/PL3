<?php
define("_CHEMIN_BASE_URL", "../../");
require_once(_CHEMIN_BASE_URL."petilabo/pl3_init.php");

/* Initialisations */
$html = "";
$ajax_objet_valide = pl3_ajax_init::Init_contenu();

/* Traitement des paramètres */
if ($ajax_objet_valide) {
	$contenu = pl3_ajax_init::Get_contenu();
	$contenu_id = $contenu->get_id();
	$editeur_contenu = new pl3_admin_editeur_objet($contenu, "editeur_type_contenu", "contenu-".$contenu_id);
	$html = $editeur_contenu->editer();
}

/* Retour JSON de la requête AJAX */
echo json_encode(array("valide" => $ajax_objet_valide, "html" => $html));
