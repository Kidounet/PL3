<?php

/**
 * Classe de gestion des éléments d'un objet composite de la fiche theme
 */

abstract class pl3_outil_objet_xml_css_bloc extends pl3_outil_objet_xml {
	const NOM_FICHE = "theme";
	const TYPE = self::TYPE_COMPOSITE;

	/* Affichage */
	public function afficher($mode) {
		$groupe = str_replace("style_", "", static::NOM_BALISE);
		$nom = $this->get_attribut_nom();
		$ret = ".".$groupe."_".$nom."{";
		$ret .= $this->afficher_objets_fils($mode);
		$ret .= "}\n";
		return $ret;
	}
}
