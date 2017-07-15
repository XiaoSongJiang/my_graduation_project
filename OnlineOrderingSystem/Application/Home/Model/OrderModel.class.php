<?php
namespace Home\Model;
use Think\Model;
class OrderModel extends Model 
{
   
    
	// 表单允许提交的字段
	protected $insertFields = array('address','delivery_method','pay_method','order_foods');
	protected $_validate = array(
		array('address', 'require', '收货人信息id不能为空！', 1, 'regex', 3),
		array('pay_method', 'require', '支付方式不能为空！', 1, 'regex', 3),
		array('delivery_method', 'require', '发货方式不能为空！', 1, 'regex', 3),
	);
	protected function _before_insert(&$data, $option)
	{
	    $foodsModel = M("Foods");
	    $total_price = 0; // 菜品总价钱
	    //处理立即购买
	    if (!empty(I("post.buy_now"))){
	        // 加锁-> 高并发下单时，库存量会出现混乱的问题，加锁来解决
	        $this->fp = fopen('./order.lock', 'r');
	        flock($this->fp, LOCK_EX);
	        // 取出这件菜品的库存量
	        $foods_inventory = $foodsModel->field('inventory')->where(array(
	            'foods_id' => array('eq', I('post.foods_id'))
	        ))->find();
	        if($foods_inventory['inventory'] < I('post.foods_num'))
	        {
	            $this->error = '菜品库存量不足无法下单';
	            return FALSE;
	        }
	        //计算总价
	        $total_price += I('post.foods_num') * I('post.foods_price');
	    } else {
	        // 判断购物车中是否有商品
	        $cartModel = D('Home/Cart');
	        $cartData = $cartModel->showCartList();
	        if(count($cartData) == 0)
	        {
	            $this->error = '购物车有菜品才能下单';
	            return FALSE;
	        }
	        // 循环购物车中每件菜品检查库存量够不够，并且计算总价
	        // 加锁-> 高并发下单时，库存量会出现混乱的问题，加锁来解决
	        $this->fp = fopen('./order.lock', 'r');
	        flock($this->fp, LOCK_EX);
	        
	        
	        $buy_ids = session('buy_ids');
	        foreach ($cartData as $k => $v)
	        {
	            // 判断这件菜品有没有被选择
	            if(!in_array($v['foods_id'], $buy_ids))
	                continue;
	            // 取出这件菜品的库存量
	            $foods_inventory = $foodsModel->field('inventory')->where(array(
	                'foods_id' => array('eq', $v['foods_id']),
	            ))->find();
	            if($foods_inventory['inventory'] < $v['foods_num'])
	            {
	                $this->error = '菜品库存量不足无法下单';
	                return FALSE;
	            }
	            // 计算总价
	            $total_price += $v['price'] * $v['foods_num'];
	        }
	    }
	    
		// 下单前把定单的其他信息补就即可
		$data['member_id'] = session("member_id");
		$data['addtime'] = time();
	    $address_id = $data["address"];
	    $addressModel = M("MemberAddress");
	    $address_now = $addressModel -> find($_POST['address']);
	    $data['receiver_address'] = $address_now['address'];
	    $data['receiver_name'] = $address_now['receiver'];
	    $data['receiver_mobile'] = $address_now['receiver_mobile'];
	    unset($data['address']);
	    $data['total_price'] = $total_price;
		// 启用事务
		mysqli_query('START TRANSACTION');
	}
	protected function _after_insert($data, $option)
	{
	    
	    //再把订单对应的菜品存入表中
	    $orderfoodsModel = M("OrderFoods");
	    $order_foods = I('post.order_foods');
	    foreach ($order_foods as $key => $val){
	        $order_foods_one = explode("-", $val);
	        //减少对应菜品的库存数量
	        $foodsModel = M("Foods");
	        $res = $foodsModel -> where(array(
	            "foods_id" => $order_foods_one[0]
	        )) -> setDec('inventory', $order_foods_one[2]);
	        if ($res === false){
	            mysqli_query('ROLLBACK');
	            return FALSE;
	        }
	        //再把订单对应的菜品存入订单菜品表中
	        $last_insert_id = $orderfoodsModel ->add(array(
	            'order_id' => $data['order_id'],
	            "member_id" => session("member_id"),
	            "foods_id" => $order_foods_one[0],
	            "foods_price" => $order_foods_one[1],
	            "foods_num" => $order_foods_one[2]
	        ));
	        if ($last_insert_id === false){
	            mysqli_query('ROLLBACK');
	            return FALSE;
	        }
	        
	    }
	    mysqli_query('COMMIT'); // 提交事务
	    // 释放锁
	    flock($this->fp, LOCK_UN);
	    fclose($this->fp);
	    // 清空购物车中所选择的商品
	    $cartModel = D('Home/Cart');
	    $cartModel->clearDb();
	    // 把SESSION保存的勾选的数据删除
	    session('buy_ids', null);
	}
	protected function _before_delete($options){
	    //删除订单菜品表中的数据
	    $orderfoodsModel = M("OrderFoods");
	    $res = $orderfoodsModel -> where("order_id=".$options['where']['order_id'])->delete();
	    
	}
	// 设置定单为已支付的状态
	public function setPaid($order_id){
		// 更新定单的状态为已支付的状态
		$res = $this->where(array('order_id'=>array('eq', $order_id)))->setField('pay_status', 1);
		// 增加会员的经验值和积分 - 查询菜品对应的经验和积分值
		$orderfoodsModel = M("OrderFoods");
		$points_xp = $orderfoodsModel -> field("b.points,b.xp,a.foods_num")->alias("a")->join("left join yd_foods b on a.foods_id = b.foods_id")->where("a.order_id = $order_id")->select();
		
		$total_points = 0;
		$total_xp = 0;
		foreach ($points_xp as $key => $val ){
		    $total_points += $val['points'] * $val['foods_num'];
		    $total_xp += $val['xp'] * $val['foods_num'];
		}
		$member_id = session("member_id");
		$ret = $this->execute('UPDATE yd_member SET points=points+'.$total_points.',xp=xp+'.$total_xp.' WHERE member_id='.$member_id);
		//增加菜品总销量
	   $total_sales_info = $orderfoodsModel -> field("b.total_sales,a.foods_num,a.foods_id")->alias("a")->join("left join yd_foods b on a.foods_id = b.foods_id")->where("a.order_id = $order_id")->select();
	   foreach ($total_sales_info as $k => $v){
	       $res1 = $this->execute('UPDATE yd_foods SET total_sales=total_sales+'.$v['foods_num'].' WHERE foods_id='.$v['foods_id']);
	       if ($res1 === FALSE){
	           return FALSE;
	       }
	   }
	   
	   //插入数据到餐厅销售情况表中
	   $restsalesModel = M("RestaurantSales");
	   $nowtime = time();
	   $foods_ids = $orderfoodsModel -> field("a.foods_id,a.foods_num,b.restaurant_id")->alias("a")->join("left join yd_foods b on a.foods_id = b.foods_id")->where("a.order_id = $order_id")->select();
	   if ($foods_ids){
	       foreach ($foods_ids as $key => $val){
	           //查找菜品所在餐厅
	           $restModel = M("Restaurant");
	           $rest_id = $restModel->find($val['restaurant_id'])['restaurant_id'];
	           //判断在餐厅销售情况表是否有此菜品的记录
	           $is_exist = $restsalesModel -> where("foods_id={$val['foods_id']}") -> find();
	           if ($is_exist){
	               $foods_date = $is_exist['sales_time'];
	               $prev = getdate($foods_date);
	               $now = getdate($nowtime);
	               if($prev['year'] == $now['year'] && $prev['yday'] == $now['yday']){
	                   $restsalesModel->execute("update yd_restaurant_sales set num=num+".$val['foods_num']." where foods_id=".$val['foods_id']);
	               }else{
	                   $restsalesModel->execute("insert into yd_restaurant_sales value($rest_id,{$val['foods_id']},{$val['foods_num']},$nowtime)");
	               }
	           }else{
	               $restsalesModel->execute("insert into yd_restaurant_sales value($rest_id,{$val['foods_id']},{$val['foods_num']},$nowtime)");
	           }
	       }
	   }
	   
	   if ($res !== FALSE && $ret !== FALSE){
	       return TRUE;
	   }else{
	       return FALSE;
	   }
	   
	}
	public function cancelPaid($order_id){
	    //增加菜品库存量
	    $orderfoodsModel = M("OrderFoods");
	    $data = $orderfoodsModel->where(array("order_id"=>array("eq",$order_id)))->select();
	    
	    foreach ($data as $key => $val){
	        $foodsModel = M("Foods");
	        $res = $foodsModel -> where(array(
	            "foods_id" => $val['foods_id']
	        )) -> setInc('inventory', $val['foods_num']);
	    }
	    //设置订单为取消状态
	    $res = $this->where(array('order_id'=>array('eq', $order_id)))->setField('pay_status', 2);
	    return $res;
	}
	/**
	 * 用户订单展示信息 ()分为订餐用户id和系统管理员id
	 */
	public function showOrderList($id){
	    $where = array();
	    //按交易状态
	    $pay_status = I("get.pay_status",-1);
	    if ($pay_status != -1){
	        $where['a.pay_status'] = array("eq",$pay_status);
	    }
	    
	    /***************************由于这三个涉及到同一个字段，故同时处理****************************/
	    //按订单号搜索
	    $order_id = I("get.order_id");
	    $restaurant_name = I("get.restaurant_name");
	    $foods_name = I("get.foods_name");
	    
	    if ($restaurant_name){
	        $restaurantModel = M("Restaurant");
	        $order_ids = $restaurantModel->field("c.order_id")
	        ->alias("a")
	        ->join("left join yd_foods b on a.restaurant_id = b.restaurant_id")
	        ->join("left join yd_order_foods c on b.foods_id = c.foods_id")
	        ->where("a.restaurant_name = '".$restaurant_name."'")
	        ->select();
	        if (empty($order_ids)){
	             $this->error = "无此餐厅";
	             return false;
	        }
	        $temp = array();
	        foreach ($order_ids as $k => $v){
	            $temp[] = $v['order_id'];
	        }
	        
	    }
	    
	    //按菜品名称搜索
	     
	    $foodsModel = M("Foods");
	    if ($foods_name){
	        $order_ids = $foodsModel ->field("b.order_id")
	        ->alias("a")
	        ->join("left join yd_order_foods b on a.foods_id = b.foods_id")
	        ->where("a.foods_name = '".$foods_name."'")
	        ->select();
	        $arr = array();
	        foreach ($order_ids as $k => $v){
	            $arr[] = $v['order_id'];
	        }
	    }
	    //分情况处理
	    //都不存在
	    if (!$order_id && !$restaurant_name && !$foods_name){
	        
	    }
	    //只存在order_id
	    if ($order_id && !$restaurant_name && !$foods_name){
	        $where['a.order_id'] = array("eq",$order_id);
	    }
	    //只存在restaurant_name
	    if (!$order_id && $restaurant_name && !$foods_name){
	        $where['a.order_id'] = array("in",$temp);
	    }
	    //只存在$oods_name
	    if (!$order_id && !$restaurant_name && $foods_name){
	        $where['a.order_id'] = array("in",$arr);
	    }
	    
	    if ($order_id && $restaurant_name && !$foods_name){
	           if (!empty($temp)){
	                if(!in_array($order_id, $temp)){
	                    $where['a.order_id'] = array("eq",-1);
	                }else{
	                    $where['a.order_id'] = array("eq",$order_id);
	                }
	            }else{
	                $where['a.order_id'] = array("eq",-1);
	            }
	    }
	    
	    if ($order_id && !$restaurant_name && $foods_name){
	           if (!empty($arr)){
	                if(!in_array($order_id, $arr)){
	                    $where['a.order_id'] = array("eq",-1);
	                }else{
	                    $where['a.order_id'] = array("eq",$order_id);
	                }
	            }else{
	                $where['a.order_id'] = array("eq",-1);
	            }
	    }
	    
	    if (!$order_id && $restaurant_name && $foods_name){
	       
	        if (!empty($arr) && !empty($temp)){
	            
	           $unique_arr = array_unique((array_intersect($temp,$arr)));
	           $where['a.order_id'] = array("in",$unique_arr);
	        }else{
	            $where['a.order_id'] = array("eq",-1);
	        }
	       
	    }
	    
	    if ($order_id && $restaurant_name && $foods_name){
	       if (!empty($arr) && !empty($temp)){
	            
	           $unique_arr = array_unique((array_intersect($temp,$arr)));
	           if (in_array($order_id, $unique_arr)){
	               $where['a.order_id'] = array("eq",$order_id);
	           }else{
	               $where['a.order_id'] = array("eq",-1);
	           }
	           
	        }else{
	            $where['a.order_id'] = array("eq",-1);
	        }
	    }


	   
	    /*************************************************************************/
	    //按订单价格搜索
	    $min_price = I("get.min_price");
	    $max_price = I("get.max_price");
	    if ($min_price){
	        $where['a.total_price'] = array("egt","$min_price");
	    }
	    if($max_price){
	        $where['a.total_price'] = array("elt","$max_price");
	    }
	    if ($min_price && $max_price){
	        $where['a.total_price'] = array("between",array($min_price,$max_price));
	    }
	    //按下单日期搜索
	    $start_addtime = I("get.start_dealtime");
	    $end_addtime = I("get.end_dealtime");
	    
	    if ($start_addtime && $end_addtime){
	        $where['a.addtime'] = array("between",array(strtotime("$start_addtime 00:00:01"),strtotime("$end_addtime 23:59:59")));
	    }
	    elseif ($start_addtime){
	        $where['a.addtime'] = array("egt",strtotime("$start_addtime 00:00:01"));
	    }
	    elseif($end_addtime){
	        $where['a.addtime'] = array("elt",strtotime("$end_addtime 23:59:59"));
	    }
	    if($id != -1){
	        $where['a.member_id'] = array("eq",session("member_id"));
	    }
	    $totalRows = $this->alias("a")->where($where)->count();
	    if($restaurant_name){
	        $where['d.restaurant_name'] = array("eq","$restaurant_name");
	    }
	    
	    if ($totalRows>0){
	        $Page = setPage($totalRows,4);
            $show = $Page->show();// 分页显示输出
	        // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
    	    $order_all = $this->field("a.*,group_concat(b.foods_price) foods_prices,group_concat(b.foods_num) foods_nums,group_concat(c.foods_name) foods_names,group_concat(c.foods_id) foods_ids,group_concat(c.logo) logos, d.restaurant_name")
                              ->alias('a')->join("left join yd_order_foods b on a.order_id = b.order_id")
                              ->join("left join yd_foods c on b.foods_id = c.foods_id")
                              ->join("left join yd_restaurant d on c.restaurant_id = d.restaurant_id")
                              ->group("a.order_id,c.restaurant_id")
                              ->order("a.order_id desc")
                              ->limit($Page->firstRow . ',' . $Page->listRows)
                              ->where($where)
                              ->select();
    	    return array(
	            'order_all' => $order_all,
	            'pages' => $show,   
	        );
	    }
	    
	}
	
	
}




















