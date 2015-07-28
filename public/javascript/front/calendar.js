
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

var Hfos_Date = {

	_formatDate: function(activeDate){
		var year = activeDate.getFullYear();
		var month = activeDate.getMonth();
		var day = activeDate.getDate();
		month++;
		if(month<10){
			month = "0"+month;
		};
		if(day<10){
			day = "0"+day;
		};
		return year+'-'+month+'-'+day;
	},

	nextDay: function(date){
		var activeDate = new Date(date.substr(5, 2)+'/'+date.substr(8, 2)+'/'+date.substr(0, 4));
		activeDate.add({
			'days': 1
		});
		return Hfos_Date._formatDate(activeDate);
	},

	prevDay: function(date){
		var activeDate = new Date(date.substr(5, 2)+'/'+date.substr(8, 2)+'/'+date.substr(0, 4));
		activeDate.add({
			'days': -1
		});
		return Hfos_Date._formatDate(activeDate);
	},

	prevWeek: function(date){
		var activeDate = new Date(date.substr(5, 2)+'/'+date.substr(8, 2)+'/'+date.substr(0, 4));
		activeDate.addWeeks(-1)
		return Hfos_Date._formatDate(activeDate);
	},

	nextWeek: function(date){
		var activeDate = new Date(date.substr(5, 2)+'/'+date.substr(8, 2)+'/'+date.substr(0, 4));
		activeDate.addWeeks(1)
		return Hfos_Date._formatDate(activeDate);
	},

	prevMonth: function(date){
		var activeDate = new Date(date.substr(5, 2)+'/'+date.substr(8, 2)+'/'+date.substr(0, 4));
		activeDate.add({
			'month': -1
		});
		return Hfos_Date._formatDate(activeDate);
	},

	nextMonth: function(date){
		var activeDate = new Date(date.substr(5, 2)+'/'+date.substr(8, 2)+'/'+date.substr(0, 4));
		activeDate.add({
			'month': 1
		});
		return Hfos_Date._formatDate(activeDate);
	}

}

var Calendar = {

	rango: "day",

	addEventFreeCallbacks: function(){
		$$('.event-free').each(function(element){
			element.lang = element.title;
			element.title = "Libre";
			element.observe('dblclick', function(){
				window.location = "?action=actividad&option=13"+this.lang;
			});
		});
	},

	addEventDataCallbacks: function(){
		$$('.event-data').each(function(element){
			element.lang = element.title;
			element.title = "";
			element.observe('dblclick', function(){
				window.location = "?action=actividad&option=13&id="+this.lang;
			});
		})
	},

	addDayCallbacks: function(){
		Calendar.addEventFreeCallbacks();
		Calendar.addEventDataCallbacks();
	},

	addWeekCallbacks: function(){
		Calendar.addEventFreeCallbacks();
		Calendar.addEventDataCallbacks();
	},

	showActiveDay: function(){
		new Ajax.Request('dispatch.php?action=calendara&a=day', {
			parameters: {
				'fecha': $('id_fecha').value
			},
			onSuccess: function(transport){
				$('calContent').update(transport.responseText);
				Calendar.addDayCallbacks();
			}
		});
	},

	showActiveWeek: function(){
		new Ajax.Request('dispatch.php?action=calendara&a=week', {
			parameters: {
				'fecha': $('id_fecha').value
			},
			onSuccess: function(transport){
				$('calContent').update(transport.responseText);
				Calendar.addWeekCallbacks();
			}
		});
	},

	showActiveMonth: function(){
		new Ajax.Request('dispatch.php?action=calendara&a=month', {
			parameters: {
				'fecha': $('id_fecha').value
			},
			onSuccess: function(transport){
				$('calContent').update(transport.responseText);
				//Calendar.addMonthCallbacks();
			}
		});
	},

};

new Event.observe(document, 'dom:loaded', function(){
	/*$('codsal').observe('change', function(){
		if(Calendar.rango=='day'){
			Calendar.showActiveDay();
		} else {
			if(Calendar.rango=='week'){
				Calendar.showActiveDay();
			}
		}
	});*/
	$('btDay').observe('click', function(){
		Calendar.rango = 'day';
		Calendar.showActiveDay();
	});
	$('btWeek').observe('click', function(){
		Calendar.rango = 'week';
		Calendar.showActiveWeek();
	});
	$('btMonth').observe('click', function(){
		Calendar.rango = 'month';
		Calendar.showActiveMonth();
	});
	$('btPrev').observe('mousedown', function(){
		this.addClassName('flowPressed');
	});
	$('btPrev').observe('mouseup', function(){
		this.removeClassName('flowPressed');
	});
	$('btPrev').observe('click', function(){
		if(Calendar.rango=='day'){
			var prevDay = Hfos_Date.prevDay($('id_fecha').value);
			setCalendarValue('fecha', prevDay);
			Calendar.showActiveDay();
		} else {
			if(Calendar.rango=='week'){
				var prevWeek = Hfos_Date.prevWeek($('id_fecha').value);
				setCalendarValue('fecha', prevWeek);
				Calendar.showActiveWeek();
			} else {
				if(Calendar.rango=='month'){
					var prevMonth = Hfos_Date.prevMonth($('id_fecha').value);
					setCalendarValue('fecha', prevMonth);
					Calendar.showActiveMonth();
				}
			}
		}
	});
	$('btNext').observe('mousedown', function(){
		this.addClassName('flowPressed');
	});
	$('btNext').observe('mouseup', function(){
		this.removeClassName('flowPressed');
	});
	$('btNext').observe('click', function(){
		if(Calendar.rango=='day'){
			var nextDay = Hfos_Date.nextDay($('id_fecha').value);
			setCalendarValue('fecha', nextDay);
			Calendar.showActiveDay();
		} else {
			if(Calendar.rango=='week'){
				var nextDay = Hfos_Date.nextWeek($('id_fecha').value);
				setCalendarValue('fecha', nextDay);
				Calendar.showActiveWeek();
			} else {
				if(Calendar.rango=='month'){
					var nextMonth = Hfos_Date.nextMonth($('id_fecha').value);
					setCalendarValue('fecha', nextMonth);
					Calendar.showActiveMonth();
				}
			}
		}
	});
	$$('.flowTab').each(function(element){
		element.observe('click', function(){
			var thisElement = this;
			$$('.flowTab').each(function(element){
				if(element!=thisElement){
					element.removeClassName('flowPressed');
				}
			});
			this.addClassName('flowPressed');
		});
	});
	enableDateInput('fecha');
	Calendar.showActiveDay();
});
