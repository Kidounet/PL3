<?php

/**
 * Classe de gestion de l'éditeur d'images
 */

class pl3_admin_editeur_image extends pl3_admin_editeur {

	/* Fonctions d'édition */
	public function editer() {
		$ret = "<form id=\"formulaire-".$this->id_objet."\" class=\"editeur_formulaire\" method=\"post\">\n";
		$ret .= $this->editer_attributs();
		$ret .= $this->editer_valeurs();
		$ret .= "<p class=\"boutons_formulaire\">\n";
		$ret .= "<button id=\"soumettre-media-".$this->id_objet."\" class=\"soumettre_formulaire\" type=\"submit\" value=\"ok\" title=\"Enregistrer et fermer\">";
		$ret .= "<span class=\"fa fa-check editeur_formulaire_icone_bouton\"></span>OK";
		$ret .= "</button>";
		$ret .= "<button id=\"annuler-media-".$this->id_objet."\" class=\"annuler_formulaire\" value=\"annuler\" title=\"Annuler et fermer\">";
		$ret .= "<span class=\"fa fa-times editeur_formulaire_icone_bouton\"></span>Annuler";
		$ret .= "</button>";
		$ret .= "<button id=\"supprimer-media-".$this->id_objet."\" class=\"supprimer_formulaire\" value=\"supprimer\" title=\"Supprimer cette image\">";
		$ret .= "<span class=\"fa fa-trash editeur_formulaire_icone_bouton\"></span>Supprimer";
		$ret .= "</button>";
		$ret .= "</p>\n";
		$ret .= "</form>\n";
		return $ret;
	}

	private function editer_valeurs() {
		$ret = "";
		$ret .= "<p class=\"editeur_objet_titre_valeur\">Valeurs</p>\n";
		$balise_alt = pl3_objet_media_image_alt::NOM_BALISE;
		$valeur_alt = $this->objet->get_objet_fils_valeur(pl3_objet_media_image_alt::NOM_BALISE);
		$ret .= $this->afficher_champ_form($balise_alt, $valeur_alt);
		return $ret;
	}

	private function editer_attributs() {
		$ret = "";
		$liste_attributs = $this->objet->get_liste_attributs();
		if (count($liste_attributs) > 0) {
			$ret .= "<p class=\"editeur_objet_titre_attributs\">Attributs&nbsp;:</p>\n";
			foreach ($liste_attributs as $attribut) {
				$valeur = $this->attribut_to_valeur($attribut);
				$ret .= $this->afficher_champ_form($attribut, $valeur);
			}
		}
		return $ret;
	}
}
