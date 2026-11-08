		function getXhr(){
                                var xhr = null; 
				if(window.XMLHttpRequest) // Firefox et autres
				   xhr = new XMLHttpRequest(); 
				else if(window.ActiveXObject){ // Internet Explorer 
				   try {
			                xhr = new ActiveXObject("Msxml2.XMLHTTP");
			            } catch (e) {
			                xhr = new ActiveXObject("Microsoft.XMLHTTP");
			            }
				}
				else { // XMLHttpRequest non supporté par le navigateur 
				   alert("Votre navigateur ne supporte pas les objets XMLHTTPRequest..."); 
				   xhr = false; 
				} 
                                return xhr;
			}
 
			/**
			* Méthode qui sera appelée sur les clicks 
			*/
			function go(action,varia){
				var xhr = getXhr();
				var lesVariables = "";

				// On défini ce qu'on va faire quand on aura la réponse
				xhr.onreadystatechange = function(){
					// On ne fait quelque chose que si on a tout reçu et que le serveur est ok
					if(xhr.readyState == 4 && xhr.status == 200){
							var items = xhr.responseText;

				if(action == "ajoutJeune" && varia == "showModal")
							$('#fenModal').modal('show');
				else 
				if(action == "rechercheBagri"){
				var text = items.split("|");							
				var corpsTab = text[0];
				var nbreLigne = text[1];
				if(corpsTab.length!=0){
				document.getElementById("corpsTab").innerHTML = corpsTab;
				document.getElementById("nbreLigne").innerHTML = "Nombre de lignes : <strong>"+nbreLigne+"</strong>";
				}
				else{
				document.getElementById("corpsTab").innerHTML = "<tr><td colspan=\"16\" style=\"text-align: center;color:#FE9206;font-weight:bold\">Auncun enregistrement ne correspond a votre recherche.</td></tr>";
				document.getElementById("nbreLigne").innerHTML = "Nombre de lignes : <strong>0</strong>";
				}
				}
				else 
				if(action == "rechercheCRAJeune"){
				var text = items.split("|");							
				var corpsTab = text[0];
				var nbreLigne = text[1];
				if(corpsTab.length!=0){
				document.getElementById("corpsTab").innerHTML = corpsTab;
				document.getElementById("nbreLigne").innerHTML = "Nombre de lignes : <strong>"+nbreLigne+"</strong>";
				}
				else{
				document.getElementById("corpsTab").innerHTML = "<tr><td colspan=\"16\" style=\"text-align: center;color:#FE9206;font-weight:bold\">Auncun enregistrement ne correspond a votre recherche.</td></tr>";
				document.getElementById("nbreLigne").innerHTML = "Nombre de lignes : <strong>0</strong>";
				}
				}
				else 
				if(action == "rechercheLoc"){
				var text = items.split("|");							
				var corpsTab = text[0];
				var nbreLigne = text[1];
				if(corpsTab.length!=0){
				document.getElementById("corpsTab").innerHTML = corpsTab;
				document.getElementById("nbreLigne").innerHTML = "Nombre de lignes : <strong>"+nbreLigne+"</strong>";
				}
				else{
				document.getElementById("corpsTab").innerHTML = "<tr><td colspan=\"16\" style=\"text-align: center;color:#FE9206;font-weight:bold\">Auncun enregistrement ne correspond a votre recherche.</td></tr>";
				document.getElementById("nbreLigne").innerHTML = "Nombre de lignes : <strong>0</strong>";
				}
				}
				else 
				if(action == "rechercheCRACentre" || action == "rechercheCRDCentre"){
				var text = items.split("|");							
				var corpsTab = text[0];
				var nbreLigne = text[1];
				if(corpsTab.length!=0){
				document.getElementById("corpsTab").innerHTML = corpsTab;
				document.getElementById("nbreLigne").innerHTML = "Nombre de lignes : <strong>"+nbreLigne+"</strong>";
				}
				else{
				document.getElementById("corpsTab").innerHTML = "<tr><td colspan=\"16\" style=\"text-align: center;color:#FE9206;font-weight:bold\">Auncun enregistrement ne correspond a votre recherche.</td></tr>";
				document.getElementById("nbreLigne").innerHTML = "Nombre de lignes : <strong>0</strong>";
				}
				}
				else 
				if(action == "rechercheCRDJeune"){
				var text = items.split("|");							
				var corpsTab = text[0];
				var nbreLigne = text[1];
				if(corpsTab.length!=0){
				document.getElementById("corpsTab").innerHTML = corpsTab;
				document.getElementById("nbreLigne").innerHTML = "Nombre de lignes : <strong>"+nbreLigne+"</strong>";
				}
				else{
				document.getElementById("corpsTab").innerHTML = "<tr><td colspan=\"16\" style=\"text-align: center;color:#FE9206;font-weight:bold\">Auncun enregistrement ne correspond a votre recherche.</td></tr>";
				document.getElementById("nbreLigne").innerHTML = "Nombre de lignes : <strong>0</strong>";
				}
				}
				else 
				if(action == "SelectDep"){
				document.getElementById("cODECOM").innerHTML = items;
				document.getElementById("divCom").setAttribute("style","display:run-in");
				document.getElementById("divLoc").setAttribute("style","display:none");
				document.getElementById("divJeune").setAttribute("style","display:none");
				}
				else 
				if(action == "SelectCom"){
				document.getElementById("cODELOC").innerHTML = items;
				document.getElementById("divLoc").setAttribute("style","display:run-in");
				document.getElementById("divJeune").setAttribute("style","display:none");
				}
				else 
				if(action == "bagriSelectLoc" || action == "CRASelectLoc"){
				document.getElementById("nUMORDREJEU").innerHTML = items;
				document.getElementById("divJeune").setAttribute("style","display:run-in");
				}

				}	
				}
				xhr.open("POST","traiteurAjax.php",true);
				xhr.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
				
				if(action == "rechercheBagri"){
				lesVariables = lesVariables+"CODELOC="+document.getElementById("filtreLoc").value+"&";
				lesVariables = lesVariables+"NUMCOMPTE="+document.getElementById("rechercheNumCompte").value+"&";
				lesVariables = lesVariables+"NOMPRENOMJEUNE="+document.getElementById("rechercheNomPrenom").value;
				}
				else
				if(action == "rechercheLoc"){
				lesVariables = "CODECOM="+varia;
				}
				else
				if(action == "rechercheCRAJeune"){
				lesVariables = lesVariables+"CODELOC="+document.getElementById("CODELOC").value+"&";
				lesVariables = lesVariables+"SEXE="+document.getElementById("SEXE").value+"&";
				lesVariables = lesVariables+"NOMPRENOMJEUNE="+document.getElementById("NOMPRENOMJEUNE").value;
				}
				else
				if(action == "rechercheCRACentre" || action == "rechercheCRDCentre"){
				lesVariables = lesVariables+"NOMCENTRE="+document.getElementById("NOMCENTRE").value+"&";
				lesVariables = lesVariables+"CODECOM="+document.getElementById("CODECOM").value;
				}
				else
				if(action == "rechercheCRDJeune"){
				lesVariables = lesVariables+"NOMPRENOMJEUNE="+document.getElementById("NOMPRENOMJEUNE").value+"&";
				lesVariables = lesVariables+"CODELOC="+document.getElementById("CODELOC").value+"&";
				lesVariables = lesVariables+"SEXE="+document.getElementById("SEXE").value+"&";
				lesVariables = lesVariables+"CODEMAI="+document.getElementById("CODEMAI").value;
				}
				else
				if(action == "SelectDep"){
				lesVariables = "CODEDEP="+varia;
				}
				else
				if(action == "SelectCom"){
				lesVariables = "CODECOM="+varia;
				}
				else
				if(action == "bagriSelectLoc" || action == "CRASelectLoc"){
				lesVariables = "CODELOC="+varia;
				}
				xhr.send(lesVariables+"&action="+action);
}