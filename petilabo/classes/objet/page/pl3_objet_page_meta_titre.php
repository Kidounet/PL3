<?php

class pl3_objet_page_meta_titre extends pl3_outil_objet_xml {
	const NOM_FICHE = "page";
	const NOM_BALISE = "titre";
	const TYPE = self::TYPE_REFERENCE;
	const REFERENCE = "pl3_objet_texte_texte";

	/* Affichage */
	public function afficher($mode) {
		$ret = "";
		$valeur_titre = $this->get_valeur();
		if (strlen($valeur_titre) > 0) {
			$ret .= "<title>".$valeur_titre."</title>\n";
		}
		return $ret;
	}
}