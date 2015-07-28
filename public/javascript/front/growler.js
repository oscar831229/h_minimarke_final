
var GrowlServer = {

	messages: [],
	cumulativeTop: 0,

	calculatePositions: function(){
		var windowScroll = WindowUtilities.getWindowScroll(document.body);
		var pageSize = WindowUtilities.getPageSize(document.body);
		var top, left;
		var left = (pageSize.windowWidth-250+windowScroll.left);
		if($$(".xchat").length==0){
			top = (pageSize.windowHeight-70+windowScroll.top);
		} else {
			top = windowScroll.top;
		};
		var positions = {
			"top": top+"px",
			"left": left+"px"
		};
		return positions;
	},

	add: function(icon, message){
		var d = document.createElement('DIV');
		var dimensions = GrowlServer.calculatePositions();
		d.addClassName('notifier');
		d.innerHTML = message;
		d.setStyle({
			"top": dimensions.top,
			"left": dimensions.left
		});

		var numberActiveMessages = GrowlServer.messages.length;
		var offSet = numberActiveMessages*70;-

		var position = GrowlServer.messages.length;
		GrowlServer.messages[position] = d;



		document.body.appendChild(d);
		d.show();
	}

};

