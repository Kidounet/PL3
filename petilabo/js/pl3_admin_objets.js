/*
 * JS PL3 mode administration objets
 */

/* Appel AJAX pour ouverture d'un éditeur d'objet */
function editer_objet(nom_page, balise_id, nom_balise) {
	var editeur_id = "editeur-"+nom_balise+"-"+balise_id;
	var editeur = $("#"+editeur_id);
	if (editeur.length > 0) {
		alert("ERREUR : L'éditeur est déjà ouvert pour cet objet !");
		return;
	}
	$.ajax({
		type: "POST",
		url: "../petilabo/ajax/pl3_editer_objet.php",
		data: {nom_page: nom_page, balise_id: balise_id, nom_balise: nom_balise},
		dataType: "json"
	}).done(function(data) {
		var valide = data["valide"];
		if (valide) {
			var html = data["html"];
			afficher_editeur(balise_id, nom_balise, html);
		}
		else {
			alert("ERREUR : Origine de l'objet éditable introuvable");
		}
	}).fail(function() {
		alert("ERREUR : Script AJAX en échec ou introuvable");
	});
}

/* Appel AJAX pour soumission d'un éditeur d'objet */
function soumettre_objet(nom_page, balise_id, nom_balise, parametres) {
	var editeur_id = "editeur-"+nom_balise+"-"+balise_id;
	var liste_trumbowyg = $("#"+editeur_id).find(".trumbowyg-editor");
	if (liste_trumbowyg.length > 0) {
		var elem_trumbowyg = liste_trumbowyg.first();
		valeur_trumbowyg = elem_trumbowyg.html();
		if (valeur_trumbowyg.length > 0) {
			parametres += (parametres.length > 0)?"&":"";
			parametres += "valeur="+encodeURI(valeur_trumbowyg);
		}
	}
	$.ajax({
		type: "POST",
		url: "../petilabo/ajax/pl3_soumettre_objet.php",
		data: {nom_page: nom_page, balise_id: balise_id, nom_balise: nom_balise, parametres: parametres},
		dataType: "json"
	}).done(function(data) {
		var valide = data["valide"];
		if (valide) {
			var maj = data["maj"];
			if (maj) {
				var html = data["html"];
				var balise = $("#"+nom_balise+"-"+balise_id);
				if (balise) {
					var parent_balise = balise.parent();
					if (parent_balise) {
						parent_balise.replaceWith(html);
						parent_balise.addClass("ui-sortable-handle");
					}
				}
			}
			$("#editeur-"+nom_balise+"-"+balise_id).remove();
		}
		else {
			alert("ERREUR : Origine de l'objet éditable introuvable");
		}
	}).fail(function() {
		alert("ERREUR : Script AJAX en échec ou introuvable");
	});
}

/* Appel AJAX pour déplacement d'un objet dans un bloc */
function deplacer_objet(nom_page, bloc_id, tab_ordre) {
	$.ajax({
		type: "POST",
		url: "../petilabo/ajax/pl3_deplacer_objet.php",
		data: {nom_page: nom_page, bloc_id: bloc_id, tab_ordre: tab_ordre},
		dataType: "json"
	}).done(function(data) {
		var valide = data["valide"];
		if (valide) {
			var re_id = 1;
			var bloc = $("#bloc-"+bloc_id);
			/* Renumérotation des id des éléments du bloc */
			bloc.children("div").children("*[id]").each(function() {
				var html_id = $(this).attr("id");
				var parsing_objet_id = parser_html_id(html_id);
				var erreur_parsing = parsing_objet_id["erreur"];
				if (!erreur_parsing) {
					var nom_balise = parsing_objet_id["nom_balise"];
					var re_html_id = nom_balise+"-"+bloc_id+"-"+re_id;
					$(this).attr("id", re_html_id);
					re_id += 1;
				}
			});
		}
		else {
			alert("ERREUR : Le déplacement d'objet n'a pas pu être enregistré");
		}
	}).fail(function() {
		alert("ERREUR : Script AJAX en échec ou introuvable");
	});
}

/* Appel AJAX pour ajout d'un objet dans un bloc */
function ajouter_objet(nom_page, bloc_id, classe_objet) {
	$.ajax({
		type: "POST",
		url: "../petilabo/ajax/pl3_ajouter_objet.php",
		data: {nom_page: nom_page, bloc_id: bloc_id, classe_objet: classe_objet},
		dataType: "json"
	}).done(function(data) {
		var valide = data["valide"];
		if (valide) {
			var html = data["html"];
			var bloc = $("#bloc-"+bloc_id);
			bloc.replaceWith(html);
			appliquer_sortable("#bloc-"+bloc_id);
		}
		else {
			alert("ERREUR : L'ajout d'un objet a échoué");
		}
	}).fail(function() {
		alert("ERREUR : Script AJAX en échec ou introuvable");
	});
}

/* Appel AJAX pour soumission d'un éditeur d'objet */
function supprimer_objet(nom_page, balise_id, nom_balise) {
	$.ajax({
		type: "POST",
		url: "../petilabo/ajax/pl3_supprimer_objet.php",
		data: {nom_page: nom_page, balise_id: balise_id, nom_balise: nom_balise},
		dataType: "json"
	}).done(function(data) {
		var valide = data["valide"];
		if (valide) {
			var html = data["html"];
			var bloc_id = data["bloc_id"];
			var bloc = $("#bloc-"+bloc_id);
			bloc.replaceWith(html);
			appliquer_sortable("#bloc-"+bloc_id);
			$("#editeur-"+nom_balise+"-"+balise_id).remove();
		}
		else {
			alert("NOK");
		}
	});
}

/* Affichage du code attaché à l'objet */
function afficher_editeur(balise_id, nom_balise, html) {
	var objet_id = nom_balise+"-"+balise_id;
	var objet = $("#"+objet_id);
	if (objet) {
		/* Constitution de l'éditeur */
		var style = calculer_coord_editeur(objet, false);
		var div_id = "editeur-"+objet_id;
		var div = "<div id=\""+div_id+"\" class=\"editeur_objet\" style=\""+style+"\" >";
		div += "<p class=\"editeur_objet_barre_outils\">";
		div += "<a class=\"editeur_objet_bouton_agrandir\" href=\"#\" title=\"Agrandir\"><span class=\"fa fa-expand\"></span></a>";
		div += "<a class=\"editeur_objet_bouton_fermer\" href=\"#\" title=\"Fermer\"><span class=\"fa fa-times\"></span></a>";
		div += "</p>";
		div += html;
		div += "</div>";

		/* Affichage de l'éditeur */
		$("div.page").append(div);

		/* Déclenchement de trumbowyg */
		$("#"+div_id).find(".editeur_trumbowyg").each(function() {
			var elem_id = $(this).attr("id");
			appliquer_editable("#"+elem_id);
		});
	}
}

function parser_html_id(html_id) {
	if (html_id.length > 0) {
		var pos_separateur = html_id.indexOf("-");
		if (pos_separateur >= 0) {
			var nom_page = parser_page();
			var nom_balise = html_id.substr(0, pos_separateur);
			var balise_id = html_id.substr(1 + pos_separateur);
			return {"erreur": false, "nom_page": nom_page, "nom_balise": nom_balise, "balise_id": balise_id};
		}
	}
	return {"erreur": true};
}

/* Application de plugins */
function appliquer_sortable(selecteur) {
	$(selecteur).sortable({
		placeholder: 'deplaceur_objet',
		items: "div[class^='container_']",
		update: function() {
			/* Gestion du déplacement d'un objet */
			var bloc_attr_id = $(this).attr("id");
			var bloc_id = bloc_attr_id.replace("bloc-", "");
			var tab_ordre = [];
			$(this).find("div[class^='container_'] > *[id]").each(function() {
				var elem_id = $(this).attr("id");
				var editeur_id = "editeur-"+elem_id;
				deplacer_editeur(editeur_id);
				var parsing_objet_id = parser_html_id(elem_id);
				var erreur_parsing = parsing_objet_id["erreur"];
				if (!erreur_parsing) {
					var balise_id = parsing_objet_id["balise_id"];
					var ordre_id = balise_id.replace(bloc_id+"-", "");
					tab_ordre.push(ordre_id);
				}
			});
			if (tab_ordre.length > 0) {
				var nom_page = parser_page();
				deplacer_objet(nom_page, bloc_id, tab_ordre);
			}
		},
		start: function (e, ui) {ui.placeholder.height(ui.item.children().height());},
		opacity: 0.7
	});
	$(selecteur).disableSelection();
}

function appliquer_editable(selecteur, langue) {
	if (langue === undefined) {langue = "fr";}
	$(selecteur).trumbowyg({
		lang: langue,
		fullscreenable: false,
		autogrow: true,
		btns: [
			'btnGrp-semantic',
			'underline',
			['link'],
			'btnGrp-justify',
			'btnGrp-lists',
			['removeformat'],
			['foreColor', 'backColor']
		]
	});
}

/* Initialisations */
$(document).ready(function() {
	/* Gestion du clic sur un objet éditable */
	$("div.page").on("click", ".objet_editable", function() {
		var html_id = $(this).attr("id");
		var parsing_objet_id = parser_html_id(html_id);
		var erreur_parsing = parsing_objet_id["erreur"];
		if (!erreur_parsing) {
			var nom_page = parsing_objet_id["nom_page"];
			var nom_balise = parsing_objet_id["nom_balise"];
			var balise_id = parsing_objet_id["balise_id"];
			editer_objet(nom_page, balise_id, nom_balise);
		}
	});

	/* Gestion du bouton "ajout" au survol d'un bloc */
	$("div.page").on("mouseenter", ".bloc", function() {
		var bloc_id = $(this).attr("id");
		$("#poignee-"+bloc_id).stop().slideDown(250);
	});
	$("div.page").on("mouseleave", ".bloc", function() {
		var bloc_id = $(this).attr("id");
		$("#poignee-"+bloc_id).stop().slideUp(250);
	});

	/* Gestion de la bordure au survol d'un objet éditable */
	$("div.page").on("mouseenter", ".objet_editable", function() {
		$(this).addClass("admin_survol_objet");
	});
	$("div.page").on("mouseleave", ".objet_editable", function() {
		$(this).removeClass("admin_survol_objet");
	});

	/* Gestion des boutons pour l'ajout d'objets */
	$("div.page").on("click", "p.bloc_poignee_ajout a", function() {
		var bloc_attr_id = $(this).parent().attr("id");
		var bloc_id = bloc_attr_id.replace("poignee-bloc-", "");
		var classe = $(this).attr("href");
		var nom_page = parser_page();
		ajouter_objet(nom_page, bloc_id, classe);
		return false;
	});

	/* Bouton "soumettre" dans les éditeurs d'objets */
	$("div.page").on("submit", "form.editeur_formulaire", function() {
		var form_id = $(this).attr("id");
		var html_id = form_id.replace("formulaire-", "");
		var parsing_objet_id = parser_html_id(html_id);
		var erreur_parsing = parsing_objet_id["erreur"];
		if (!erreur_parsing) {
			var nom_page = parsing_objet_id["nom_page"];
			var nom_balise = parsing_objet_id["nom_balise"];
			var balise_id = parsing_objet_id["balise_id"];
			var parametres = $(this).serialize();
			soumettre_objet(nom_page, balise_id, nom_balise, parametres);
		}
		return false;
	});

	/* Boutons "supprimer" dans les éditeurs d'objets */
	$("div.page").on("click", "form.editeur_formulaire button.supprimer_formulaire", function() {
		var form_id = $(this).attr("id");
		var html_id = form_id.replace("supprimer-", "");
		var parsing_objet_id = parser_html_id(html_id);
		var erreur_parsing = parsing_objet_id["erreur"];
		if (!erreur_parsing) {
			var nom_page = parsing_objet_id["nom_page"];
			var nom_balise = parsing_objet_id["nom_balise"];
			var balise_id = parsing_objet_id["balise_id"];
			var confirmation = confirm("Confirmez-vous la suppression de cet objet "+nom_balise+" ?");
			if (confirmation) {
				supprimer_objet(nom_page, balise_id, nom_balise);
			}
		}
		return false;
	});

	/* Gestion des éditeurs d'objets lors du retaillage de la fenêtre */
	$(window).on("resize", function() {
		$(".trumbowyg-box").each(function() {
			var barre_outils = $(this).find(".trumbowyg-button-pane");
			var hauteur_barre_outils = barre_outils.height();
			var editeur = $(this).find(".trumbowyg-editor");
			var marge_editeur = hauteur_barre_outils - 36;
			editeur.css("margin-top", marge_editeur+"px");
		});
	});

	/* Possibilité de changer l'ordre des éléments dans un bloc */
	appliquer_sortable(".bloc");
});