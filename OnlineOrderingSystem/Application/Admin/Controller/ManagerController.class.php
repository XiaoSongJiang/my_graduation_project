<?php
namespace Admin\Controller;
use Tools\AdminBaseController;
class ManagerController extends AdminBaseController {
    /**
     * 添加管理员
     */
    public function managerAdd()
    {
        // 处理表单数据
        if (IS_POST) {
            // 建立数据处理模型
            $model = D("Admin/Manager");
            if ($model->create(I("post."),1)) {
                // 插入数据库
                if ($model->add()) {
                    // 提示信息
                    $this->success('操作成功！', U('managerList'));
                    // 停止执行后面的代码
                    exit();
                }
            }
            // 错误处理
            $error = $model->getError();
            $this->error($error);
        }
        $this->setPageBtn('管理员添加', '管理员列表', U("managerList"));
        //取出所有的角色
        $roleModel = D("Admin/Role");
        $role_info = $roleModel->select();
        $this->assign("role_info",$role_info);
        $this->display();
    }
    /**
     * 管理员修改
     */
    public function managerEdit(){
        // 建立数据处理模型
        $model = D("Admin/Manager");
        if (IS_POST){
           
            if ($model->create(I("post."),2)) {
                // 插入数据库
                if (false !== $model->save()) { //save()返回的是受影响的记录数，没有修改则返回0 错误返回false
                    // 提示信息
                   
                    $this->success('修改成功！',U('managerList?p='.I("get.p")));
                    // 停止执行后面的代码
                    exit();
                }
            }
            // 错误处理
            $error = $model->getError();
            $this->error($error);
        }
        //获得修改管理员的id
        $manager_id = I("get.manager_id");
        //获得当前登录的管理员的id
        $now_manager_id = session("manager_id");
        //如果是普通管理员则只能修改自己，超级管理员可以修改所有
        if ($now_manager_id != 1 && $now_manager_id != $manager_id){
           $this->error("无权修改!");
        }
        $manager_info = $model->find($manager_id);
        $this->assign("manager_info",$manager_info);
        //取出所有的角色
        $roleModel = D("Admin/Role");
        $role_info = $roleModel->select();
        $this->assign("role_info",$role_info);
        //取出当前管理员所属的角色 操作管理员角色表
        $mrModel = M("ManagerRole");
        $mr_info = $mrModel->field("group_concat(role_id) role_ids")->where(array(
            "manager_id"=>array("eq",I("get.manager_id"))
        ))->find();
        $mr_info = explode(",",$mr_info['role_ids']);
        $this->assign("mr_info",$mr_info);
        $this->setPageBtn('管理员编辑', '管理员列表', U("managerList"));
        $this->display();
    }
    /**
     * 管理员删除
     */
    public function managerDelete(){
        $model = D("Admin/Manager");
        
        $res = $model -> delete(I("get.manager_id"));
        if($res !== false){
            
            $this->success("删除成功！",U("managerList?p=".I("get.p")));
        }else {
            $this->error($model->getError());
        }
       
    }
    /**
     * 管理员列表
     */
    public function managerList(){
        $manModel = D("Admin/Manager");
        $manager_info = $manModel->showlist();
       
        $this->assign(array(
           "data" => $manager_info['data'],
           "page" => $manager_info['page']
        ));
       $this->setPageBtn('管理员列表', '添加管理员', U("managerAdd"));
       $this->display();
        
    }
    /**
     * ajax改变管理员账号是否启用
     * 
     */
    public function ajaxChangeIsuse(){
        //获得将要修改的管理员的id
        
        $manager_id = I("get.manager_id");
        if ($manager_id == 1){
            return FALSE;
        }
        $managerModel = M("Manager");
        
        $manager_info = $managerModel->find($manager_id);
        //如果当前为启用则变为禁用
        if ($manager_info['is_use'] == 1){
            $managerModel->where(array("manager_id"=>array("eq",$manager_id)))->setField("is_use",0);
            
            echo "0";
            
        }else{
            $managerModel->where(array("manager_id"=>array("eq",$manager_id)))->setField("is_use",1);
           
            echo "1";
        }
    }

    /**
     * 餐厅信息修改
     */
    public function restaurantEdit(){
        
        if (IS_POST){
             
            $restaurantModel = D('Admin/Restaurant');
            if($restaurantModel->create(I('post.'), 2)){
                 
                if (FALSE !== $restaurantModel->save()) {
                    $this->success('修改成功！');
                     
                    // 停止执行后面的代码
                    exit();
                }
            }
            $this->error($restaurantModel->getError());
        }
    
        $restaurant_id = I("get.restaurant_id");
        $restaurantModel = D("Admin/Restaurant");
        $restaurant_info = $restaurantModel->where("restaurant_id = $restaurant_id")->find();
        $this->assign("rest_info",$restaurant_info);
        $this->assign('page_title', "餐厅信息中心");
        $this->assign('net_title', "餐厅信息中心");
        $this->display();
         
    }
    public function  restaurantDelete(){
        $model = D("Admin/Restaurant");
    
        $res = $model -> delete(I("get.restaurant_id"));
        if($res !== false){
    
            $this->success("删除成功！");
        }
    }
    /**
     * ajax方式允许开店申请
     */
    public function applyConfirmByAjax(){
        $restaurant_id = I("get.restaurant_id");
        $restaurantModel = D("Admin/Restaurant");
        $res = $restaurantModel->where("restaurant_id = $restaurant_id")->setField("restaurant_state","1");
        if ($res) {
            echo 1;
        }
    }
    /**
     * 餐厅列表,用于管理员查看
     */
    public function restaurantList(){
        $manager_id = session('manager_id');
         
        if(empty($manager_id)){
            $url = U('Admin/Loginer/login');
            $str = <<<eof
            <script type="text/javascript">
                window.top.location.href = "$url";
            </script>
eof;
            echo $str;
             
            exit();
        }
        $restaurantModel = D("Admin/Restaurant");
        $list = $restaurantModel->showList();
    
        $this->assign(array(
            "list" => $list['data'],
            "page" => $list['page']
    
        ));
        $this->assign('page_title', "餐厅列表");
        $this->assign('net_title', "餐厅列表");
    
        $this->display();
    }
    /**
     * 所有菜品列表
     */
    public function foodsList(){
        $model = D("Admin/Foods");
        $foods_info = $model->showlist();
         
        $this->assign(array(
            "data" => $foods_info['data'],
            "page" => $foods_info['page']
        ));
        $this->setPageBtn('菜品列表');
        //获得菜品分类信息
        $cateModel = D("Admin/FoodsCate");
        $cate_info = $cateModel->tree();
        $this->assign("cate_info",$cate_info);
        $this->display();
    
    }
    public function managerPwdModify(){
        //获得修改管理员的id
        $manager_id = session("manager_id");
        
        // 建立数据处理模型
        $model = D("Admin/Manager");
        if (IS_POST){
             
            if (!!$data = $model->validate($model->modify_validate)->create(I('post.'),15)) {
                // 插入数据库
                dump($data);
               $model = M("Manager");die;
               $data['password'] = md5(C("MD5_KEY").$data['password']);
                if (false !== $model->setField($data)) { //setField()返回的是受影响的记录数，没有修改则返回0 错误返回false
                    // 提示信息
                    //要求重新dl
                    session("manager_id",null);
                    cookie("manager_id",null,"/");
                    customredict("Admin/Loginer/login","修改成功,请重新登录");
                    // 停止执行后面的代码
                    exit();
                }
            }
            // 错误处理
            $error = $model->getError();
            $this->error($error);
        }
        $this->setPageBtn('管理员密码修改', '', "","管理员密码修改");
        $manager_info = $model->find($manager_id);
        $this->assign("manager_info",$manager_info);
        $this->display();
    }
    /**
     * 获取所有餐厅所有菜品的评论信息
     */
    public function restaurantFoodsCommentList() {
       
        $commentModel = D("Home/Comment");
        $comment_foods_info =  $commentModel->getAllComment();
        $this->assign("comment_foods_info",$comment_foods_info);
         
        $this->setPageBtn('订餐用户购买菜品评论','','','订餐用户购买菜品评论');
        $this->display();
    }
    public function restaurantFoodsCommentEdit() {
        $comment_id = I("get.comment_id");
        $commentModel = D("Home/Comment");
        $comment_foods_one =  $commentModel->field("a.*,b.foods_name")->alias("a")->join("left join yd_foods b on a.foods_id = b.foods_id")->where("a.comment_id=$comment_id")->find();
        if (IS_POST){
            if ($commentModel->create(I("post."),2)) {
                // 插入数据库
                 
                if (false !== $commentModel->save()) { //save()返回的是受影响的记录数，没有修改则返回0 错误返回false
                    // 提示信息
                     
                    $this->success('修改成功！',U('restaurantFoodsCommentList?p='.I("get.p")));
                    // 停止执行后面的代码
                    exit();
                }
            }
            // 错误处理
            $error = $commentModel->getError();
            $this->error($error);
        }
        $this->assign("comment_foods_one",$comment_foods_one);
         
        $this->setPageBtn('菜品评论编辑','','','菜品评论编辑');
        $this->display();
    }
    public function restaurantFoodsCommentDelete() {
         
        $commentModel = D("Home/Comment");
        $res = $commentModel -> delete(I("get.comment_id"));
        if($res !== false){
            
            $this->success("删除成功！",U("Admin/Manager/restaurantFoodsCommentList?p=".I("get.p")));
        }
    }
}














