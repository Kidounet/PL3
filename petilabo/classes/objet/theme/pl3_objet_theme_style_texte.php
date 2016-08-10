<?php

/**
 * Classe de gestion des styles de texte
 */

class pl3_objet_theme_style_texte extends pl3_outil_objet_xml_css_bloc {
	const NOM_BALISE = "style_texte";

	/* Attributs */
	const ATTRIBUTS = array(
		array("nom" => "nom", "type" => self::TYPE_CHAINE));

	/* objets fils */
	const OBJETS = array(
		"pl3_objet_theme_css_marge",
		"pl3_objet_theme_css_retrait",
		"pl3_objet_theme_css_couleur",
		"pl3_objet_theme_css_taille",
		"pl3_objet_theme_css");
}