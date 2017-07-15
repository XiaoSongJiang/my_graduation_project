<?php

namespace Admin\Controller;
use Tools\AdminBaseController;
class RoleController extends AdminBaseController{
    /**
     * 角色添加
     */
    function roleAdd(){
        //建立数据模型
        $roleModel = D("Admin/Role");
        if(IS_POST){

            //收集表单数据
            if ($roleModel->create(I("post."),1)){
                //插入数据到数据库
              
                if($roleModel->add()){
                    // 提示信息
                    $this->success('操作成功！', U('roleList'));
                    // 停止执行后面的代码
                    exit();
                }
            }
            // 错误处理
            $error = $roleModel->getError();
            $this->error($error);
        }
         
        $this->setPageBtn('角色添加', '角色列表', U("roleList"));
        //查询所有的权限用于显示
        $priModel = new \Admin\Model\PrivilegeModel();
        $pri_data = $priModel->tree();
        $this->assign("pri_data",$pri_data);
        $this->display();
    }
    /**
     *角色修改
     */
    function roleEdit(){
        $role_id = I("get.role_id");
        $roleModel = D("Role");
        if(IS_POST){
            //收集表单数据
            if ($roleModel->create(I("post."),2)){
                //更新数据到数据库
                
                if($roleModel->save()!==FALSE){
                    // 提示信息
                    $this->success('修改成功！', U('roleList', array('p' => I('get.p', 1))));
                    // 停止执行后面的代码
                    exit();
                }
            }
            // 错误处理
            $error = $roleModel->getError();
            $this->error($error);
        }
        //获取角色信息
        $one_role = $roleModel->find($role_id);//选中的id的一条记录
        $this->assign("onerole",$one_role);
        //根据角色id查询对应的权限
        $rpleModel = M('RolePrivilege');
        $rp_data = $rpleModel->field("role_id,group_concat(pri_id) pri_ids")->where(array(
            "role_id"=>array("eq",$role_id)
        ))->find();
         
        $rp_data = explode(",", $rp_data['pri_ids']);
        $this->assign("rp_data",$rp_data);
        //查询所有的权限用于显示
        $priModel = new \Admin\Model\PrivilegeModel();
        $pri_data = $priModel->tree();
        $this->assign("pri_data",$pri_data);
        $this->setPageBtn('角色修改', '角色列表', U("roleList"));
        $this->display();
    }
    /**
     * 角色列表
     */
    function roleList(){
        $roleModel = D("Admin/Role");
        $role_info = $roleModel->showlist();
      
        $this->assign(array(
           "data" => $role_info['data'],
           "page" => $role_info['page']
        ));
        $this->setPageBtn('角色列表', '角色添加', U("roleAdd"));
        $this->display();
    }
    /**
     * 角色删除
     */
    function roleDelete(){
        $roleModel = D("Admin/Role");
        if ($roleModel->delete(I("get.role_id",0))!==FALSE){//如果不存在$_GET['role_id'] 则返回0
            $this->success('删除成功！', U('roleList', array('p' => I('get.p', 1))));
            exit;
        }
        else{
            $this->error($roleModel->getError());
        }

    }
}
?>