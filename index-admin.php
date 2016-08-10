<?php
/* Initialisations */
$memoire_avant = memory_get_usage() / 1024;
define("_CHEMIN_BASE_URL", "./");
define("_CHEMIN_BASE_FICHIER", "../");
require_once(_CHEMIN_BASE_URL."petilabo/pl3_init.php");

/* Ouverture de la session */
$id_session = pl3_admin_session::Ouvrir_session();
if (strlen($id_session) == 0) {die("ERREUR : Impossible d'ouvrir la session");}
$admin_interface = pl3_admin_interface::Get();
$mode_admin = $admin_interface->lire_mode_actuel();

/* Chargement de la page et du thème */
$source_page = pl3_outil_source_page::Get();
$source_page->charger_xml();
$source_page->generer_theme($mode_admin);

/* Récupération des éléments pour affichage */
$page = $source_page->get_page();
$page->set_mode($mode_admin);
$media_local = $source_page->get_media(_NOM_SOURCE_LOCAL);
$media_local->set_mode($mode_admin);

/* Constitution du code HTML */
$html = "";
$html .= $page->afficher_head();
$html .= $page->ouvrir_body();
$html .= $admin_interface->ecrire_barre_outils();
if ($mode_admin == _MODE_ADMIN_MEDIA) {
	$html .= $media_local->afficher(_MODE_ADMIN_MEDIA);
}
else {
	$html .= $page->ecrire_body();
}
$html .= $page->fermer_body();

/* Affichage */
echo $html;

$memoire_apres = memory_get_usage() / 1024;
printf("<br><b>Avant</b> : %.2f ko<br>",$memoire_avant);
printf("<b>Après</b> : %.2f ko<br>",$memoire_apres);
printf("<b>Mémoire utilisée</b> : %.2f ko<br>",$memoire_apres - $memoire_avant);
