<?php

 namespace Admin\Model;
 use Think\Model;
  class FoodsCateModel extends Model{
     // 在添加时调用create方法时允许接收的字段
     protected $insertFields = array('cate_name','parent_id');
     // 在更新时调用create方法时允许接收的字段
     protected $updateFields = array('cate_id','cate_name','parent_id');
     // 自动验证
     protected $_validate = array(
		array('cate_name', 'require', '分类名称不能为空！', 1, 'regex', 3),
		array('cate_name', '1,30', '分类名称的值最长不能超过 30 个字符！', 1, 'length', 3),
		array('parent_id', 'number', '上级分类的ID，0：代表顶级必须是一个整数！', 2, 'regex', 3),
	);
     /**
      * 树状显示分类数据
      * 
      */
     public function tree(){
         $data = $this->select();
         
         return $this->sortByTree($data);
     }
     private function sortByTree($data,$parent_id = 0,$level = 0){
         static $arr = array();
         foreach ($data as $key => $val){
             if ($val['parent_id'] == $parent_id){
               $val['level'] = $level;
               $arr[] = $val;
               $this->sortByTree($data, $val['cate_id'], $level+1);
             }
         }
         return $arr;
     }
     public  function getChildren($id){
         $data = $this->select();
         return $this->children($data,$id);
     }
     /**
      * 获得当前分类的子分类
      * @param array $data 二维数组
      * @param number $parent_id 当前分类的父id   
      * @return array $arr 当前分类的子分类数组
      */
     private function children($data,$parent_id = 0){
         static $arr = array();
          foreach ($data as $key=>$val){
             if ($val['parent_id'] == $parent_id){
               $arr[] = $val['cate_id'];
               $this->children($data, $val['cate_id']);
             }
         }
         return $arr;
     }
      protected function _before_update(&$data, $options){
        //找出所要修改菜品分类的子分类
        $sub_cate_ids = $this->getChildren($options['where']['cate_id']);
        $sub_cate_ids[] = (string)$options['where']['cate_id'];
        //修改的分类不能将自身或者为其的子分类作为上级分类
        if (in_array($data["parent_id"],$sub_cate_ids)){
            $this->error = "不能将当前分类及其子分类作为上级分类";
            //redirect(U("foodsCateList"),3,"不能将当前分类及其子分类作为上级分类");
             return FALSE;
        }
        
     }
     protected function _before_delete($options){
         
         //找出所要修改菜品分类的子分类
         $sub_cate_ids = $this->getChildren($options['where']['cate_id']);
         //删除的分类需要将自身或者为其的子分类删掉
         if($sub_cate_ids)
         {
             $sub_cate_ids = implode(',', $sub_cate_ids);
             
             $this->execute("DELETE FROM yd_foods_cate WHERE cate_id IN($sub_cate_ids)");
         }
     }
     /**
      * 前台显示分类栏
      * @return 一个包含分类的数组
      */
     public function getNavCateInfo(){
         $data = $this->select();
         return $this->cateTree($data);
     }
     public function cateTree($data,$parent_id=0){
         $arr = array();
         foreach ($data as $key=>$val){
             if ($val['parent_id'] == $parent_id){
                 $children = $this->cateTree($data, $val['cate_id']);
                 $val['children'] = $children;
                 $arr[] = $val;
             }
         }
         return $arr;
     }
 }
?>