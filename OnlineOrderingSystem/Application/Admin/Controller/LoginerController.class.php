<?php

namespace Admin\Controller;
use Think\Controller;
/**
 * 登录者控制器
 */
class LoginerController extends Controller{
    
    /**
     * 登录动作
     */
    public function login(){
        if (IS_POST) {
            //生成管理员模型
            $manModel = D("Admin/Manager");
            //验证登录信息的合法性
            //我这里把登录的规则和添加修改管理员的规则分成了两个，所以这里要指定使用哪个 
            
            if ($manModel->validate($manModel->login_validate)->create('',10)) {
                //验证管理员个人信息正确与否           
               
                if ($manModel->chk_login() === TRUE) {
                   $this->success("登录成功!",U("Index/index"),3);// 直接跳转可以不提示信息
                   exit();
                } 
            }
            $this->error($manModel->getError());
        }
        $this->display();
    }
    
    /**
     * 制作验证码图片
     */
    public function verify(){
		ob_clean();
        $Verify = new \Think\Verify(array(
            'length' => 4,
            'useNoise' => true,
            'fontSize' =>15,
            'imageH'    => 40,
            'imageW'    => 120
        ));
        $Verify->entry();
    }
    /**
     * 登出
     */
    public function logout(){
        session("manager_id",null);
        cookie("manager_id",null,"/");
        $this->success("成功退出","login",2);
    }
}
 
?>