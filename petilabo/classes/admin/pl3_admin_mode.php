<?php

/**
 * Classe de gestion des modes de l'interface
 */

class pl3_admin_mode {

	/* Accesseur au mode */
	public static function Lire_mode() {
		$mode = (int) pl3_admin_session::Lire_session_param("mode_admin");
		if ($mode == 0) {$mode = _MODE_ADMIN_PAR_DEFAUT; }
		return $mode;
	}

	/* Modifieur du mode */
	public static function Ecrire_mode($mode) {
		pl3_admin_session::Ecrire_session_param("mode_admin", $mode);
	}
}