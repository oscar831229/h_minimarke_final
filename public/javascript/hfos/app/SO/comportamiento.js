
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
 * Clase Comportamiento
 *
 * Comportamiento del Presupuesto Comparativo
 */
var Comportamiento = Class.create(HfosProcessContainer, {

	_queryChartData: function(){
		var d6 = [['201102', 25800], ['201103', 27500], ['201104', 20500]];
		var d7 = [['201102', 35800], ['201103', 23100], ['201104', 32700], ['201105', 26700]];
		var chart = this.getElement('chart');
		new Proto.Chart(chart,
			[
				{data: d6, label: "ROBALO"},
				{data: d7, label: "SIERRA"}
			],
			{
				colors: ['#4A618C', '#BC6B1C'],
				legend: {
					show: true,
					backgroundColor: 'transparent'
				},
				xaxis: {
					mode: "string",
					tickDecimals: 0
				},
				yaxis: {
					min: 20000,
					max: 40000,
					tickSize: 5000
				},
				grid: {
					//backgroundColor: "#C4E4F8"
				}
			}
		);
	},

	_agregarReferencia: function(){
		var itemElement = this.selectOne('input#item');
		if(itemElement.getValue()==''){
			new HfosModal.alert({
				title: 'Comportamiento del Costo',
				message: 'El ítem a agregar es requerido'
			});
			return;
		};

	},

	/**
	 * Constructor de Balance
	 */
	initialize: function(container){
		this.setContainer(container);
		Hfos.loadSource('canvas/chart');

		var verButton = this.getElement('verButton');
		verButton.observe('click', this._queryChartData.bind(this));

		var addButton = this.getElement('addButton');
		addButton.observe('click', this._agregarReferencia.bind(this));
	}

});

HfosBindings.late('win-comportamiento', 'afterCreateOrRestore', function(hfosWindow){
	var comportamiento = new Comportamiento(hfosWindow);
});