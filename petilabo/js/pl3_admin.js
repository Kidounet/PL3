/*
 * JS PL3 général
 */

/* Blocage des mises en cache */
$.ajaxSetup ({cache: false});

/* ReplaceWith avec renvoi du nouvel objet */
$.fn.replaceWithPush = function(a) {
	var $a = $(a);
	this.replaceWith($a);
	return $a;
};

/* Appel AJAX pour le changement de mode d'administration */
function changer_mode_admin(mode_admin) {
	$.ajax({
		type: "POST",
		url: "../petilabo/ajax/pl3_changer_mode_admin.php",
		data: {mode_admin: mode_admin},
		dataType: "json"
	}).done(function(data) {
		var valide = data["valide"];
		if (valide) {location.reload();}
		else {alert("ERREUR : Changement de mode d'administration impossible");}
	}).fail(function() {
		alert("ERREUR : Script AJAX en échec ou introuvable");
	});
}

function calculer_coord_editeur(objet, plein_ecran) {
	/* Détection de la position en fonction du scrolling */
	var body = document.body;
	var html = document.documentElement;
	var scrollTop = window.pageYOffset || html.scrollTop || body.scrollTop || 0;
	var scrollLeft = window.pageXOffset || html.scrollLeft || body.scrollLeft || 0;
	var box = objet.get(0).getBoundingClientRect();
	var pos_haut = box.top + scrollTop;
	var pos_gauche = box.left + scrollLeft;

	/* Calcul de la position top en tenant compte de la position de la page */
	var decalage_page = parseInt($("div.page").css("margin-top").replace("px", ""));
	var hauteur = parseInt(objet.outerHeight());
	var pos_y = pos_haut + hauteur - decalage_page;

	/* Calcul de la position de l'éditeur */
	if (plein_ecran) {
		var pos_x = 9;
		var largeur = $("div.page").innerWidth() - 19;
		var style = "top:"+pos_y+"px;";
		style += "left:"+pos_x+"px;";
		style += "width:"+largeur+"px;";
	}
	else {
		var pos_x = pos_gauche + 1;
		var largeur_min = parseInt(objet.width()) + 6;

		/* Elaboration du style */
		var style = "top:"+pos_y+"px;";
		style += "left:"+pos_x+"px;";
		style += "min-width:"+largeur_min+"px;";
	}

	return style;
}

/* Déplacement (ou retaillage) d'un éditeur d'objets */
function deplacer_editeur(editeur_id) {
	var editeur = $("#"+editeur_id);
	if (editeur) {
		var plein_ecran = editeur.hasClass("editeur_objet_plein_ecran");
		var objet_id = editeur_id.replace("editeur-", "");
		var objet = $("#"+objet_id);
		if (objet) {
			var style = calculer_coord_editeur(objet, plein_ecran);
			editeur.attr("style", style);
		}
	}
}

/* Récupération du nom de la page */
function parser_page() {
	var nom_page = $("div.page").attr("name");
	return nom_page;
}

/* Initialisations */
$(document).ready(function() {
	/* Items de la barre d'outils "admin" */
	$("a.admin_item_barre_outils").click(function() {
		var item_id = $(this).attr("id");
		var mode_id = item_id.replace("admin-mode-", "");
		var mode_admin = parseInt(mode_id);
		changer_mode_admin(mode_admin);
	});

	/* Gestion des boutons de la barre d'outils dans l'éditeur d'objets */
	$("div.page").on("click", "p.editeur_objet_barre_outils a.editeur_objet_bouton_fermer", function() {
		var editeur = $(this).closest("div.editeur_objet");
		if (editeur) {editeur.remove();}
		return false;
	});
	$("div.page").on("click", "p.editeur_objet_barre_outils a.editeur_objet_bouton_agrandir", function() {
		var editeur = $(this).closest("div.editeur_objet");
		if (editeur) {
			var editeur_id = editeur.attr("id");
			var objet_id = editeur_id.replace("editeur-", "");
			var objet = $("#"+objet_id);
			if (objet.length > 0) {
				var plein_ecran = editeur.hasClass("editeur_objet_plein_ecran");
				if (plein_ecran) {
					editeur.removeClass("editeur_objet_plein_ecran");
					$(this).attr("title", "Agrandir");
					$(this).find("span").addClass("fa-expand").removeClass("fa-compress");
				}
				else {
					editeur.addClass("editeur_objet_plein_ecran");
					$(this).attr("title", "Réduire");
					$(this).find("span").removeClass("fa-expand").addClass("fa-compress");
				}
				var style = calculer_coord_editeur(objet, !plein_ecran);
				editeur.attr("style", style);
			}
		}
		return false;
	});

	/* Gestion du survol d'un bouton */
	$("div.page").on("mouseover", "button", function() {
		$(this).css("cursor", "pointer");
	});

	/* Bouton "annuler" dans les éditeurs d'objets */
	$("div.page").on("click", "form.editeur_formulaire button.annuler_formulaire", function() {
		var form_id = $(this).attr("id");
		var html_id = form_id.replace("annuler-", "editeur-");
		$("#"+html_id).remove();
		return false;
	});

	/* Gestion des éditeurs d'objets lors du retaillage de la fenêtre */
	$(window).on("resize", function() {
		$("div.page div.editeur_objet").each(function() {
			var editeur_id = $(this).attr("id");
			deplacer_editeur(editeur_id);
		});
	});
});
