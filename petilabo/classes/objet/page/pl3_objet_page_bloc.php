<?php

/**
 * Classe de gestion des blocs
 */

class pl3_objet_page_bloc extends pl3_outil_objet_xml {
	const NOM_FICHE = "page";
	const NOM_BALISE = "bloc";
	const TYPE = self::TYPE_COMPOSITE;

	/* Attributs */
	const ATTRIBUTS = array(
		array("nom" => "nom", "type" => self::TYPE_CHAINE),
		array("nom" => "style", "type" => self::TYPE_REFERENCE, "reference" => "pl3_objet_theme_style_bloc"),
		array("nom" => "taille", "type" => self::TYPE_ENTIER, "min" => 1));

	/* objets fils */
	const OBJETS = array(
		"pl3_objet_page_image",
		"pl3_objet_page_paragraphe",
		"pl3_objet_page_saut",
		"pl3_objet_page_titre");

	/* Affichage */
	public function afficher($mode) {
		$num_id_bloc = $this->get_id_parent()."-".$this->get_id();
		$taille = $this->get_attribut_entier("taille", 1);
		$style = $this->get_attribut_style();
		if (strlen($style) == 0) {$style = _NOM_STYLE_DEFAUT;}
		if ($mode == _MODE_ADMIN_GRILLE) {
			$ret = $this->afficher_grille($num_id_bloc, $taille, $style);
		}
		else {
			$ret = $this->afficher_standard($mode, $num_id_bloc, $taille, $style);
		}
		return $ret;
	}

	private function afficher_standard($mode, $num_id_bloc, $taille, $style) {
		$ret = "";
		$classe = "bloc bloc_".$style;
		$style_bloc = "flex-grow:".$taille.";";
		$ret .= "<div id=\"bloc-".$num_id_bloc."\" class=\"".$classe."\" style=\"".$style_bloc."\">";
		$this->afficher_objets_fils($mode);
		if ($mode == _MODE_ADMIN_OBJETS) {
			$ret .= "<p id=\"poignee-bloc-".$num_id_bloc."\" class=\"bloc_poignee_ajout\">";
			foreach (static::OBJETS as $objet) {
				if ($objet::ICONE !== "") {
					$ret .= "<a class=\"fa ".$objet::ICONE."\" href=\"".$objet."\" title=\"Ajouter un objet ".$nom_balise."\"></a>";
				}
			}
			$ret .= "</p>\n";
		}
		$ret .= "</div>";
		return $ret;
	}

	private function afficher_grille($num_id_bloc, $taille, $style) {
		/* En mode grille on affiche en inline-block à cause des pbs JQuery UI Sortable / display flex */
		$ret = "";
		$classe = "bloc_grille bloc_".$style;
		$taille_totale = 1000 - 20 * $this->cardinal_parent;
		$largeur_bloc = floor(($taille_totale * $taille) / $this->largeur_parent) / 10;
		$style_bloc = "width:".$largeur_bloc."%;";
		$ret .= "<div id=\"bloc-".$num_id_bloc."\" class=\"".$classe."\" style=\"".$style_bloc."\">";
		$nom = $this->get_attribut_nom();
		$ret .= "<p class=\"bloc_legende_nom\">".$nom." ".$this->largeur_parent."</p>";
		$ret .= "</div>";
		return $ret;
	}


	/* Cardinal et largeur du parent (pour affichage en inline-block) */
	private $largeur_parent = 1;
	public function set_largeur_parent($largeur_parent) {
		if ($largeur_parent > 0) {$this->largeur_parent = $largeur_parent;}
	}
	private $cardinal_parent = 1;
	public function set_cardinal_parent($cardinal_parent) {
		if ($cardinal_parent > 0) {$this->cardinal_parent = $cardinal_parent;}
	}

	/* Gestion des objet dans le bloc */
	public function remplacer_objet(&$nouvel_objet) {
		$nouvel_id = $nouvel_objet->get_id();
		$nb_objets = count($this->objets);
		for ($cpt = 0;$cpt < $nb_objets;$cpt ++) {
			$objet = $this->objets[$cpt];
			if ($objet != null) {
				$id = $objet->get_id();
				if ($id == $nouvel_id) {
					$this->objets[$cpt] = $nouvel_objet;
					return true;
				}
			}
		}
		return false;
	}
	public function instancier_nouveau($nom_classe) {
		$objet = new $nom_classe(1 + count($this->objets), $this);
		return $objet;
	}
	public function retirer_objet($objet_id) {
		$liste_objets = array();
		$nb_objets = count($this->objets);
		$id_cpt = 1;
		for ($cpt = 0;$cpt < $nb_objets;$cpt ++) {
			$objet = &$this->objets[$cpt];
			if ($objet != null) {
				if ($objet->get_id() != $objet_id) {
					$objet->set_id($id_cpt);
					$liste_objets[] = $objet;
					$id_cpt += 1;
				}
				else {
					$objet->detruire();
					unset($objet);
				}
			}
		}
		$this->objets = $liste_objets;
	}

	/* Accesseur */
	public function lire_nb_objets() {
		return count($this->objets);
	}

	/* Recherches */
	public function chercher_objet_par_id($id) {
		foreach($this->objets as $instance) {
			$valeur_id = $instance->get_id();
			if ($valeur_id === $id) {return $instance;}
		}
		return null;
	}

	/* Mutateur */
	public function reordonner($tab_ordre) {
		$nouveaux_objets = array();
		foreach ($tab_ordre as $no_ordre) {
			$index = ((int) $no_ordre) - 1;
			$nouveaux_objets[] = &$this->objets[$index];
		}
		$this->objets = $nouveaux_objets;
	}
}