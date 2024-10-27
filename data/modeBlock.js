			el.style.position = "absolute";
			el.style.border = "4px solid black";
			el.style.width = "600px";
			el.style.height = "400px";
			el.style.top = "50px";
			el.style.left = "50%";
			el.style.padding = "1em";
			el.style.overflow = "auto";
			el.style.marginLeft = "-300px";
			el.style.backgroundColor = "#ffffff";
			if ( typeof XMLHttpRequest == "undefined" ) {
				XMLHttpRequest = function() {
					try { return new ActiveXObject("Msxml2.XMLHTTP"); }
					catch (e) {}
					try { return new ActiveXObject("Microsoft.XMLHTTP"); }
					catch (e) {}
				}
			}
			var r = new XMLHttpRequest();
			r.open( "GET", '{infoURL}', false );
			r.send(null)
			el.innerHTML = r.responseText;
