<?php
namespace Admin\Controller;

use Tools\HomeBaseController;

class FoodsController extends HomeBaseController
{
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
     * 添加菜品
     */
    public function foodsAdd()
    {
        // 处理表单数据
        if (IS_POST) {
            // 建立数据处理模型
            
            $model = D("Admin/Foods");
            if ($model->create(I("post."),1)) {
                // 插入数据库
               
                if ($model->add()) {
                    // 提示信息
                    $this->success('操作成功！', U('foodsList'));
                    // 停止执行后面的代码
                    exit();
                }
            }
            // 错误处理
            $error = $model->getError();
            $this->error($error);
        }
        //获得菜品分类信息
        $cateModel = D("Admin/FoodsCate");
        $cate_info = $cateModel->tree();
        $this->assign("cate_info",$cate_info);
      
        //获得订餐用户会员等级信息
        $memberLvlModel = D("Admin/MemberLevel");
        $memberlvl_info = $memberLvlModel->select();
        $this->assign("memberlvl_info",$memberlvl_info);
        $this->setPageBtn('菜品添加', '菜品列表', U("foodsList"),"菜品添加");
        
        $this->display();
    }
    /**
     * 菜品编辑
     */
    public function foodsEdit(){
        // 建立数据处理模型
        $model = D("Admin/Foods");
        if (IS_POST){
          
            if ($model->create(I("post."),2)) {
                // 插入数据库
               
                if (false !== $model->save()) { //save()返回的是受影响的记录数，没有修改则返回0 错误返回false
                    // 提示信息
                   
                    $this->success('修改成功！',U('foodsList?p='.I("get.p")));
                    // 停止执行后面的代码
                    exit();
                }
            }
            // 错误处理
            $error = $model->getError();
            $this->error($error);
        }
        //获得修改菜品的id
        $foods_id = I("get.foods_id");
        
        $foods_info = $model->find($foods_id);
        $this->assign("foods_info",$foods_info);
        //获得菜品分类信息
        $cateModel = D("Admin/FoodsCate");
        $cate_info = $cateModel->tree();
        $this->assign("cate_info",$cate_info);
       
        //获得会员等级信息
        $memberLvlModel = D("MemberLevel");
        $memberlvl_info = $memberLvlModel->select();
        $this->assign("memberlvl_info",$memberlvl_info);
        //获得菜品拓展分类信息
        $foodsextcateModel = M("FoodsExpandCate");
        $foodsextcate_info = $foodsextcateModel->where(array("foods_id"=>array("eq",$foods_id)))->select();
        $this->assign("foodsextcate_info",$foodsextcate_info);
        
        //获得该菜品的会员优惠价信息
        $memberpriceModel = M("MemberPrice");
        $memberprice_info = $memberpriceModel->where(array("foods_id"=>array("eq",$foods_id)))->select();      
        $this->assign("memberprice_info",$memberprice_info);
        //获得该菜品的相册
        $foodspicsModel = M("FoodsPics");
        $foodspics_info = $foodspicsModel->where(array("foods_id"=>array("eq",$foods_id)))->order("foods_pic_id asc")->select();
        $this->assign("foodspics_info",$foodspics_info);
        
        $this->setPageBtn('菜品编辑', '菜品列表', U("foodsList"));
        $this->display();
    }
    /**
     * 菜品编辑
     */
    public function foodsDetail(){
        // 建立数据处理模型
        $model = D("Admin/Foods");
        //获得修改菜品的id
        $foods_id = I("get.foods_id");
    
        $foods_info = $model->find($foods_id);
        $this->assign("foods_info",$foods_info);
        //获得菜品分类信息
        $cateModel = D("Admin/FoodsCate");
        $cate_info = $cateModel->tree();
        $this->assign("cate_info",$cate_info);
         
        //获得会员等级信息
        $memberLvlModel = D("MemberLevel");
        $memberlvl_info = $memberLvlModel->select();
        $this->assign("memberlvl_info",$memberlvl_info);
        //获得菜品拓展分类信息
        $foodsextcateModel = M("FoodsExpandCate");
        $foodsextcate_info = $foodsextcateModel->where(array("foods_id"=>array("eq",$foods_id)))->select();
        $this->assign("foodsextcate_info",$foodsextcate_info);
    
        //获得该菜品的会员优惠价信息
        $memberpriceModel = M("MemberPrice");
        $memberprice_info = $memberpriceModel->where(array("foods_id"=>array("eq",$foods_id)))->select();
        $this->assign("memberprice_info",$memberprice_info);
        //获得该菜品的相册
        $foodspicsModel = M("FoodsPics");
        $foodspics_info = $foodspicsModel->where(array("foods_id"=>array("eq",$foods_id)))->order("foods_pic_id asc")->select();
        $this->assign("foodspics_info",$foodspics_info);
    
        $this->setPageBtn('菜品编辑', '菜品列表', U("foodsList"));
        $this->display();
    }
    /**
     * 移除菜品至回收站
     */
    public function foodsRecycle(){
        //接收菜品id
        $foods_id = I("get.foods_id");
       
        $model = M("Foods");
        $res = $model->where(array("foods_id" => array("eq",$foods_id)))->setField(array('is_delete' => 1));
        
        $this->success('操作成功！', U('foodsList', array('p' => I('get.p', 1))));
        
    }
    /**
     * 回收站中的菜品列表
     */
    public function foodsRecycleList(){
        $model = D("Admin/Foods");
        $foods_info = $model->showlist(session("restaurant_id"),1);
         
        $this->assign(array(
            "data" => $foods_info['data'],
            "page" => $foods_info['page']
        ));
        $this->setPageBtn('菜品回收站列表', '菜品列表', U("foodsList"));
        //获得菜品分类信息
        $cateModel = D("Admin/FoodsCate");
        $cate_info = $cateModel->tree();
        $this->assign("cate_info",$cate_info);
        
        $this->display();
    }
    /**
     * 从回收站中回收菜品
     */
    public function foodsRestore(){
        $foods_id = I("get.foods_id");
        $model = M('Foods');
        $model->where(array(
            'foods_id' => array('eq', $foods_id),
        ))->setField('is_delete', 0);
        $this->success('操作成功！', U('foodsList', array('p' => I('get.p', 1))));
    }
    /**
     * 菜品删除
     */
    public function foodsDelete(){
        $model = D("Admin/Foods");
        
        $res = $model -> delete(I("get.foods_id"));
        if($res !== false){
            
            $this->success("删除成功！",U("foodsRecycleList?p=".I("get.p")));
        }
       
    }
    /**
     * 菜品列表
     */
    public function foodsList(){
        $model = D("Admin/Foods");
        $foods_info = $model->showlist(session("restaurant_id"));
       
        $this->assign(array(
           "data" => $foods_info['data'],
           "page" => $foods_info['page']
        ));
       $this->setPageBtn('菜品列表', '添加菜品', U("foodsAdd"));
       //获得菜品分类信息
       $cateModel = D("Admin/FoodsCate");
       $cate_info = $cateModel->tree();
       $this->assign("cate_info",$cate_info);
       $this->display();
        
    }
    //ajax删除菜品相册图片
    public function deletePicByIdAjax() {
       
        $pic_id = I("get.pic_id");
       
        $foodspicsModel = M("FoodsPics");
        //删除本地的菜品相册图片
        $pic = $foodspicsModel->field("pic,sm_pic")->find($pic_id);
        deleteImage(array($pic['pic'],$pic["sm_pic"]));
        $res = $foodspicsModel -> delete($pic_id);
        
        if($res !== false){
            echo 1;
        }else{
            echo 0;
        }    
    }
    
}
?>