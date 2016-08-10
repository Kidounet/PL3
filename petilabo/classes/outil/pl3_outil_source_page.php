<?php

class pl3_outil_source_page {
	/* Singleton */
	private static $Source_page = null;

	/* Ressources */
	private $liste_sources = array();
	private $liste_themes = array();
	private $page = null;

	/* Thèmes */
	private $theme = null;
	private $chemin_theme = null;
	private $fichier_theme_css = null;
	private $fichier_theme_style_xml = null;
	private $fichier_theme_additionnel_css = null;
	private $theme_a_jour = false;

	/* Constructeur privé */
	private function __construct() {
		/* Déclaration des textes */
		$liste_textes = new pl3_outil_liste_fiches_xml("texte");
		$liste_textes->ajouter_source(_NOM_SOURCE_GLOBAL, _CHEMIN_XML);
		$liste_textes->ajouter_source(_NOM_SOURCE_LOCAL, _CHEMIN_PAGE_COURANTE);
		$this->liste_sources[pl3_fiche_texte::NOM_FICHE] = $liste_textes;

		/* Déclaration des media */
		$liste_medias = new pl3_outil_liste_fiches_xml("media");
		$liste_medias->ajouter_source(_NOM_SOURCE_GLOBAL, _CHEMIN_XML);
		$liste_medias->ajouter_source(_NOM_SOURCE_LOCAL, _CHEMIN_PAGE_COURANTE);
		$this->liste_sources[pl3_fiche_media::NOM_FICHE] = $liste_medias;

		/* Déclaration du fichier page */
		$this->page = new pl3_fiche_page(_CHEMIN_PAGE_COURANTE);
	}
	public static function &Get() {
		if (is_null(self::$Source_page)) {
			self::$Source_page = new pl3_outil_source_page();
		}
		return self::$Source_page;
	}

	/* Instanciation de nouveaux objets */
	public function instancier_nouveau($classe_objet, $id_contenu = 0, $id_bloc = 0) {
		$instance = null;
		$nom_fiche = $classe_objet::NOM_FICHE;
		/* Cas d'un objet hors page : on l'insère à la racine de la fiche */
		if ($nom_fiche !== "page") {
			if (isset($this->liste_sources[$nom_fiche])) {
				$liste_fiches = $this->liste_sources[$nom_fiche];
				$instance = $liste_fiches->instancier_nouveau(_NOM_SOURCE_LOCAL, $classe_objet);
			}
		}
		/* Cas d'un objet page : on l'insère dans un bloc */
		else {
			$contenu = $this->page->chercher_objet_classe_par_id("pl3_objet_page_contenu", $id_contenu);
			if ($contenu != null) {
				$bloc = $contenu->chercher_objet_classe_par_id("pl3_objet_page_bloc", $id_bloc);
				if ($bloc != null) {
					$instance = $bloc->instancier_nouveau($classe_objet);
				}
			}
		}
		return $instance;
	}

	/* Enregistrement de nouveaux objets */
	public function enregistrer_nouveau(&$objet, $id_contenu = 0, $id_bloc = 0) {
		$classe_objet = get_class($objet);
		$nom_fiche = $classe_objet::NOM_FICHE;
		/* Cas d'un objet hors page : on l'insère à la racine de la fiche */
		if ($nom_fiche !== "page") {
			if (isset($this->liste_sources[$nom_fiche])) {
				$liste_fiches = $this->liste_sources[$nom_fiche];
				$liste_fiches->ajouter_objet(_NOM_SOURCE_LOCAL, $objet);
				$liste_fiches->enregistrer_xml(_NOM_SOURCE_LOCAL);
			}
		}
		/* Cas d'un objet page : on l'insère dans un bloc */
		else {
			$contenu = $this->page->chercher_objet_classe_par_id("pl3_objet_page_contenu", $id_contenu);
			if ($contenu != null) {
				$bloc = $contenu->chercher_objet_classe_par_id("pl3_objet_page_bloc", $id_bloc);
				if ($bloc != null) {
					$instance = $bloc->ajouter_objet($objet);
					$this->enregistrer_page_xml();
				}
			}
		}
	}

	/* Suppression d'objets */
	public function supprimer(&$objet) {
		$classe_objet = get_class($objet);
		$nom_fiche = $classe_objet::NOM_FICHE;
		if (isset($this->liste_sources[$nom_fiche])) {
			$liste_fiches = $this->liste_sources[$nom_fiche];
			$liste_fiches->enlever_objet(_NOM_SOURCE_LOCAL, $objet);
			$liste_fiches->enregistrer_xml(_NOM_SOURCE_LOCAL);
		}
	}

	/* Chargement XML */
	public function charger_xml() {
		$this->charger_ressources_xml();
		$this->charger_page_xml();

		/* Identification du thème */
		$this->theme = $this->page->get_nom_theme();
		$this->chemin_theme = _CHEMIN_THEMES_XML.$this->theme."/";
		$this->fichier_theme_css = _CHEMIN_THEMES_CSS."style_".$this->theme._SUFFIXE_CSS;
		$this->fichier_theme_style_xml = $this->chemin_theme."theme.xml";
		$this->fichier_theme_additionnel_css = $this->chemin_theme."theme.css";
		$this->theme_a_jour = $this->verifier_theme_a_jour();

		/* Déclaration du thème */
		$this->liste_themes = new pl3_outil_liste_fiches_xml("theme");
		$this->liste_themes->ajouter_source(_NOM_SOURCE_THEME, $this->chemin_theme);

		/* Chargement du thème */
		$this->charger_theme_xml();
	}
	public function charger_ressources_xml() {
		foreach ($this->liste_sources as $nom_fiche => $liste_fiches) {
			$liste_fiches->charger_xml();
		}
	}
	public function charger_page_xml() {
		$this->page->charger_xml();
	}
	public function charger_theme_xml() {
		$this->liste_themes->charger_xml();
	}

	/* Enregistrement XML */
	public function enregistrer_xml() {
		$this->enregistrer_ressources_xml();
		$this->enregistrer_page_xml();
	}
	public function enregistrer_ressources_xml() {
		foreach ($this->liste_sources as $nom_fiche => $liste_fiches) {
			$liste_fiches->enregistrer_xml();
		}
	}
	public function enregistrer_page_xml() {$this->page->enregistrer_xml();}

	/* Affichage */
	public function afficher($mode) {
		$this->generer_theme($mode);
		$this->page->set_mode($mode);
		$html = $this->page->afficher($mode);
		return $html;
	}

	/* Génération du CSS */
	public function generer_theme($mode) {
		if (!($this->theme_a_jour)) {
			$css = $this->liste_themes->afficher($mode);
			$this->generer_theme_css($css);
		}
	}

	/* Accesseurs */
	public function &get_page() {return $this->page;}
	public function &get_media($nom_source) {
		$liste_medias = $this->liste_sources[pl3_fiche_media::NOM_FICHE];
		$source_media = $liste_medias->get_source($nom_source);
		return $source_media;
	}
	public function &get_theme() {
		$theme = $this->liste_themes->get_source(_NOM_SOURCE_THEME);
		return $theme;
	}
	public function get_nom_theme() {return $this->theme;}

	/* Recherches */
	public function chercher_liste_textes_par_nom($balise, $nom) {
		return $this->chercher_liste_fiches_par_nom(pl3_fiche_texte::NOM_FICHE, $balise, $nom);
	}
	public function chercher_liste_medias_par_nom($balise, $nom) {
		return $this->chercher_liste_fiches_par_nom(pl3_fiche_media::NOM_FICHE, $balise, $nom);
	}
	public function chercher_liste_themes_par_nom($balise, $nom) {
		return $this->liste_themes->chercher_instance_balise_par_nom($balise, $nom);
	}
	public function chercher_liste_fiches_par_nom($nom_fiche, $balise, $nom) {
		if (isset($this->liste_sources[$nom_fiche])) {
			return $this->liste_sources[$nom_fiche]->chercher_instance_balise_par_nom($balise, $nom);
		}
		else {return null;}
	}
	public function chercher_liste_noms_par_fiche($nom_fiche, $nom_classe) {
		if (isset($this->liste_sources[$nom_fiche])) {
			return $this->liste_sources[$nom_fiche]->chercher_liste_noms_par_classe($nom_classe);
		}
		else if ($nom_fiche === "theme") {
			return $this->liste_themes->chercher_liste_noms_par_classe($nom_classe);
		}
		else {return null;}
	}

	/* Méthodes de parsing */
	public function parser_balise($fiche, &$objet_parent, &$noeud, $nom_classe) {
		$ret = array();
		if ($noeud != null) {
			$reflection = new ReflectionClass($nom_classe);
			$balise = $reflection->getConstant("NOM_BALISE");
			$liste = $noeud->getElementsByTagName($balise);
			foreach($liste as $dom_element) {
				$objet = $reflection->newInstanceArgs(array(1 + count($ret), &$objet_parent, &$element));

				/* parser les attributs */
				foreach($objet->get_liste_attributs() as $attribut) {
					$nom_attribut = $attribut["nom"];
					if ($dom_element->hasAttribute($nom_attribut)) {
						$valeur_attribut = $dom_element->getAttribute($nom_attribut);
						$objet->set_attribut($nom_attribut, $valeur_attribut);
					}
				}

				/* parser la valeur */
				if ($objet->avec_valeur()) {
					$objet->set_valeur($dom_element->nodeValue);
				}

				$ret[] = $objet;
			}
		}
		return $ret;
	}

	/* Vérification que le thème est à jour */
	private function verifier_theme_a_jour() {
		$ret = false;
		if (@file_exists($this->fichier_theme_css)) {
			$date_theme_css = @filemtime($this->fichier_theme_css);
			$date_theme_style_xml = @filemtime($this->fichier_theme_style_xml);
			if ($date_theme_css > $date_theme_style_xml) {
				if (@file_exists($this->fichier_theme_additionnel_css)) {
					$date_theme_additionnel_css = @filemtime($this->fichier_theme_additionnel_css);
					$ret = ($date_theme_css > $date_theme_additionnel_css);
				}
				else {
					$ret = true;
				}
			}
		}
		return $ret;
	}

	/* Generation du fichier CSS */
	private function generer_theme_css($css) {
		$css = "/* CSS thème ".$this->theme." */\n".$css;
		if (file_exists($this->fichier_theme_additionnel_css)) {
			$css .= "\n".@file_get_contents($this->fichier_theme_additionnel_css);
		}
		file_put_contents($this->fichier_theme_css, $css);
	}
}