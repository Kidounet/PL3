<?php

class pl3_objet_media_image_taille extends pl3_outil_objet_xml {
	const NOM_FICHE = "media";
	const NOM_BALISE = "taille_standard";
	const TYPE = self::TYPE_REFERENCE;
	const REFERENCE = "pl3_objet_theme_taille_image";

	/* Affichage */
	public function afficher($mode) {return null;}
}