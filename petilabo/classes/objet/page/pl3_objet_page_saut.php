<?php

/**
 * Classe de gestion des sauts
 */
 
class pl3_objet_page_saut extends pl3_outil_objet_xml {
	const NOM_BALISE = "saut";
	public static $Liste_attributs = array();

	public function ecrire_xml($niveau) {
		$xml = $this->ouvrir_fermer_xml($niveau);
		return $xml;
	}
	
	public function afficher() {
		$ret = "";
		$html_id = $this->get_html_id();
		$ret .= "<div class=\"container_saut\">\n";
		$ret .= "<p id=\"".$html_id."\" class=\"saut objet_editable\">&nbsp;</p>\n";
		$ret .= "</div>\n";
		return $ret;
	}
}