<?php

class pl3_ajax_init {
	private static $Nom_page = null;
	private static $Nom_balise = null;
	private static $Balise_id = null;
	private static $Objet = null;	
		
	public static function Init() {
		/* Récupération du nom de la page */
		$ajax_objet_valide = false;
		self::$Nom_page = pl3_ajax_post::Post("nom_page");
		if (strlen(self::$Nom_page) > 0) {
			$chemin_page = _CHEMIN_PAGES_XML.(self::$Nom_page)."/";
			$fichier_page = (pl3_fiche_page::NOM_FICHE)._SUFFIXE_XML;
			define("_PAGE_COURANTE", self::$Nom_page);
			define("_CHEMIN_PAGE_COURANTE", $chemin_page);
			$ajax_objet_valide = @file_exists($chemin_page.$fichier_page);
		}

		/* Récupération de la balise et de son id */
		if ($ajax_objet_valide) {
			$ajax_objet_valide = false;
			self::$Balise_id = pl3_ajax_post::Post("balise_id");
			self::$Nom_balise = pl3_ajax_post::Post("nom_balise");
			if ((strlen(self::$Balise_id) > 0) && (strlen(self::$Nom_balise) > 0)) {
				$liste_id = explode("-", self::$Balise_id);
				if (count($liste_id) == 3) {
					list($contenu_param, $bloc_param, $objet_param) = $liste_id;
					$contenu_id = (int) $contenu_param;
					$bloc_id = (int) $bloc_param;
					$objet_id = (int) $objet_param;
					$ajax_objet_valide = (($contenu_id > 0) && ($bloc_id > 0) && ($objet_id > 0));
				}
			}
		}

		/* Chargement des objets XML en fonction des paramètres */
		if ($ajax_objet_valide) {
			$ajax_objet_valide = false;
			$page = new pl3_fiche_page(_CHEMIN_PAGE_COURANTE);
			$contenu = $page->charger_objet_xml("pl3_objet_page_contenu", $contenu_id);
			if ($contenu != null) {
				$bloc = $contenu->chercher_objet_classe_par_id("pl3_objet_page_bloc", $bloc_id);
				if ($bloc != null) {
					self::$Objet = $bloc->chercher_objet_par_id($objet_id);
					if (self::$Objet != null) {$ajax_objet_valide = true;}
				}
			}
		}
		
		return $ajax_objet_valide;
	}
	
	/* Accesseurs */
	public static function Get_nom_page() {return self::$Nom_page;}
	public static function Get_nom_balise() {return self::$Nom_balise;}
	public static function Get_balise_id() {return self::$Balise_id;}
	public static function Get_nom_balise_id() {return self::$Nom_balise."-".self::$Balise_id;}
	public static function Get_objet() {return self::$Objet;}
}