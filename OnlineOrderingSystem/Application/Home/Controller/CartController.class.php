<?php
namespace Home\Controller;
use Tools\HomeBaseController;
class CartController extends HomeBaseController {
    /**
     * 添加商品到购物车
     */
	public function cartAdd(){
	    //dump($_POST);die;
		$cartModel = D('Home/Cart');
		$cartModel->addToCart(I('post.foods_id'),I('post.foods_num'));
		
		redirect(U('cartList'));
	}
	/**
	 * 购物车列表
	 */
	public function cartList(){
	    $cartModel = D('Home/Cart');
	    $cart_info = $cartModel->showCartList();
	    $this->assign("cart_info",$cart_info);
	    //计算出购物车商品总价
	    $wholetotal = 0;
	    foreach ($cart_info as $key=>$val){
	        $wholetotal += $cart_info[$key]["subtotal"];
	    }
	   
	    $this->assign("wholetotal",sprintf("%1.2f",$wholetotal));
	    $this->setHeadInfo("购物车", "购物车", "购物车",0,array("cart"),array("cart"));
	    $this->display();
	}
	public function updateCartByAjax(){
	    $foods_id = I("get.foods_id");
	    $foods_num = I("get.foods_num");
	    $cartModel = D("Home/Cart");
	    $cartModel->updateCartInfo($foods_id,$foods_num);
	}
	/**
	 * 处理立即购买
	 */
	public function buyNowOrder(){
	    
	    $now_buy_foods_id = I('post.foods_id');
	    $now_buy_foods_num = I('post.foods_num');
	    $now_buy_foods_price = I('post.now_shop_price');
	    if(!$now_buy_foods_id)
	    {
	         
	        $now_buy_foods_id = session('now_buy_foods_id');
	        
	        $now_buy_foods_num = session('now_buy_foods_num');
	        $now_buy_foods_price = session('now_buy_foods_price');
	        $foods_id = I("get.foods_id");
	        if($now_buy_foods_num < 1)
	            $this->error('菜品数量至少为1', U("Home/Index/foods/foods_id/$foods_id"));
	    }
	    else{
	        //未登录时选中的菜品id存在session中
	        session('now_buy_foods_id', $now_buy_foods_id);
	        session('now_buy_foods_num', $now_buy_foods_num);
	        session('now_buy_foods_price', $now_buy_foods_price);
	    }
	    //判断是否有会员登录
	    $member_id = session('member_id');
	    // 如果会员没有登录跳到登录页面，登录成功之后再跳回到这个页面
	    if(!$member_id)
	    {
	        // 把当前这个页面的地址存到cookie中，这样登录成功之后就跳回来了
	        cookie('return_url', U('buyNowOrder'));
	        redirect(U('Home/Member/login'));
	    }
	    // 如果是下单的表单就处理
	    if(IS_POST && isset($_POST['order_foods']))
	    {
	    
	        $orderModel = D('Home/Order');
	        if($orderModel->create(I('post.'), 1))
	        {
	             
	            if(!!$last_insert_id = $orderModel->add())
	            {
	                $this->success('下单成功！', U('orderSuccess?order_id='.$last_insert_id));
	                exit;
	            }
	        }
	        $this->error($orderModel->getError());
	    }
	    //获取地址信息
	    $memberaddressModel = D("Home/MemberAddress");
	    $member_address = $memberaddressModel->getMemberAddress($member_id);
	    $this->assign("member_address",$member_address);
	    //获取当前菜品的信息
	    $foodsModel = D("Admin/Foods");
	    $now_foods_info = $foodsModel->field('a.logo,a.foods_name,b.restaurant_name')
	                             ->alias("a")
                        	     ->join("left join yd_restaurant b on a.restaurant_id = b.restaurant_id")
                        	     ->find($now_buy_foods_id);
	    $this->assign("now_foods_info",$now_foods_info);
	    $this->assign("foods_num",$now_buy_foods_num);
	    $this->assign("foods_price",$now_buy_foods_price);
	    $this->assign("foods_id",$now_buy_foods_id);
	    //计算出当前商品总数和总价
	    $wholenum = $now_buy_foods_num;
	    $wholetotal = $now_buy_foods_num * $now_buy_foods_price;	 
	    $this->assign("wholenum",$wholenum);
	    $deliver_money = 10;
	    $this->assign("wholetotal",sprintf("%1.2f",$wholetotal));
	    $this->assign("deliver_money",$deliver_money);
	    $this->setHeadInfo('下订单', '下订单', '下订单', 0, array('fillin'));
	    $this->display();
	}
	/**
	 * 接收购物车菜品信息,确认订单信息
	 */
	public function confirmCartInfo(){
	    
	    $buy_ids = I('post.buy_ids');
	    // 先判断购物车表单中是否选择了至少一件菜品
	    if(!$buy_ids)
	    {
	        
	        $buy_ids = session('buy_ids');
	        if(!$buy_ids)
	            $this->error('必须先选择一份菜品', U('cartList'));
	    }
	    else{
	        //未登录时选中的菜品id存在session中
	        session('buy_ids', $buy_ids);
	    }
	       
	    //判断是否有会员登录
	    $member_id = session('member_id');
	    // 如果会员没有登录跳到登录页面，登录成功之后再跳回到这个页面
	    if(!$member_id)
	    {
	        // 把当前这个页面的地址存到cookie中，这样登录成功之后就跳回来了
	        cookie('return_url', U('confirmCartInfo'));
	        redirect(U('Home/Member/login'));
	    }
	    // 如果是下单的表单就处理
	   if(IS_POST && !isset($_POST['buy_ids']))
	    {
	       
	        $orderModel = D('Home/Order');
	        if($orderModel->create(I('post.'), 1))
	        {   
	            
	            if(!!$last_insert_id = $orderModel->add())
	            {
	                $this->success('下单成功！', U('orderSuccess?order_id='.$last_insert_id));
	                exit;
	            }
	        }
	        $this->error($orderModel->getError());
	    }
	    //获取地址信息
	    $memberaddressModel = D("Home/MemberAddress");
	    $member_address = $memberaddressModel->getMemberAddress($member_id);
	    $this->assign("member_address",$member_address);
	    $cartModel = D('Home/Cart');
	    if (I("post.buy_ids") != null){
	        $buy_ids =I("post.buy_ids");
	       
	    }else{
	        $buy_ids = session("buy_ids");
	    }
	    //session("buy_ids",null);
	    $cart_info = $cartModel->showCartList($buy_ids);
	    
	    $this->assign("cart_info",$cart_info);
	    
	    //计算出购物车商品总数和总价
	    $wholenum = 0;
	    $wholetotal = 0;
	    foreach ($cart_info as $key=>$val){
	        $wholenum += $cart_info[$key]["foods_num"];
	        $wholetotal += $cart_info[$key]["subtotal"];
	    }
	    $this->assign("wholenum",$wholenum);
	    $deliver_money = 10*count($cart_info);
	    $this->assign("wholetotal",sprintf("%1.2f",$wholetotal));
	    $this->assign("deliver_money",$deliver_money);
	    $this->setHeadInfo('下订单', '下订单', '下订单', 0, array('fillin'));
	    $this->display();
	}
	
	// 下单成功之后的页面
	public function orderSuccess(){
	    $this->setHeadInfo('下单成功', '下单成功', '下单成功', 0, array('order_success'));
	    $this->display();
	}
	/**
	 * 取消订单
	 */
	public function cancelOrder(){
	    $order_id = I("get.order_id");
	    $orderModel = D("Home/Order");
	    if($orderModel -> cancelPaid($order_id)){
	        $this->success("交易关闭！",U("Home/Cart/orderList"));
	    }
	}
	/**
	 * 支付账单
	 */
	public function payForOrder(){
	    $order_id = I("get.order_id");
	    $memberModel = D("Home/Member");
	    //支付成功返回true，否则返回false
	    $res = $memberModel->memberPayForOrder($order_id);
	    if ($res){
	       //把订单表中的支付状态设为已支付
	       $orderModel = D("Home/Order");
	       if($orderModel -> setPaid($order_id)){
	           $this->success("支付成功！",U("Home/Cart/orderList"));	       
	       }
	    }else{
	        $this->error($memberModel->getError());
	    }
	}
	/**
	 * 展示账单
	 */
	public function orderList(){
	    if (!session("member_id")){
	        cookie("return_url",U("orderList"));
	        $this->error("请先登录！",U("Home/Member/login"),1);
	    }
	    $orderModel = D("Home/Order");
	    
	    $member_id = session("member_id");
	    $order_list_info = $orderModel -> showOrderList($member_id);
	    $order_info = $order_list_info['order_all'];
	    
	    foreach ($order_info as $key => $val){
	        $logos = explode(",", $val['logos']);
	        $foods_names = explode(",", $val['foods_names']);
	        $foods_ids = explode(",", $val['foods_ids']);
	        $foods_nums = explode(",", $val['foods_nums']);
	        $foods_prices = explode(",", $val['foods_prices']);
	      
            $order_info[$key]['logos'] = $logos;
            $order_info[$key]['foods_names'] = $foods_names;
            $order_info[$key]['foods_ids'] = $foods_ids;
            $order_info[$key]['foods_nums'] = $foods_nums;
            $order_info[$key]['foods_prices'] = $foods_prices;
	    }
	    $arr = array();
	    $arr1 = array();
	    foreach ($order_info as $k => $v){
	            if (count($v['foods_prices']) > 1){
	                $arr['receiver_name'] = $v['receiver_name'];
	                $arr['receiver_mobile'] = $v['receiver_mobile'];
	                $arr['receiver_address'] = $v['receiver_address'];
	                foreach ($v['foods_prices'] as $k1 => $v1){
	                    $arr['foods_price'] = $v1;
	                    $arr['foods_num'] = $v['foods_nums'][$k1];
	                    $arr['foods_name'] = $v['foods_names'][$k1];
	                    $arr['foods_id'] = $v['foods_ids'][$k1];
	                    $arr['logo'] = $v['logos'][$k1];
	                    $order_info[$k]['foods_list'][] = $arr;
	                }
	            }else{
	                $arr1['receiver_name'] = $v['receiver_name'];
	                $arr1['receiver_mobile'] = $v['receiver_mobile'];
	                $arr1['receiver_address'] = $v['receiver_address'];
	                $arr1['foods_price'] = $v['foods_prices'][0];
	                $arr1['foods_num'] = $v['foods_nums'][0];
	                $arr1['foods_name'] = $v['foods_names'][0];
	                $arr1['foods_id'] = $v['foods_ids'][0];
	                $arr1['logo'] = $v['logos'][0];
	                $order_info[$k]['foods_list'][] = $arr1;
	            }
	            
	            
	        
	    }
	    
	    $this->assign("order_list_info",$order_info);
	    $this->assign("pages",$order_list_info['pages']);
	    $this->setHeadInfo('订单列表', '订单列表', '订单列表', 0, array('user','home','orderlist'));
	    $this->display();
	}
	/**
	 * 新增收货地址
	 */
	public function addressAdd(){

	    $member_id = session("member_id");
	    $addressModel = D("Home/MemberAddress");
	    if (IS_POST) {
	        $data = $addressModel->validate($addressModel->_address_validate)->create(I('post.'), 1);
	        if($data){
	            //插入收货地址前，须判断当前用户收货地址是否拥有唯一默认值
	             
	            $address_default = $addressModel->field("is_default")->where("member_id = $member_id")->select();
	            foreach ($address_default as $key => $val){
	                if ($val['is_default'] == $data['is_default']) {
	                    //把数据表中所有的is_default字段设置为0
	                    $res = $addressModel->where("member_id = $member_id")->setField("is_default","0");
	                    if ($res) {
	                        break;
	                    }
	                }
	            }
	
	            if(!!$last_insert_id = $addressModel->add()) {
	                //查询出新纪录并返回ajax处理
	                $insert_rescord = $addressModel->find($last_insert_id);
	                $insert_rescord['ok'] = 1;
	                
	                echo json_encode($insert_rescord);
	                exit;
	            }
	        }
	
	        echo json_encode(array(
	            "ok" => 0,
	            "error" => $addressModel->getError()
	        ));
	    }
	}
	/**
	 * 修改收货地址
	 */
	public function addressEdit(){
	    $member_id = session("member_id");
	
	    $addressModel = D("Home/MemberAddress");
	    if (IS_POST){
	        $data = $addressModel->validate($addressModel->_address_validate)->create(I('post.'), 2);
	
	        if ($data){
	            //修改收货地址前，须判断当前用户收货地址是否拥有唯一默认值
	
	            $address_default = $addressModel->field("is_default")->where("member_id = $member_id")->select();
	            foreach ($address_default as $key => $val){
	                if ($val['is_default'] == $data['is_default']) {
	                    //把数据表中所有的is_default字段设置为0
	                    $res = $addressModel->where("member_id = $member_id")->setField("is_default","0");
	                    if ($res) {
	                        break;
	                    }
	                }
	            }
	            if($addressModel->save() !== FALSE ) {
	                //查询出新纪录并返回ajax处理
	                $update_rescord = $addressModel->find($data['address_id']);
	                $update_rescord['ok'] = 1;
	                
	                echo json_encode($update_rescord);
	                exit;
	            }
	        }
	         
	        echo json_encode(array(
	            "ok" => 0,
	            "error" => $addressModel->getError()
	        ));
	
	    }
	}
	/**
	 * 删除收货地址
	 */
	public function addressDelete(){
	    
	    $member_id = session("member_id");
	    $address_id = I("get.address_id");
	
	    $addressModel = M("MemberAddress");
	    $res = $addressModel->where("member_id = $member_id and address_id = $address_id")->delete();
	    if ($res !== false){
	        echo "ok";
	    }else{
	        echo $addressModel->getError();
	    }
	}
	/**
	 * 设置默认收货地址
	 */
	public function changeAddressDefault(){
	    //接受参数
	    $member_id = session("member_id");
	    $address_id = I("get.address_id");
	
	    $addressModel = M("MemberAddress");
	    $addressModel->where("member_id = $member_id")->setField("is_default","0");
	    $res = $addressModel->where("member_id = $member_id and address_id = $address_id")->setField("is_default","1");
	    if ($res !== false){
	        echo "ok";
	    }else{
	        echo $addressModel->getError();
	    }
	}
}




















