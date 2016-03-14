<?php

/**
 * Classe de gestion du parser XML
 */

class pl3_outil_parser_xml {

	public static function Parser_balise($fiche, $id, $nom_balise, &$noeud) {
		$ret = array();
		if ($noeud != null) {
			$nom_classe = _PREFIXE_OBJET.$fiche."_".$nom_balise;
			$reflection = new ReflectionClass($nom_classe);
			$balise = $reflection->getConstant("NOM_BALISE");
			$attributs = $reflection->getStaticPropertyValue("Noms_attributs");

			$liste = $noeud->getElementsByTagName($balise);
			foreach($liste as $element) {
				$instance = $reflection->newInstanceArgs(array($fiche, $id, &$element));	
				foreach($attributs as $attribut) {
					$avec_attribut = $element->hasAttribute($attribut);
					if ($avec_attribut) {
						$valeur_attribut = $element->getAttribute($attribut);
						$instance->set_attribut($attribut, $valeur_attribut);
					}
				}
				$ret[] = $instance;
			}
		}
		return $ret;
	}
	
	public static function Parser_balise_fille($fiche, $id, $nom_classe, $nom_balise, &$noeud) {
		$ret = array();
		if ($noeud != null) {
			$nom_classe = $nom_classe."_".$nom_balise;
			$reflection = new ReflectionClass($nom_classe);
			$balise = $reflection->getConstant("NOM_BALISE");
			$attributs = $reflection->getStaticPropertyValue("Noms_attributs");

			$liste = $noeud->getElementsByTagName($balise);
			foreach($liste as $element) {
				$instance = $reflection->newInstanceArgs(array($fiche, $id, &$element));	
				
				/* Traitement des attributs */
				foreach($attributs as $attribut) {
					$avec_attribut = $element->hasAttribute($attribut);
					if ($avec_attribut) {
						$valeur_attribut = $element->getAttribute($attribut);
						$instance->set_attribut($attribut, $valeur_attribut);
					}
				}
				
				/* Traitement de la valeur si la balise doit en avoir une */
				$balise_avec_valeur = $instance->avec_valeur();
				if ($balise_avec_valeur) {
					$valeur = $element->nodeValue;
					$instance->set_valeur($valeur);
				}
					
				$ret[] = $instance;
			}
		}
		return $ret;
	}
	
	public static function Parser_toute_balise($fiche, $id, &$noeud) {
		$ret = array();
		if ($noeud != null) {
			$liste_objets = $noeud->childNodes;
			foreach ($liste_objets as $objet) {
				if ($objet->nodeType != XML_ELEMENT_NODE) {continue;}
				$nom_balise = $objet->nodeName;
				$nom_classe = _PREFIXE_OBJET.$fiche."_".$nom_balise;
				$nom_fichier = _CHEMIN_OBJET.$fiche."/".$nom_classe.".php";
				/* On teste le fichier et non la classe car l'échec de l'autoload provoque un die */
				$fichier_existe = @file_exists($nom_fichier);
				if ($fichier_existe) {
					$reflection = new ReflectionClass($nom_classe);
					$instance = $reflection->newInstanceArgs(array($fiche, $id, &$objet));
					/* Traitement des attributs */
					$attributs = $reflection->getStaticPropertyValue("Noms_attributs");
					foreach($attributs as $attribut) {
						$avec_attribut = $objet->hasAttribute($attribut);
						if ($avec_attribut) {
							$valeur_attribut = $objet->getAttribute($attribut);
							$instance->set_attribut($attribut, $valeur_attribut);
						}
					}
					/* Traitement de la valeur si la balise doit en avoir une */
					$balise_avec_valeur = $instance->avec_valeur();
					if ($balise_avec_valeur) {
						$valeur = $objet->nodeValue;
						$instance->set_valeur($valeur);
					}
					$ret[] = $instance;
				}
				else {
					echo "L'objet ".$nom_balise." n'existe pas.<br>\n";
				}
			}
		}
		return $ret;
	}
}