
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
 * HfosReportType
 *
 * Genera un componente para escoger el tipo de reporte
 */
var HfosReportType = {

	_showSelect: function(hyChReportType, hyReportTable, hyReportType, reportTypeElement){
		hyChReportType.show();
		hyReportTable.hide();
		hyReportType.setStyle({width: '180px'});
		reportTypeElement.store('previous', reportTypeElement.getValue());
	},

	/**
	 *
	 * @this {Element}
	 */
	_onChangeType: function(hyChReportType, hyReportTable, hyReportType){
		this.blur();
		hyChReportType.hide();
		hyReportTable.show();
		hyReportType.setStyle({width: '100px'});
		var units = 0;
		var value = this.getValue();
		var previous = this.retrieve('previous');
		switch(value){
			case 'screen':
				switch(previous){
					case 'html':
						units = 1;
						break;
					case 'excel':
						units = 2;
						break;
					case 'pdf':
						units = 3;
						break;
					case 'pdf':
						units = 4;
						break;
				}
				break;
			case 'html':
				switch(previous){
					case 'screen':
						units = -1;
						break;
					case 'excel':
						units = 1;
						break;
					case 'pdf':
						units = 2;
						break;
					case 'text':
						units = 3;
						break;
				}
				break;
			case 'excel':
				switch(previous){
					case 'screen':
						units = -2;
						break;
					case 'html':
						units = -1;
						break;
					case 'pdf':
						units = 1;
						break;
					case 'text':
						units = 2;
						break;
				}
				break;
			case 'pdf':
				switch(previous){
					case 'screen':
						units = -3;
						break;
					case 'html':
						units = -2;
						break;
					case 'excel':
						units = -1;
						break;
					case 'text':
						units = 1;
						break;
				}
				break;
			case 'text':
				switch(previous){
					case 'screen':
						units = -4;
						break;
					case 'html':
						units = -3;
						break;
					case 'excel':
						units = -2;
						break;
					case 'text':
						units = -1;
						break;
				}
				break;
		};
		new Effect.Move(hyReportTable, {
			duration: 0.5,
			y: units*50
		});
	},

	observeReportType: function(container){
		if(typeof container == "undefined"){
			container = Hfos.getApplication().getWorkspace().getWindowManager().getActiveWindow();
		};
		var hyReportType = container.getElement('hyReportType');
		if(hyReportType){
			var hyChReportType = container.getElement('hyChReportType');
			var hyReportTable = container.getElement('hyReportTable');
			var reportTypeElement = container.getElement('reportType');
			var onChangeType = HfosReportType._onChangeType.bind(reportTypeElement, hyChReportType, hyReportTable, hyReportType);
			hyReportTable.observe('click', HfosReportType._showSelect.bind(window, hyChReportType, hyReportTable, hyReportType, reportTypeElement));
			reportTypeElement.observe('change', onChangeType);
			onChangeType();
		};
	},

	/**
	 * Cambia el tipo de reporte manualmente
	 */
	changeReportType: function(reportType, container){
		if(typeof container == "undefined"){
			container = Hfos.getApplication().getWorkspace().getWindowManager().getActiveWindow();
		};
		var hyReportType = container.getElement('hyReportType');
		if(hyReportType){
			var hyChReportType = container.getElement('hyChReportType');
			var hyReportTable = container.getElement('hyReportTable');
			var reportTypeElement = container.getElement('reportType');
			var onChangeType = HfosReportType._onChangeType.bind(reportTypeElement, hyChReportType, hyReportTable, hyReportType);
			reportTypeElement.setValue(reportType);
			onChangeType();
		};
	}

}