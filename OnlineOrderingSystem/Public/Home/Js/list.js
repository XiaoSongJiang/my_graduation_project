

$(function(){
	$(".child h3").click(function(){
		$(this).toggleClass("on").parent().find("ul").toggle();
	});
});