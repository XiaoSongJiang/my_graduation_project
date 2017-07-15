/*
@功能：头部js
*/
/* 注意，要在页面中先引入jquery*/
$(function(){
	//搜索框，注意此处，获取文本框的默认值使用defaultValue属性，但是只能通过this.defaultValue，不能使用$(this).defalutValue。
	$(".search_form .txt").focus(function(){
		if ($(this).val() == this.defaultValue){
			$(this).val("").css({color:"#333"});
		}	
	}).blur(function(){
		if ($(this).val() == ""){
			$(this).val(this.defaultValue).css({color:"#999"});
		}
	});
	
	//导航菜单效果
	$(".cat").hover(function(){
		$(this).find(".cat_detail").show();
		$(this).find("h3").addClass("on");
	},function(){
		$(this).find(".cat_detail").hide();
		$(this).find("h3").removeClass("on");
	});

	//非首页，导航菜单显隐效果
	
	$(".cat1").hover(function(){
		$(".cat1 .cat_hd").addClass("on").removeClass("off");
		$(".cat1 .cat_bd").show();
	},function(){
		$(".cat1 .cat_hd").addClass("off").removeClass("on");
		$(".cat1 .cat_bd").hide();
	});
	
	//头部点击切换选中的效果,初始化时就设置为0=>首页
	if(localStorage.lastindex == undefined){
		localStorage.lastindex = 0;
	}
	$(".navitems ul li:eq("+localStorage.lastindex+")").addClass("current");
	
	$(".navitems ul li").click(function(){
		localStorage.lastindex = $(this).index();
		
		$(this).siblings().each(function(k,v){
			$(v).removeClass("current");
			
		});
		
		$(this).addClass("current");
		
	});
	
});
