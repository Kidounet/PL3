<?php

class pl3_objet_media_image_largeur extends pl3_outil_objet_xml {
	const NOM_FICHE = "media";
	const NOM_BALISE = "largeur_reelle";
	const TYPE = self::TYPE_ENTIER;

	/* Affichage */
	public function afficher($mode) {
		$largeur = (int) $this->get_valeur();
		$ret = " width=\"".$largeur."\"";
		return $ret;
	}
}