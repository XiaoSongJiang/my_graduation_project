<?php
namespace Home\Controller;
use Tools\HomeBaseController;
class SearchController extends HomeBaseController 
{
	public function searchFoods(){
	    
		$cate_id = I('get.cate_id');
		$restaurantModel = D("Admin/Restaurant");
		//设置所在位置
		$foodscateModel = M("FoodsCate");
		$foodscate_info = $foodscateModel->find($cate_id);
		$position = array();
		$position[1] = $foodscate_info['cate_name'];
		if ($foodscate_info['parent_id'] != 0){
		    $temp = $foodscateModel->find($foodscate_info['parent_id']);
		    $position[0] = $temp['cate_name'];;
		}
		$position = array_reverse($position);
		$this->assign("position",$position);
		$cate_ids = array();
		$foodsModel = D('Admin/Foods');
		if($cate_id){
		    $foods_all_info = $foodsModel->showlistByCateId($cate_id);
		   
		}
		$this->assign("foods_all_info",$foods_all_info);
		//设置菜品价格区间用于搜索
		$price = array("0-100","101-200","201-300","301-400","401-500");
		$this->assign("price",$price);
		//餐厅信息
		
		$data = $restaurantModel->select();
		$this->assign("restaurant_info",$data);
		
		//菜品相关推荐
		$promote_foods = $foodsModel->getPromotefoods(0,4);
		$hot_foods = $foodsModel->getHotfoods(0,4);
		$new_foods = $foodsModel->getNewfoods(0,3);
		$foods_sales_top = $foodsModel->getFoodsSalesTop(3);
		
		$this->assign(array(
		    'promote_foods' => $promote_foods['data'],
		    'hot_foods' => $hot_foods['data'],
		    'new_foods' => $new_foods['data'],
		    'foods_sales_top' => $foods_sales_top
		));
		
		//最近浏览
		$recent_through = isset($_COOKIE['recent_through']) ? unserialize($_COOKIE['recent_through']) : array();
		if($recent_through)
		{
		    // 再根据菜品的ID取出菜品的信息
		    $foodsModel = M('Foods');
		    $recent_through_str = implode(',', $recent_through);
		    //取出的foods_id与cookie中的foods_id顺序保持一致
		    $foods = $foodsModel->field('foods_id,foods_name,logo,sm_logo,shop_price')->where(array('foods_id'=> array('in', $recent_through)))->order("INSTR(',$recent_through_str,',CONCAT(',',foods_id,','))")->select();
		    $this->assign("recent_through",$foods);
		
		}
		// 设置页面的信息
		$this->setHeadInfo('菜品搜索', '搜索页', '搜索页', 0, array('list','common'), array('list'));
		// 显示页面
		$this->display();
	}
    public function searchByHead(){
        $foodsModel = D("Admin/Foods");
        $foods_key = I("get.foods_key");
        $restaurantModel = D("Admin/Restaurant");
        if (IS_POST){
			
            $search_by = I("post.search_by");
            $search_key = I("post.search_key");
            if ($search_by == 'foods_method'){
                
                $foods_all_info = $foodsModel->showlistByKey($search_key);
                
                //dump($foods_all_info);
        
            }
            if ($search_by == 'rest_method'){
        
                $rest_info = $restaurantModel -> showlist($search_key);
                $this->assign(array(
                    "list" => $rest_info['data'],
                    "pages" => $rest_info['page']
        
                ));
                $this->setHeadInfo('餐厅列表', '餐厅列表', '餐厅列表', 0, array('home','restaurantlist'));
                $this->display("Index:restaurantList");
                exit();
            }
        
        
        }
        if ($foods_key){
            $foods_all_info = $foodsModel->showlistByKey($foods_key);
            $_POST['search_key'] = $foods_key;
        }
        $this->assign("foods_all_info",$foods_all_info);
        //设置菜品价格区间用于搜索
        $price = array("0-100","101-200","201-300","301-400","401-500");
        $this->assign("price",$price);
        //餐厅信息
        
        $data = $restaurantModel->select();
        $this->assign("restaurant_info",$data);
        
        //菜品相关推荐
        $promote_foods = $foodsModel->getPromotefoods(0,4);
        $hot_foods = $foodsModel->getHotfoods(0,4);
        $new_foods = $foodsModel->getNewfoods(0,3);
        $foods_sales_top = $foodsModel->getFoodsSalesTop(3);
        
        $this->assign(array(
            'promote_foods' => $promote_foods['data'],
            'hot_foods' => $hot_foods['data'],
            'new_foods' => $new_foods['data'],
            'foods_sales_top' => $foods_sales_top
        ));
        
        //最近浏览
        $recent_through = isset($_COOKIE['recent_through']) ? unserialize($_COOKIE['recent_through']) : array();
        if($recent_through)
        {
            // 再根据菜品的ID取出菜品的信息
            $foodsModel = M('Foods');
            $recent_through_str = implode(',', $recent_through);
            //取出的foods_id与cookie中的foods_id顺序保持一致
            $foods = $foodsModel->field('foods_id,foods_name,logo,sm_logo,shop_price')->where(array('foods_id'=> array('in', $recent_through)))->order("INSTR(',$recent_through_str,',CONCAT(',',foods_id,','))")->select();
            $this->assign("recent_through",$foods);
        
        }
        // 设置页面的信息
        $this->setHeadInfo('菜品搜索', '搜索页', '搜索页', 0, array('list','common'), array('list'));
        // 显示页面
        $this->display();
    }
    public function searchFoodsInRest(){
        
        $restaurant_id = I("get.restaurant_id");
        $rest_search_key = I('get.rest_search_key');
        $restaurantModel = D("Admin/Restaurant");
        $restaurant_info = $restaurantModel->find($restaurant_id);
        $this->assign("restaurant_info",$restaurant_info);
        
        $foodsModel = D("Admin/Foods");
        $foods_all_info = $foodsModel->showlistByKey($rest_search_key);
        
        
        $this->assign(array(
            'data' => $foods_all_info['data'],
            'page' => $foods_all_info['page']
        ));
        $this->setHeadInfo($restaurant_info['restaurant_name'], '易达在线订餐', '易达在线订餐', 1, array('index','home','restaurant'),array("index"));
        $this->display();
    }
}




















