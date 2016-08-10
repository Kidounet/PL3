<?php

/**
 * Classe de gestion des fichiers XML
 */

abstract class pl3_outil_fiche_xml extends pl3_outil_objet_xml {
	const OBLIGATOIRE = false;
	const NOM_BALISE = _NOM_BALISE_GENERIQUE;

	/* Propriétés */
	protected $mode = _MODE_NORMAL;
	protected $nom_fichier_xml = null;
	protected $fiche_a_jour = true;
	private $dom = null;

	/* Constructeur */
	public function __construct($chemin, $id=1) {
		$this->id = $id;
		$this->nom_fichier_xml = $chemin.(static::NOM_FICHE)._SUFFIXE_XML;
		$this->dom = new DOMDocument();
		foreach (static::OBJETS as $nom_classe) {
			$this->objet_classe_par_balise[$nom_classe::NOM_BALISE] = $nom_classe;
			$this->objet_balise_par_classe[$nom_classe] = $nom_classe::NOM_BALISE;
		}
	}

	/* Gestion des objets */
	public function instancier_nouveau($nom_classe) {
		if (isset($this->objet_balise_par_classe[$nom_classe])) {
			$objet = new $nom_classe(1 + count($this->liste_objets), $this);
			$objet->construire_nouveau();
			return $objet;
		}
		else {
			die("ERREUR : Instanciation d'un objet inexistant");
		}
	}

	/* Accesseurs / mutateurs */
	public function set_mode($mode) {$this->mode = $mode;}
	public function get_mode() {return $this->mode;}
	public function get_id() {return $this->id;}
	public function lire_nom_fichier_xml() {return $this->nom_fichier_xml;}
	public function fiche_a_jour() {return $this->fiche_a_jour;}

	/* Chargement */
	public function charger_xml() {
		$ret = $this->charger();
		if ($ret) {$this->charger_objets(); }
		else if (static::OBLIGATOIRE) {die("ERREUR : Fichier XML obligatoire introuvable");}
		if (!($this->fiche_a_jour)) {
			$this->enregistrer_xml();
			$this->fiche_a_jour = true;
		}
		return $ret;
	}
	protected function charger() {
		$ret = false;
		$load = @$this->dom->load($this->nom_fichier_xml);
		if ($load) {
			$document = $this->dom->getElementsByTagName(_NOM_BALISE_GENERIQUE);
			if ($document->length > 0) {
				$this->noeud = $document->item(0);
				$ret = true;
			}
		}
		return $ret;
	}
	protected function charger_objets() {
		foreach ($this->objet_balise_par_classe as $nom_classe => $nom_balise) {
			$liste = $this->parser_balise($nom_classe);
			foreach($liste as $objet) {
				$objet->charger_xml();
				$this->liste_objets[] = $objet;
				if (!($objet->objet_a_jour())) {$this->fiche_a_jour = false;}
			}
		}
	}

	/* Sauvegarde */
	public function enregistrer_xml() {
		$ret = file_put_contents($this->nom_fichier_xml, $this->ecrire_xml());
		return $ret;
	}

	/* Ajouts "inline" */
	public function ajouter_objet(&$objet) {
		$nom_classe = get_class($objet);
		if (isset($this->objet_balise_par_classe[$nom_classe])) {
			$this->liste_objets[] = $objet;
		}
		else {die("ERREUR : Tentative d'ajout d'un objet dans une classe non déclarée.");}
	}

	/* Suppressions "inline" */
	public function enlever_objet(&$objet_enleve) {
		$nom_classe = get_class($objet_enleve);
		if (isset($this->objet_balise_par_classe[$nom_classe])) {
			$index_enleve = -1;
			$objet_enleve_id = $objet_enleve->get_id();
			foreach($this->liste_objets[$nom_classe] as $index => $objet) {
				if ($objet->get_id() === $objet_enleve_id) {$index_enleve = $index;}
			}
			if ($index_enleve >= 0) {unset($this->liste_objets[$nom_classe][$index_enleve]);}
		}
		else {die("ERREUR : Tentative d'ajout d'un objet dans une classe non déclarée.");}
	}

	/* Afficher */
	public function afficher($mode) {
		$ret = $this->afficher_objets_fils($mode);
		return $ret;
	}

	/* Recherches */
	public function chercher_objet_classe_par_id($nom_classe, $valeur_id) {
		if (isset($this->liste_objets[$nom_classe])) {
			foreach($this->liste_objets[$nom_classe] as $instance) {
				$id = $instance->get_id();
				if ($valeur_id == $id) {return $instance;}
			}
		}
		return null;
	}
	public function chercher_objet_classe_par_attribut($nom_classe, $nom_attribut, $valeur_attribut) {
		if (isset($this->liste_objets[$nom_classe])) {
			foreach($this->liste_objets[$nom_classe] as $instance) {
				$valeur_instance = $instance->get_attribut_chaine($nom_attribut);
				if ($valeur_instance == $valeur_attribut) {return $instance;}
			}
		}
		return null;
	}
	public function chercher_objet_balise_par_attribut($nom_balise, $nom_attribut, $valeur_attribut) {
		$nom_classe = $this->objet_classe_par_balise($nom_balise);
		if ($nom_classe != null) {return $this->chercher_objet_classe_par_attribut($nom_classe, $nom_attribut, $valeur_attribut);}
		else {return null;}
	}
	public function chercher_liste_noms_par_classe($nom_classe) {
		$ret = array();
		if (isset($this->liste_objets[$nom_classe])) {
			foreach($this->liste_objets[$nom_classe] as $instance) {
				$nom = $instance->get_attribut_chaine("nom");
				$id = (int) $instance->get_id();
				$ret[$id] = $nom;
			}
		}
		return $ret;
	}

}