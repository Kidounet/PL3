<?php

/**
 * Classe de gestion des textes
 */

class pl3_objet_texte_texte extends pl3_outil_objet_xml {
	const NOM_FICHE = "texte";
	const NOM_BALISE = "texte";
	const TYPE = self::TYPE_CHAINE;

	/* Attributs */
	const ATTRIBUTS = array(
		array("nom" => "nom", "type" => self::TYPE_CHAINE));

	/* Initialisation */
	public function construire_nouveau() {
		$this->construire_nouveau_nom();
		$this->set_valeur("[...]");
	}

	/* Affichage */
	public function afficher($mode) {
		echo "<!-- Texte -->\n";
	}
}