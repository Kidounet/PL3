<?php

/**
 * Classe de gestion des galeries
 */

class pl3_objet_media_galerie extends pl3_outil_objet_xml {
	const NOM_FICHE = "media";
	const NOM_BALISE = "galerie";
	const TYPE = self::TYPE_COMPOSITE;

	/* Attributs */
	const ATTRIBUTS = array(
		array("nom" => "nom", "type" => self::TYPE_CHAINE));

	/* objets fils */
	const OBJETS = array(
		"pl3_objet_media_galerie_element");

	/* Affichage */
	public function afficher($mode) {
		$ret = "";
		$ret .= "<!-- Galerie -->\n";
		$ret .= $this->afficher_objets_fils($mode);
		return $ret;
	}
}