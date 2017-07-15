<?php
namespace Admin\Controller;
use Tools\HomeBaseController;
class FoodsCateController extends HomeBaseController{
    public function __construct(){
        parent::__construct();
        $restaurant_id = session('restaurant_id');
        $manager_id = session("manager_id");
        if(empty($restaurant_id) && empty($manager_id)){
            $url = U('Admin/Restaurant/login');
            $str = <<<eof
            <script type="text/javascript">
        
                window.top.location.href = "$url";
            </script>
eof;
            echo $str;
            exit();
        }
    }
     /**
      * 菜品分类添加
      */
    
     function foodsCateAdd(){
         //建立数据模型
         $cateModel = D("Admin/FoodsCate");
         if(IS_POST){
            
             //收集表单数据
             if ($cateModel->create(I("post."),1)){
                 //插入数据到数据库
                 if($cateModel->add()){
                     // 提示信息
                     $this->success('操作成功！', U('foodsCateList'));
                     // 停止执行后面的代码
                     exit();
                 } 
             }
             // 错误处理
             $error = $cateModel->getError();
             $this->error($error);
         }
         
         $this->setPageBtn('菜品分类添加', '菜品分类列表', U("foodsCateList"));
         //查询所有的分类用于显示
         $category_data = $cateModel->tree();
         $this->assign("category_data",$category_data);
         $this->display();
     }
     /**
      * 菜品分类修改
      */
     function foodsCateEdit(){
         $cate_id = I("get.cate_id");
         $cateModel = D("Admin/FoodsCate");
         if(IS_POST){
             //收集表单数据
             if ($cateModel->create(I("post."),2)){
                 //更新数据到数据库
                 
                 if($cateModel->save() !== FALSE){
                     // 提示信息
                     $this->success('修改成功！', U('foodsCateList'));
                     // 停止执行后面的代码
                     exit();
                 }
             }
             // 错误处理
             $error = $cateModel->getError();
             $this->error($error);
         }
         $one_cate = $cateModel->find($cate_id);//选中的id的一条记录
         $this->assign("onecate",$one_cate);
         //查询所有的分类用于显示
         $category_data = $cateModel->tree();
         $this->assign("category_data",$category_data);
         $this->setPageBtn('菜品分类修改', '菜品分类列表', U("foodsCateList"));
         $this->display();
     }
     /**
      * 菜品分类列表
      */
     function foodsCateList(){
         $cateModel = D("Admin/FoodsCate");
         $category_data = $cateModel->tree();
         $this->assign("category_data",$category_data);
         $this->setPageBtn('菜品分类列表', '菜品分类添加', U("foodsCateAdd"));
         $this->display();
     }
     /**
      * 菜品分类删除
      */
     function foodsCateDelete(){
         $cateModel = D("Admin/FoodsCate");
         if ($cateModel->delete(I("get.cate_id",0))!==FALSE){//如果不存在$_GET['id'] 则返回0
             $this->success('删除成功！', U('foodsCateList', array('p' => I('get.p', 1))));
             exit;
         }
         else{
             $this->error($cateModel->getError());
         }
        
     }
 }
?>