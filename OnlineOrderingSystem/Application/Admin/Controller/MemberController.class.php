<?php
namespace Admin\Controller;
use Tools\AdminBaseController;
class MemberController extends AdminBaseController {
    /**
     * 添加订餐用户会员
     */
    public function memberAdd()
    {
        // 处理表单数据
        if (IS_POST) {
            // 建立数据处理模型
            $model = D("Home/Member");
            $model->is_manager_add = true;        
            if ($model->create(I("post."),1)) {
                
                if ($model->add()) {
                    // 提示信息
                    $this->success('操作成功！', U('memberList'));
                    // 停止执行后面的代码
                    exit();
                }
            }
            // 错误处理
            $error = $model->getError();
            $this->error($error);
        }
        $this->setPageBtn('订餐用户会员添加', '订餐用户会员列表', U("memberList"));
        $this->display();
    }
    /**
     * 修改
     */
    public function memberEdit(){
        // 建立数据处理模型
        $model = D("Home/Member");
        if (IS_POST){
           
            if ($model->create(I("post."),2)) {
                // 插入数据库
                if (false !== $model->save()) { //save()返回的是受影响的记录数，没有修改则返回0 错误返回false
                    // 提示信息
                   
                    $this->success('修改成功！',U('memberList?p='.I("get.p")));
                    // 停止执行后面的代码
                    exit();
                }
            }
            // 错误处理
            $error = $model->getError();
            $this->error($error);
        }
        //获得修改订餐用户会员的id
        $member_id = I("get.member_id");
        $member_info = $model->find($member_id);
        $this->assign("member_info",$member_info);
        $this->setPageBtn('订餐用户会员编辑', '订餐用户会员列表', U("memberList"));
        $this->display();
    }
    /**
     * 订餐用户会员删除
     */
    public function memberDelete(){
        $model = D("Home/Member");
        
        $res = $model -> delete(I("get.member_id"));
        if($res !== false){
            
            $this->success("删除成功！",U("memberList?p=".I("get.p")));
        }else {
            $this->error($model->getError());
        }
       
    }
    /**
     * 订餐用户会员列表
     */
    public function memberList(){
        $Model = D("Home/Member");
        $member_info = $Model->showlist();
       
        $this->assign(array(
           "data" => $member_info['data'],
           "page" => $member_info['page']
        ));
       $this->setPageBtn('订餐用户会员列表', '添加订餐用户会员', U("memberAdd"));
       $this->display();
        
    }
    /**
     * ajax改变订餐用户会员账号是否启用
     * 
     */
    public function ajaxChangeIsuse(){
        //获得将要修改的订餐用户会员的id
        
        $member_id = I("get.member_id");
        
        $memberModel = M("Member");
        
        $member_info = $memberModel->find($member_id);
        //如果当前为启用则变为禁用
        if ($member_info['is_use'] == 1){
            $memberModel->where(array("member_id"=>array("eq",$member_id)))->setField("is_use",0);
            
            echo "0";
            
        }else{
            $memberModel->where(array("member_id"=>array("eq",$member_id)))->setField("is_use",1);
           
            echo "1";
        }
    } 
}














