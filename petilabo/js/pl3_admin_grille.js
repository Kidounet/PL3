/*
 * JS PL3 mode administration objets
 */

/* Appel AJAX pour soumission d'un éditeur de contenu */
function soumettre_contenu(nom_page, contenu_id, parametres) {
	$.ajax({
		type: "POST",
		url: "../petilabo/ajax/pl3_soumettre_contenu.php",
		data: {nom_page: nom_page, contenu_id: contenu_id, parametres: parametres},
		dataType: "json"
	}).done(function(data) {
		var valide = data["valide"];
		if (valide) {
			var maj = data["maj"];
			if (maj) {
				var html = data["html"];
				var contenu = $("#contenu-"+contenu_id);
				contenu.closest(".contenu_flex").replaceWith(html);
				appliquer_sortable_contenu("#contenu-"+contenu_id);
			}
			$("#editeur-contenu-"+contenu_id).remove();
		}
		else {
			alert("ERREUR : Origine du contenu introuvable");
		}
	}).fail(function() {
		alert("ERREUR : Script AJAX en échec ou introuvable");
	});
}

function ajouter_contenu(nom_page) {
	$.ajax({
		type: "POST",
		url: "../petilabo/ajax/pl3_ajouter_contenu.php",
		data: {nom_page: nom_page},
		dataType: "json"
	}).done(function(data) {
		var valide = data["valide"];
		if (valide) {
			var html = data["html"];
			$(".contenu_ajout").before(html);
		}
		else {
			alert("NOK");
		}
	});
}

function editer_contenu(nom_page, contenu_id) {
	var editeur_id = "editeur-contenu-"+contenu_id;
	var editeur = $("#"+editeur_id);
	if (editeur.length > 0) {
		alert("ERREUR : L'éditeur est déjà ouvert pour ce contenu !");
		return;
	}
	$.ajax({
		type: "POST",
		url: "../petilabo/ajax/pl3_editer_contenu.php",
		data: {nom_page: nom_page, contenu_id: contenu_id},
		dataType: "json"
	}).done(function(data) {
		var valide = data["valide"];
		if (valide) {
			var html = data["html"];
			afficher_editeur("contenu", contenu_id, html);
		}
		else {
			alert("ERREUR : Origine de l'objet éditable introuvable");
		}
	}).fail(function() {
		alert("ERREUR : Script AJAX en échec ou introuvable");
	});
}

function ajouter_bloc(nom_page, contenu_id) {
	$.ajax({
		type: "POST",
		url: "../petilabo/ajax/pl3_ajouter_bloc.php",
		data: {nom_page: nom_page, contenu_id: contenu_id},
		dataType: "json"
	}).done(function(data) {
		var valide = data["valide"];
		if (valide) {
			var html = data["html"];
			$("#contenu-"+contenu_id).replaceWith(html);
			appliquer_sortable_contenu("#contenu-"+contenu_id);
		}
		else {
			alert("NOK");
		}
	});
}

function editer_bloc(nom_page, contenu_id, bloc_id) {
	var editeur_bloc_id = contenu_id+"-"+bloc_id;
	var editeur_id = "editeur-bloc-"+editeur_bloc_id;
	var editeur = $("#"+editeur_id);
	if (editeur.length > 0) {
		alert("ERREUR : L'éditeur est déjà ouvert pour ce bloc !");
		return;
	}
	$.ajax({
		type: "POST",
		url: "../petilabo/ajax/pl3_editer_bloc.php",
		data: {nom_page: nom_page, bloc_id: editeur_bloc_id},
		dataType: "json"
	}).done(function(data) {
		var valide = data["valide"];
		if (valide) {
			var html = data["html"];
			afficher_editeur("bloc", editeur_bloc_id, html);
		}
		else {
			alert("ERREUR : Origine de l'objet éditable introuvable");
		}
	}).fail(function() {
		alert("ERREUR : Script AJAX en échec ou introuvable");
	});
}

/* Appel AJAX pour soumission d'un éditeur de bloc */
function soumettre_bloc(nom_page, bloc_id, parametres) {
	$.ajax({
		type: "POST",
		url: "../petilabo/ajax/pl3_soumettre_bloc.php",
		data: {nom_page: nom_page, bloc_id: bloc_id, parametres: parametres},
		dataType: "json"
	}).done(function(data) {
		var valide = data["valide"];
		if (valide) {
			var maj = data["maj"];
			if (maj) {
				var html = data["html"];
				var bloc = $("#bloc-"+bloc_id);
				bloc.parent().replaceWith(html);
			}
			$("#editeur-bloc-"+bloc_id).remove();
		}
		else {
			alert("ERREUR : Origine du bloc introuvable");
		}
	}).fail(function() {
		alert("ERREUR : Script AJAX en échec ou introuvable");
	});
}

/* Affichage du code attaché à l'éditeur d'image */
function afficher_editeur(elem_nom, elem_id, html) {
	var elem = $("#"+elem_nom+"-"+elem_id);
	if (elem.length > 0) {
		/* Constitution de l'éditeur */
		var style = calculer_coord_editeur(elem, false);
		var div_id = "editeur-"+elem_nom+"-"+elem_id;
		var div = "<div id=\""+div_id+"\" class=\"editeur_objet\" style=\""+style+"\" >";
		div += "<p class=\"editeur_objet_barre_outils\">";
		div += "<a class=\"editeur_objet_bouton_agrandir\" href=\"#\" title=\"Agrandir\"><span class=\"fa fa-expand\"></span></a>";
		div += "<a class=\"editeur_objet_bouton_fermer\" href=\"#\" title=\"Fermer\"><span class=\"fa fa-times\"></span></a>";
		div += "</p>";
		div += html;
		div += "</div>";

		/* Affichage de l'éditeur */
		$("div.page").append(div);
	}
}

/* Application de plugins */
function appliquer_sortable_contenu(selecteur) {
	$(selecteur).sortable({
		items: ".bloc_grille",
		placeholder: "deplaceur_bloc",
		update: function() {
			/* Gestion du déplacement d'un objet */
		},
		start: function (e, ui) {
			var elem = $(ui.item);
			var hauteur = elem.height();
			var largeur = elem.width();
			ui.placeholder.height(hauteur).width(largeur);
		},
		opacity: 0.7
	});
	$(selecteur).disableSelection();
}

/* Initialisations */
$(document).ready(function() {
	/* Gestion de la bordure au survol d'une légende de bloc */
	$("div.page").on("mouseenter", ".bloc_legende_nom", function() {
		$(this).addClass("bloc_legende_survol");
	});
	$("div.page").on("mouseleave", ".bloc_legende_nom", function() {
		$(this).removeClass("bloc_legende_survol");
	});

	/* Gestion du clic sur un bloc */
	$("div.page").on("click", ".bloc_grille", function() {
		var bloc_attr_id = $(this).attr("id");
		var html_id = bloc_attr_id.replace("bloc-", "");
		var pos_separateur = html_id.indexOf("-");
		if (pos_separateur >= 0) {
			var nom_page = parser_page();
			var contenu_id = parseInt(html_id.substr(0, pos_separateur));
			var bloc_id = parseInt(html_id.substr(1 + pos_separateur));
			if ((contenu_id > 0) && (bloc_id > 0)) {
				editer_bloc(nom_page, contenu_id, bloc_id);
			}
		}
	});

	/* Gestion du bouton pour l'édition de contenu */
	$("div.page").on("click", "p.contenu_poignee_edit a", function() {
		var contenu_attr_id = $(this).parent().attr("id");
		var contenu_id = parseInt(contenu_attr_id.replace("poignee-contenu-", ""));
		var nom_page = parser_page();
		editer_contenu(nom_page, contenu_id);
		return false;
	});

	/* Gestion du bouton pour l'ajout de contenu */
	$("div.page").on("click", "p.contenu_poignee_ajout a", function() {
		var nom_page = parser_page();
		ajouter_contenu(nom_page);
		return false;
	});

	/* Gestion des boutons pour l'ajout de bloc */
	$("div.page").on("click", "p.bloc_poignee_ajout a", function() {
		var bloc_attr_id = $(this).parent().attr("id");
		var contenu_id = bloc_attr_id.replace("poignee-bloc-", "");
		var nom_page = parser_page();
		ajouter_bloc(nom_page, contenu_id);
		return false;
	});

	/* Bouton "soumettre" dans les éditeurs de contenu */
	$("div.page").on("submit", "form.editeur_type_contenu", function() {
		var form_id = $(this).attr("id");
		var contenu_id = form_id.replace("formulaire-contenu-", "");
		var nom_page = parser_page();
		var parametres = $(this).serialize();
		soumettre_contenu(nom_page, contenu_id, parametres);
		return false;
	});

	/* Bouton "soumettre" dans les éditeurs de bloc */
	$("div.page").on("submit", "form.editeur_type_bloc", function() {
		var form_id = $(this).attr("id");
		var bloc_id = form_id.replace("formulaire-bloc-", "");
		var nom_page = parser_page();
		var parametres = $(this).serialize();
		soumettre_bloc(nom_page, bloc_id, parametres);
		return false;
	});

	/* Possibilité de changer l'ordre des éléments */
	$(".page").sortable({
		items: ".contenu_flex",
		handle: ".contenu_poignee_edit",
		placeholder: "deplaceur_contenu",
		update: function() {
			/* Gestion du déplacement d'un objet */
		},
		start: function (e, ui) {
			ui.placeholder.height(ui.item.children().height());
		},
		opacity: 0.7
	});
	$(".page").disableSelection();
	appliquer_sortable_contenu(".contenu_grille");
});