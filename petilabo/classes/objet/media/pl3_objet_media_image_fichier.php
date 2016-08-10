<?php

class pl3_objet_media_image_fichier extends pl3_outil_objet_xml {
	const NOM_FICHE = "media";
	const NOM_BALISE = "fichier";
	const TYPE = self::TYPE_FICHIER;

	/* Affichage */
	public function afficher($mode) {
		$fichier = html_entity_decode($this->get_valeur(), ENT_QUOTES, "UTF-8");
		$ret = " src=\""._CHEMIN_IMAGES_XML.$fichier."\"";
		return $ret;
	}
}