<?php

/**
 * Classe de gestion des styles de texte
 */

class pl3_objet_theme_style_survol extends pl3_outil_objet_xml_css_bloc {
	const NOM_BALISE = "style_survol";

	/* Attributs */
	const ATTRIBUTS = array(
		array("nom" => "nom", "type" => self::TYPE_CHAINE));
}