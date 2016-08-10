<?php

/**
 * Classe de gestion des fiches media.xml
 */

class pl3_fiche_media extends pl3_outil_fiche_xml {
	const NOM_FICHE = "media";

	/* objets fils */
	const OBJETS = array(
		"pl3_objet_media_image",
		"pl3_objet_media_galerie");

	/* Affichage */
	public function afficher($mode) {
		$ret = "";
		$source_page = pl3_outil_source_page::Get();
		$liste_tailles = $source_page->chercher_liste_noms_par_fiche("theme", "pl3_objet_theme_taille_image");
		$liste_medias_par_taille = array();
		foreach($liste_tailles as $id_taille => $nom_taille) {
			$liste_medias_par_taille[$nom_taille] = array("id" => 1 + $id_taille, "medias" => array());
		}

		/* Classement des images selon les tailles */
		$theme = $source_page->get_theme();
		$liste_medias = $this->liste_objets;
		foreach($liste_medias as $media) {
			if ($media->get_nom_balise() == "image") {
				$nom_taille = $media->get_valeur_taille_standard();
				if (in_array($nom_taille, $liste_tailles)) {
					$liste_medias_par_taille[$nom_taille]["medias"][] = $media;
				}
			}
		}

		$classe = "page page_media".((($this->mode & _MODE_ADMIN) > 0)?" page_mode_admin":"");
		$ret .= "<div class=\"".$classe."\" name=\""._PAGE_COURANTE."\">\n";
		/* Liste des images taille par taille */
		foreach($liste_tailles as $nom_taille) {
			$info_media = $liste_medias_par_taille[$nom_taille];
			$id_taille = $info_media["id"];
			$liste_medias = $info_media["medias"];
			$taille = $theme->chercher_objet_classe_par_attribut("pl3_objet_theme_taille_image", "nom", $nom_taille);
			$largeur = $taille->get_valeur_largeur();
			if (((int) $largeur) == 0) {$largeur = "...";}
			$hauteur = $taille->get_valeur_hauteur();
			if (((int) $hauteur) == 0) {$hauteur = "...";}
			$compression = (int) $taille->get_valeur_compression();
			$ret .= "<h2 id=\"titre-taille-".$id_taille."\" data-largeur=\"".$largeur."\" data-hauteur=\"".$hauteur."\" data-compression=\"".$compression."\" >".$nom_taille." ";
			$ret .= "<span class=\"indication_taille_image\">(".$largeur."x".$hauteur.")</span>";
			$ret .= "</h2>\n";
			$ret .= "<div id=\"taille-".$id_taille."\" class=\"taille_container\">\n";
			foreach($liste_medias as $media) {$ret .= $this->afficher_vignette_media($media);}
			$ret .= self::Afficher_ajout_media($id_taille, $nom_taille);
			$ret .= "<div class=\"clearfix\"></div>\n";
			$ret .= "</div>\n";
		}
		$ret .= "</div>\n";
		return $ret;
	}

	/* Appel interne et via AJAX */
	public function afficher_vignette_media(&$media) {
		$ret = "";
		$nom = $media->get_attribut_nom();
		$ret .= "<div class=\"vignette_container\">\n";
		$ret .= "<a id=\"media-".$media->get_id()."\" class=\"vignette_apercu_lien\" href=\"#\" title=\"Editer l'image ".$nom."\">";
		$ret .= $media->afficher($this->mode);
		$ret .= "</a>";
		$ret .= "<p class=\"vignette_legende_image\">".$nom."</p>";
		$ret .= "</div>\n";
		return $ret;
	}

	/* Appel interne et via AJAX */
	public static function Afficher_ajout_media($id_taille, $nom_taille) {
		$ret = "";
		$ret .= "<div class=\"vignette_container\">";
		$ret .= "<a id=\"ajout-".$id_taille."\" name=\"".$nom_taille."\" class=\"fa fa-plus-circle vignette_plus\" href=\"#\" title=\"Ajouter une image au format ".strtolower($nom_taille)."\"></a>";
		$ret .= "<input type=\"file\" id=\"input-".$id_taille."\" style=\"display:none;\" name=\"img-".$id_taille."\" value=\"\"/>\n";
		$ret .= "</div>\n";
		return $ret;
	}

	/* Appel via AJAX */
	public function instancier_image($fichier, $taille, $largeur, $hauteur) {
		$fichier_sans_prefixe = substr($fichier, 0, strpos($fichier,  "."));
		$nom_image = htmlspecialchars($fichier_sans_prefixe, ENT_QUOTES, "UTF-8");
		$doublon = $this->chercher_objet_classe_par_attribut("pl3_objet_media_image", "nom", $nom_image);
		if (is_null($doublon)) {
			$objet = parent::instancier_nouveau("pl3_objet_media_image");
			if ($objet) {
				$objet->set_valeur_fichier($fichier);
				$objet->set_valeur_taille_standard($taille);
				$objet->set_valeur_largeur_reelle($largeur);
				$objet->set_valeur_hauteur_reelle($hauteur);
				$fichier_sans_prefixe = substr($fichier, 0, strpos($fichier,  "."));
				$nom_image = htmlspecialchars($fichier_sans_prefixe, ENT_QUOTES, "UTF-8");
				$objet->set_attribut_nom($nom_image);
			}
		}
		else {$objet = null;}
		return $objet;
	}

	/* Appel via AJAX */
	public function retirer_image($image_id) {
		$id_cpt = 1;
		$id_del = -1;
		foreach ($this->liste_objets as $id=>$objet) {
			if ($objet->get_nom_balise() == "image" && $objet->get_id() == $image_id) {
				$id_del = $id;
				$objet->detruire();
				unset($objet);
			}
			else {
				$id_cpt += 1;
				$objet->set_id($id_cpt);
			}
		}
		if ($id_del >= 0) unset($this->liste_objets[$id_del]);
	}

}