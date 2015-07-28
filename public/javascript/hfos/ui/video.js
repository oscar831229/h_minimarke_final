
/**
 * Hotel Front-Office Solution
 *
 * LICENSE
 *
 * This source file is subject to license that is bundled
 * with this package in the file docs/LICENSE.txt.
 *
 * @package 	Back-Office
 * @copyright 	BH-TECK Inc. 2009-2010
 * @version		$Id$
 */

/**
 * HfosVideo
 *
 * Permite ejecutar acciones sobre elementos de video
 */
var HfosVideo = {

	/**
	 * Pasa un video a full screen
	 */
	toFullScreen: function(element){
		/*var element = $(element);
		var videoElement = element.cloneNode(true);
		videoElement.id = 'video-full';
		videoElement.autoplay = true;
		document.body.appendChild(videoElement);
		var pageSize = WindowUtilities.getPageSize(document.body);
		videoElement.setStyle({
			'position': 'absolute',
			'zIndex': 1500,
			'top': parseInt(pageSize.windowHeight/2, 10)+'px',
			'left': parseInt(pageSize.windowWidth/2, 10)+'px'
		});
		new Effect.Morph(videoElement, {
			'style': {
				'top': '0px',
				'left': '0px',
				'width': pageSize.windowWidth+'px',
				'height': pageSize.windowHeight+'px',
			},
			'afterFinish': function(){
				document.body.observe('dblclick', function(){
					$('video-full').erase();
				})
			}
		});
		videoElement.observe('ended', function(){
			this.erase();
		});*/
	}

};