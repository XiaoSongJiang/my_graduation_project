

$(function(){
	
	//减少
	$(".reduce_num").click(function(){
		var amount = $(this).parent().find(".amount");
		if (parseInt($(amount).val()) <= 1){
			alert("菜品数量至少为1");
		} else{
			
			$(amount).val(parseInt($(amount).val()) - 1);
			// 先获取所在的tr
			var tr = $(this).parent().parent();
			var foods_id = tr.attr("foods_id");
			// 执行AJAX更新到服务器
			ajaxUpdateCartData(foods_id, $(amount).val());
		}
		//小计
		var subtotal = parseFloat($(this).parent().parent().find(".col3 span").text()) * parseInt($(amount).val());
		$(this).parent().parent().find(".col5 span").text(subtotal.toFixed(2));
		//总计金额
		var total = 0;
		$(".col5 span").each(function(){
			total += parseFloat($(this).text());
		});

		$("#total").text(total.toFixed(2));
	});

	//增加
	$(".add_num").click(function(){
		var amount = $(this).parent().find(".amount");
		$(amount).val(parseInt($(amount).val()) + 1);
		
			// 先获取所在的tr
			var tr = $(this).parent().parent();
			var foods_id = tr.attr("foods_id");
			// 执行AJAX更新到服务器
			ajaxUpdateCartData(foods_id,  $(amount).val());
		//小计
		var subtotal = parseFloat($(this).parent().parent().find(".col3 span").text()) * parseInt($(amount).val());
		$(this).parent().parent().find(".col5 span").text(subtotal.toFixed(2));
		//总计金额
		var total = 0;
		$(".col5 span").each(function(){
			total += parseFloat($(this).text());
		});

		$("#total").text(total.toFixed(2));
	});

	//直接输入
	$(".amount").blur(function(){
		if (parseInt($(this).val()) < 1){
			alert("商品数量最少为1");
			$(this).val(1);
		}
		
			// 先获取所在的tr
			var tr = $(this).parent().parent();
			var foods_id = tr.attr("foods_id");
			// 执行AJAX更新到服务器
			ajaxUpdateCartData(foods_id, parseInt($(this).val()));
		//小计
		var subtotal = parseFloat($(this).parent().parent().find(".col3 span").text()) * parseInt($(this).val());
		$(this).parent().parent().find(".col5 span").text(subtotal.toFixed(2));
		//总计金额
		var total = 0;
		$(".col5 span").each(function(){
			total += parseFloat($(this).text());
		});

		$("#total").text(total.toFixed(2));

	});
	//删除
		$(".col6 a").click(function(){
			
			if(confirm("确认删除么?")){
				// 先获取所在的TR
				var tr = $(this).parent().parent();
				var foods_id = tr.attr("foods_id");
				
				// 执行AJAX更新到服务器
				ajaxUpdateCartData(foods_id, 0);
				tr.remove();
				var newTp = parseFloat($("#total").html()) - parseFloat(tr.find(".col5").find("span").html());
				$("#total").html(newTp.toFixed(2));
			}
			return false; 
	});
});