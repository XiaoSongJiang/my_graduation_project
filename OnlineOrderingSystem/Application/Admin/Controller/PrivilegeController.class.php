<?php

 namespace Admin\Controller;
 use Tools\AdminBaseController;
 class PrivilegeController extends AdminBaseController{
     /**
      * 权限添加
      */
     function privilegeAdd(){
         //建立数据模型
         $priModel = D("Privilege");
         if(IS_POST){
            
             //收集表单数据
             if ($priModel->create(I("post."),1)){
                 //插入数据到数据库
                 if($priModel->add()){
                     // 提示信息
                     $this->success('操作成功！', U('privilegeList'));
                     // 停止执行后面的代码
                     exit();
                 } 
             }
             // 错误处理
             $error = $priModel->getError();
             $this->error($error);
         }
         
         $this->setPageBtn('权限添加', '权限列表', U("privilegeList"));
         //查询所有的分类用于显示
         $privilege_data = $priModel->tree();
         
         $this->assign("privilege_data",$privilege_data);
         $this->display();
     }
     /**
      *权限修改
      */
     function privilegeEdit(){
         $pri_id = I("get.pri_id");
         $priModel = D("Admin/Privilege");
         if(IS_POST){
             //收集表单数据
             
             if ($priModel->create(I("post."),2)){
                 //更新数据到数据库
                
                 if($priModel->save() !== FALSE){
                     // 提示信息
                     $this->success('修改成功！', U('privilegeList'));
                     // 停止执行后面的代码
                     exit();
                 }
                 $error = $priModel->getError();
                 $this->error($error,U("Privilege/privilegeList"),2);
             }
             // 错误处理
             $error = $priModel->getError();
             $this->error($error);
         }
         $one_pri = $priModel->find($pri_id);//选中的id的一条记录
         $this->assign("onepri",$one_pri);
         //查询所有的分类用于显示
         $privilege_data = $priModel->tree();
         $this->assign("privilege_data",$privilege_data);
         
         $this->setPageBtn('权限修改', '权限列表', U("privilegeList"));
         $this->display();
     }
     /**
      * 权限列表
      */
     function privilegeList(){
         $priModel = D("Admin/Privilege");
         $privilege_data = $priModel->tree();
         $this->assign("privilege_data",$privilege_data);
         $this->setPageBtn('权限列表', '权限添加', U("privilegeAdd"));
         $this->display();
     }
     /**
      * 权限删除
      */
     function privilegeDelete(){
         $priModel = D("Admin/Privilege");
         if ($priModel->delete(I("get.pri_id",0)) !== FALSE){//如果不存在$_GET['id'] 则返回0
             $this->success('删除成功！', U('privilegeList'));
             exit;
         } else {
             $this->error($priModel->getError());
         }
        
     }
 }

?>