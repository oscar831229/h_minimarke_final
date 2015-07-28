google.load("language", "1");
google.load("maps", "1");

var Traslate = {

	traslateTo: function(languageFrom, languageTo, text){
		google.language.translate(text, languageFrom.substr(0, 2), languageTo.substr(0, 2), function(result){
    		if(result.translation){
	      		Speak.speak(result.translation, languageTo);
    		}
  		});
	}

}

/*var Maps = {

  addMap: function(){
	  if (GBrowserIsCompatible()) {
	    var map = new GMap2(document.getElementById("map_canvas"));
	    map.setCenter(new GLatLng(37.4419, -122.1419), 13);
	    map.addControl(new GLargeMapControl());
	    map.addControl(new GMapTypeControl());
	    map.addControl(new google.maps.LocalSearch(), new GControlPosition(G_ANCHOR_BOTTOM_RIGHT, new GSize(10,20)));
	  }
  }
}*/


/*function initialize() {
	google.language.translate('hello to all', 'en', 'es', function(result) {
    	if(result.translation){
      		Speak.speak(result.translation);
    	}
  	});
}
google.setOnLoadCallback(initialize);*/

