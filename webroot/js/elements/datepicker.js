/**
 * This file uses the jquery.calendar plugin to generate a datepicker with two calendars.
 * 
 * This element should be used when you want a datepicker where it's possible to choose "from" and "to" dates.
 * 
 * @author Andreas Kristiansen <ak@nodes.dk>
 * @since 2012.02.09
 */
jQuery(document).ready(function () {
	//Calendars
	var calendarOne = $('#calendarElementCalendarOne').calendar({
		onSelect : function (date) {
			//Updating cal2
			calendarTwo
			.set('minDate', new Date(date))
			.set('currentMonth', new Date(date))
			.show();
		}
	});
	var calendarTwo = $('#calendarElementCalendarTwo').calendar();

	//Buttons and callbacks
	$('#calendarElementButtonSubmit').bind('click', function () {
		//Invoking the submit callback
		calendarElementSendSubmitCallback({
			from : {
				date   : calendarOne.get('selectedDate'),
				hour   : $('#calendarElementTimeFromHour').val(),
				minute : $('#calendarElementTimeFromMinute').val()
			},
			to : {
				date   : calendarTwo.get('selectedDate'),
				hour   : $('#calendarElementTimeToHour').val(),
				minute : $('#calendarElementTimeToMinute').val()
			}
		});
	});
	//Invoking the cancel callback
	$('#calendarElementButtonCancel').bind('click', function () {
		calendarElementSendCancelCallback();
	});
});