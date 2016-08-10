<?php

/**
 * Classe de gestion des titres
 */

class pl3_objet_page_titre extends pl3_outil_objet_xml {
	const ICONE = "fa-text-height";
	const NOM_FICHE = "page";
	const NOM_BALISE = "titre";
	const TYPE = self::TYPE_INDIRECTION;
	const REFERENCE = "pl3_objet_texte_texte";

	/* Attributs */
	const ATTRIBUTS = array(
		array("nom" => "style", "type" => self::TYPE_REFERENCE, "reference" => "pl3_objet_theme_style_texte"),
		array("nom" => "niveau", "type" => self::TYPE_ENTIER, "min" => 1, "max" => 6));

	/* Création */
	public function construire_nouveau() {
		/* Création d'une instance de texte */
		$source_page = pl3_outil_source_page::Get();
		$objet_texte = $source_page->instancier_nouveau(self::REFERENCE);
		if ($objet_texte) {
			$objet_texte->construire_nouveau();
			$source_page->enregistrer_nouveau($objet_texte);

			/* Ce nouveau texe riche est la valeur du nouveau paragraphe */
			$this->set_valeur($objet_texte->get_attribut_nom());
			$this->set_attribut_niveau(1);
		}
	}

	/* Affichage */
	public function afficher($mode) {
		$ret = "";
		$source_page = pl3_outil_source_page::Get();
		$nom_texte = $this->get_valeur();
		$texte = $source_page->chercher_liste_textes_par_nom(pl3_objet_texte_texte::NOM_BALISE, $nom_texte);
		if ($texte != null) {
			$html_id = $this->get_html_id();
			$valeur_texte = $texte->get_valeur();
			$niveau = $this->get_attribut_entier("niveau");
			$style = $this->get_attribut_chaine("style");
			if (strlen($style) == 0) {$style = _NOM_STYLE_DEFAUT;}
			$ret .= "<div class=\"container_titre\">\n";
			$ret .= "<h".$niveau." id=\"".$html_id."\" class=\"titre objet_editable texte_".$style."\">".$valeur_texte."</h".$niveau.">\n";
			$ret .= "</div>\n";
		}
		return $ret;
	}
}