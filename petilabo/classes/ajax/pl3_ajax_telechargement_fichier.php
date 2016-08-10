<?php

/* Classe de service pour le traitement du $_FILES en post */

class pl3_ajax_telechargement_fichier {
	private $file_post = array();
	private $file_tmp_name = null;

	public function __construct($file_post) {
		$this->file_post = $file_post;
		$this->file_tmp_name = $file_post["tmp_name"];
	}

	public function controle_post(&$message) {
		$ret_chargement = $this->file_post["error"];
		if ($ret_chargement == UPLOAD_ERR_OK) {
			return true;
		}
		else {
			$message = $this->traduire_erreur_upload($ret_chargement);
			return false;
		}
	}

	public function get_tmp_name() {return $this->file_tmp_name;}

	public function effacer() {
		if (@file_exists($this->file_tmp_name)) {return (@unlink($this->file_tmp_name));}
	}

	private function traduire_erreur_upload($erreur) {
		$ret = "ERREUR : ";
		switch($erreur) {
			case UPLOAD_ERR_INI_SIZE:
				$taille_autorisee = ini_get("upload_max_filesize");
				$ret .= "Le fichier sélectionné dépasse la taille maximum (".$taille_autorisee.")";
				break;
			case UPLOAD_ERR_PARTIAL:
				$ret .= "Le fichier n'a pu être que partiellement téléchargé.";
				break;
			case UPLOAD_ERR_NO_FILE:
				$ret .= "Aucun fichier n'a été téléchargé.";
				break;
			case UPLOAD_ERR_NO_TMP_DIR:
				$ret .= "Le dossier temporaire de téléchargement n'a pas été trouvé.";
				break;
			case UPLOAD_ERR_CANT_WRITE:
				$ret .= "Un problème est survenu lors de l'écriture du fichier.";
				break;
			default:
				$ret .= "Le fichier n'a pas pu être téléchargé pour une raison inconnue.";
		}
		return $ret;
	}
}