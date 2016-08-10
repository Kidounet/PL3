<?php

/**
 * Classe de gestion des sauts
 */

class pl3_objet_page_saut extends pl3_outil_objet_xml {
	const ICONE = "fa-arrows-v";
	const NOM_FICHE = "page";
	const NOM_BALISE = "saut";
	const TYPE = self::TYPE_ENTIER;
	const MIN = 1;

	/* Création */
	public function construire_nouveau() {
		$this->set_valeur(1);
	}

	/* Affichage */
	public function afficher($mode) {
		$ret = "";
		$html_id = $this->get_html_id();
		$valeur = $this->get_valeur();
		$ret .= "<div class=\"container_saut\">\n";
		$ret .= "<p id=\"".$html_id."\" class=\"saut objet_editable\" style=\"line-height:".$valeur.";\">&nbsp;</p>\n";
		$ret .= "</div>\n";
		return $ret;
	}
}