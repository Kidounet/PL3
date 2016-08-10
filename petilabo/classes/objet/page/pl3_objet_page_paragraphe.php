<?php

/**
 * Classe de gestion des paragraphes
 */

class pl3_objet_page_paragraphe extends pl3_outil_objet_xml {
	const ICONE = "fa-file-text-o ";
	const NOM_FICHE = "page";
	const NOM_BALISE = "paragraphe";
	const TYPE = self::TYPE_INDIRECTION;
	const REFERENCE = "pl3_objet_texte_texte_riche";

	/* Attributs */
	const ATTRIBUTS = array(
		array("nom" => "style", "type" => self::TYPE_REFERENCE, "reference" => "pl3_objet_theme_style_texte"));

	/* Création */
	public function construire_nouveau() {
		$source_page = pl3_outil_source_page::Get();
		/* Création d'une instance de texte riche */
		$objet_texte_riche = $source_page->instancier_nouveau(self::REFERENCE);
		if ($objet_texte_riche) {
			$objet_texte_riche->construire_nouveau();
			$source_page->enregistrer_nouveau($objet_texte_riche);

			/* Ce nouveau texe riche est la valeur du nouveau paragraphe */
			$this->set_valeur($objet_texte_riche->get_attribut_nom());
		}
	}

	/* Affichage */
	public function afficher($mode) {
		$ret = "";
		$source_page = pl3_outil_source_page::Get();
		$nom_texte = $this->get_valeur();
		$texte = $source_page->chercher_liste_textes_par_nom(pl3_objet_texte_texte_riche::NOM_BALISE, $nom_texte);
		if ($texte != null) {
			$html_id = $this->get_html_id();
			$valeur_texte = html_entity_decode($texte->get_valeur(), ENT_QUOTES, "UTF-8");
			$style = $this->get_attribut_chaine("style");
			if (strlen($style) == 0) {$style = _NOM_STYLE_DEFAUT;}
			$ret .= "<div class=\"container_paragraphe\">\n";
			$ret .= "<div id=\"".$html_id."\" class=\"paragraphe objet_editable texte_".$style."\">".$valeur_texte."</div>\n";
			$ret .= "</div>\n";
		}
		return $ret;
	}
}