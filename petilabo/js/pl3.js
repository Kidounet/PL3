/* Blocage des mises en cache */
$.ajaxSetup ({cache: false});

/* Appel AJAX pour ouverture d'un éditeur d'objet */
function editer_objet(nom_page, balise_id, nom_balise) {
	$.ajax({
		type: "POST",
		url: "petilabo/ajax/pl3_editer_objet.php",
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
	$.ajax({
		type: "POST",
		url: "petilabo/ajax/pl3_soumettre_objet.php",
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
		url: "petilabo/ajax/pl3_deplacer_objet.php",
		data: {nom_page: nom_page, bloc_id: bloc_id, tab_ordre: tab_ordre},
		dataType: "json"
	}).done(function(data) {
		var valide = data["valide"];
		if (valide) {
			alert(nom_page+" "+bloc_id+" : "+tab_ordre);
		}
		else {
			alert("ERREUR : Le déplacement d'objet n'a pas pu être enregistré");
		}
	}).fail(function() {
		alert("ERREUR : Script AJAX en échec ou introuvable");
	});
}

/* Affichage du code attaché à l'objet */
function afficher_editeur(balise_id, nom_balise, html) {
	var objet_id = nom_balise+"-"+balise_id;
	var objet = $("#"+objet_id);
	if (objet) {
		/* Constitution de l'éditeur */
		var style = calculer_coord_editeur(objet, false);
		var div = "<div id=\"editeur-"+objet_id+"\" class=\"editeur_objet\" style=\""+style+"\" >";
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

function calculer_coord_editeur(objet, plein_ecran) {
	/* Récupération de la position de l'objet */
	var position = objet.position();
	var pos_haut = parseInt(position.top);
	var hauteur = parseInt(objet.height());
	var pos_y = pos_haut + hauteur + 6;

	/* Calcul de la position de l'éditeur */	
	if (plein_ecran) {
		var pos_x = 5;
		var largeur = $("div.page").innerWidth() - 25;
		var style = "top:"+pos_y+"px;";
		style += "left:"+pos_x+"px;";
		style += "width:"+largeur+"px;";
	}
	else {	
		var pos_x = parseInt(position.left);
		var largeur_min = parseInt(objet.width()) - 6;

		/* Elaboration du style */
		var style = "top:"+pos_y+"px;";
		style += "left:"+pos_x+"px;";
		style += "min-width:"+largeur_min+"px;";
	}
	
	return style;
}

function parser_page() {
	var nom_page = $("div.page").attr("name");
	return nom_page;
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
	
	/* Gestion du survol d'un objet éditable */
	$("div.page").on("mouseenter", ".objet_editable", function() {
		$(this).css({"cursor": "pointer", "border-color": "#f00"});
	});
	$("div.page").on("mouseleave", ".objet_editable", function() {
		$(this).css({"cursor": "default", "border-color": "transparent"});
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
			if (objet) {
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
	
	/* Gestion du survol d'un input submit */
	$("div.page").on("mouseover", "input[type='submit']", function() {
		$(this).css("cursor", "pointer");
	});
	
	/* Soumission des formulaires dans les éditeurs d'objets */
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
	
	/* Gestion des éditeurs d'objets lors du retaillage de la fenêtre */
	$(window).resize(function() {
		$("div.page > div.editeur_objet").each(function() {
			var editeur_id = $(this).attr("id");
			deplacer_editeur(editeur_id);
		});
	});
	
	/* Possibilité de changer l'ordre des éléments dans un bloc */
	$(".bloc").sortable({
		placeholder: 'highlight', // A FAIRE
		update: function() {
			var bloc_attr_id = $(this).attr("id");
			var bloc_id = bloc_attr_id.replace("bloc-", "");
			var tab_ordre = [];
			$(this).find("*[id]").each(function() {
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
		}
	});
	$(".bloc").disableSelection();
});