
/**
 * Hotel Front-Office Solution
 *
 * LICENSE
 *
 * This source file is subject to license that is bundled
 * with this package in the file docs/LICENSE.txt.
 *
 * @package		Front-Office
 * @copyright	BH-TECK Inc. 2009-2010
 * @version		$Id$
 */

if (typeof HTMLAudioElement != 'undefined') {
	HTMLAudioElement.prototype.playSong = function(src){
 		if(!myAudio.canPlayType){
 			return false;
 		};
 		this.src = src;
 		this.play();
 	}
};

var Speak = {

	activeAudio: null,
	pendendSpeak: null,

	speak: function(text, language){
		var audioCompatible = (navigator.userAgent.indexOf('Firefox/3.5')!=-1)||(navigator.userAgent.indexOf('Safari/5')!=-1);
		if(audioCompatible){
			if(localStorage.speak==1){
				if(typeof language == "undefined"){
					language = "es-la"
				};
				new Ajax.Request('dispatch.php?action=speak&t='+text+'&l='+language, {
					method: 'GET',
					onSuccess: function(transport){
						var src = 'temp/'+transport.responseText+'.wav?x='+parseInt(Math.random()*1000);
						Speak.play(src);
						if(Speak.activeAudio!=null){
							Speak.pendendSpeak = audio;
						} else {
							Speak.activeAudio = audio;
							audio.observe("load", function(){
								this.play();
							});
							audio.load();
							Speak.dropAudio(audio)
						}
					}
				});
			}
		}
	},

	play: function(src){
		var audio = document.createElement('AUDIO');
		audio.controls = true;
		audio.autobuffer = true;
		audio.autoplay = false;
		audio.setAttribute('src', src);
		audio.style.visibility = "hidden";
		audio.observe("ended", Speak.dropAudio);
		audio.observe("load", function(){
			this.play();
		});
		document.body.appendChild(audio);
	},

	dropAudio: function(){
		document.body.removeChild(this);
		Speak.activeAudio = null;
		if(Speak.pendendSpeak!=null){
			var audio = Speak.pendendSpeak;
			Speak.activeAudio = audio;
			Speak.pendendSpeak = null;
			audio.play();
		}
	},

	stopActive: function(){
		if(Speak.activeAudio!=null){
			Speak.activeAudio.pause();
			Speak.activeAudio = null;
		}
	}
}