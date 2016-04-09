<?php

/**
 * Classe de gestion des paragraphes
 */
 
class pl3_objet_page_paragraphe extends pl3_outil_objet_xml {
	/* Balise */
	const NOM_BALISE = "paragraphe";
	public static $Balise = array("nom" => self::NOM_VALEUR, "type" => self::TYPE_REFERENCE, "reference" => "pl3_objet_texte_texte");
	
	/* Attributs */
	const NOM_ATTRIBUT_STYLE = "style";
	public static $Liste_attributs = array(
		array("nom" => self::NOM_ATTRIBUT_STYLE, "type" => self::TYPE_REFERENCE, "reference" => "pl3_objet_style_style_texte"));

	/* Méthodes */
	public function ecrire_xml($niveau) {
		$attr_style = $this->get_xml_attribut(self::NOM_ATTRIBUT_STYLE);
		$xml = $this->ouvrir_fermer_xml($niveau, array($attr_style));
		return $xml;
	}
	
	public function afficher($mode) {
		$ret = "";
		$nom_texte = $this->get_valeur();
		$texte = $this->source_page->chercher_liste_textes_par_nom(pl3_objet_texte_texte::NOM_BALISE, $nom_texte);
		if ($texte != null) {
			$html_id = $this->get_html_id();
			$valeur_texte = $texte->get_valeur();
			$ret .= "<div class=\"container_paragraphe\">\n";
			$ret .= "<p id=\"".$html_id."\" class=\"paragraphe objet_editable\">".$valeur_texte."</p>\n";
			$ret .= "</div>\n";
		}
		return $ret;
	}
}