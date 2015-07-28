
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

var supportedNavigator = /Firefox\/3\.[5-9]/.test(navigator.userAgent);

function changePassword(){
	new Effect.toggle("camcla", "slide", {
		duration: 0.5,
		afterFinish: function(){
			window.setTimeout(function(){
				$('aclave').activate();
			}, 100);
		}
	});
}

function valida(){
	if($('aclave').value.length==0){
		new Modal.alert({
			'title': 'Cambio de clave',
			'message': 'Debe digitar la clave anterior',
			'onAccept': function(){
				new Effect.Highlight('aclave');
				$('aclave').activate();
			}
		});
		return;
	};
   	if($('pclave').value.length<6){
   		new Modal.alert({
			'title': 'Cambio de clave',
			'message': 'Su nueva clave debe tener mínimo 6 caracteres',
			'onAccept': function(){
				new Effect.Highlight('pclave');
				$('pclave').activate();
			}
		});
		return;
	};
	if($('pclave').value!=$('rclave').value){
		new Modal.alert({
			'title': 'Cambio de clave',
			'message': 'La clave de confirmación no coincide con la nueva clave',
			'onAccept': function(){
				new Effect.Highlight('pclave');
				$('pclave').activate();
			}
		});
		return;
	}
	document.fl.submit();
}

function myCompletionHandler(msg){
	if(msg.match(/(http\:\/\/\S+)/)){
		var image_url = RegExp.$1;
			document.getElementById('upload_results').innerHTML =
			'<h1>Upload Successful!</h1>' +
			'<h3>JPEG URL: ' + image_url + '</h3>' +
			'<img src="' + image_url + '">';
		webcam.reset();
	} else {
		alert("PHP Error: " + msg);
	}
}

var Camera = {

	initialize: function(){
		webcam.set_api_url('dispatch.php?action=savephoto');
		webcam.set_quality(100);
		webcam.set_shutter_sound(true);
		webcam.set_swf_url('swf/webcam.swf');
		$('camdiv').update(webcam.get_html(320, 240, 160, 120));
		webcam.set_hook('onComplete', 'myCompletionHandler');
	},

	takeSnapshot: function(){
		// take snapshot and upload to server
		document.getElementById('upload_results').innerHTML = '<h1>Uploading...</h1>';
		webcam.snap();
	}

}

function forceLogout(){
	new Modal.alert({
		'title': 'Sesión Expirada',
		'message': 'Su sesión será cerrada en este momento',
		'onAccept': function(){
			closeAppAction();
		}
	});
	return;
}

new Event.observe(document, "dom:loaded", function(){
	if(supportedNavigator){
		if(localStorage.high==null){
			localStorage.high = $Jasmin.high;
		};
		if(localStorage.high==1){
			$('high_pe').checked = true;
		} else {
			$('high_pe').checked = false;
		};
		$('high_pe').observe('change', function(){
			if(supportedNavigator){
				if(localStorage.high==null){
					localStorage.high = $Jasmin.high;
				};
				if(this.checked){
					localStorage.high = 1;
				} else {
					localStorage.high = 0;
				};
				new Modal.confirm({
					'title': 'Reiniciar',
					'message': 'Debe reiniciar su sesión para que este cambio tenga efecto. ¿Desea hacerlo ahora?',
					'onAccept': function(){
						closeAppAction();
					}
				});
			}
		});
	} else {
		$('high_pe').disable();
	};

	//Speak
	if(supportedNavigator){
		if(localStorage.speak==null){
			localStorage.speak = 0;
		};
		if(localStorage.speak==1){
			$('speak_pe').checked = true;
		} else {
			$('speak_pe').checked = false;
		};
		$('speak_pe').observe('change', function(){
			if(supportedNavigator){
				if(localStorage.speak==null){
					localStorage.speak = 1;
				};
				if(this.checked){
					localStorage.speak = 1;
				} else {
					localStorage.speak = 0;
				}
			}
		});
	} else {
		$('speak_pe').disable();
	};

	$('pclave').observe('keyup', function(){
		if(this.value.length>0){
			if(this.value.length<6){
				$('strength').update('DEBIL');
				new Effect.Morph('strength', {
					style: {
						'color': '#ffffff',
						'width': '50px',
						'backgroundColor': '#ff0000'
					}
				});
			} else {
				if(this.value.length==6){
					new Effect.Morph('strength', {
						style: {
							'width': '100px',
							'backgroundColor': '#fdd017',
							'color': '#000000'
						},
						afterFinish: function(){
							$('strength').update('NORMAL')
						}
					});
				} else {
					if(this.value.length>=8){
						new Effect.Morph('strength', {
							style: {
								'width': '150px',
								'backgroundColor': '#4aa02c',
								'color': '#ffffff'
							},
							afterFinish: function(){
								$('strength').update('FUERTE')
							}
						});
					}
				}
			}
		} else {
			$('strength').update('');
			new Effect.Morph('strength', {
				style: {
					'width': '0px'
				}
			});
		}
	});

});

