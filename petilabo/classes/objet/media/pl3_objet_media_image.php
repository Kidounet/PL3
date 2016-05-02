<?php

/**
 * Classe de gestion des images
 */
 
class pl3_objet_media_image_fichier extends pl3_outil_objet_xml {
	/* Fiche */
	const NOM_FICHE = "media";

	/* Balise */
	const NOM_BALISE = "fichier";
	public static $Balise = array("nom" => self::NOM_BALISE, "type" => self::TYPE_FICHIER);
	
	/* Attributs */
	public static $Liste_attributs = array();
	
	/* Méthodes */
	public function ecrire_xml($niveau) {
		$xml = $this->ouvrir_fermer_xml($niveau);
		return $xml;
	}
	
	public function afficher($mode) {
		$valeur_fichier = html_entity_decode($this->get_valeur(), ENT_QUOTES, "UTF-8");
		$ret = " src=\""._CHEMIN_IMAGES_XML.$valeur_fichier."\"";
		return $ret;
	}
}

class pl3_objet_media_image_alt extends pl3_outil_objet_xml {
	/* Fiche */
	const NOM_FICHE = "media";

	/* Balise */
	const NOM_BALISE = "alt";
	public static $Balise = array("nom" => self::NOM_BALISE, "type" => self::TYPE_REFERENCE, "reference" => "pl3_objet_texte_texte");
	
	/* Attributs */
	public static $Liste_attributs = array();
	
	/* Méthodes */
	public function ecrire_xml($niveau) {
		$xml = $this->ouvrir_fermer_xml($niveau);
		return $xml;
	}
	
	public function afficher($mode) {
		$valeur_alt = html_entity_decode($this->get_valeur(), ENT_QUOTES, "UTF-8");
		$ret = " alt=\"".$valeur_alt."\"";
		return $ret;
	}
}

class pl3_objet_media_image_taille_standard extends pl3_outil_objet_xml {
	/* Fiche */
	const NOM_FICHE = "media";

	/* Balise */
	const NOM_BALISE = "taille_standard";
	public static $Balise = array("nom" => self::NOM_BALISE, "type" => self::TYPE_REFERENCE, "reference" => "pl3_objet_theme_taille_image");
	
	/* Attributs */
	public static $Liste_attributs = array();
	
	/* Méthodes */
	public function ecrire_xml($niveau) {
		$xml = $this->ouvrir_fermer_xml($niveau);
		return $xml;
	}
	
	public function afficher($mode) {return null;}
}

class pl3_objet_media_image_largeur_reelle extends pl3_outil_objet_xml {
	/* Fiche */
	const NOM_FICHE = "media";

	/* Balise */
	const NOM_BALISE = "largeur_reelle";
	public static $Balise = array("nom" => self::NOM_BALISE, "type" => self::TYPE_ENTIER);
	
	/* Attributs */
	public static $Liste_attributs = array();
	
	/* Méthodes */
	public function ecrire_xml($niveau) {
		$xml = $this->ouvrir_fermer_xml($niveau);
		return $xml;
	}
	
	public function afficher($mode) {
		$valeur_largeur = (int) $this->get_valeur();
		$ret = " width=\"".$valeur_largeur."\"";
		return $ret;
	}
}

class pl3_objet_media_image_hauteur_reelle extends pl3_outil_objet_xml {
	/* Fiche */
	const NOM_FICHE = "media";

	/* Balise */
	const NOM_BALISE = "hauteur_reelle";
	public static $Balise = array("nom" => self::NOM_BALISE, "type" => self::TYPE_ENTIER);
	
	/* Attributs */
	public static $Liste_attributs = array();
	
	/* Méthodes */
	public function ecrire_xml($niveau) {
		$xml = $this->ouvrir_fermer_xml($niveau);
		return $xml;
	}
	
	public function afficher($mode) {
		$valeur_hauteur = (int) $this->get_valeur();
		$ret = " height=\"".$valeur_hauteur."\"";
		return $ret;
	}
}

class pl3_objet_media_image extends pl3_outil_objet_composite_xml {
	/* Fiche */
	const NOM_FICHE = "media";

	/* Balise */
	const NOM_BALISE = "image";
	public static $Balise = array("nom" => self::NOM_BALISE, "type" => self::TYPE_COMPOSITE);
	
	/* Attributs */
	public static $Liste_attributs = array(
		array("nom" => self::NOM_ATTRIBUT_NOM, "type" => self::TYPE_CHAINE));
	
	/* Méthodes */
	public function __construct($id, &$parent, &$noeud = null) {
		$this->declarer_element(pl3_objet_media_image_fichier::NOM_BALISE);
		$this->declarer_element(pl3_objet_media_image_alt::NOM_BALISE);
		$this->declarer_element(pl3_objet_media_image_taille_standard::NOM_BALISE);
		$this->declarer_element(pl3_objet_media_image_largeur_reelle::NOM_BALISE);
		$this->declarer_element(pl3_objet_media_image_hauteur_reelle::NOM_BALISE);
		parent::__construct($id, $parent, $noeud);
	}

	public function charger_xml() {
		$this->charger_elements_xml();
		/* Si la taille réelle n'est pas renseignée on la rajoute */
		$est_l = $this->est_charge_element_xml(pl3_objet_media_image_largeur_reelle::NOM_BALISE);
		$est_h = $this->est_charge_element_xml(pl3_objet_media_image_hauteur_reelle::NOM_BALISE);
		if ((!($est_l)) || (!($est_h))) {
			$fichier = _CHEMIN_XML."images/".html_entity_decode($this->get_valeur_fichier(), ENT_QUOTES, "UTF-8");
			list($largeur_reelle, $hauteur_reelle) = @getimagesize($fichier);
			if ((!($est_l)) && ($largeur_reelle > 0)) {
				$element_largeur_reelle = new pl3_objet_media_image_largeur_reelle(1+$this->nb_elements_charges(), $this);
				$element_largeur_reelle->set_valeur($largeur_reelle);
				$this->ajouter_element_xml($element_largeur_reelle);
				$this->objet_a_jour = false;
			}
			if ((!($est_h)) && ($hauteur_reelle > 0)) {
				$element_hauteur_reelle = new pl3_objet_media_image_hauteur_reelle(1+$this->nb_elements_charges(), $this);
				$element_hauteur_reelle->set_valeur($hauteur_reelle);
				$this->ajouter_element_xml($element_hauteur_reelle);
				$this->objet_a_jour = false;
			}
		}
	}

	public function ecrire_xml($niveau) {
		$attr_nom = $this->get_xml_attribut(self::NOM_ATTRIBUT_NOM);
		$xml = $this->ouvrir_xml($niveau, array($attr_nom));
		$xml .= $this->ecrire_elements_xml(1 + $niveau);
		$xml .= $this->fermer_xml($niveau);
		return $xml;
	}
	
	public function afficher($mode) {
		$ret = "";
		$ret .= "<img class=\"image_responsive\"";
		$ret .= $this->afficher_elements_xml($mode);
		$ret .= " />\n";
		return $ret;
	}
}