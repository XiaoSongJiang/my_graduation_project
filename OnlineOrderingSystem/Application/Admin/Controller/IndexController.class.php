<?php
namespace Admin\Controller;
use Tools\AdminBaseController;
class IndexController extends AdminBaseController {
    public function index(){
        $this->display();
    }
    public function left(){
        //根据管理员的权限显示菜单栏
        $manager_id = session("manager_id");
        
        //先获取当前管理员所拥有的访问权限
	    //超级管理员拥有所有权限
	    if ($manager_id == 1){
	        $sql = "select * from yd_privilege";
	    }else {
	        $sql = "select b.*
                from yd_role_privilege a
                left join yd_privilege b on a.pri_id = b.pri_id
                left join yd_manager_role c on a.role_id = c.role_id
                where c.manager_id = $manager_id";
	    }
	    
	    $model = M();
	    $pri = $model->query($sql);
	   //  dump($pri);
	    //因一个管理员可能为多种角色，权限去重
	    $temp = array();
	    $temp_arr = array();
	    
	    foreach ($pri as $k => $v){
    	    if(!in_array($v['pri_id'],$temp) && $v['parent_id'] != null){
    	        
    				$temp[] = $v['pri_id'];
    				$temp_arr[] = $v;
    			}
	    }
	    $pri = $temp_arr;
	    //$pri = array_unique($pri);
	    //取出前两级权限放在menu中 四位数组表示
	    $two_level_pri = array();
	    foreach ($pri as $key => $val){
	        if ($val["parent_id"] == 0){
	            foreach ($pri as $k => $v){
	                if ($v["parent_id"] == $val["pri_id"]){
	                    $val['children'][] = $v;
	                }
	            }
	            $two_level_pri[] = $val;
	        }
	        
	    }
	   // dump($two_level_pri);
	    $this->assign("two_level_pri",$two_level_pri);
		$this->display();
    }
    
    public function head(){
        
        $this->display();
    }
    
    public function right(){
        $this->display();
    }
}