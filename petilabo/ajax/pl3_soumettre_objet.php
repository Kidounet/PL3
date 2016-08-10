<?php
define("_CHEMIN_BASE_URL", "../../");
define("_CHEMIN_BASE_RESSOURCES", "../");
require_once(_CHEMIN_BASE_URL."petilabo/pl3_init.php");

/* Initialisations */
$html = "";
$ajax_objet_maj = false;
$ajax_objet_valide = pl3_ajax_init::Init_objet();

/* Traitement des paramètres */
if ($ajax_objet_valide) {
	$parametres = pl3_admin_post::Post("parametres");
	if (strlen($parametres) > 0) {
		$objet = pl3_ajax_init::Get_objet();
		parse_str($parametres, $liste_parametres);
		if (($objet != null) && (count($liste_parametres) > 0)) {
			$source_page = pl3_ajax_init::Get_source_page();
			$nom_valeur = $objet->get_nom_balise();
			foreach ($liste_parametres as $nom_parametre => $valeur_parametre) {
				/* Cas où le paramètre est un attribut */
				if (strncmp($nom_parametre, $nom_valeur, strlen($nom_valeur))) {
					$attribut_maj = $objet->set_attribut($nom_parametre, $valeur_parametre);
					$ajax_objet_maj = $ajax_objet_maj || $attribut_maj ;
				}
				/* Cas où le paramètre est la valeur */
				else {
					$type_valeur = $objet->get_type();
					/* Traitement de l'indirection */
					if ($type_valeur == pl3_outil_objet_xml::TYPE_INDIRECTION) {
						$valeur = $objet->get_valeur();
						$nom_classe = $objet->get_reference();
						$nom_fiche = $nom_classe::NOM_FICHE;
						$nom_balise = $nom_classe::NOM_BALISE;
						$objet_indirection = $source_page->chercher_liste_fiches_par_nom($nom_fiche, $nom_balise, $valeur);
						$valeur_maj = $objet_indirection->set_valeur($valeur_parametre);
					}
					else {
						$valeur_maj = $objet->set_valeur($valeur_parametre);
					}
					$ajax_objet_maj = $ajax_objet_maj || $valeur_maj;
				}
			}
			if ($ajax_objet_maj) {
				$bloc = pl3_ajax_init::Get_bloc();
				$bloc->remplacer_objet($objet);
				$source_page->enregistrer_xml();
				$html .= $objet->afficher(_MODE_ADMIN_OBJETS);
			}
		}
	}
}

/* Retour JSON de la requête AJAX */
echo json_encode(array("valide" => $ajax_objet_valide, "maj" => $ajax_objet_maj, "html" => $html));
