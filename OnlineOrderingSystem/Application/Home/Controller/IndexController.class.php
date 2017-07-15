<?php
namespace Home\Controller;
use Tools\HomeBaseController;
class IndexController extends HomeBaseController {
    public function serach(){
        dump($_POST);
    }
    public function index(){
       
        $foodsModel = D('Admin/Foods');
        // 获取疯狂抢购的菜品
        $promote_foods = $foodsModel->getPromotefoods();
        $hot_foods = $foodsModel->getHotfoods();
        $best_foods = $foodsModel->getBestfoods();
        $new_foods = $foodsModel->getNewfoods();
        //分配首页面PPT图片
        $foods_ppt_pic = $foodsModel->getPPTPic(); 
        //获取销量排行榜
        $foods_sales_top = $foodsModel->getFoodsSalesTop();
        
        //取出购物车数据
        
        $this->assign(array(
            'promote_foods' => $promote_foods['data'],
            'hot_foods' => $hot_foods['data'],
            'best_foods' => $best_foods['data'],
            'new_foods' => $new_foods['data'],
            
            'promote_foods_page' => $promote_foods['page'],
            'hot_foods_page' => $hot_foods['page'],
            'best_foods_page' => $best_foods['page'],
            'new_foods_page' => $new_foods['page'],
            
            'foods_ppt_pic' => $foods_ppt_pic,
            'foods_sales_top' => $foods_sales_top
        ));
        
        //通过redis获得最新登录的五个用户
        $redis = new \Redis();
        $redis->connect('127.0.0.1',6379);
        $redis->select(5);
        $redis_users = $redis->lrange('newlogin',0,4);
        $this->assign("login_members",$redis_users);
         
        //dump($_COOKIE);
        $this->setHeadInfo("易达在线订餐", "易达在线订餐", "易达在线订餐",1,array("index"),array("index"));
        $this->display();
    }
    /**
     * 菜品详情
     */
    public function foods(){
        //接收菜品的id
        
        $foods_id = I("get.foods_id");
        $foodsModel = D("Admin/Foods");
        //获取菜品的基本信息
        $foods_basic_info = $foodsModel->find($foods_id);
        $this->assign("foods_basic_info",$foods_basic_info);
       
        //获得餐厅名
        $restaurantModel = D("Admin/Restaurant");
        $restaurant_name = $restaurantModel->field("restaurant_id,restaurant_name")->find($foods_basic_info['restaurant_id']);
        $this->assign("restaurant_name",$restaurant_name["restaurant_name"]);
        $this->assign("restaurant_id",$restaurant_name["restaurant_id"]);
        
        //餐厅热销排行榜
        $hot_info = $restaurantModel->getHotFoods($restaurant_name['restaurant_id']);
        $this->assign("hot_foods",$hot_info);
       
        //获取菜品相册信息
    
        $foodspicsModel = M("foodsPics");
        $foods_pics = $foodspicsModel->where("foods_id = $foods_id")->select();
        //取出相册后转换为一维数组，放在菜品信息中
    
        $pic = array();
        foreach ($foods_pics as $key => $val){
            $pic[] = $val['pic'];
        }
        $this->assign("foods_pics",$pic);
        //获取菜品所属主分类和扩展分类
        //菜品主分类id
        
        $cate_key_id = $foods_basic_info['cate_id'];
        //存放主分类名
        
        $cate_arr = array();
        $cateModel = D("Admin/FoodsCate");
        $cate_all = $cateModel -> select();
        $cate_info = $cateModel -> find($cate_key_id);
        $cate_arr[] = $cate_info['cate_name'];
        foreach($cate_all as $key => $val){
            if ($cate_info['parent_id'] == $val['cate_id']){
                $cate_arr[] = $val['cate_name'];
            }
        }
        $this->assign("cate_arr",array_reverse($cate_arr));
        
        //扩展分类
        $foodsextcate_names = $cateModel->alias('a')->field("a.cate_name")->join("LEFT JOIN yd_foods_expand_cate b on a.cate_id = b.cate_id")->where("b.foods_id = $foods_id")->select();
        $foodsextcate_arr[] = $cate_info['cate_name'];
        foreach ($foodsextcate_names as $k => $v){
            $foodsextcate_arr[] = $v['cate_name'];
        }
        $this->assign("foodsextcate_arr",$foodsextcate_arr);
        
        //获取菜品的评价 （好评 中评 差评）和评价的人数
        $commentModel = D("Home/Comment");
        $comment_person = $commentModel->getFoodsEvaluate($foods_id);
        $this->assign("comment_person",$comment_person);
       
        $this->setHeadInfo("菜品详情", "菜品详情", "菜品详情",0,array("foods","common","jqzoom"),array("foods","jqzoom-core","jquery.cookie"));
    
        $this->display();
    }
    
    /**
     * 处理订餐用户价格，并把正在浏览的菜品加入cookie
     */
    public function getPriceByAjax(){
        $foods_id = I("get.foods_id");
        $foodsModel = D("Admin/Foods");
        //判断cookie中是否有最近浏览的菜品
        $recent_through = isset($_COOKIE['recent_through']) ? unserialize($_COOKIE['recent_through']) : array();
    
        //把正在浏览的菜品id添加到cooki中
        array_unshift($recent_through, $foods_id);
        $recent_through = array_unique($recent_through);
    
        if (count($recent_through)>10){
            //数组截取前五个
            $recent_through = array_slice($recent_through,0,10);
        }
        //把处理后的数组存入cookie
        $cookie_time = 30*86400;
    
        cookie("recent_through",serialize($recent_through));
       
        echo $foodsModel->getFoodsPrice($foods_id);
    }
    //ajax获取最近浏览的菜品
    public function getRecentFoodsByAjax(){
        // 先从COOKIE中取出最近浏览过的菜品的ID
        $recent_through = isset($_COOKIE['recent_through']) ? unserialize($_COOKIE['recent_through']) : array();
        if($recent_through)
        {
            // 再根据菜品的ID取出菜品的信息
            $foodsModel = M('Foods');
            $recent_through_str = implode(',', $recent_through);
            //取出的foods_id与cookie中的foods_id顺序保持一致
            $foods = $foodsModel->field('foods_id,foods_name,logo,sm_logo,shop_price')->where(array('foods_id'=> array('in', $recent_through)))->order("INSTR(',$recent_through_str,',CONCAT(',',foods_id,','))")->select();
            echo json_encode($foods);
        }
    }
    //ajax清除最近浏览菜品cookie
    public function clearRecentFoodsCookie(){     
        cookie("recent_through",null);
        echo 1;
    }
    //ajax获取评论
    public function ajaxGetComment(){
        $foods_id = I("get.foods_id");
        $ret = array('login' => 0);
        $member_id = session('member_id');
        if($member_id)
        {
            $ret['login'] = 1;
        }
        echo json_encode($ret);
    }
    /**
     * 餐厅列表展示
     */
    public function restaurantList(){
        $restaurantModel = D("Admin/Restaurant");
        $data = $restaurantModel->showlist();
        $this->assign(array(
            "list" => $data['data'],
            "pages" => $data['page']
        
        ));
        //dump($data['page']);
        $this->setHeadInfo('餐厅列表', '餐厅列表', '餐厅列表', 0, array('home','restaurantlist'));
        $this->display();
    }
    public function restaurant(){
        $restaurant_id = I("get.restaurant_id");
        $restaurantModel = D("Admin/Restaurant");
        $restaurant_info = $restaurantModel->find($restaurant_id);
        $this->assign("restaurant_info",$restaurant_info);
        //
        $foodsModel = D('Admin/Foods');
        // 获取餐厅各类菜品
        $promote_foods = $foodsModel->getPromotefoods($restaurant_id,5);
        $hot_foods = $foodsModel->getHotfoods($restaurant_id,5);
        $best_foods = $foodsModel->getBestfoods($restaurant_id,5);
        $new_foods = $foodsModel->getNewfoods($restaurant_id,5);
        //分配首页面PPT图片
        $foods_ppt_pic = $foodsModel->getPPTPic($restaurant_id,3);
        
        $foods_hot_top = $foodsModel->getHotTop($restaurant_id);
        $this->assign(array(
            'promote_foods' => $promote_foods['data'],
            'hot_foods' => $hot_foods['data'],
            'best_foods' => $best_foods['data'],
            'new_foods' => $new_foods['data'],
            'foods_ppt_pic' => $foods_ppt_pic,
            'foods_hot_top' => $foods_hot_top
        ));
        $this->setHeadInfo($restaurant_info['restaurant_name'], '易达在线订餐', '易达在线订餐', 1, array('index','home','restaurant'),array("index"));
        $this->display();
    }
    //餐厅展示所有选中类型的菜品
    public function restaurantFoodsType(){
        $restaurant_id = I("get.restaurant_id");
        $type = I("get.type");
        $restaurantModel = D("Admin/Restaurant");
        $restaurant_info = $restaurantModel->find($restaurant_id);
        $this->assign("restaurant_info",$restaurant_info);
        //
        $foodsModel = D('Admin/Foods');
        // 根据type获取餐厅各类菜品
        switch ($type){
            case "all_promote":
                $promote_foods = $foodsModel->getPromotefoods($restaurant_id,10);
                $this->assign("type_name","所有抢购菜品");
                $this->assign("data",$promote_foods['data']);
                $this->assign("page",$promote_foods['page']);
                break;
            case "all_hot":
                $this->assign("type_name","所有热销菜品");
               $hot_foods = $foodsModel->getHotfoods($restaurant_id,10);
               $this->assign("data",$hot_foods['data']);
               $this->assign("page",$hot_foods['page']);
                break;
            case "all_best":
                $best_foods = $foodsModel->getBestfoods($restaurant_id,10);
                $this->assign("type_name","所有精品菜品");
                $this->assign("data",$best_foods['data']);
                $this->assign("page",$best_foods['page']);
                break;
            case "all_new":
                $new_foods = $foodsModel->getNewfoods($restaurant_id,10);
                $this->assign("type_name","所有新品菜品");
                $this->assign("data",$new_foods['data']);
                $this->assign("page",$new_foods['page']);
                break;
        }
       
        $this->setHeadInfo($restaurant_info['restaurant_name'], '易达在线订餐', '易达在线订餐', 1, array('index','home','restaurant'),array("index"));
        $this->display();
    }
    //所有热销商品
    public function foodsHotAll(){
        $foodsModel = D('Admin/Foods');
        $hot_foods = $foodsModel->getHotfoods();
        $this->assign(array(
            "data" => $hot_foods['data'],
            "page" => $hot_foods['page']
        ));
        $this->setHeadInfo("所有热销菜品", '易达在线订餐', '易达在线订餐', 1, array('restaurant'));
        $this->display();
    }
    //所有新品商品
    public function foodsNewAll(){
        $foodsModel = D('Admin/Foods');
        $new_foods = $foodsModel->getNewfoods();
        $this->assign(array(
            "data" => $new_foods['data'],
            "page" => $new_foods['page']
        ));
        $this->setHeadInfo("所有新品菜品", '易达在线订餐', '易达在线订餐', 1, array('restaurant'));
        $this->display();
    }
    
    //最近浏览
    public function recentThrough(){
        // 先从COOKIE中取出最近浏览过的菜品的ID
        $recent_through = isset($_COOKIE['recent_through']) ? unserialize($_COOKIE['recent_through']) : array();
        if($recent_through)
        {
            // 再根据菜品的ID取出菜品的信息
            $foodsModel = M('Foods');
            $recent_through_str = implode(',', $recent_through);
            //取出的foods_id与cookie中的foods_id顺序保持一致
            $foods = $foodsModel->field('foods_id,foods_name,logo,sm_logo,shop_price')->where(array('foods_id'=> array('in', $recent_through)))->order("INSTR(',$recent_through_str,',CONCAT(',',foods_id,','))")->select();
            $this->assign("data",$foods);
            
        }
        $this->setHeadInfo("最近浏览菜品", '易达在线订餐', '易达在线订餐', 0);
        $this->display();
    }
    /**
     * 用户留言
     */
    public function memberMessageLeave(){
      
        $rest_name = I("get.restaurant_name");
        $rest_id = I("get.restaurant_id");

        $guestbookModel = D('Home/Guestbook');
        if (IS_POST){
            
            if($guestbookModel->create(I('post.'), 1))
            {
                
                if($guestbookModel->add())
                {
                     $this->success('留言成功！');
                     exit;
                 }
            }
            $this->error($guestbookModel->getError());
        }
        //显示留言信息
        $guestbook_info = $guestbookModel -> showGuestBook($rest_id);
       
        $this->assign("guestbook_info",$guestbook_info);
        $this->setHeadInfo("用户留言", '易达在线订餐', '易达在线订餐', 0, array('restaurant','membermessageleave'),array("jquery.cookie"));
        $this->display();
    }
}