<?php
header('Content-type: application/json');

define("_CHEMIN_BASE_URL", "../../");
define("_CHEMIN_BASE_RESSOURCES", "../");
require_once(_CHEMIN_BASE_URL."petilabo/pl3_init.php");

/* Préparation des données */
$info_sortie = "";
$retour_valide = false;
$index_taille = (int) pl3_admin_post::Post("taille");
$nom_taille = pl3_admin_post::Post("nom_taille");
$nom_taille = "Ardoise";
$largeur_taille = (int) pl3_admin_post::Post("largeur_taille");
$hauteur_taille = (int) pl3_admin_post::Post("hauteur_taille");
$compression = (int) pl3_admin_post::Post("compression");
$taille = htmlspecialchars($nom_taille, ENT_QUOTES, "UTF-8");
$nom_page = pl3_admin_post::Post("page");
$nom_champ_post = "img-".$index_taille;
$html = pl3_fiche_media::Afficher_ajout_media($index_taille, $taille);

/* Traitement de l'upload */
if (($index_taille > 0) && (strlen($nom_taille) > 0) && (strlen($nom_page) > 0) && (isset($_FILES[$nom_champ_post]))) {
	define("_PAGE_COURANTE", $nom_page);
	define("_CHEMIN_PAGE_COURANTE", _CHEMIN_PAGES_XML.$nom_page."/");

	/* Traitement du $_FILES en post */
	$fichier_post = new pl3_ajax_telechargement_fichier($_FILES[$nom_champ_post]);
	$retour_valide = $fichier_post->controle_post($info_sortie);
	if ($retour_valide) {
		$fichier_temporaire = $fichier_post->get_tmp_name();

		/* Chargement de la fiche média locale */
		$source_page = pl3_outil_source_page::Get();
		$source_page->charger_ressources_xml();
		$fiche_media = $source_page->get_media(_NOM_SOURCE_LOCAL);
		$retour_valide = ($fiche_media != null);

		/* Rapatriement de l'image uploadée si la fiche média est disponible */
		if ($retour_valide) {
			$telechargement = new pl3_ajax_telechargement_image($fichier_temporaire, $largeur_taille, $hauteur_taille, $compression);
			$telechargement->set_destination($_FILES[$nom_champ_post]["name"]);
			$retour_valide = $telechargement->move_and_resize_uploaded_file($info_sortie);
			if ($retour_valide) {
				list($largeur, $hauteur) = $telechargement->get_image_size();
				$nom_image = $telechargement->get_nom_image();
				$image = $fiche_media->instancier_image($nom_image, $taille, $largeur, $hauteur);
				if ($image) {
					$fiche_media->ajouter_objet($image);
					$source_page->enregistrer_ressources_xml();
					$html = ($fiche_media->afficher_vignette_media($image)).$html;
				}
				else {
					$retour_valide = false;
					$telechargement->effacer();
					$info_sortie = "ERREUR : Une image du même nom existe déjà.";
				}
			}
		}
		else {
			$fichier_post->effacer();
			$info_sortie = "ERREUR : Impossible de charger la fiche XML des média.";
		}
	}
}
else {
	$info_sortie = "ERREUR : Les informations envoyées sont incorrectes.";
}

echo json_encode(array("code" => $retour_valide, "info" => $info_sortie, "html" => $html));