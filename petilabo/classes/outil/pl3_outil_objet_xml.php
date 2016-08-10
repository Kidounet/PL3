<?php

/**
 * Classe de gestion des objets XML
 */

abstract class pl3_outil_objet_xml {
	// constantes obligatoires
	const NOM_FICHE = "";       // fiche dont dépend cet objet (media, page, texte ou theme)
	const NOM_BALISE = "";      // balise correspondante dans le fichier XML
	const TYPE = 0;             // un des types déclarés dans la classe pl3_outil_objet_xml (constante commmençant par TYPE)

	// constantes facultatives
	const ICONE = "";           // icone à afficher en mode admin
	const REFERENCE = null;     // nom de la classe ciblée par le type TYPE_REFERENCE
	const OBJETS = array();     // tableau des classes filles
	const ATTRIBUTS = array();  // tableau de tableaux contenant à minima nom, type + reference si type référence, min et/ou max pour entier

	// types d'objet possible, pour la constante TYPE
	const TYPE_ENTIER = 1;
	const TYPE_CHAINE = 2;
	const TYPE_TEXTE = 3;
	const TYPE_ICONE = 4;
	const TYPE_LIEN = 5;
	const TYPE_REFERENCE = 6;
	const TYPE_INDIRECTION = 7;
	const TYPE_FICHIER = 8;
	const TYPE_COMPOSITE = 9;

	// propriété interne d'un objet
	protected $id = 0;
	protected $objet_parent = null;
	protected $objet_a_jour = true;
	protected $noeud = null;
	protected $objet_classe_par_balise = array();
	protected $objet_balise_par_classe = array();
	protected $liste_objets = array();
	protected $valeur; // voir les fonction de gestion de la valeur

	/* a vérifier */
	protected $attributs = array();

	/* Constructeur */
	public function __construct($id, &$objet_parent, &$noeud = null) {
		$this->id = $id;
		$this->objet_parent = $objet_parent;
		$this->noeud = $noeud;
		foreach (static::OBJETS as $nom_classe) {
			$this->objet_classe_par_balise[$nom_classe::NOM_BALISE] = $nom_classe;
			$this->objet_balise_par_classe[$nom_classe] = $nom_classe::NOM_BALISE;
		}
	}

	/* Affichage */
	abstract public function afficher($mode);
	protected function afficher_objets_fils($mode, $nom_classe = null) {
		$ret = "";
		if ($nom_classe !== null) {
			if (isset($this->objet_balise_par_classe[$nom_classe])) {
				foreach ($this->liste_objets as $objet) {
					if ($objet::NOM_BALISE == $this->objet_balise_par_classe[$nom_classe]) {
						$ret .= "bb".$objet->afficher($mode)."bb";
					}
				}
			}
		}
		else {
			foreach ($this->liste_objets as $objet) {
				$ret .= "aaaa".$objet->afficher($mode)."aaaa";
			}
		}
		return $ret;
	}
	protected function get_html_id() {
		$objet_parent = $this->get_parent();
		$ret = $objet_parent->get_id_parent()."-".$this->get_id_parent()."-".$this->get_id();
		return $this->get_html_name()."-".$ret;
	}
	protected function get_html_name() {
		$nom_classe = get_called_class();
		$nom = str_replace(_PREFIXE_OBJET, "", $nom_classe);
		$nom = str_replace(pl3_fiche_page::NOM_FICHE."_", "", $nom);
		return $nom;
	}

	/* Gestion de la valeur */
	public function avec_valeur() {return (static::TYPE <> self::TYPE_COMPOSITE);}
	public function get_valeur() {return $this->valeur;}
	public function set_valeur($valeur) {
		$ret = false;
		if ($this->avec_valeur()) {
			$valeur = htmlspecialchars($valeur, ENT_QUOTES, "UTF-8");
			if ($this->valeur != $valeur) {
				$this->valeur = $valeur;
				$ret = true;
			}
		}
		return $ret;
	}

	/* Mutateurs / accesseurs */
	public function get_id() {return $this->id;}
	public function set_id($id) {$this->id = $id;}
	public function get_id_parent() {return $this->objet_parent->get_id();}
	public function objet_a_jour() {return $this->objet_a_jour;}
	public function get_nom_balise() {return static::NOM_BALISE;}
	public function get_type() {return static::TYPE;}
	public function get_reference() {return static::REFERENCE;}
	public function &get_noeud() {return $this->noeud;}
	public function &get_parent() {return $this->objet_parent;}

	public function &get_objet_fils_par_balise($nom_balise) {
		$ret = null;
		foreach ($this->liste_objets as $objet) {
			if ($objet->get_nom_balise() == $nom_balise) $ret = $objet;
		}
		return $ret;
	}

	/* Mutateurs / accesseurs par élément */
	public function get_objet_fils_valeur($nom_balise) {
		$objet = $this->get_objet_fils_par_balise($nom_balise);
		return is_null($objet) ? null : $objet->get_valeur();
	}
	public function get_objet_fils_type($nom_balise) {
		$objet = $this->get_objet_fils_par_balise($nom_balise);
		return is_null($objet) ? null : $objet->get_type();
	}
	public function get_objet_fils_reference($nom_balise) {
		$objet = $this->get_objet_fils_par_balise($nom_balise);
		return is_null($objet) ? null : $objet->get_reference();
	}
	public function set_objet_fils_valeur($nom_balise, $valeur) {
		$objet = $this->get_objet_fils_par_balise($nom_balise);
		if ($objet === null) {
			$nom_classe = $this->objet_classe_par_balise[$nom_balise];
			$objet = new $nom_classe(1, $this);
			$this->ajouter_objet($objet);
		}
		$ret = $objet->set_valeur($valeur);
		return $ret;
	}
	public function ajouter_objet(&$objet) {
		$nom_balise = $objet->get_nom_balise();
		if (isset($this->objet_classe_par_balise[$nom_balise])) {
			$this->liste_objets[] = $objet;
		}
		else {die("ERREUR : Tentative d'ajout d'un objet dans une classe non déclarée.");}
	}

	/* Affichage des balises XML */
	public function ouvrir_xml($niveau, $liste_attributs = null) {
		$xml = $this->indenter_xml($niveau);
		$xml .= "<".static::NOM_BALISE;
		$xml .= $this->ouvrir_attributs_xml($liste_attributs);
		$xml .= ">\n";
		return $xml;
	}
	public function fermer_xml($niveau) {
		$xml = $this->indenter_xml($niveau);
		$xml .= "</".static::NOM_BALISE.">\n";
		return $xml;
	}
	public function ouvrir_fermer_xml($niveau, $liste_attributs = null) {
		$xml = $this->indenter_xml($niveau);
		$xml .= "<".static::NOM_BALISE;
		$xml .= $this->ouvrir_attributs_xml($liste_attributs);
		if ($this->avec_valeur()) {
			$xml .= ">";
			$xml .= $this->valeur;
			$xml .= "</".static::NOM_BALISE;
		}
		else {$xml .= "/";}
		$xml .= ">\n";
		return $xml;
	}
	public function ecrire_xml($niveau=0) {
		$xml = "";
		if ($niveau == 0) $xml .= "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
		$attr = array();
		foreach (static::ATTRIBUTS as $attribut) {
			$attr[] = $this->get_xml_attribut($attribut["nom"]);
		}
		if (count($this->liste_objets) == 0) {
			$xml .= $this->ouvrir_fermer_xml($niveau, $attr);
		}
		else {
			$xml .= $this->ouvrir_xml($niveau, $attr);
			$xml .= $this->ecrire_xml_objets_fils(1 + $niveau);
			$xml .= $this->fermer_xml($niveau);
		}
		return $xml;
	}
	protected function ecrire_xml_objets_fils($niveau) {
		$xml = "";
		foreach ($this->liste_objets as $objet) {
			$xml .= $objet->ecrire_xml($niveau);
		}
		return $xml;
	}

	/* Méthodes de service pour l'affichage des balises XML */
	protected function indenter_xml($niveau) {
		$ret = str_repeat("\t", ((int) ($niveau)));
		return $ret;
	}
	protected function ouvrir_attributs_xml($liste_attributs) {
		$ret = "";
		$nb_attributs = count($liste_attributs);
		if ($nb_attributs > 0) {
			foreach($liste_attributs as $attribut) {
				if (strlen($attribut) > 0) {
					$ret .= " ".$attribut;
				}
			}
		}
		return $ret;
	}





	/* fin vérifiés */


	/* Gestion des objets : instanciation d'un objet fils */
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
	/* pour ajouter des actions après instancer_nouveau */
	public function construire_nouveau() {return null;}
	/* construction d'un nom pour une nouvelle instance */
	public function construire_nouveau_nom() {
		if (!(isset($this->attributs["nom"]))) {
			$attribut_nom = _PREFIXE_ID_OBJET.(static::NOM_BALISE)."_".($this->get_id());
			$this->attributs["nom"] = $attribut_nom;
		}
	}

	/* Parsing des balises */
	public function parser_balise($nom_classe, $unique = false) {
		$source_page = pl3_outil_source_page::Get();
		$tab_ret = $source_page->parser_balise(static::NOM_FICHE, $this, $this->noeud, $nom_classe);
		if ($unique) {
			$nb_ret = (int) count($tab_ret);
			$ret = ($nb_ret > 0) ? array($tab_ret[$nb_ret - 1]) : array();
		}
		else {
			$ret = $tab_ret;
		}
		return $ret;
	}

	/* Gestion des attributs */
	public function get_liste_attributs() {return static::ATTRIBUTS;}
	public function set_attribut($nom_attribut, $valeur_attribut) {
		$ret = true;
		if (isset($this->attributs[$nom_attribut])) {
			$ret = ($this->attributs[$nom_attribut] != $valeur_attribut);
		}
		$this->attributs[$nom_attribut] = $valeur_attribut;
		return $ret;
	}
	public function get_attribut_chaine($nom_attribut) {
		$ret = isset($this->attributs[$nom_attribut])?$this->attributs[$nom_attribut]:null;
		return $ret;
	}
	public function get_attribut_entier($nom_attribut, $defaut = 0) {
		$ret = isset($this->attributs[$nom_attribut])?((int) $this->attributs[$nom_attribut]):((int) $defaut);
		return $ret;
	}
	public function has_attribut($nom_attribut) {
		return isset($this->attributs[$nom_attribut]);
	}

	/* Mise en forme XML des attributs */
	public function get_xml_attribut($nom_attribut) {
		$ret = ($this->has_attribut($nom_attribut))?($nom_attribut."=\"".$this->get_attribut_chaine($nom_attribut))."\"":"";
		return $ret;
	}

	/* Recherches */
	public function chercher_objet_classe_par_id($nom_classe, $id) {
		foreach($this->liste_objets as $objet) {
			if ($objet->get_id() === $id) return $objet;
		}
		return null;
	}


	/* Destruction */
	public function detruire() {//indirection page titre
		$source_page = pl3_outil_source_page::Get();
		$nom_texte = $this->get_valeur();
		$texte = $source_page->chercher_liste_textes_par_nom(pl3_objet_texte_texte::NOM_BALISE, $nom_texte);
		if ($texte != null) {
			$source_page->supprimer($texte);
		}
	}
	///* Destruction */
	//public function detruire() { //composite image
	//	$alt = $this->get_objet_fils_par_balise(pl3_objet_media_image_alt::NOM_BALISE);
	//	if ($alt) {$alt->detruire();}
	//	$fichier = $this->get_objet_fils_par_balise(pl3_objet_media_image_fichier::NOM_BALISE);
	//	if ($fichier) {$fichier->detruire();}
	//}
	///* Destruction */
	//public function detruire() { //entier saut
	//	$source_page = pl3_outil_source_page::Get();
	//	$nom_texte = $this->get_valeur();
	//	$texte = $source_page->chercher_liste_textes_par_nom(pl3_objet_page_saut::NOM_BALISE, $nom_texte);
	//	if ($texte != null) {
	//		$source_page->supprimer($texte);
	//	}
	//}
	///* Destruction */
	//public function detruire() { //indirection paragraphe
	//	$source_page = pl3_outil_source_page::Get();
	//	$nom_texte = $this->get_valeur();
	//	$texte = $source_page->chercher_liste_textes_par_nom(pl3_objet_texte_texte_riche::NOM_BALISE, $nom_texte);
	//	if ($texte != null) {
	//		$source_page->supprimer($texte);
	//	}
	//}
	///* Destruction */
	//public function detruire() { // reference image
	//	$source_page = pl3_outil_source_page::Get();
	//	$nom_texte = $this->get_valeur();
	//	$texte = $source_page->chercher_liste_textes_par_nom(pl3_objet_texte_texte::NOM_BALISE, $nom_texte);
	//	if ($texte != null) {
	//		$source_page->supprimer($texte);
	//	}
	//}
	//public function detruire() { // fichier image
	//	$valeur_fichier = html_entity_decode($this->get_valeur(), ENT_QUOTES, "UTF-8");
	//	@unlink(_CHEMIN_XML."images/".$valeur_fichier);
	//}
	//public function detruire() { // indirection alt
	//	$source_page = pl3_outil_source_page::Get();
	//	$alt = $source_page->chercher_liste_textes_par_nom(pl3_objet_texte_texte::NOM_BALISE, $this->nom_alt);
	//	if ($alt != null) {$source_page->supprimer($alt);}
	//}


	public function charger_xml() {
		foreach ($this->objet_balise_par_classe as $nom_classe => $nom_balise) {
			$liste = $this->parser_balise($nom_classe);
			foreach($liste as $objet) {
				$objet->charger_xml();
				$this->liste_objets[] = $objet;
			}
		}
	}

	protected function nb_elements_charges() {
		return count($this->liste_objets);
	}

	protected function est_charge_element_xml($nom_balise) {
		$ret = false;
		foreach ($this->liste_objets as $objet) {
			if ($objet->get_nom_balise() === $nom_balise) $ret = true;
		}
		return $ret;
	}



	public function __call($methode, $args) {
		if (!(strncmp($methode, "get_valeur_", 11))) {
			$nom_balise = substr($methode, 11);
			return $this->get_objet_fils_valeur($nom_balise);
		}
		else if (!(strncmp($methode, "set_valeur_", 11))) {
			$nom_balise = substr($methode, 11);
			$this->set_objet_fils_valeur($nom_balise, $args[0]);
		}
		else if (!(strncmp($methode, "get_attribut_", 13))) {
			$nom_attribut = substr($methode, 13);
			return $this->get_attribut_chaine($nom_attribut);
		}
		else if (!(strncmp($methode, "set_attribut_", 13))) {
			$nom_attribut = substr($methode, 13);
			return $this->set_attribut($nom_attribut, $args[0]);
		}
		else {die("ERREUR : Appel d'une méthode ".$methode." non définie dans un objet XML"); }
	}
}