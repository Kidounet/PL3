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
	$parametres = pl3_admin_post::Post("parametres");
	if (strlen($parametres) > 0) {
		$media = pl3_ajax_init::Get_media();
		parse_str($parametres, $liste_parametres);
		if (($media != null) && (count($liste_parametres) > 0)) {
			$source_page = pl3_ajax_init::Get_source_page();
			foreach ($liste_parametres as $nom_parametre => $valeur_parametre) {
				if ($nom_parametre === "nom") {
					$attribut_nom_maj = $media->set_attribut_nom($valeur_parametre);
					$ajax_media_maj = $ajax_media_maj || $attribut_nom_maj;
				}
				else {
					$type_valeur = $media->get_objet_fils_type($nom_parametre);
					if ($type_valeur == pl3_outil_objet_xml::TYPE_INDIRECTION) {
						$valeur = $media->get_objet_fils_valeur($nom_parametre);
						$nom_classe = $media->get_objet_fils_reference($nom_parametre);
						$nom_fiche = $nom_classe::NOM_FICHE;
						$nom_balise = $nom_classe::NOM_BALISE;
						$objet_indirection = $source_page->chercher_liste_fiches_par_nom($nom_fiche, $nom_balise, $valeur);
						if ($objet_indirection) {
							$valeur_maj = $objet_indirection->set_valeur($valeur_parametre);
							$ajax_media_maj = $ajax_media_maj || $valeur_maj;
						}
					}
					else {
						$valeur_maj = $media->set_objet_fils_valeur($nom_parametre, $valeur_parametre);
						$ajax_media_maj = $ajax_media_maj || $valeur_maj;
					}
				}
			}
			if ($ajax_media_maj) {
				$source_page->enregistrer_xml();
				$fiche_media = pl3_ajax_init::Get_fiche_media();
				$html .= $fiche_media->afficher_vignette_media($media); // $media->afficher(_MODE_ADMIN_MEDIA);
			}
		}
	}
}

/* Retour JSON de la requête AJAX */
echo json_encode(array("valide" => $ajax_media_valide, "maj" => $ajax_media_maj, "html" => $html));
