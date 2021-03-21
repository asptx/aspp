//главное меню
$(function(){
	var pull = $('#pull_main');
	menu_main = $('menu ul');
	menuHeight = menu_main.height();
	$(pull).on('click', function(e){
		e.preventDefault();
		menu_main.slideToggle();
	});
	$(window).resize(function(){
		var w = $(window).width();
		if(w > 320 && menu_main.is(':hidden')){
			menu_main.removeAttr('style');
		}
	});
});
//меню настройки группы
$(function(){
	var pull = $('#pull_setting');
	menu_set = $('set mob');
	menuHeight = menu_set.height();
	$(pull).on('click', function(e){
		e.preventDefault();
		menu_set.slideToggle();
	});
	$(window).resize(function(){
		var w = $(window).width();
		if(w > 320 && menu_set.is(':hidden')){
			menu_set.removeAttr('style');
		}
	});
});
//меню статистики
$(function(){
	var pull = $('#pull_statistics');
	menu_stat = $('stat mob');
	menuHeight = menu_stat.height();
	$(pull).on('click', function(e){
		e.preventDefault();
		menu_stat.slideToggle();
	});
	$(window).resize(function(){
		var w = $(window).width();
		if(w > 320 && menu_stat.is(':hidden')){
			menu_stat.removeAttr('style');
		}
	});
});
//табы в настройках потока
$(function(){
	var $tabs = $('.tab-container').accordionToTabs({
		breakpoint:'600px'
	});
});