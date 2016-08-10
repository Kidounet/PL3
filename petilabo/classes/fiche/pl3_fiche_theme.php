<?php

/**
 * Classe de gestion des fiches theme.xml
 */

class pl3_fiche_theme extends pl3_outil_fiche_xml {
	const NOM_FICHE = "theme";

	/* objets fils */
	const OBJETS = array(
		"pl3_objet_theme_taille_image",
		"pl3_objet_theme_style_page",
		"pl3_objet_theme_style_contenu",
		"pl3_objet_theme_style_bloc",
		"pl3_objet_theme_style_texte",
		"pl3_objet_theme_style_survol");
}