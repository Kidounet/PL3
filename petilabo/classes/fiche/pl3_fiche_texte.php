<?php

/**
 * Classe de gestion des fiches texte.xml
 */

class pl3_fiche_texte extends pl3_outil_fiche_xml {
	const NOM_FICHE = "texte";

	/* objets fils */
	const OBJETS = array(
		"pl3_objet_texte_texte",
		"pl3_objet_texte_texte_riche");
}