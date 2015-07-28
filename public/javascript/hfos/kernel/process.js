
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

var HfosProcess = {

	_onCompleteIFrame: function(iframe, submitForm, options){

	},

	liveJsonForm: function(form, options){

		//options = HfosAjax.setCallbacks(options);
		//options = HfosAjax.bindJson('onSuccess', options);

		var iframe = document.createElement('IFRAME');
		var iframeID = 'iframe'+parseInt((new Date()).getTime());
		iframe.name = iframeID;
		iframe.hide();
		document.body.appendChild(iframe);

		var submitForm = document.createElement('FORM');
		submitForm.target = iframeID;
		submitForm.method = form.getAttribute('method');
		submitForm.action = form.getAttribute('action');
		submitForm.hide();
		document.body.appendChild(iframe);

		var inputs = form.getInputs();
		for(var i=0;i<inputs.length;i++){
			var input = document.createElement('INPUT');
			input.type = 'hidden';
			input.name = inputs[i].name;
			input.value = inputs[i].getValue();
			submitForm.appendChild(input[i]);
		};

		iframe.observe('load', HfosProcess._onCompleteIFrame(iframe, submitForm, options));

		submitForm.submit();
		if(typeof options.onLoading != "undefined"){
			options.onLoading();
		};

		//return new HfosHttpRequest(form.getAttribute('action'), options);

	}

}

