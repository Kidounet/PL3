<?php

/**
 * Classe de gestion des images
 */

class pl3_objet_page_image extends pl3_outil_objet_xml {
	const ICONE = "fa-picture-o";
	const NOM_FICHE = "page";
	const NOM_BALISE = "image";
	const TYPE = self::TYPE_REFERENCE;
	const REFERENCE = "pl3_objet_media_image";

	/* Attributs */
	const ATTRIBUTS = array(
		array("nom" => "lien", "type" => self::TYPE_CHAINE),
		array("nom" => "survol", "type" => self::TYPE_REFERENCE, "reference" => "pl3_objet_theme_style_survol"));

	/* Affichage */
	public function afficher($mode) {
		$ret = "";
		$source_page = pl3_outil_source_page::Get();
		$nom_image = $this->get_valeur();
		$image = $source_page->chercher_liste_medias_par_nom(pl3_objet_media_image::NOM_BALISE, $nom_image);
		if ($image != null) {
			$fichier = $image->get_valeur_fichier();
			$src = " src=\""._CHEMIN_IMAGES_XML.$fichier."\"";
			$nom_alt = $image->get_valeur_alt();
			if (strlen($nom_alt) > 0) {
				$texte_alt = $source_page->chercher_liste_textes_par_nom(pl3_objet_texte_texte::NOM_BALISE, $nom_alt);
				if ($texte_alt != null) {$nom_alt = $texte_alt->get_valeur();}
			}
			$alt = (strlen($nom_alt) > 0)?" alt=\"".$nom_alt."\"":"";
			$taille = "";
			$largeur = $image->get_valeur_largeur_reelle();
			if ($largeur > 0) {$taille .= " width=\"".$largeur."\"";}
			$hauteur = $image->get_valeur_hauteur_reelle();
			if ($hauteur > 0) {$taille .= " height=\"".$hauteur."\"";}
			$html_id = $this->get_html_id();
			$ret .= "<div class=\"container_image\">";
			$ret .= "<img id=\"".$html_id."\" class=\"image_responsive objet_editable\"".$src.$alt.$taille." />";
			$ret .= "</div>\n";
		}
		return $ret;
	}
}