
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

var Photos = {

	add: function(){
		$('uploadDiv').show();
		$('nombre').setValue('');
		$('descripcion').setValue('');
		$('foto').setValue('');
		$('filePreview').hide();
		$('upForm').show();
		$('nombre').activate();
	},

	cancelAdd: function(){
		$('uploadDiv').hide();
		$('nombre').setValue('');
		$('descripcion').setValue('');
		$('foto').setValue('');
		$('filePreview').hide();
		$('upForm').hide();
	},

	upload: function(){
		if($F('nombre')==""){
			new Modal.alert({
				'title': 'Subir foto',
				'message': 'Por favor indique el nombre ó título de la foto',
				'onAccept': function(){
					$('nombre').activate();
				}
			});
			return;
		}
		if($F('foto')==""){
			new Modal.alert({
				'title': 'Subir foto',
				'message': 'Por favor seleccione el archivo de la foto'
			});
			return;
		};
		$('uploadFrame').observe('load', Photos.frameComplete)
		$('upForm').submit();
		$('upForm').hide();
		$('spinner').show();
	},

	frameComplete: function(){
		var image = document.createElement('IMG');
		image.src = $('filePreview').src;
		image.hide();
		$('sortlist').appendChild(image);
		new Effect.Grow(image, {
			afterFinish: function(){
				Photos.createSortable();
			}
		});
		$('uploadDiv').hide();
		$('subirFoto').show();
		$('spinner').hide();
		$('uploadFrame').stopObserving('load', Photos.frameComplete);
	},

	handleFiles: function(files){
		if(files.length>0){
			var file = files[0];
			var imageType = /image.*/;
			if(!file.type.match(imageType)){
				$('filePreview').hide();
				$('subirButton').disable();
				new Modal.alert({
					'title': 'Subir foto',
					'message': 'El archivo seleccionado no es una imágen válida'
				});
				return;
			};
			var reader = new FileReader();
			reader.onload = (function(aImage){
				$('filePreview').show();
				$('subirButton').enable();
				return function(e){
					aImage.src = e.target.result
				};
			})($('filePreview'));
			if($F('nombre')==''){
				var fileName = /(.+)\.([a-z0-9])+/
				if(file.name.match(fileName)){
					$('nombre').setValue(RegExp.$1);
				};
			};
			reader.readAsDataURL(file);
		}
	},

	prevUpload: function(img, file){

		this.ctrl = createThrobber(img);
		var xhr = new XMLHttpRequest();
		this.xhr = xhr;

		var self = this;
		this.xhr.upload.addEventListener("progress", function(e) {
			if(e.lengthComputable){
				var percentage = Math.round((e.loaded * 100) / e.total);
				self.ctrl.update(percentage);
			}
		}, false);

		xhr.upload.addEventListener("load", function(e){
			self.ctrl.update(100);
			var canvas = self.ctrl.ctx.canvas;
			canvas.parentNode.removeChild(canvas);
		}, false);

		xhr.open("POST", "http://demos.hacks.mozilla.org/paul/demos/resources/webservices/devnull.php");
		xhr.overrideMimeType('text/plain; charset=x-user-defined-binary');
		xhr.sendAsBinary(file.getAsBinary());
	},

	createSortable: function(){
		Sortable.destroy('sortlist');
		Sortable.create('sortlist', {
			tag: 'img',
			overlap: 'horizontal',
			constraint: false,
			onChange: function(){
				new Ajax.Request("dispatch.php?action=sortpho", {
					method: "post",
					parameters: Sortable.serialize("sortlist")
				});
			}
		});
	},

	deletePhoto: function(imageId){
		var element = $(imageId);
		Event.stopObserving(element, 'mouseenter', Photos.showOptions);
		var optionShade = $('optionShade');
		if(optionShade){
			optionShade.hide();
		}
		new Effect.Shrink(imageId, {
			duration: 0.5,
			afterFinish: function(imageId){
				var optionShade = $('optionShade');
				if(optionShade){
					optionShade.hide();
				};
				new Ajax.Request("dispatch.php?action=delpho", {
					parameters: 'id='+imageId,
					onSuccess: function(transport){
						if(transport.responseText=='FAILED'){
							new Modal.alert({
								'title': 'Eliminar foto',
								'message': 'No se pudo eliminar la foto puede que esté en uso'
							});
							return;
						}
					}
				});
			}.bind(this, imageId)
		});
	},

	showOptions: function(){
		var optionShade = $('optionShade');
		if(!optionShade){
			optionShade = document.createElement('DIV');
			optionShade.id = 'optionShade';
			document.body.appendChild(optionShade);
			optionShade.hide();
		}
		var position = this.positionedOffset();
		optionShade.setStyle({
			'top': (position[1]+this.getHeight()-25)+'px',
			'left': (position[0]+72)+'px',
			'height': '40px',
			'width': this.getWidth()+'px'
		});
		var html = '<div id="photo-options"><table width="100%"><tr><td><div class="photo-button"><table><tr><td><img src="img/edit-photo.png"></td><td>Editar</td></tr></table></div>';
		html+='</td><td><div class="photo-button" onclick="Photos.deletePhoto(\''+this.id+'\')"><table><tr><td><img src="img/delete-photo.png"></td><td>Eliminar</td></tr></table></div></td></tr></table></div>';
		optionShade.update(html);
		optionShade.show();
	},

	initialize: function(){
		Photos.createSortable();
		$$('div#sortlist img').each(function(element){
			element.observe('mouseenter', Photos.showOptions);
		});
	}

}

new Event.observe(window, 'load', Photos.initialize);
