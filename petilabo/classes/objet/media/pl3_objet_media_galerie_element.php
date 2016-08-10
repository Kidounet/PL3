<?php

class pl3_objet_media_galerie_element extends pl3_outil_objet_xml {
	const NOM_FICHE = "media";
	const NOM_BALISE = "element";
	const TYPE = self::TYPE_REFERENCE;
	const REFERENCE = "pl3_objet_media_image";

	/* Affichage */
	public function afficher($mode) {
		$ret = "";
		$ret .= "<!-- Element -->\n";
		return $ret;
	}
}