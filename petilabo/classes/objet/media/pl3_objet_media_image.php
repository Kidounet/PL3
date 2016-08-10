<?php

/**
 * Classe de gestion des images
 */

class pl3_objet_media_image extends pl3_outil_objet_xml {
	const NOM_FICHE = "media";
	const NOM_BALISE = "image";
	const TYPE = self::TYPE_COMPOSITE;

	/* Attributs */
	const ATTRIBUTS = array(
		array("nom" => "nom", "type" => self::TYPE_CHAINE));

	/* objets fils */
	const OBJETS = array(
		"pl3_objet_media_image_fichier",
		"pl3_objet_media_image_alt",
		"pl3_objet_media_image_taille",
		"pl3_objet_media_image_largeur",
		"pl3_objet_media_image_hauteur");

	/* Création */
	public function construire_nouveau() {
		$alt = new pl3_objet_media_image_alt(1, $this);
		$nom_alt = $alt->construire_nouveau();
		if (strlen($nom_alt) > 0) {
			$this->ajouter_objet($alt);
			$this->set_valeur_alt($nom_alt);
		}
	}

	/* Chargement */
	public function charger_xml() {
		parent::charger_xml();
		/* Si la taille réelle n'est pas renseignée on la rajoute */
		$est_l = $this->est_charge_element_xml(pl3_objet_media_image_largeur::NOM_BALISE);
		$est_h = $this->est_charge_element_xml(pl3_objet_media_image_hauteur::NOM_BALISE);
		if ((!($est_l)) || (!($est_h))) {
			$fichier = _CHEMIN_XML."images/".html_entity_decode($this->get_valeur_fichier(), ENT_QUOTES, "UTF-8");
			list($largeur_reelle, $hauteur_reelle) = @getimagesize($fichier);
			if ((!($est_l)) && ($largeur_reelle > 0)) {
				$element_largeur = new pl3_objet_media_image_largeur(1+$this->nb_elements_charges(), $this);
				$element_largeur->set_valeur($largeur_reelle);
				$this->ajouter_objet($element_largeur);
				$this->objet_a_jour = false;
			}
			if ((!($est_h)) && ($hauteur_reelle > 0)) {
				$element_hauteur = new pl3_objet_media_image_hauteur(1+$this->nb_elements_charges(), $this);
				$element_hauteur->set_valeur($hauteur_reelle);
				$this->ajouter_objet($element_hauteur);
				$this->objet_a_jour = false;
			}
		}
	}

	/* Affichage */
	public function afficher($mode) {
		$ret = "";
		$ret .= "<img class=\"image_responsive\"";
		$ret .= $this->afficher_objets_fils($mode);
		$ret .= " />\n";
		return $ret;
	}
}