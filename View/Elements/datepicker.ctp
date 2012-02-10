<style>
	.calendar { width: 266px; border: 1px solid #ccc; -webkit-border-radius: 4px; -moz-border-radius: 4px; border-radius: 4px; -webkit-border-radius: 4px; -moz-border-radius: 4px; border-radius: 4px; -webkit-background-clip: padding-box; -moz-background-clip: padding-box; background-clip: padding-box; -webkit-background-clip: padding-box; -moz-background-clip: padding-box; background-clip: padding-box; }
	.calendar .calendarHolder { color: #494949; text-align: center; }
	.calendar .calendarHolder a { color: #494949; text-decoration: none; }
	.calendar .calendarHolder .calendarHeader { font: bold 17px/37px Arial, Helvetica, sans-serif; background-color: #fbfbfb; background-image: -moz-linear-gradient(top, #ffffff, #f5f4f4); background-image: -ms-linear-gradient(top, #ffffff, #f5f4f4); background-image: -webkit-gradient(linear, 0 0, 0 100%, from(#ffffff), to(#f5f4f4)); background-image: -webkit-linear-gradient(top, #ffffff, #f5f4f4); background-image: -o-linear-gradient(top, #ffffff, #f5f4f4); background-image: linear-gradient(top, #ffffff, #f5f4f4); background-repeat: repeat-x; filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#ffffff', endColorstr='#f5f4f4', GradientType=0); background-color: #fbfbfb; background-image: -moz-linear-gradient(top, #ffffff, #f5f4f4); background-image: -ms-linear-gradient(top, #ffffff, #f5f4f4); background-image: -webkit-gradient(linear, 0 0, 0 100%, from(#ffffff), to(#f5f4f4)); background-image: -webkit-linear-gradient(top, #ffffff, #f5f4f4); background-image: -o-linear-gradient(top, #ffffff, #f5f4f4); background-image: linear-gradient(top, #ffffff, #f5f4f4); background-repeat: repeat-x; filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#ffffff', endColorstr='#f5f4f4', GradientType=0); -webkit-box-shadow: inset 0 -1px 0 #ffffff; -moz-box-shadow: inset 0 -1px 0 #ffffff; box-shadow: inset 0 -1px 0 #ffffff; -webkit-box-shadow: inset 0 -1px 0 #ffffff; -moz-box-shadow: inset 0 -1px 0 #ffffff; box-shadow: inset 0 -1px 0 #ffffff; -webkit-border-radius: 2px 2px 0 0; -moz-border-radius: 2px 2px 0 0; border-radius: 2px 2px 0 0; -webkit-border-radius: 2px 2px 0 0; -moz-border-radius: 2px 2px 0 0; border-radius: 2px 2px 0 0; -webkit-background-clip: padding-box; -moz-background-clip: padding-box; background-clip: padding-box; -webkit-background-clip: padding-box; -moz-background-clip: padding-box; background-clip: padding-box; }
	.calendar .calendarHolder .calendarHeader .arrow { width: 37px; height: 37px; }
	.calendar .calendarHolder .calendarHeader .arrow.previous { float: left; }
	.calendar .calendarHolder .calendarHeader .arrow.next { float: right; }
	.calendar .calendarHolder .calendarHeader .month { margin-right: 4px; }
	.calendar .calendarHolder .weekDays { display: none; }
	.calendar .calendarHolder .calendarDates { margin-left: -1px; margin-right: -1px; -webkit-border-radius: 0 0 2px 2px; -moz-border-radius: 0 0 2px 2px; border-radius: 0 0 2px 2px; -webkit-border-radius: 0 0 2px 2px; -moz-border-radius: 0 0 2px 2px; border-radius: 0 0 2px 2px; -webkit-background-clip: padding-box; -moz-background-clip: padding-box; background-clip: padding-box; -webkit-background-clip: padding-box; -moz-background-clip: padding-box; background-clip: padding-box; }
	.calendar .calendarHolder .calendarDates > div { float: left; width: 37px; height: 37px; line-height: 37px; border-top: 1px solid #ccc; border-left: 1px solid #ccc; background: #f3f3f4; -webkit-box-shadow: inset 0 -1px 0 #ffffff, inset 1px 0 0 #ffffff; -moz-box-shadow: inset 0 -1px 0 #ffffff, inset 1px 0 0 #ffffff; box-shadow: inset 0 -1px 0 #ffffff, inset 1px 0 0 #ffffff; -webkit-box-shadow: inset 0 -1px 0 #ffffff, inset 1px 0 0 #ffffff; -moz-box-shadow: inset 0 -1px 0 #ffffff, inset 1px 0 0 #ffffff; box-shadow: inset 0 -1px 0 #ffffff, inset 1px 0 0 #ffffff; }
	.calendar .calendarHolder .calendarDates > div a { font: bold 15px/37px Helvetica, sans-serif; }
	.calendar .calendarHolder .calendarDates > div a.disabled { color: #c8c8c8; font-weight: normal; }
	.calendar .calendarHolder .calendarDates > div.selected { background-color: #eb66ba; background-image: -moz-linear-gradient(top, #ee79c3, #e64aad); background-image: -ms-linear-gradient(top, #ee79c3, #e64aad); background-image: -webkit-gradient(linear, 0 0, 0 100%, from(#ee79c3), to(#e64aad)); background-image: -webkit-linear-gradient(top, #ee79c3, #e64aad); background-image: -o-linear-gradient(top, #ee79c3, #e64aad); background-image: linear-gradient(top, #ee79c3, #e64aad); background-repeat: repeat-x; filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#ee79c3', endColorstr='#e64aad', GradientType=0); background-color: #eb66ba; background-image: -moz-linear-gradient(top, #ee79c3, #e64aad); background-image: -ms-linear-gradient(top, #ee79c3, #e64aad); background-image: -webkit-gradient(linear, 0 0, 0 100%, from(#ee79c3), to(#e64aad)); background-image: -webkit-linear-gradient(top, #ee79c3, #e64aad); background-image: -o-linear-gradient(top, #ee79c3, #e64aad); background-image: linear-gradient(top, #ee79c3, #e64aad); background-repeat: repeat-x; filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#ee79c3', endColorstr='#e64aad', GradientType=0); -webkit-box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.15), 0 1px 2px rgba(0, 0, 0, 0.05); -moz-box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.15), 0 1px 2px rgba(0, 0, 0, 0.05); box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.15), 0 1px 2px rgba(0, 0, 0, 0.05); -webkit-box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.15), 0 1px 2px rgba(0, 0, 0, 0.05); -moz-box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.15), 0 1px 2px rgba(0, 0, 0, 0.05); box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.15), 0 1px 2px rgba(0, 0, 0, 0.05); }
	.calendar .calendarHolder .calendarDates > div.selected a { color: #fff; text-shadow: 0 1px #6d0948; }
	.calendar.sb .calendarHolder { color: #454545; }
	.calendar.sb .calendarHolder a { color: #454545; }
	.calendar.sb .calendarHolder .calendarDates > div { background: #fff; }
	.calendar.sb .calendarHolder .calendarDates > div.selected { background-color: #eb66ba; background-image: -moz-linear-gradient(top, #ee79c3, #e64aad); background-image: -ms-linear-gradient(top, #ee79c3, #e64aad); background-image: -webkit-gradient(linear, 0 0, 0 100%, from(#ee79c3), to(#e64aad)); background-image: -webkit-linear-gradient(top, #ee79c3, #e64aad); background-image: -o-linear-gradient(top, #ee79c3, #e64aad); background-image: linear-gradient(top, #ee79c3, #e64aad); background-repeat: repeat-x; filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#ee79c3', endColorstr='#e64aad', GradientType=0); background-color: #eb66ba; background-image: -moz-linear-gradient(top, #ee79c3, #e64aad); background-image: -ms-linear-gradient(top, #ee79c3, #e64aad); background-image: -webkit-gradient(linear, 0 0, 0 100%, from(#ee79c3), to(#e64aad)); background-image: -webkit-linear-gradient(top, #ee79c3, #e64aad); background-image: -o-linear-gradient(top, #ee79c3, #e64aad); background-image: linear-gradient(top, #ee79c3, #e64aad); background-repeat: repeat-x; filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#ee79c3', endColorstr='#e64aad', GradientType=0); }
</style>
<table>
	<tr>
		<td><div class="calendar" id="calendarElementCalendarOne"></div></td>
		<td><div class="calendar" id="calendarElementCalendarTwo"></div></td>
	</tr>
	<?php if($showTime) : ?>
	<tr>
		<td>
			Time:
			<input type="text" id="calendarElementTimeFromHour">
			<input type="text" id="calendarElementTimeFromMinute">
		</td>
		<td>
			Time:
			<input type="text" id="calendarElementTimeToHour">
			<input type="text" id="calendarElementTimeToMinute">
		</td>
	</tr>
	<?php endif; ?>
	<tr>
		<td><input type="button" value="Cancel" id="calendarElementButtonCancel"></td>
		<td><input type="button" value="Send" id="calendarElementButtonSubmit"></td>
	</tr>
</table>

<script>
	function calendarElementSendSubmitCallback (obj) {
		<?php echo $onSubmit;?>(obj);
	}
	function calendarElementSendCancelCallback () {
		<?php echo $onCancel;?>();
	}
</script>
<?php $this->Html->script('/common/js/elements/datepicker.js', array('block' => 'footerScript')); ?>