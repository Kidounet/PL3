<?php

/* Classe de service pour le traitement du fichier image */

class pl3_ajax_telechargement_image {
	const EXTENSION_IMAGE_JPEG = "jpeg";
	const EXTENSION_IMAGE_JPG = "jpg";
	const EXTENSION_IMAGE_PNG = "png";
	const EXTENSION_IMAGE_GIF = "gif";

	private $src = null;
	private $nom_dest = null;
	private $dest = null;
	private $ext = null;
	private $largeur = 0;
	private $hauteur = 0;
	private $compression = 0;

	public function __construct($src, $largeur, $hauteur, $compression) {
		$this->src = $src;
		$this->largeur = $largeur;
		$this->hauteur = $hauteur;
		$this->compression = $compression;
	}

	public function set_destination($nom_origine) {
		$this->nom_dest = $this->nettoyer_nom_fichier($nom_origine);
		$this->dest = _CHEMIN_XML."images/".$this->nom_dest;
		$this->ext = $this->get_extension_fichier($this->nom_dest);
	}

	public function move_and_resize_uploaded_file(&$message) {
		$ret = false;
		if ((@file_exists($this->src)) && is_uploaded_file($this->src)){
			if (@file_exists($this->dest)) {
				$message = "ERREUR : Un fichier portant le même nom existe déjà.";
			}
			else {
				if ($this->is_extension_media($this->ext)) {
					$ret = $this->resize_uploaded_file();
					if (!($ret)) {$message = "ERREUR : Impossible d'installer le fichier téléchargé.";}
				}
				else {
					$message = "ERREUR : Les fichiers au format ".$this->ext." ne peuvent pas être gérés comme des médias.";
				}
			}
			@unlink($this->src);
		}
		else {
			$message = "ERREUR : Le fichier téléchargé est inaccessible";
		}
		return $ret;
	}

	public function get_image_size() {
		if (@file_exists($this->dest)) {return (@getimagesize($this->dest));}
		else {return array(0, 0);}
	}

	public function effacer() {
		if (@file_exists($this->dest)) {return (@unlink($this->dest));}
	}

	public function get_nom_image() {return $this->nom_dest;}

	private function resize_uploaded_file() {
		$ret = true;
		list($largeur_image, $hauteur_image) = getimagesize($this->src);
		// Taille standard
		if (($this->largeur > 0) && ($this->hauteur > 0)) {
			list($delta_l, $delta_h) = $this->calculer_delta($largeur_image, $hauteur_image);
			$largeur = $this->largeur;$hauteur = $this->hauteur;
		}
		elseif (($this->largeur > 0) && ($this->hauteur <= 0)) {
			$delta_l = 0;$delta_h = 0;$largeur = $this->largeur;
			$hauteur = $hauteur_image * (float) (((float) $this->largeur) / ((float) max($largeur_image,1)));
		}
		elseif (($this->largeur <= 0) && ($this->hauteur > 0)) {
			$delta_l = 0;$delta_h = 0;$hauteur = $this->hauteur;
			$largeur = $largeur_image * (float) (((float) $this->hauteur) / ((float) max($hauteur_image,1)));
		}
		if (($this->largeur > 0) || ($this->hauteur > 0)) {
			$this->retailler($largeur_image, $hauteur_image, $largeur, $hauteur, $delta_l, $delta_h);
		}
		else {
			$ret = move_uploaded_file($this->src, $this->dest);
		}
		return $ret;
	}

	private function calculer_delta($largeur_image, $hauteur_image) {
		if ($hauteur_image == 0) {$rapport_1 = 1;}
		else {$rapport_1 = (float) (((float) $largeur_image) / ((float) $hauteur_image));}
		if ($this->hauteur == 0) {$rapport_0 = 1;}
		else {$rapport_0 = (float) (((float) $this->largeur) / ((float) $this->hauteur));}
		if ($rapport_0 > $rapport_1) {
			$delta_l = 0;
			$delta_h = (int) (($hauteur_image * $this->largeur - $largeur_image * $this->hauteur) / (2 * max($this->largeur, 1)));
		}
		elseif ($rapport_0 < $rapport_1) {
			$delta_l = (int) (($largeur_image * $this->hauteur - $hauteur_image * $this->largeur) / (2 * max($this->hauteur,1)));
			$delta_h = 0;
		}
		else {
			$delta_l = 0;
			$delta_h = 0;
		}
		return array($delta_l, $delta_h);
	}

	private function retailler($largeur_image, $hauteur_image, $nouvelle_largeur, $nouvelle_hauteur, $delta_largeur, $delta_hauteur) {
		if ($this->ext === self::EXTENSION_IMAGE_JPG) {
			$this->retailler_jpg($largeur_image, $hauteur_image, $nouvelle_largeur, $nouvelle_hauteur, $delta_largeur, $delta_hauteur);
		}
		elseif ($this->ext === self::EXTENSION_IMAGE_PNG) {
			$this->retailler_png($largeur_image, $hauteur_image, $nouvelle_largeur, $nouvelle_hauteur, $delta_largeur, $delta_hauteur);
		}
		elseif ($this->ext === self::EXTENSION_IMAGE_GIF) {
			$this->retailler_gif($largeur_image, $hauteur_image, $nouvelle_largeur, $nouvelle_hauteur, $delta_largeur, $delta_hauteur);
		}
		@unlink($this->src);
	}

	private function retailler_jpg($largeur_image, $hauteur_image, $nouvelle_largeur, $nouvelle_hauteur, $delta_largeur, $delta_hauteur) {
		$src_r = imagecreatefromjpeg($this->src);
		if ($src_r) {
			$dst_r = ImageCreateTrueColor($nouvelle_largeur, $nouvelle_hauteur);
			if ($dst_r) {
				imagecopyresampled($dst_r, $src_r,
									0, 0,
									$delta_largeur, $delta_hauteur,
									$nouvelle_largeur, $nouvelle_hauteur,
									$largeur_image - (2*$delta_largeur), $hauteur_image - (2*$delta_hauteur));
				$qualite = $this->compression;
				$ret = imagejpeg($dst_r, $this->dest, $qualite);
				imagedestroy($dst_r);
			}
			imagedestroy($src_r);
		}
	}

	private function retailler_png($largeur_image, $hauteur_image, $nouvelle_largeur, $nouvelle_hauteur, $delta_largeur, $delta_hauteur) {
		$src_r = imagecreatefrompng($this->src);
		if ($src_r) {
			$src_alpha = $this->png_has_transparency($this->src);
			$dst_r = ImageCreateTrueColor($nouvelle_largeur, $nouvelle_hauteur);
			if ($dst_r) {
				if ($src_alpha) {
					imagealphablending($dst_r, false);
					imagesavealpha($dst_r, true);
				}
				imagecopyresampled($dst_r, $src_r,
									0, 0,
									$delta_largeur, $delta_hauteur,
									$nouvelle_largeur, $nouvelle_hauteur,
									$largeur_image - (2*$delta_largeur), $hauteur_image - (2*$delta_hauteur));
				/* En cas d'image non transparente on reduit à une image avec palette (pb de taille) */
				if (!$src_alpha) {
					$tmp = ImageCreateTrueColor($nouvelle_largeur, $nouvelle_hauteur);
					ImageCopyMerge($tmp, $dst_r, 0, 0, 0, 0, $nouvelle_largeur, $nouvelle_hauteur, 100);
					ImageTrueColorToPalette($dst_r, false, 8192);
					ImageColorMatch($tmp, $dst_r);
					ImageDestroy($tmp);
				}
				$qualite = (int) ($this->compression / 10);
				$ret = imagepng($dst_r, $this->dest, $qualite);
				imagedestroy($dst_r);
			}
			imagedestroy($src_r);
		}
	}

	private function retailler_gif($largeur_image, $hauteur_image, $nouvelle_largeur, $nouvelle_hauteur, $delta_largeur, $delta_hauteur) {
		$src_r = imagecreatefromgif($this->src);
		if ($src_r) {
			$dst_r = ImageCreateTrueColor($nouvelle_largeur, $nouvelle_hauteur);
			if ($dst_r) {
				imagecopyresampled($dst_r, $src_r,
									0, 0,
									$delta_largeur, $delta_hauteur,
									$nouvelle_largeur, $nouvelle_hauteur,
									$largeur_image - (2*$delta_largeur), $hauteur_image - (2*$delta_hauteur));
				$ret = imagegif($dst_r, $this->dest);
				imagedestroy($dst_r);
			}
			imagedestroy($src_r);
		}
		return $ret;
	}

	// Grand merci à http://www.jonefox.com/ !!!
	private function png_has_transparency($filename) {
		if (strlen($filename) == 0 || !file_exists($filename)) return false;
		if (ord(file_get_contents($filename, false, null, 25, 1)) & 4) return true;
		$contents = file_get_contents($filename);
		if (stripos($contents, 'PLTE') !== false && stripos($contents, 'tRNS') !== false) return true;
		return false;
	}

	private function nettoyer_nom_fichier($nom_fichier) {
		$ret = trim(strtolower($nom_fichier));
		$ret = str_replace(" ", "-", $ret);
		$ret = str_replace(".".self::EXTENSION_IMAGE_JPEG, ".".self::EXTENSION_IMAGE_JPG, $ret);
		return $ret;
	}

	private function get_extension_fichier($fichier) {
		$ret = strtolower(pathinfo($fichier, PATHINFO_EXTENSION));
		return $ret;
	}

	private function is_extension_media($ext) {
		$ret = ($ext === self::EXTENSION_IMAGE_JPG);
		$ret = $ret || ($ext === self::EXTENSION_IMAGE_PNG);
		$ret = $ret || ($ext === self::EXTENSION_IMAGE_GIF);
		return $ret;
	}
}