<?php

class pl3_objet_media_image_alt extends pl3_outil_objet_xml {
	const NOM_FICHE = "media";
	const NOM_BALISE = "alt";
	const TYPE = self::TYPE_INDIRECTION;
	const REFERENCE = "pl3_objet_texte_texte";

	/* Attributs spécifiques */
	private $nom_alt = null;

	/* Création */
	public function construire_nouveau() {
		/* Création d'une instance de texte */
		$source_page = pl3_outil_source_page::Get();
		$objet_texte = $source_page->instancier_nouveau(self::REFERENCE);
		if ($objet_texte) {
			$source_page->enregistrer_nouveau($objet_texte);
			$this->nom_alt = $objet_texte->get_attribut_nom();
		}
		return $this->nom_alt;
	}

	/* Affichage */
	public function afficher($mode) {
		$source_page = pl3_outil_source_page::Get();
		$nom_alt = $this->get_valeur();
		if (strlen($nom_alt) > 0) {
			$texte_alt = $source_page->chercher_liste_textes_par_nom(pl3_objet_texte_texte::NOM_BALISE, $nom_alt);
			if ($texte_alt != null) {$nom_alt = $texte_alt->get_valeur();}
		}
		$ret = (strlen($nom_alt) > 0)?" alt=\"".$nom_alt."\"":"";
		return $ret;
	}
}