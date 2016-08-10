<?php

/**
 * Classe de gestion des éléments d'un objet composite de la fiche theme
 */

abstract class pl3_outil_objet_xml_css_attribut extends pl3_outil_objet_xml {
	const NOM_FICHE = "theme";
	const TYPE = self::TYPE_CHAINE;
	const NOM_CSS = "";

	/* Affichage */
	public function afficher($mode) {
		$valeur_texte = html_entity_decode($this->get_valeur(), ENT_QUOTES, "UTF-8");
		$ret = static::NOM_CSS.(static::NOM_CSS?":":"").$valeur_texte.";";
		return $ret;
	}
}
