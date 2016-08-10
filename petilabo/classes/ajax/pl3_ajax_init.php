<?php

class pl3_ajax_init {
	private static $Source_page = null;
	private static $Nom_page = null;
	private static $Nom_balise = null;
	private static $Balise_id = null;
	private static $Bloc_id = null;
	private static $Page = null;
	private static $Contenu = null;
	private static $Bloc = null;
	private static $Objet = null;
	private static $Fiche_media_local = null;
	private static $Media_id = null;
	private static $Media = null;

	public static function Init_objet() {
		/* Récupération du nom de la page */
		$ajax_objet_valide = self::Init_page();

		/* Récupération de la balise et de son id */
		if ($ajax_objet_valide) {
			$ajax_objet_valide = false;
			self::$Balise_id = pl3_admin_post::Post("balise_id");
			self::$Nom_balise = pl3_admin_post::Post("nom_balise");
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
			// TODO : Réfléchir sur la nécessité de tout recharger..
			// self::$Source_page->charger_page_xml();
			self::$Source_page->charger_xml();
			self::$Page = self::$Source_page->get_page();
			self::$Contenu = self::$Page->chercher_objet_classe_par_id("pl3_objet_page_contenu", $contenu_id);
			if (self::$Contenu != null) {
				self::$Bloc = self::$Contenu->chercher_objet_classe_par_id("pl3_objet_page_bloc", $bloc_id);
				if (self::$Bloc != null) {
					self::$Objet = self::$Bloc->chercher_objet_par_id($objet_id);
					if (self::$Objet != null) {$ajax_objet_valide = true;}
				}
			}
		}

		return $ajax_objet_valide;
	}

	public static function Init_contenu() {
		/* Récupération du nom de la page */
		$ajax_objet_valide = self::Init_page();

		/* Récupération du contenu */
		if ($ajax_objet_valide) {
			$ajax_objet_valide = false;
			$contenu_id = pl3_admin_post::Post("contenu_id");
			// TODO : Réfléchir sur la nécessité de tout recharger..
			// self::$Source_page->charger_page_xml();
			self::$Source_page->charger_xml();
			self::$Page = self::$Source_page->get_page();
			self::$Contenu = self::$Page->chercher_objet_classe_par_id("pl3_objet_page_contenu", $contenu_id);
			if (self::$Contenu != null) {$ajax_objet_valide = true;}
		}

		return $ajax_objet_valide;
	}

	public static function Init_bloc() {
		/* Récupération du nom de la page */
		$ajax_objet_valide = self::Init_page();

		/* Récupération du bloc et de son id */
		if ($ajax_objet_valide) {
			$ajax_objet_valide = false;
			self::$Bloc_id = pl3_admin_post::Post("bloc_id");
			$liste_id = explode("-", self::$Bloc_id);
			if (count($liste_id) == 2) {
				list($contenu_param, $bloc_param) = $liste_id;
				$contenu_id = (int) $contenu_param;
				$bloc_id = (int) $bloc_param;
				$ajax_objet_valide = (($contenu_id > 0) && ($bloc_id > 0));
			}
		}

		/* Chargement des objets XML en fonction des paramètres */
		if ($ajax_objet_valide) {
			$ajax_objet_valide = false;
			// TODO : Réfléchir sur la nécessité de tout recharger..
			// self::$Source_page->charger_page_xml();
			self::$Source_page->charger_xml();
			self::$Page = self::$Source_page->get_page();
			self::$Contenu = self::$Page->chercher_objet_classe_par_id("pl3_objet_page_contenu", $contenu_id);
			if (self::$Contenu != null) {
				self::$Bloc = self::$Contenu->chercher_objet_classe_par_id("pl3_objet_page_bloc", $bloc_id);
				if (self::$Bloc != null) {$ajax_objet_valide = true;}
			}
		}

		return $ajax_objet_valide;
	}

	public static function Init_media() {
		/* Récupération du nom de la page */
		$ajax_media_valide = self::Init_page();

		/* Récupération du bloc et de son id */
		if ($ajax_media_valide) {
			$ajax_media_valide = false;
			self::$Media_id = pl3_admin_post::Post("media_id");
			// TODO : Réfléchir sur la nécessité de tout recharger..
			// self::$Source_page->charger_page_xml();
			self::$Source_page->charger_xml();
			self::$Fiche_media_local = self::$Source_page->get_media(_NOM_SOURCE_LOCAL);
			if (self::$Fiche_media_local != null) {
				self::$Media = self::$Fiche_media_local->chercher_objet_classe_par_id("pl3_objet_media_image", self::$Media_id);
				if (self::$Media != null) {$ajax_media_valide = true;}
			}
		}

		return $ajax_media_valide;
	}

	/* Récupération du nom de la page */
	public static function Init_page() {
		$ajax_page_valide = false;
		self::$Nom_page = pl3_admin_post::Post("nom_page");
		if (strlen(self::$Nom_page) > 0) {
			$chemin_page = _CHEMIN_PAGES_XML.(self::$Nom_page)."/";
			$fichier_page = (pl3_fiche_page::NOM_FICHE)._SUFFIXE_XML;
			$ajax_page_valide = @file_exists($chemin_page.$fichier_page);
		}
		/* Définition des constantes pour la page courante */
		if ($ajax_page_valide) {
			define("_PAGE_COURANTE", self::$Nom_page);
			define("_CHEMIN_PAGE_COURANTE", $chemin_page);
			self::$Source_page = pl3_outil_source_page::Get();
		}
		return $ajax_page_valide;
	}

	/* Accesseurs */
	public static function Get_nom_balise() {return self::$Nom_balise;}
	public static function Get_balise_id() {return self::$Balise_id;}
	public static function Get_nom_balise_id() {return self::$Nom_balise."-".self::$Balise_id;}
	public static function Get_source_page() {return self::$Source_page;}
	public static function Get_page() {return self::$Page;}
	public static function Get_contenu() {return self::$Contenu;}
	public static function Get_bloc() {return self::$Bloc;}
	public static function Get_bloc_id() {return self::$Bloc_id;}
	public static function Get_fiche_media() {return self::$Fiche_media_local;}
	public static function Get_media_id() {return self::$Media_id;}
	public static function Get_media() {return self::$Media;}
	public static function Get_objet() {return self::$Objet;}
}