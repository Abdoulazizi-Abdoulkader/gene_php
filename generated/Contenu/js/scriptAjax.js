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
						document.getElementById('resultat_tirage').innerHTML = "";

				// On défini ce qu'on va faire quand on aura la réponse
				xhr.onreadystatechange = function(){
					// On ne fait quelque chose que si on a tout reçu et que le serveur est ok
					if(xhr.readyState == 4 && xhr.status == 200){
							var items = xhr.responseText;
						
						
						if(action == "liste_participants"){
							var i = items.split('</li>').length-1;
							var j;
						document.getElementById(action).innerHTML = items;
						document.getElementById("span_nbreTotal").innerHTML = i;
						document.getElementById("nbreTotal").value = i;
						var el ='<option value="">Pr&eacute;ciser le nombre d&rsquo;&eacute;lements &aacute; tirer</option>';
						
						for(j=1;j<i+1;j++)
						el = el+'<option value="'+j+'">'+j+'</option>';
						document.getElementById("element_a_tirer").innerHTML = el;
						document.getElementById("form").setAttribute("style","display:run-in");
						}
						
						else 
						if(action == "un_element" && varia == "modalOpen"){
							$('#fenModal').modal('show');
							
						var text = items.split("/");							
						var chaine = text[0];								
						var nb_car = chaine.length; 
						var tableau = chaine.split("");
						texte = new Array;
						var txt = '';
						var nb_msg = nb_car - 1;
						for (var i=0; i<nb_car; i++) {
						texte[i] = txt+'<span class="digit static">'+tableau[i]+'</span>';
						var txt = texte[i];
						}
						
						actual_texte = 0;
									document.getElementById('resultat_tirage').innerHTML = '';
									document.getElementById('nom').innerHTML = '';

                        var k = setInterval(
									function changeMessage()
									{
										if(actual_texte<nb_car)
									document.getElementById("numero_gagnant").innerHTML = texte[actual_texte];
									actual_texte++;
									if(actual_texte >= nb_car){
									document.getElementById("nom").innerHTML = text[1];
									}
									if(actual_texte>nb_car){
									actual_texte = nb_msg;
							$('#fenModal').modal('hide');
							go('un_element','modalClose');
									clearInterval(k);
									}
									}, 4286);
                   
							}

						else 
						if(action == "nbre_element"){
							$('#fenModal2').modal('show');
                        var k = setInterval(
									function changeMessage()
									{
							$('#fenModal2').modal('hide');
							document.getElementById('resultat_tirage').innerHTML = items;
									clearInterval(k);
									}, 7000);
							
						}
						else 
						if(action == "un_element" && varia == "modalClose"){
									document.getElementById("numero_gagnant").innerHTML = '';
									document.getElementById("nom").innerHTML = '';
									document.getElementById('resultat_tirage').innerHTML = items;

						}
					}
				}				
 
				// Ici on va voir comment faire du post
				xhr.open("POST","traiteurAjax.php?varia="+varia+"&action="+action,true);
				xhr.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
				// ne pas oublier de poster les arguments
				if(varia == "modalSans"){
					element_a_tirer = document.getElementById("element_a_tirer");
					choice = element_a_tirer.selectedIndex;
					valeur_selectionnee = element_a_tirer.options[choice].value;
					xhr.send("varia="+valeur_selectionnee+"&action="+action);

					}
				else
				if(varia == "modalSans2"){
					nbreTotal = document.getElementById("nbreTotal").value;
					xhr.send("varia="+nbreTotal+"&action="+action);
				}
				else
				xhr.send("varia="+varia+"&action="+action);
			}

