
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

Hfos.getApplication().getMenu().setOptions([
{
	'title': 'Consecutivos',
	'icon': 'document-library.png',
	'options': [
		{
			'title': 'Consecutivo',
			'icon': 'document-library.png',
			'click': function(){
				Hfos.getApplication().run({
					id: 'win-consecutivo',
					icon: 'document-library.png',
					title: "Consecutivo",
					action: "consecutivo",
					height: '570px'
				});
			}
		}
	]
}
]);
