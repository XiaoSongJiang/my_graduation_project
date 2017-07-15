<?php
namespace Home\Model;
use Think\Model;
class CartModel extends Model 
{
	// 加入购物车
	public function addToCart($foods_id,$foods_num = 1)
	{
	    
		$member_id = session('member_id');
		// 如果登录了就加入到数据库中，否则就加入到COOKIE中
		if($member_id)
		{
		    //先取出菜品的相关信息
		    $foodsModel = M("Foods");
		    $foods_info = $foodsModel->find($foods_id);
			$cartModel = M('Cart');
			$has = $cartModel->where(array(
				'member_id' => array('eq', $member_id),
				'foods_id' => array('eq', $foods_id),
			))->find();
			// 判断是否菜品已经存在
			if($has)
				$cartModel->where('cart_id='.$has['cart_id'])->setInc('foods_num', $foods_num);
			else 
				$cartModel->add(array(
				    'member_id' => $member_id,
					'restaurant_id' => $foods_info['restaurant_id'],
				    'foods_id' => $foods_id,
				    'foods_name' => $foods_info['foods_name'],
					'foods_num' => $foods_num,
					'logo' => $foods_info['logo']
				));
		}
		else 
		{
			// 先从COOKIE中取出购物车的数组
			$cart = isset($_COOKIE['cart']) ? unserialize($_COOKIE['cart']) : array();
			// 把菜品加入到这个数组中
			$key = $foods_id;
			// 先判断数组中有没有这件菜品
			if(isset($cart[$key]))
				$cart[$key] += $foods_num;
			else
				$cart[$key] = $foods_num;
			// 把这个数组存回到cookie
			
			
			cookie('cart', serialize($cart));
			
		}
	}
	// 购物车列表
	public function showCartList($selectd = "all_ids"){
	    //先判断用户是否登录，如果登录了则从数据库中取数据，否则在cookie中取数据
	    $member_id = session("member_id");
	    $cartModel = M('Cart');
	    if ($member_id){
	        if($selectd == 'all_ids'){
	            
	            $_cart = $cartModel->where(array('member_id'=>array('eq', $member_id)))->select();      
	        } else{
	            $_cart = $cartModel->where(array('member_id'=>array('eq', $member_id),"foods_id" => array("in",$selectd)))->select();
	        }
	           
	         
	    }else{
	        $_cart_ = isset($_COOKIE['cart']) ? unserialize($_COOKIE['cart']) : array();
	        //把cookie中的数据转化为二维数组，保证和数据库取出的格式一致
	        $_cart = array();
	        
	        foreach ($_cart_ as $key => $val){
	           
	            $_cart[] = array(
	                "foods_id" => $key,
	                "foods_num" => $val,
	                "member_id" => 0
	            );
	        }
	    }
	    //循环购车中的数据取出菜品的详细信息
	    $foodsModel = D("Admin/Foods");
	   
	    
	    foreach ($_cart as $k => $v)
	    {
	        $foods_info = $foodsModel->field('a.logo,a.foods_name,b.restaurant_name')->alias("a")->join("left join yd_restaurant b on a.restaurant_id = b.restaurant_id")->find($v['foods_id']);
	        $_cart[$k]['foods_name'] = $foods_info['foods_name'];
	        $_cart[$k]['logo'] = $foods_info['logo'];
	        $_cart[$k]['restaurant_name'] = $foods_info['restaurant_name'];
	        // 计算会员价格
	        $_cart[$k]['price'] = $foodsModel->getFoodsPrice($v['foods_id']);
	        $_cart[$k]['price'] = sprintf("%01.2f",$_cart[$k]['price']);
	       
	        $_cart[$k]["subtotal"] = $_cart[$k]['price']*$_cart[$k]['foods_num'];
	        $_cart[$k]['subtotal'] = sprintf("%01.2f",$_cart[$k]['subtotal']);
	    }
	    
	    return $_cart;
	}
	//把cookie中的购物车数据转移到数据库
	public function moveDataToDb(){
	    $member_id = session('member_id');
	    if($member_id)
	    {
	        // 先从COOKIE中取出购物车的数据
	        $cart = isset($_COOKIE['cart']) ? unserialize($_COOKIE['cart']) : array();
	        if($cart)
	        {
	            // 循环每件菜品加入到数据库中
	            foreach ($cart as $k => $v)
	            {
	                
	                $this->addToCart($k,$v);
	            }
	            // 清空COOKIE中的数据
	            cookie('cart', null);
	        }
	    }
	}
	/**
	 * 更新修改购物车中的数据
	 * @param int $foods_id 菜品id
	 * @param int $foods_num 菜品的数量
	 */
	public function updateCartInfo($foods_id,$foods_num){
	    $member_id = session("member_id");
	    if($member_id){
	        if ($foods_num == 0){
	            //删除该菜品记录
	            $this->where(array(
	                "foods_id"=>array("eq",$foods_id),
	                "member_id"=>array("eq",$member_id),
	            ))->delete();
	        }else{
	            $this->where(array(
	                "foods_id"=>array("eq",$foods_id),
	                "member_id"=>array("eq",$member_id),
	            ))->setField("foods_num",$foods_num);
	        }
	    }else{
	        //先从cookie中取出数据
	        $cart_info = unserialize($_COOKIE['cart']);
	        if ($foods_num == 0){
	            unset($cart_info[$foods_id]);
	        }else{
	            $cart_info[$foods_id] = $foods_num;
	        }
	        //把更改的数据重新写入cookie
	        
			cookie('cart', serialize($cart_info));
	    }
	}
	/**
	 * 清空购物车中的选中数据
	 */
	public function clearDb()
	{
	    $member_id = session('member_id');
	    if($member_id)
	    {
	        // 取出勾选的菜品
	        $buy_ids = session('buy_ids');
	        $cartModel = M('Cart');
	        // 循环勾选 的菜品进行删除
	        foreach ($buy_ids as $k => $v)
	        {
	            $cartModel->where(array('member_id'=>array('eq', $member_id), 'foods_id'=>array('eq', $v)))->delete();
	        }
	    }
	}
	
}


















