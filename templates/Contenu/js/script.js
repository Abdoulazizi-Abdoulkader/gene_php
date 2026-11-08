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
				/*$("#contenu").css({
					"width": navWidth,
					"height": navHeight-92
				});*/
			}

$(document).ready(function() {				   
	var auto_refre2= setInterval(function (){
	
	$("#erreur").fadeOut(2000);	
	
}, 2000);
	});

			
function calculerBagri1(){
	var coutP=document.getElementById("cOUTPROJET").value;
	var tauxAP=document.getElementById("tAUXAPPERSO").value;
	var apportV=document.getElementById("aPPORTVERSE").value;
	document.getElementById("mTPRETSOL").value=(coutP*((60-tauxAP)/100)).toFixed(0);
	document.getElementById("mTAPPERSODEM").value=(coutP*tauxAP/100).toFixed(0);
	var mtAP=document.getElementById("mTAPPERSODEM").value;
	document.getElementById("mTSUBDEM").value=(coutP*0.4).toFixed(0);
	document.getElementById("tAUXMOBAPPORT").value=((apportV/mtAP)*100).toFixed(2);
	document.getElementById("tAUXMOBAPPORT1").value=(((apportV/mtAP)*100).toFixed(2))+" %";
}

function calculerBagri2(){
	var coutPV=document.getElementById("cOUTPROJETVAL").value;
	var tauxAP=document.getElementById("tAUXAPPERSO").value;
	document.getElementById("cREDITACCORDE").value=(coutPV*((60-tauxAP)/100)).toFixed(0);
	document.getElementById("aPPORTPERSOVAL").value=(coutPV*tauxAP/100).toFixed(0);
	document.getElementById("sUBACCORDEE").value=(coutPV*0.4).toFixed(0);
}

function difDate(d1,d2){
	var dat1=document.getElementById(d1).value;
	var dat2=document.getElementById(d2).value;
	var date1=new Date(dat1);
	var date2=new Date(dat2);
	if(dat1!="" && dat2!="")
	return new Number((date2.getTime()-date1.getTime())/2613600000).toFixed(0);
	else
	return 0;
}

function dispHidde(elmt,flag){

if(flag=="Oui" || flag=="1")
	document.getElementById(""+elmt).setAttribute("style","display:run-in");
else
	document.getElementById(""+elmt).setAttribute("style","display:none");

}