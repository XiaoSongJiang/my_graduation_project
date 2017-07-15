<?php

 namespace Admin\Model;
 use Think\Model;
 class FoodsModel extends Model{
    protected $insertFields = array('foods_name','cate_id','shop_price','points','xp',
                                    'is_promote','promote_price','promote_start_time',
                                    'promote_end_time','is_hot','is_new','is_best','is_on_sale',
                                    'sort_num','is_delete','foods_desc','is_promote','inventory'
        
    );
	protected $updateFields = array('foods_id','foods_name','cate_id','shop_price','points','inventory',
	                                'xp','is_promote','promote_price','promote_start_time','promote_end_time',
	                                'is_hot','is_new','is_best','is_on_sale','sort_num','is_delete','foods_desc','is_promote'
	    
	);
	protected $_validate = array(
		array('foods_name', 'require', '菜品名称不能为空！', 1, 'regex', 3),
		array('foods_name', '1,45', '菜品名称的值最长不能超过 45 个字符！', 1, 'length', 3),
		array('cate_id', 'require', '主分类名不能为空！', 1, 'regex', 3),
		array('cate_id', 'number', '主分类的id必须是一个整数！', 1, 'regex', 3),
		array('shop_price', 'currency', '本店价必须是货币格式！', 2, 'regex', 3),
		array('points', 'require', '赠送积分不能为空！', 1, 'regex', 3),
		array('xp', 'number', '赠送积分必须是一个整数！', 1, 'regex', 3),
		array('xp', 'require', '赠送经验值不能为空！', 1, 'regex', 3),
		array('xp', 'number', '赠送经验值必须是一个整数！', 1, 'regex', 3),
		array('promote_price', 'currency', '促销价必须是货币格式！', 2, 'regex', 3),
	    array('promote_price', 'require', '促销价不能为空！', 2, 'regex', 3),
		array('promote_start_time', '/^\d{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2]\d|3[0-1])$/', '促销开始时间格式不正确！', 0, 'regex', 3),
		array('promote_end_time', '/^\d{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2]\d|3[0-1])$/', '促销开始时间格式不正确！', 0, 'regex', 3),
		array('is_hot', 'number', '是否热卖必须是一个整数！', 2, 'regex', 3),
		array('is_new', 'number', '是否新品必须是一个整数！', 2, 'regex', 3),
		array('is_best', 'number', '是否精品必须是一个整数！', 2, 'regex', 3),
		array('is_on_sale', 'number', '是否上架：1：上架，0：下架必须是一个整数！', 2, 'regex', 3),
		array('sort_num', 'number', '排序数字必须是一个整数！', 2, 'regex', 3),
		array('is_delete', 'number', '是否放到回收站：1：是，0：否必须是一个整数！', 2, 'regex', 3),
	);
     // TP在调用add方法之前会自动调用这个方法，我们可以把在插入数据库之前要执行的代码写到这里
     // 第一个参数：就是表单中的数据（要插入到数据库中的数据）是一个一维数组
     // 第二个参数：额外信息，：当前模型对应的实际的表名是什么
     // 说明：在这个函数中要改变这个函数外部的$data，需要按钮引用传递，否则修改也无效
     // 说明：如果return false是指控制器中的add方法返回了false
     protected function _before_insert(&$data, $option)
     {
         $data['restaurant_id'] = session("restaurant_id");
         // 获取当前时间
         $data['addtime'] = time();
         //如果促销时间存在，将促销时间转化为时间戳
         if (isset($data["promote_price"])){
             $data['is_promote'] = $_POST['is_promote'];
             $data['promote_start_time'] = strtotime("{$_POST['promote_start_time']}");
             $data['promote_end_time'] = strtotime("{$_POST['promote_end_time']}");
         }
         
         //菜品logo上传处理
         if(isset($_FILES['logo']) && $_FILES['logo']['error'] == 0){
             $res = uploadOne("logo", "Foods",array(
                 array(150,150)
             ));
            
             if($res["ok"] == 1){
                 $ori_logo_name = ltrim($res["images"][0],'.');
                 $sm_logo_name = ltrim($res["images"][1],'.');
                
                 $data['logo'] = $ori_logo_name;
                 $data['sm_logo'] = $sm_logo_name;
               
                 
             }else{
                 $this->error = $res["error"];
                 
                 return false;
             }
         }  
             
         
   }
   /**
    * !CodeTemplates.overridecomment.nonjd!
    * @see \Think\Model::_after_insert()
    */ 
   public  function _after_insert($data, $options){
       /**************** 处理菜品的扩展分类 ********************/
       //获得扩展分类的表单信息
       $restaurant_id = session("restaurant_id");
       $ext_cate_id = I("post.ext_cate_id");
       if ($ext_cate_id){
          
           $foods_ext_cateModel = M("FoodsExpandCate");
           foreach ($ext_cate_id as $key => $val){
               // 如果分类为空就跳过处理下一个
               if(empty($val)){
                   continue;
               }
               $foods_ext_cateModel->add(array(
                   'foods_id' => $data['foods_id'],
                   'cate_id' => $val,
                   'restaurant_id'=> $restaurant_id
               ));
               
           }
       }
       /********************处理会员价格***************************/
       //获会员等级价格的表单信息
       $member_price = I("post.member_price");
       
       if ($member_price){
           $memberpriceModel = M("MemberPrice");
           foreach ($member_price as $key => $val){
               
              
               if(empty($val))
                   continue;
               $memberpriceModel->add(array(
                   'foods_id' => $data['foods_id'],
                   'level_id' => $key,
                   'price'    => $val
               ));
           }
       }
        /********************处理菜品相册***************************/
        //处理表单中的file=pics[]
        
        
        if (hasImage("pics")){
            
            $foodspicsModel = M('FoodsPics');
            //由于上传时，菜品logo和菜品相册图片同时上传，$_FILE是一个三维数组，故把上传的图片做成一个二维数组
            $pics = array();
            foreach ($_FILES['pics']['name'] as $key => $val){
                if ($_FILES['pics']['size'][$key] != 0){
                    $pics[] = array(
                        'name' => $val,
                        'type' => $_FILES['pics']['type'][$key],
                        'tmp_name' => $_FILES['pics']['tmp_name'][$key],
                        'error' => $_FILES['pics']['error'][$key],
                        'size' => $_FILES['pics']['size'][$key],
                    );
                }
            }
            $_FILES = $pics;
            
            //循环调用函数上传图片
            foreach ($_FILES as $key=>$val){
                $res = uploadOne($key, "FoodsPic",array(
                    array(150,150)
                ));
               
                if($res["ok"] == 1){
                    $foodspicsModel->add(array(
                        'foods_id' => $data['foods_id'],
                        'pic' => ltrim($res["images"][0],'.'),     // 原图存到pic字段
                        'sm_pic' => ltrim($res["images"][1],'.')  // 第一个缩略图存到sm_pic字段中
                    ));
                     
                }else{
                    $this->error = $res["error"];
                    return false;
                }
            }
        }
       
   }
    /**
     * !CodeTemplates.overridecomment.nonjd!
     * @see \Think\Model::_before_update()
     */
    protected function _before_update(&$data, $options){
        //如果促销价格存在，将促销时间转化为时间戳
        if (isset($data["promote_price"])){
            
            $data['is_promote'] = $_POST['is_promote'];
            $data['promote_start_time'] = strtotime("{$_POST['promote_start_time']}");
            $data['promote_end_time'] = strtotime("{$_POST['promote_end_time']}");
        }else{
            $data['is_promote'] = 0;
            
        }
        //菜品logo上传处理
         if(isset($_FILES['logo']) && $_FILES['logo']['error'] == 0){
             $res = uploadOne("logo", "foods",array(
                 array(150,150)
             ));
            
             if($res["ok"] == 1){
                 $ori_logo_name = ltrim($res["images"][0],'.');
                 $sm_logo_name = ltrim($res["images"][1],'.');
               
                 $data['logo'] = $ori_logo_name;
                 $data['sm_logo'] = $sm_logo_name;
                 //删除原图片
                 $logo = $this->field("logo,sm_logo")->find(I("get.id"));
                 deleteImage(array($logo['logo'],$logo["sm_logo"]
                 ));
                 
             }else{
                 $this->error = $res["error"];
                 
                 return false;
             }
         }  
              
    }
    //处理其他的信息
    protected function _after_update($data, $options){
        $restaurant_id = session("restaurant_id");
       /**************** 处理菜品的扩展分类 ********************/
       //获得扩展分类的表单信息
       $ext_cate_id = I("post.ext_cate_id");
       //删除原来的分类信息
       
       $foodsextcateModel = M("FoodsExpandCate");
       $foodsextcateModel->where(array('foods_id'=>array('eq', $options['where']['foods_id'])))->delete();
       if ($ext_cate_id){
           foreach ($ext_cate_id as $key => $val){
               // 如果分类为空就跳过处理下一个
               if(empty($val)){
                   continue;
               }
               $foodsextcateModel->add(array(
                   'foods_id' => $options['where']['foods_id'],
                   'cate_id' => $val,
                   'restaurant_id' => $restaurant_id
               ));
               
           }
       }
       /********************处理会员价格***************************/
       //获会员等级价格的表单信息
       $member_price = I("post.member_price");
       $memberpriceModel = M("MemberPrice");
       //删除原来的会员信息
       $memberpriceModel->where(array('foods_id'=>array('eq', $options['where']['foods_id'])))->delete();
       if ($member_price){
           
           foreach ($member_price as $key => $val){
               if(empty($val))
                   continue;
               $memberpriceModel->add(array(
                   'foods_id' => $options['where']['foods_id'],
                   'level_id' => $key,
                   'price'    => $val
               ));
           }
       }
       
       /********************处理菜品相册***************************/
       //处理表单中的file=pics[]
       
       
       if (hasImage("pics")){
       
           $foodspicsModel = M('FoodsPics');
           //把上传的图片做成一个二维数组
           $pics = array();
           foreach ($_FILES['pics']['name'] as $key=>$val){
               if ($_FILES['pics']['size'][$key] != 0){
                   $pics[] = array(
                       'name' => $val,
                       'type' => $_FILES['pics']['type'][$key],
                       'tmp_name' => $_FILES['pics']['tmp_name'][$key],
                       'error' => $_FILES['pics']['error'][$key],
                       'size' => $_FILES['pics']['size'][$key],
                   );
               }
           }
           $_FILES = $pics;
       
           //循环调用函数上传图片
           foreach ($_FILES as $key=>$val){
               $res = uploadOne($key, "foodsPic",array(
                   array(150,150)
               ));
                
               if($res["ok"] == 1){
                   $foodspicsModel->add(array(
                       'foods_id' => $options['where']['foods_id'],
                       'pic' => ltrim($res["images"][0],'.'),     // 原图存到pic字段
                       'sm_pic' => ltrim($res["images"][1],'.')  // 第一个缩略图存到sm_pic字段中
                   ));
                    
               }else{
                   $this->error = $res["error"];
                   return false;
               }
           }
       }
        
    }
     protected function _before_delete($options){
         //根据菜品id找到菜品图片
         
         $logo = $this->field("logo,sm_logo")->find($options['where']['foods_id']);
         deleteImage(array($logo['logo'],$logo["sm_logo"]));
         /****************************** 先删除菜品的其他的信息 ********************************/
         // 扩展分类
         $model = M('FoodsExpandCate');
         $model->where(array('foods_id'=>array('eq', $options['where']['foods_id'])))->delete();
         // 会员价格
         $model = M('MemberPrice');
         $model->where(array('foods_id'=>array('eq', $options['where']['foods_id'])))->delete();
         // 菜品图册
         $model = M('foodsPics');
         // 先取出图片的路径
         $pics = $model->field('pic,sm_pic')->where(array('foods_id'=>array('eq', $options['where']['foods_id'])))->select();
         // 循环每个图片进行删除
         foreach ($pics as $p)
         {
             deleteImage($p);
         }
         $model->where(array('foods_id'=>array('eq', $options['where']['foods_id'])))->delete();
     }  
     protected function _after_delete($data, $options){
        
         
     }   
     /**
      * 菜品以某种形式展示展示
      */
     public function showlist($restaurant_id=0,$is_delete=0){
        $where = array();
        //按菜品分类搜索
        $cate_id = I("get.cate_id");
        if ($cate_id){
            $where['cate_id'] = array("eq",$cate_id);
        }
        //按菜品名称搜索
        $foods_name = I("get.foods_name");
        if ($foods_name){
            $where['foods_name'] = array("like","%$foods_name%");
        }
        //按菜品价格搜索
        $min_price = I("get.min_price");
        $max_price = I("get.max_price");
        if ($min_price){
            $where['shop_price'] = array("egt","$min_price");
        }
        if($max_price){
            $where['shop_price'] = array("elt","$max_price");
        }
        if ($min_price && $max_price){
            $where['shop_price'] = array("between",array($min_price,$max_price));
        }
        //按菜品添加日期搜索
        $start_addtime = I("get.start_addtime");
        $end_addtime = I("get.end_addtime");
        
        if ($start_addtime && $end_addtime){
            $where['addtime'] = array("between",array(strtotime("$start_addtime 00:00:01"),strtotime("$end_addtime 23:59:59")));
        }
        elseif ($start_addtime){
            $where['addtime'] = array("egt",strtotime("$start_addtime 00:00:01"));
        }
        elseif($end_addtime){
            $where['addtime'] = array("elt",strtotime("$end_addtime 23:59:59"));
        }
        //按菜品是否热卖搜索
        $is_hot = I("get.is_hot",-1);
        if ($is_hot != -1){
            $where['is_hot'] = array("eq","$is_hot");
        }
        //按菜品是否上架搜索
        $is_on_sale = I("get.is_on_sale",-1);
        if ($is_on_sale != -1){
            $where['is_on_sale'] = array("eq","$is_on_sale");
        }
        //按菜品是否新品搜索
        $is_new = I("get.is_new",-1);
        if ($is_new != -1){
            $where['is_new'] = array("eq","$is_new");
        }
        //按菜品是否精品搜索
        $is_best = I("get.is_best",-1);
        if ($is_best != -1){
            $where['is_best'] = array("eq","$is_best");
        }
        //按菜品排序搜索
        $orderby = 'foods_id';  // 默认排序字段
		$orderway = 'asc'; // 默认排序方式
		$odby = I('get.odby');
		if($odby && in_array($odby, array('id_asc','id_desc','shop_price_asc','shop_price_desc')))
		{
			if($odby == 'id_desc')
				$orderway = 'desc';
			elseif ($odby == 'shop_price_asc')
				$orderby = 'shop_price';
			elseif ($odby == 'shop_price_desc')
			{
				$orderby = 'shop_price';
				$orderway = 'desc';
			}
		}
		$where['is_delete'] = array("eq",$is_delete);
		if($restaurant_id != 0){
		    $where['restaurant_id'] = array("eq",$restaurant_id);
		}
		
        //翻页实现
        $totalRows = $this->where($where)->count();
        if ($totalRows > 0){
            $Page = setPage($totalRows,4);
            $show = $Page->show();// 分页显示输出
            // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
            $list = $this->where($where)
                        ->group('foods_id')
                        ->order("$orderby $orderway")
                        ->limit($Page->firstRow . ',' . $Page->listRows)
                        ->select();
           
            return array(
                'data' => $list,
                'page' => $show
            );
        }
     }
     /**
      * 获得疯狂抢购菜品
      */
     public function getPromotefoods($restaurant_id = 0,$limit=15){
         $now = time();
         $where = array(
             'is_on_sale' => array('eq', 1),  // 售卖中
             'is_delete' => array('eq', 0),   // 不在回收站
             'is_promote' => array('eq', 1),  // 促销的菜品
             'promote_start_time' => array('elt', $now),
             'promote_end_time' => array('egt', $now),
         );
         if ($restaurant_id != 0){
             $where["restaurant_id"] = $restaurant_id;
         }
         //翻页实现
         $totalRows = $this->where($where)->count();
         if ($totalRows > 0){
             $Page = setPage($totalRows,$limit);
             $show = $Page->show();// 分页显示输出
             // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
             $list = $this->where($where)
             ->group('foods_id')
             ->order("sort_num ASC")
             ->limit($Page->firstRow . ',' . $Page->listRows)
             ->select();
              
             return array(
                 'data' => $list,
                 'page' => $show
             );
         }
         
     }
     /**
      * 获得最新菜品
      */
     public function getNewfoods($restaurant_id = 0,$limit=15){
        $now = time();
        
        $foods_new_key = I("get.foods_new_key");
        if ($foods_new_key){
            $where = array(
                'foods_name' =>array("like","%$foods_new_key%"),
                'is_on_sale' => array('eq', 1),  // 售卖中
                'is_delete' => array('eq', 0),   // 不在回收站
                'is_hot' => array('eq', 1),  // 热卖
            );
        }else{
            $where = array(
                'is_on_sale' => array('eq', 1),  // 售卖中
                'is_delete' => array('eq', 0),   // 不在回收站
                'is_hot' => array('eq', 1),  // 热卖
            );
        }
        if ($restaurant_id != 0){
            $where["restaurant_id"] = $restaurant_id;
        }
     //翻页实现
         $totalRows = $this->where($where)->count();
         if ($totalRows > 0){
             $Page = setPage($totalRows,$limit);
             $show = $Page->show();// 分页显示输出
             // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
             $list = $this->where($where)
             ->group('foods_id')
             ->order("sort_num ASC")
             ->limit($Page->firstRow . ',' . $Page->listRows)
             ->select();
              
             return array(
                 'data' => $list,
                 'page' => $show
             );
         }
     }
     /**
      * 获得热销菜品
      */
     public function getHotfoods($restaurant_id = 0,$limit=15){
         $now = time();
         $foods_hot_key = I("get.foods_hot_key");
         if ($foods_hot_key){
             $where = array(
                 'foods_name' =>array("like","%$foods_hot_key%"),
                 'is_on_sale' => array('eq', 1),  // 售卖中
                 'is_delete' => array('eq', 0),   // 不在回收站
                 'is_hot' => array('eq', 1),  // 热卖
             );
         }else{
             $where = array(
                 'is_on_sale' => array('eq', 1),  // 售卖中
                 'is_delete' => array('eq', 0),   // 不在回收站
                 'is_hot' => array('eq', 1),  // 热卖
             );
         }
         
         if ($restaurant_id != 0){
             $where["restaurant_id"] = $restaurant_id;
         }
         //翻页实现
         $totalRows = $this->where($where)->count();
         if ($totalRows > 0){
             $Page = setPage($totalRows,$limit);
             $show = $Page->show();// 分页显示输出
             // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
             $list = $this->where($where)
             ->group('foods_id')
             ->order("sort_num ASC")
             ->limit($Page->firstRow . ',' . $Page->listRows)
             ->select();
         
             return array(
                 'data' => $list,
                 'page' => $show
             );
         }
		 
     }
     /**
      * 获得精品菜品
      */
     public function getBestfoods($restaurant_id = 0,$limit=15){
         $now = time();
         $where = array(
             'is_on_sale' => array('eq', 1),  // 售卖中
             'is_delete' => array('eq', 0),   // 不在回收站
             'is_best' => array('eq', 1),  // 精品
         );
        if ($restaurant_id != 0){
             $where["restaurant_id"] = $restaurant_id;
         }
         //翻页实现
         $totalRows = $this->where($where)->count();
         if ($totalRows > 0){
             $Page = setPage($totalRows,$limit);
             $show = $Page->show();// 分页显示输出
             // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
             $list = $this->where($where)
             ->group('foods_id')
             ->order("sort_num ASC")
             ->limit($Page->firstRow . ',' . $Page->listRows)
             ->select();
         
             return array(
                 'data' => $list,
                 'page' => $show
             );
         }
     }
     /**
      * 获得首页PPT图片
      */
     public function getPPTPic($restaurant_id = 0,$limit=6){
         $where = array();
         if ($restaurant_id != 0){
             $where["restaurant_id"] = $restaurant_id;
         }
         return $this->field('foods_id,foods_name,shop_price,logo')->where($where)->limit($limit)->order('sort_num ASC')->select();
     }
     /**
      * 获得首页销量排行榜图片
      */
     public function getFoodsSalesTop($limit=4){
          
         return $this->field('foods_id,foods_name,shop_price,logo,total_sales')->limit($limit)->order('total_sales Desc')->select();
     }
     /**
      * 获得餐厅热销排行榜图片
      */
     public function getHotTop($restaurant_id = 0,$limit=6){
         $where = array();
         $where = array(
             'is_on_sale' => array('eq', 1),  // 售卖中
             'is_delete' => array('eq', 0),   // 不在回收站
             'is_hot' => array('eq', 1),  // 热卖
         );
         if ($restaurant_id != 0){
             $where["restaurant_id"] = $restaurant_id;
         }
         
         return $this->field('foods_id,foods_name,shop_price,logo,total_sales')->where($where)->limit($limit)->order('sort_num ASC')->select();
     }
    /**
     * 在前台获得菜品的价格
     * @param unknown $foods_id
     */
    public function getFoodsPrice($foods_id){
        //判断是否有用户登录
        $now = time();
        //查询是否有促销价格
        $price = $this->field('shop_price,is_promote,promote_price,promote_start_time,promote_end_time')->find($foods_id);
        if($price['is_promote'] == 1 && ($price['promote_start_time'] < $now && $price['promote_end_time'] > $now))
        {
            $promote_price =  $price['promote_price'];
            
        }
        if(session("member_id")){
            //查询用户的会员等级决定价格
            $points = session("points");
             
            $memberLvlModel = M("MemberLevel");
            $memberlvl_info = $memberLvlModel->field("level_id,rate")->where("$points between bottom_num and top_num")->find();
       
            $memberlvl_id = $memberlvl_info["level_id"];
            
            $mempriceModel = M('MemberPrice');
            $memprice = $mempriceModel->field('price')->where(array('foods_id'=>array('eq', $foods_id), 'level_id'=>array('eq', $memberlvl_id)))->find();
            // 如果有会员价格就直接使用会员价格
            
            if($memprice['price']){
                if (isset($promote_price)){
                    return min($promote_price,$memprice['price']);
                }
                return $memprice['price'];
            }
            else{
                // 如果没有设置会员价格，就按这个级别的折扣率来算
                if (isset($promote_price)){
                    return min($promote_price,($memberlvl_info['rate'] / 100) * $price['shop_price']);
                }
                return ($memberlvl_info['rate'] / 100) * $price['shop_price'];
            }
        
        }else{
            return isset($promote_price) ? $promote_price : $price['shop_price']; //未登录的用户
        }
    }
    public function showlistByCateId($cate_id){
        
        $where = array(
            'a.is_on_sale' => array('eq', 1),
            'a.is_delete' => array('eq', 0),
        );
        //查找菜品的所有分类
        $foodscateModel = D("Admin/FoodsCate");
        $foodscate_allids = $foodscateModel->where("parent_id = $cate_id")->select();
        
        if (!$foodscate_allids){
            //
            $cate_ids[] = $cate_id;
        }else{
            
            foreach ($foodscate_allids as $key => $val){
                $cate_ids[] = $val['cate_id'];
            }
        }
       
        
        //还需在拓展分类中查找相关的菜品
        $cate_ids = implode(",", $cate_ids);
        $cateextModel = M("FoodsExpandCate");
        $foods_ext_foods = $cateextModel->where("cate_id in ($cate_ids)")->select();
       
        $foods_ext_ids = array();
        foreach ($foods_ext_foods as $key => $val){
            $foods_ext_ids[] = $val['foods_id'];
        }
       
     
        
        if ($foods_ext_ids){
            $foods_ext_ids = implode(",", $foods_ext_ids);
            $foods_ext_ids = "or a.foods_id in ($foods_ext_ids)";
        }else{
            $foods_ext_ids = '';
        }
        
        $where['(a.cate_id'] = array('exp', "in ($cate_ids) $foods_ext_ids)");
        
        // 价格搜索
        $price = I('get.price');
        if($price){
            $price = explode('-', $price);
            $where['a.shop_price'] = array('between', array($price[0], $price[1]));
        }
        //餐厅搜索菜品
        $restaurant_id = I("get.restaurant_id");
        
        if ($restaurant_id){
            $where['a.restaurant_id'] = array("eq",$restaurant_id);
            
        }
        $orderBy = 'total_sales';  // 排序字段
        $orderWay = 'DESC'; // 排序方式
        // 接收用户传的排序参数
        $ob = I('get.ob');
        $ow = I('get.ow');
        if($ob && in_array($ob, array('total_sales','shop_price','comment','addtime')))
        {
            $orderBy = $ob;
            // 如果是根据价格排序，才接收ow变量
            if($ob == 'shop_price' && $ow && in_array($ow, array('asc', 'desc')))
                $orderWay = $ow;
        }
        //合并前后所查数据
       
        $totalRows = $this->alias('a')->where($where)->count();
       
      
        if ($totalRows > 0){
            $Page = setPage($totalRows,12);
            $show = $Page->show();// 分页显示输出
            // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
            $list = $this->field("a.*,count(b.comment_id) comment")
            ->alias('a')
            ->join("left join yd_comment b on a.foods_id = b.foods_id")
            ->where($where)
            ->group('a.foods_id')
            ->order("$orderBy $orderWay")
            ->limit($Page->firstRow . ',' . $Page->listRows)
            ->select();
         
            return array(
                'data' => $list,
                'page' => $show
            );
        }
    }
    public function showlistByKey($keys){
        
        $where = array(
            'a.foods_name' =>  array("like","%$keys%"),
            'a.is_on_sale' => array('eq', 1),
            'a.is_delete' => array('eq', 0),
        );
        // 价格搜索
        $price = I('get.price');
        if($price){
            $price = explode('-', $price);
            $where['a.shop_price'] = array('between', array($price[0], $price[1]));
        }
        //餐厅搜索菜品
        $restaurant_id = I("get.restaurant_id");
        
        if ($restaurant_id){
            $where['a.restaurant_id'] = array("eq",$restaurant_id);
        
        }
        $orderBy = 'total_sales';  // 排序字段
        $orderWay = 'DESC'; // 排序方式
        // 接收用户传的排序参数
        $ob = I('get.ob');
        $ow = I('get.ow');
        if($ob && in_array($ob, array('total_sales','shop_price','comment','addtime')))
        {
            $orderBy = $ob;
            // 如果是根据价格排序，才接收ow变量
            if($ob == 'shop_price' && $ow && in_array($ow, array('asc', 'desc')))
                $orderWay = $ow;
        }
        $totalRows = $this->alias('a')->where($where)->count();
        
        if ($totalRows > 0){
            $Page = setPage($totalRows,12);
            $show = $Page->show();// 分页显示输出
            // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
            $list = $this->field("a.*,count(b.comment_id) comment")
            ->alias('a')
            ->join("left join yd_comment b on a.foods_id = b.foods_id")
            ->where($where)
            ->order("$orderBy $orderWay")
            ->group('a.foods_id')
            ->limit($Page->firstRow . ',' . $Page->listRows)
            ->select();
             
            return array(
                'data' => $list,
                'page' => $show
            );
        }
    }
     
 }
 
 
?>