//календарь
$(function(){
	$('#from, #to').datepicker({
		dateFormat: 'yy-mm-dd',
		changeMonth: true,
		numberOfMonths: 1,
		beforeShow: function(textbox, instance){
			marginLeftDatepicker = $(instance.dpDiv).outerWidth() - $(textbox).outerWidth();
			datePickerWidth = $(instance.dpDiv).outerWidth();
			var datepickerOffset = $(textbox).offset();
			if(datepickerOffset.left > datePickerWidth){
				instance.dpDiv.css({marginLeft: - marginLeftDatepicker + 'px'});
			}
			else{
				instance.dpDiv.css({marginLeft: 0});
			}
		}
	});
	from = $("#from").on("change", function(){
		to.datepicker("option", "minDate", getDate(this));
	}),
	to = $("#to").on("change", function(){
		from.datepicker("option", "maxDate", getDate(this));
	});
	function getDate(element){
		var date;
		try{
			date = $.datepicker.parseDate(dateFormat, element.value);
		}
		catch(error){
			date = null;
		}
		return date;
	}
});
//выбор даты pb
var val = $("#pb_range option:selected").val();
if(val == '12'){
	$('.range_selection').show();
	$('input[name="from"]').prop('disabled', false);
	$('input[name="to"]').prop('disabled', false);
}
else{
	$('.range_selection').hide();
	$('input[name="from"]').prop('disabled', true);
	$('input[name="to"]').prop('disabled', true);
}
$('#pb_range').change(function(){
	var val= $(this).val();
	if(val == '12'){
		$('.range_selection').show();
		$('input[name="from"]').prop('disabled', false);
		$('input[name="to"]').prop('disabled', false);
	}
	else{
		$(".range_submit").hide();
		$('.range_selection').hide();
		$('input[name="from"]').prop('disabled', true);
		$('input[name="to"]').prop('disabled', true);
	}
});
if(window.location.search.match("range=12")){
	$(".range_submit").show();
}
$("#pb_range").change(function(){
	if($(this).val() != 12){
		this.form.submit();
	}
	else{
		$(".range_submit").show();
	}
});
//выбор даты статистики
$("#stat_date").change(function(){
	this.form.submit();
});
//подсветка пунктов меню
$(function(){
	$('.editor-menu [href]').each(function(){
		if(this.href == window.location.href){
			$(this).addClass('active');
		}
	});
});
//сортировка потоков
$(function(){
	$("#sorting").sortable({
		opacity: 0.7,
		update: function(event, ui){
			var data = $(this).sortable("serialize");
			var urlParams = new URLSearchParams(window.location.search);
			if(urlParams.get('g')){
				var res = true;
				if(cnf){
					res = confirm('Reorder streams?');
				}
				if(res){
					var g = urlParams.get('g');
					$.ajax({
						data: data,
						type: 'POST',
						url: 'files/reorder.php?g='+g,
						success: function(){
							var path = window.location.pathname;
							window.location.href=path+'?g='+g;
						}
					});
				}
			}
		}
	});
});