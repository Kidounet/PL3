<?php

/**
 * Classe de gestion des tailles d'images
 */

class pl3_objet_theme_taille_image extends pl3_outil_objet_xml {
	const NOM_FICHE = "theme";
	const NOM_BALISE = "taille_image";
	const TYPE = self::TYPE_COMPOSITE;

	/* Attributs */
	const ATTRIBUTS = array(
		array("nom" => "nom", "type" => self::TYPE_CHAINE));

	/* objets fils */
	const OBJETS = array(
		"pl3_objet_theme_image_largeur",
		"pl3_objet_theme_image_hauteur",
		"pl3_objet_theme_image_compression");

	/* Affichage */
	public function afficher($mode) {return null;}
}