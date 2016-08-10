<?php

/**
 * Classe de gestion des styles de contenu
 */

class pl3_objet_theme_style_contenu extends pl3_outil_objet_xml_css_bloc {
	const NOM_BALISE = "style_contenu";

	/* Attributs */
	const ATTRIBUTS = array(
		array("nom" => "nom", "type" => self::TYPE_CHAINE));

	/* objets fils */
	const OBJETS = array(
		"pl3_objet_theme_css_marge",
		"pl3_objet_theme_css_retrait",
		"pl3_objet_theme_css_bordure",
		"pl3_objet_theme_css_fond",
		"pl3_objet_theme_css");
}