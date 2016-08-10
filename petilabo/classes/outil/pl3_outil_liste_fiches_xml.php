<?php

class pl3_outil_liste_fiches_xml {
	private $nom_classe_fiche;
	private $liste_fiches;

	/* Constructeur */
	public function __construct($nom_fiche) {
		$this->nom_classe_fiche = _PREFIXE_FICHE.$nom_fiche;
	}

	public function instancier_nouveau($nom_source, $classe_objet) {
		$instance = null;
		if (isset($this->liste_fiches[$nom_source])) {
			$fiche = $this->liste_fiches[$nom_source];
			$instance = $fiche->instancier_nouveau($classe_objet);
		}
		return $instance;
	}

	public function ajouter_objet($nom_source, &$objet) {
		if (isset($this->liste_fiches[$nom_source])) {
			$fiche = $this->liste_fiches[$nom_source];
			$instance = $fiche->ajouter_objet($objet);
		}
	}

	public function enlever_objet($nom_source, &$objet) {
		if (isset($this->liste_fiches[$nom_source])) {
			$fiche = $this->liste_fiches[$nom_source];
			$instance = $fiche->enlever_objet($objet);
		}
	}

	/* Accesseurs */
	public function &get_source($nom_source) {return $this->liste_fiches[$nom_source];}

	/* Ajout d'une fiche en provenance d'une source */
	public function ajouter_source($nom_source, $chemin_source) {
		$id_fiche = 1 + count($this->liste_fiches);
		$this->liste_fiches[$nom_source] = new $this->nom_classe_fiche($chemin_source, $id_fiche);
		return $id_fiche;
	}

	/* Chargement et enregistrement XML */
	public function charger_xml() {
		foreach ($this->liste_fiches as $nom_source => $fiche) {
			$fiche->charger_xml();
		}
	}

	public function enregistrer_xml($nom_source = null) {
		/* Si la source est précisée on n'enregistre que cette source */
		if (strlen($nom_source) > 0) {
			if (isset($this->liste_fiches[$nom_source])) {
				$fiche = $this->liste_fiches[$nom_source];
				$fiche->enregistrer_xml();
			}
		}
		/* Si la source n'est pas précisée on enregistre toutes les sources */
		else {
			foreach ($this->liste_fiches as $nom_source => $fiche) {
				$fiche->enregistrer_xml();
			}
		}
	}

	/* Affichage */
	public function afficher($mode) {
		$ret = "";
		foreach ($this->liste_fiches as $nom_source => $fiche) {
			$ret .= $fiche->afficher($mode);
		}
		return $ret;
	}

	/* Recherches */
	public function chercher_instance_balise_par_attribut($nom_balise, $nom_attribut, $valeur_attribut) {
		foreach ($this->liste_fiches as $fiche) {
			$instance = $fiche->chercher_objet_balise_par_attribut($nom_balise, $nom_attribut, $valeur_attribut);
			if ($instance != null) {return $instance;}
		}
		return null;
	}
	public function chercher_instance_balise_par_nom($nom_balise, $valeur) {
		return $this->chercher_instance_balise_par_attribut($nom_balise, "nom", $valeur);
	}
	public function chercher_liste_noms_par_classe($nom_classe) {
		$ret = array();
		foreach ($this->liste_fiches as $fiche) {
			$fiche_ret = $fiche->chercher_liste_noms_par_classe($nom_classe);
			$ret = array_merge($ret, $fiche_ret);
		}
		return $ret;
	}
}