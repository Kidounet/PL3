/*
 * JS PL3 mode administration media
 */

/* ReplaceWith avec renvoi du nouvel objet */
$.fn.replaceWithPush = function(a) {
    var $a = $(a);
    this.replaceWith($a);
    return $a;
};
 
/*
 * Adapté de :
 * singleuploadimage - jQuery plugin for upload a image, simple and elegant.
 * Copyright (c) 2014 Langwan Luo
 * Licensed under the MIT license
 * http://www.opensource.org/licenses/mit-license.php
 * Project home:
 * https://github.com/langwan/jquery.singleuploadimage.js
 * version: 1.0.3
 */
(function($) {
    $.fn.singleupload = function(options) {
        var vignette = this;
        var inputfile = null;
        var settings = $.extend({
            action: '#',
            onSuccess: function(html) {},
            onError: function(message){},
            onProgress: function(index, loaded, total) {
                var progression = Math.round(loaded * 100 / total);
				var barre = $("#barre-progression-"+index);
				if (barre) {barre.css("width", progression+"%");}
            },
            taille: 0,
            nom_taille: "",
			largeur_taille: 0,
			hauteur_taille: 0,
			compression: 75,
			page: 'index'
        }, options);

        $('#'+settings.inputId).bind('change', function() {
			var html_barre_progression = "<div class='vignette_container_progression'><div id='barre-progression-"+settings.taille+"' class='vignette_barre_progression'></div></div>";
            vignette = vignette.replaceWithPush(html_barre_progression);
            var fd = new FormData();
            fd.append($('#'+settings.inputId).attr("name"), $('#'+settings.inputId).get(0).files[0]);
            fd.append("taille", settings.taille);
            fd.append("nom_taille", settings.nom_taille);
            fd.append("largeur_taille", settings.largeur_taille);
            fd.append("hauteur_taille", settings.hauteur_taille);
            fd.append("compression", settings.compression);
            fd.append("page", settings.page);

            var xhr = new XMLHttpRequest();
            xhr.addEventListener("load", function(ev) {
                var res = eval("("+ev.target.responseText+")");
                if (!res.code) {
                    settings.onError(res.info);
                }
				/* Ajout de la nouvelle image et/ou du nouveau bouton d'ajout */
				var vignette_parent = vignette.parent().replaceWithPush(res.html);
				/* Le nouveau bouton d'ajout reçoit à son tour le plugin d'upload */
				vignette_parent.find("a.vignette_plus").each(function() {
					installer_single_image_upload($(this));
				});
                if (res.code) {
					settings.onSuccess(res.html);
				}
            },
            false);
            xhr.upload.addEventListener("progress", function(ev) {
                settings.onProgress(settings.taille, ev.loaded, ev.total);
            }, false);
            
            xhr.open("POST", settings.action, true);
            xhr.send(fd);  
        });  
    	return this;
    }
}( jQuery ));


/* Récupération du nom de la page */
function parser_page() {
	var nom_page = $("div.page_media").attr("name");
	return nom_page;
}

/* Installation du plugin single image upload sur un bouton ajout media */
function installer_single_image_upload(bouton) {
	var plus_id = bouton.attr("id");
	var taille_id = parseInt(plus_id.replace("ajout-", ""));
	var nom_taille = bouton.attr("name");
	var titre_taille = $("#titre-taille-"+taille_id);
	if (titre_taille !== undefined) {
		var largeur = parseInt(titre_taille.data("largeur"));
		var hauteur = parseInt(titre_taille.data("hauteur"));
		var compression = parseInt(titre_taille.data("compression"));
	}
	else {
		var largeur = 0;
		var hauteur = 0;
		var compression = 75;
	}
	if ((taille_id > 0) && (nom_taille.length > 0)) {
		bouton.singleupload({
			action: "../petilabo/ajax/pl3_charger_image.php",
			inputId: "input-"+taille_id,
			taille: taille_id,
			nom_taille: nom_taille,
			largeur_taille: largeur,
			hauteur_taille: hauteur,
			compression: compression,
			page: parser_page(),
			onError: function(message) {alert(message);	}
		});
	}
}

/* Initialisations */
$(document).ready(function() {
	/* Gestion du clic sur un media */
	$("div.page_media").on("click", ".vignette_apercu_lien", function() {
		var vignette_id = $(this).attr("id");
		var media_id = parseInt(vignette_id.replace("media-", ""));
		if (media_id > 0) {
			alert("Edition de l'image index "+media_id);
		}
		return false;
	});
	
	/* Gestion du clic sur un bouton d'ajout media */
	$("div.page_media").on("click", ".vignette_plus", function() {
		var plus_id = $(this).attr("id");
		var taille_id = parseInt(plus_id.replace("ajout-", ""));
		if (taille_id > 0) {
			$("#input-"+taille_id).click();
		}
		return false;
	});

	/* Attachement du plugin single image upload aux boutons d'ajout media */
	$("a.vignette_plus").each(function() {
		installer_single_image_upload($(this));
	});
});