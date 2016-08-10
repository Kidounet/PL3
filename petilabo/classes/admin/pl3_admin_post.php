<?php

/**
 * Classe de gestion des paramètres post
 */

class pl3_admin_post {
	/* Fonctions d'accès aux paramètres post */
	public static function Post($nom_param) {
		$ret = null;
		if (strlen($nom_param) > 0) {
			if (isset($_POST[$nom_param])) {
				$param = $_POST[$nom_param];
				if (strlen($param) > 0) {
					$ret = self::Nettoyer_param($param);
				}
			}
		}
		return $ret;
	}
	public static function Array_post($nom_param) {
		$ret = array();
		if (strlen($nom_param) > 0) {
			if (isset($_POST[$nom_param])) {
				$array = $_POST[$nom_param];
				foreach ($array as $elem) {
					$ret[] = self::Nettoyer_param($elem);
				}
			}
		}
		return $ret;
	}
	private static function Nettoyer_param($str) {
		if (!is_null($str)) {
			$str = str_replace("\0", '', $str);
			if (get_magic_quotes_gpc()) {$str = stripslashes($str);}
			$str = trim($str);
		}
		return $str;
	}
}
