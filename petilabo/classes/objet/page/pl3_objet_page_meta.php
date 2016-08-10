<?php

/**
 * Classe de gestion des balises meta
 */

class pl3_objet_page_meta extends pl3_outil_objet_xml {
	const NOM_FICHE = "page";
	const NOM_BALISE = "meta";
	const TYPE = self::TYPE_COMPOSITE;

	/* objets fils */
	const OBJETS = array(
		"pl3_objet_page_meta_titre",
		"pl3_objet_page_meta_description",
		"pl3_objet_page_meta_theme",
		"pl3_objet_page_meta_style");

	/* Affichage */
	public function afficher($mode) {
		$ret = $this->afficher_objets_fils($mode);
		return $ret;
	}
}