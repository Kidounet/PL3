<?php

class pl3_objet_media_image_hauteur extends pl3_outil_objet_xml {
	const NOM_FICHE = "media";
	const NOM_BALISE = "hauteur_reelle";
	const TYPE = self::TYPE_ENTIER;

	/* Affichage */
	public function afficher($mode) {
		$hauteur = (int) $this->get_valeur();
		$ret = " height=\"".$hauteur."\"";
		return $ret;
	}
}