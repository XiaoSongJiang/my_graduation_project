<?php

namespace Admin\Controller;

use Tools\AdminBaseController;

class MemberLevelController extends AdminBaseController
{

    /**
     * 添加会员级别
     */
    public function memberLevelAdd()
    {
        // 处理表单数据
        if (IS_POST) {
            // 建立数据处理模型
            $model = D("Admin/memberLevel");
            if ($model->create(I("post."),1)) {
                // 插入数据库
                if ($model->add()) {
                    // 提示信息
                    $this->success('操作成功！', U('memberLevelList'));
                    // 停止执行后面的代码
                    exit();
                }
            }
            // 错误处理
            $error = $model->getError();
            $this->error($error);
        }
        $this->setPageBtn('会员级别添加', '会员级别列表', U("memberLevelList"));
        $this->display();
    }
    /**
     * 会员级别编辑
     */
    public function memberLevelEdit(){
        // 建立数据处理模型
        $model = D("Admin/memberLevel");
        if (IS_POST){
             
            if ($model->create(I("post."),2)) {
                // 插入数据库
                if (false !== $model->save()) { //save()返回的是受影响的记录数，没有修改则返回0 错误返回false
                    // 提示信息
                     
                    $this->success('修改成功！',U('memberLevelList?p='.I("get.p")));
                    // 停止执行后面的代码
                    exit();
                }
            }
            // 错误处理
            $error = $model->getError();
            $this->error($error);
        }
        //获得修改会员级别的id
        $id = I("get.level_id");

        $memberLevel_info = $model->find($id);
        $this->assign("memberLevel_info",$memberLevel_info);
        $this->setPageBtn('会员级别编辑', '会员级别列表', U("memberLevelList"));
        $this->display();
    }
    /**
     * 会员级别删除
     */
    public function memberLevelDelete(){
        $model = D("memberLevel");

        $res = $model -> delete(I("get.level_id"));
        if($res !== false){

            $this->success("删除成功！",U("memberLevelList?p=".I("get.p")));
        }
         
    }
    /**
     * 会员级别列表
     */
    public function memberLevelList(){
        $model = D("Admin/MemberLevel");
        
        $memberLevel_info = $model->showlist();
         
        $this->assign(array(
            "data" => $memberLevel_info['data'],
            "page" => $memberLevel_info['page']
        ));
        $this->setPageBtn('会员级别列表', '添加会员级别', U("memberLevelAdd"));
        $this->display();

    }
}
?>