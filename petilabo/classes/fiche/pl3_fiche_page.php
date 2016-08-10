<?php

/**
 * Classe de gestion des fiches page.xml
 */

class pl3_fiche_page extends pl3_outil_fiche_xml {
	const NOM_FICHE   = "page";
	const OBLIGATOIRE = true;

	/* objets fils */
	const OBJETS = array(
		"pl3_objet_page_meta",
		"pl3_objet_page_contenu");

	/* Affichage (9 fonctions) */

	public function afficher($mode) {
		$ret = "";
		$ret .= $this->afficher_head();
		$ret .= $this->afficher_body();
		return $ret;
	}

	public function afficher_head() {
		$ret = "";
		$ret .= $this->ouvrir_head();
		$ret .= $this->ecrire_head();
		$ret .= $this->fermer_head();
		return $ret;
	}

	public function afficher_body() {
		$ret = "";
		$ret .= $this->ouvrir_body();
		$ret .= $this->ecrire_body();
		$ret .= $this->fermer_body();
		return $ret;
	}

	public function ouvrir_head() {
		$ret = "";
		$ret .= "<!doctype html>\n";
		$ret .= "<html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"fr\" lang=\"fr\" dir=\"ltr\">\n";
		$ret .= "<head>\n";
		$ret .= "<meta http-equiv=\"content-type\" content=\"text/html; charset=UTF-8\" />\n";
		$ret .= "<meta name=\"viewport\" content=\"width=device-width,initial-scale=1\" />\n";
		$ret .= "<meta name=\"generator\" content=\"PL3\" />\n";
		return $ret;
	}

	public function ecrire_head() {
		$ret = $this->afficher_objets_fils($this->mode, "meta");
		return $ret;
	}

	public function fermer_head() {
		$ret = "";
		/* Partie CSS */
		$ret .= $this->declarer_css("https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css");
		$ret .= $this->declarer_css(_CHEMIN_CSS."pl3.css");
		$ret .= $this->declarer_css(_CHEMIN_CSS."pl3_objets.css");
		$ret .= $this->declarer_css(_CHEMIN_CSS."pl3_admin.css", _MODE_ADMIN);
		$ret .= $this->declarer_css(_CHEMIN_CSS."pl3_admin_media.css", _MODE_ADMIN_MEDIA);
		$ret .= $this->declarer_css(_CHEMIN_CSS."pl3_admin_grille.css", _MODE_ADMIN_GRILLE);
		$ret .= $this->declarer_css(_CHEMIN_CSS."pl3_admin_objets.css", _MODE_ADMIN_OBJETS);
		$ret .= $this->declarer_css(_CHEMIN_TIERS."trumbo/ui/trumbowyg.min.css", _MODE_ADMIN_OBJETS);
		$ret .= $this->declarer_css(_CHEMIN_TIERS."trumbo/plugins/colors/ui/trumbowyg.colors.min.css", _MODE_ADMIN_OBJETS);
		$theme = $this->get_nom_theme();
		$ret .= $this->declarer_css(_CHEMIN_RESSOURCES_CSS."style_".$theme.".css");

		/* Partie JS */
		$ret .= $this->declarer_js("//code.jquery.com/jquery-1.12.0.min.js");
		$ret .= $this->declarer_js("//code.jquery.com/ui/1.11.4/jquery-ui.js", _MODE_ADMIN_GRILLE|_MODE_ADMIN_OBJETS);
		$ret .= "</head>\n";
		return $ret;
	}

	public function ouvrir_body() {
		$ret = "";
		$ret .= "<body>\n";
		return $ret;
	}

	public function ecrire_body() {
		if (($this->mode & _MODE_ADMIN_XML) > 0) {
			$xml = $this->ecrire_xml();
			$html = htmlspecialchars($xml, ENT_QUOTES, "UTF-8");
			$contenu_mode = nl2br(str_replace(" ","&nbsp;", $html));
			$classe_mode = "page_xml";
		}
		else {
			$contenu_mode = $this->afficher_objets_fils($this->mode, "contenu");
			if ($this->mode & _MODE_ADMIN_GRILLE) {
				$contenu_mode .= "<div class=\"contenu_ajout contenu_defaut\">";
				$contenu_mode .= "<p class=\"contenu_poignee_ajout\">";
				$contenu_mode .= "<a class=\"fa fa-bars\" href=\"#\" title=\"Ajouter un contenu\"></a>";
				$contenu_mode .= "</p></div>\n";
			}
			$classe_mode = "page";
		}
		$classe = $classe_mode.((($this->mode & _MODE_ADMIN) > 0)?" page_mode_admin":"");
		$ret = "<div class=\"".$classe."\" name=\""._PAGE_COURANTE."\">".$contenu_mode."</div>\n";
		return $ret;
	}

	public function fermer_body() {
		$ret = "";
		/* TEMPORAIRE : Ajout d'un lien pour switcher le mode */
		if (($this->mode & _MODE_ADMIN) > 0) {
			$ret .= "<p style=\"margin-top:20px;\"><a href=\"../"._PAGE_COURANTE._SUFFIXE_PHP."\">Mode normal</a></p>\n";
		}
		else {
			$ret .= "<p style=\"margin-top:20px;\"><a href=\"admin/"._PAGE_COURANTE._SUFFIXE_PHP."\">Mode admin</a></p>\n";
		}

		/* Appel des outils javascript */
		$ret .= $this->declarer_js(_CHEMIN_JS."pl3_admin.js", _MODE_ADMIN);
		$ret .= $this->declarer_js(_CHEMIN_JS."pl3_admin_media.js", _MODE_ADMIN_MEDIA);
		$ret .= $this->declarer_js(_CHEMIN_JS."pl3_admin_grille.js", _MODE_ADMIN_GRILLE);
		$ret .= $this->declarer_js(_CHEMIN_JS."pl3_admin_objets.js", _MODE_ADMIN_OBJETS);
		$ret .= $this->declarer_js(_CHEMIN_TIERS."trumbo/trumbowyg.min.js", _MODE_ADMIN_OBJETS);
		$ret .= $this->declarer_js(_CHEMIN_TIERS."trumbo/langs/fr.min.js", _MODE_ADMIN_OBJETS);
		$ret .= $this->declarer_js(_CHEMIN_TIERS."trumbo/plugins/colors/trumbowyg.colors.min.js", _MODE_ADMIN_OBJETS);
		$ret .= $this->declarer_js(_CHEMIN_TIERS."trumbo/plugins/editlink/trumbowyg.editlink.min.js", _MODE_ADMIN_OBJETS);
		$ret .= "</body>\n";
		$ret .= "</html>\n";
		return $ret;
	}

	/* fonctions internes diverses */

	private function get_meta() {
		foreach ($this->liste_objets as $objet) {
			if ($objet->get_nom_balise() == "meta") return $objet;
		}
		return null;;
	}

	private function declarer_css($fichier_css, $mode = -1) {
		$ret = "";
		if (($mode == -1) || (($mode & $this->mode) > 0)) {
			$ret .= "<link rel=\"stylesheet\" type=\"text/css\" href=\"".$fichier_css."\"/>\n";
		}
		return $ret;
	}
	private function declarer_js($fichier_js, $mode = -1) {
		$ret = "";
		if (($mode == -1) || (($mode & $this->mode) > 0)) {
			$ret .= "<script type=\"text/javascript\" src=\"".$fichier_js."\"></script>\n";
		}
		return $ret;
	}


	/* fin vérifié */

	/* Propriétés */
	private $nom_theme = _NOM_THEME_DEFAUT;
	private $nom_style = _NOM_STYLE_DEFAUT;

	/* Chargement */
	public function charger_xml() {
		parent::charger_xml();
		$meta = $this->get_meta();
		if ($meta != null) {
			$meta_theme = $meta->get_valeur_theme();
			if (strlen($meta_theme) > 0) {$this->nom_theme = $meta_theme;}
			$meta_style = $meta->get_valeur_style();
			if (strlen($meta_style) > 0) {$this->nom_style = $meta_style;}
		}
	}

	/* Accesseurs / mutateurs */
	public function get_nom_theme() {return $this->nom_theme;}
	public function get_nom_style() {return $this->nom_style;}

	public function ajouter_contenu(&$contenu) {
		$this->liste_objets[] = $contenu;
		$contenu->maj_cardinal_et_largeur();
	}

}