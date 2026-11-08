//fonction qui permet d'actualiser la taille des elements au redimensionnement de la fenetre
(function($,sr){
 
  var debounce = function (func, threshold, execAsap) {
      var timeout;
 
      return function debounced () {
          var obj = this, args = arguments;
          function delayed () {
              if (!execAsap)
                  func.apply(obj, args);
              timeout = null; 
          };
 
          if (timeout)
              clearTimeout(timeout);
          else if (execAsap)
              func.apply(obj, args);
 
          timeout = setTimeout(delayed, threshold || 100); 
      };
  }
	// smartresize 
	jQuery.fn[sr] = function(fn){  return fn ? this.bind('resize', debounce(fn)) : this.trigger(sr); };
 
})(jQuery,'smartresize');

$(function() {
				adjustSize();
			});
			
			// Fonction appelé lorsque le navigateur change de résolution
			$(window).smartresize(function(){
				adjustSize();
			});
			
			/* FONCTION QUI AJUSTE LE SITE AU NAVIGATEUR */
			function adjustSize() {
				// Initialisation des variables pour la taille de la fenêtre
				var navWidth, navHeight;
				if (self.innerWidth != undefined)
				{
					navWidth = self.innerWidth;
					navHeight = self.innerHeight;		
				} else {
					navWidth = document.documentElement.clientWidth;
					navHeight = document.documentElement.clientHeight;
				}
				
				// Mise-à-jour des divs principaux (avec la bonne taille tailles)	
				$("#contenu").css({
					"width": navWidth,
					"height": navHeight-90
				});
			}