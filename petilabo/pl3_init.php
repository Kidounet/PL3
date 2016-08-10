<?php

/* Définition de la version PetiLabo */
define("_VERSION_PETILABO", "3.0.0");
define("_NOM_BALISE_GENERIQUE", "petilabo");

/* Définitions complémentaires */
if (!defined("_CHEMIN_BASE_FICHIER")) {define("_CHEMIN_BASE_FICHIER", _CHEMIN_BASE_URL);}
if (!defined("_CHEMIN_BASE_RESSOURCES")) {define("_CHEMIN_BASE_RESSOURCES", _CHEMIN_BASE_FICHIER);}

/* Chemins PHP */
define("_CHEMIN_PETILABO", _CHEMIN_BASE_URL."petilabo/");
define("_CHEMIN_CLASSES", _CHEMIN_PETILABO."classes/");
define("_CHEMIN_OBJET", _CHEMIN_CLASSES."objet/");
define("_STYLE_COMMUN_CSS", "pl3_objet_theme_style_commun_css.php");

/* Chemins ressources */
define("_CHEMIN_RESSOURCES", _CHEMIN_BASE_FICHIER."petilabo/");
define("_CHEMIN_AJAX", _CHEMIN_RESSOURCES."ajax/");
define("_CHEMIN_CSS", _CHEMIN_RESSOURCES."css/");
define("_CHEMIN_JS", _CHEMIN_RESSOURCES."js/");
define("_CHEMIN_TIERS", _CHEMIN_RESSOURCES."tiers/");

/* Chemins XML */
define("_CHEMIN_XML", _CHEMIN_BASE_URL."xml/");
define("_CHEMIN_RESSOURCES_XML", _CHEMIN_BASE_RESSOURCES."xml/");
define("_CHEMIN_PAGES_XML", _CHEMIN_XML."pages/");
define("_CHEMIN_IMAGES_XML", _CHEMIN_RESSOURCES_XML."images/");

/* Chemin thèmes */
define("_CHEMIN_THEMES_XML", _CHEMIN_XML."themes/");
define("_CHEMIN_THEMES_CSS", _CHEMIN_THEMES_XML."css/");
define("_CHEMIN_RESSOURCES_CSS", _CHEMIN_RESSOURCES_XML."themes/css/");

/* Préfixes */
define("_PREFIXE_PETILABO", "pl3_");
define("_PREFIXE_FICHE", _PREFIXE_PETILABO."fiche_");
define("_PREFIXE_OBJET", _PREFIXE_PETILABO."objet_");
define("_PREFIXE_ID_OBJET", "id_");

/* Suffixes */
define("_SUFFIXE_CSS", ".css");
define("_SUFFIXE_XML", ".xml");
define("_SUFFIXE_PHP", ".php");

/* Pages principales */
define("_PAGE_PRINCIPALE", "index");
define("_PAGE_PRINCIPALE_ADMIN", "index-admin");

/* Modes PetiLabo */
define("_MODE_NORMAL", 0);
define("_MODE_ADMIN", 31);
define("_MODE_ADMIN_PAGE", 1);
define("_MODE_ADMIN_MEDIA", 2);
define("_MODE_ADMIN_GRILLE", 4);
define("_MODE_ADMIN_MAJ_GRILLE", 5);
define("_MODE_ADMIN_OBJETS", 8);
define("_MODE_ADMIN_XML", 16);
define("_MODE_ADMIN_PAR_DEFAUT", _MODE_ADMIN_PAGE);

/* Sources des fichiers XML */
define("_NOM_SOURCE_GLOBAL", "global");
define("_NOM_SOURCE_THEME", "theme");
define("_NOM_SOURCE_LOCAL", "local");

/* Thèmes et styles par défaut */
define("_NOM_THEME_DEFAUT", "standard");
define("_NOM_STYLE_DEFAUT", "defaut");

/* Gestion des erreurs */
error_reporting(E_ALL);

/* Récupération du nom de la page (sauf requête AJAX) */
if (isset($_SERVER["PHP_SELF"])) {
	$php_self = htmlentities(trim($_SERVER["PHP_SELF"]));
	$nom_php_en_cours = basename($php_self);
	if (strstr($php_self, "/ajax/") === false) {
		if (isset($_GET["p"])) {
			$nom_get_en_cours = htmlentities(basename(trim($_GET["p"])));
			if ($nom_php_en_cours === _PAGE_PRINCIPALE._SUFFIXE_PHP) {
				if (strlen($nom_get_en_cours) == 0) {$nom_page_en_cours = _PAGE_PRINCIPALE;}
				else {$nom_page_en_cours = str_replace(_SUFFIXE_PHP, "", $nom_get_en_cours);}
			}
			else if ($nom_php_en_cours === _PAGE_PRINCIPALE_ADMIN._SUFFIXE_PHP) {
				if (strlen($nom_get_en_cours) == 0) {$nom_page_en_cours = _PAGE_PRINCIPALE_ADMIN;}
				else {$nom_page_en_cours = str_replace(_SUFFIXE_PHP, "", $nom_get_en_cours);}
			}
			else {
				die("ERREUR : Page XML introuvable");
			}
		}
		else {
			die("ERREUR : Page XML introuvable");
		}
		/* Définition des constantes pour la page courante */
		define("_PAGE_COURANTE", $nom_page_en_cours);
		define("_CHEMIN_PAGE_COURANTE", _CHEMIN_PAGES_XML.$nom_page_en_cours."/");
	}
}
else {
	die("ERREUR : Page XML introuvable");
}

/**
 * Fonction d'autoload
 */
function autochargement($nom_classe) {

	$classe = explode('_', strtolower($nom_classe), 4);

	/* Toutes les classes doivent commencer par pl3_ */
	if ($classe[0] !== 'pl3') {
		die("Erreur : la classe ".$nom_classe." porte un préfixe incorrect");
	}

	$chemin = _CHEMIN_CLASSES.$classe[1]."/";
	if ($classe[1] === "objet") {
		if (count($classe) < 4) {
			die("Erreur : la classe objet ".$nom_classe." porte sur une fiche inconnue");
		}
		$chemin .= $classe[2]."/";
	}
	$fichier = $chemin.$nom_classe.".php";
	if (file_exists($fichier)) {
		require_once($fichier);
	}
	else {
		die("ERREUR : Impossible de charger le fichier ".$fichier);
	}
}

// Activation de l'autoload
spl_autoload_register("autochargement");