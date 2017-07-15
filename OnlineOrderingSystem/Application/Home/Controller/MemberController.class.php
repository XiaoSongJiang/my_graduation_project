<?php
namespace Home\Controller;
use Tools\HomeBaseController;
class MemberController extends HomeBaseController {
    
    public function  __construct(){
        parent::__construct();
        if (session("member_id")){
            $member_id = session("member_id");
            $model = D('Home/Member');
            $member_info = $model->where("member_id = $member_id")->find();
            
            if (!$member_info['is_use']){
                session("member_id",null);
                session("email",null);
                cookie("member_id",null,"/");
                $this->error("你的账号已被禁用","/",2);
                
            }
        }
        
    }
    /**
     * 会员注册
     */
    public function register(){
        //登录状态下不能注册
        if (session('member_id')){
            $this->error("请先退出登录","/",2);
        }
        if(IS_POST)
        {
            $memberModel = D('Home/Member');
            if($memberModel->create(I('post.'), 1))
            {
                if($memberModel->add())
                {
                    $this->success('注册成功，请登录到您的邮件中完成验证！');
                    exit;
                }
            }
            $this->error($memberModel->getError());
        }
        $this->setHeadInfo("易达在线订餐会员注册", "易达在线订餐会员注册", "易达在线订餐会员注册",0,array("login"));
        $this->display();
    }
    /**
     * 会员登录
     */
    public function login(){
        
        if (session('member_id')){
            $this->error("请先退出登录","/",2);
        }
        if (IS_POST){
            $memberModel = D('Home/Member');
            if($memberModel->validate($memberModel->_login_validate)->create(I('post.'), 8))
            {
                if($memberModel->loginConfirm())
                {
                    
                    $return_url = cookie("return_url");
                    
                    if ($return_url){
                        cookie("return_url",null,"/");
                        $this->success('登录成功！',$return_url,3);
                        exit;
                    }
                                        
                     $this->success('登录成功！',"/",3);
                     exit;
                }
            }
            $this->error($memberModel->getError());
        }
        $this->setHeadInfo("易达在线订餐会员登录", "易达在线订餐会员登录", "易达在线订餐会员登录",0,array("login"));
        $this->display();
    }
    /**
     * 设置用户登录cookie
     */
    public function setCookie(){
        $email = I("get.email");
        $password = I("get.password");
        cookie('email',$email,3600);
        cookie('password',$password,3600);
    }
    /**
     * 会员登出
     */
    public function logout(){
        session("member_id",null);
        session("email",null);
        cookie("member_id",null,"/");
        
        $this->success("成功退出","login",2);
    }
    /**
     * 发送email至注册用户邮箱
     */
    public function emailForRegister(){
        // 接收会员传回来的验证码
        $code = I('get.code');
        if($code)
        {
            // 把这个验证码到数据库中比较一下即可
            $model = D('Home/Member');
            $email = $model->where(array('email_code'=>array('eq', $code)))->find();
            if($email)
            {
                // 设置这个账号为已验证
                $model->where(array('member_id'=>array('eq', $email['member_id'])))->setField('email_code', '');
                $this->success('已经完成验证激活，现在可以去登录', U('login'));
                exit;
            }
        }
    }
    /**
     * ajax用户登录信息
     */
    public function ajaxLogin(){

        if(session('member_id')){
            $arr = array(
                'ok' => 1,
                'email' => session('email'),
            );
        }
        else{
            $arr = array('ok' => 0);
        }
        echo json_encode($arr);
    }
    /**
     * 个人中心，默认显示个人信息
     */
    public function memberCenter(){
        if (!session("member_id")){
            cookie("return_url",U("memberCenter"));
            $this->error("请先登录！",U("login"),1);
        }
        $memberModel = D('Home/Member');
        $member_info = $memberModel->find(session("member_id"));
       
        $this->assign("member_info",$member_info);
        $this->setHeadInfo("易达在线订餐会员中心", "易达在线订餐会员中心", "易达在线订餐会员中心",0,array("user","home"),array("home"));
        $this->display();
    }
    /**
     * 修改个人信息
     */
    public function memberInfoEdit(){
        if (!session("member_id")){
            cookie("return_url",U("memberInfoEdit"));
            $this->error("请先登录！",U("login"),1);
        }
       $memberModel = D('Home/Member');
       if($memberModel->create(I('post.'), 2)){
           
           if (FALSE !== $memberModel->save()) {
               $this->success('修改成功！',U("memberCenter"),2);
               
               // 停止执行后面的代码
               exit();
           }
       }
       $this->error($memberModel->getError());
       
    }
    /**
     * 显示和新增收货地址
     */
    public function addressAddList(){
        if (!session("member_id")){
            cookie("return_url",U("addressAddList"));
            $this->error("请先登录！",U("login"),1);
        }
        $member_id = session("member_id");
        $addressModel = D("Home/MemberAddress");
        
        if (IS_POST) {
            $data = $addressModel->validate($addressModel->_address_validate)->create(I('post.'), 1);
            if($data){
               //插入收货地址前，须判断当前用户收货地址是否拥有唯一默认值
               
                $address_default = $addressModel->field("is_default")->where("member_id = $member_id")->select();
                foreach ($address_default as $key => $val){
                    if ($val['is_default'] == $data['is_default']) {
                        //把数据表中所有的is_default字段设置为0
                        $res = $addressModel->where("member_id = $member_id")->setField("is_default","0");
                        if ($res) {
                            break;
                        }
                    }
                }
                
                if($addressModel->add()) {
                    $this->success('添加收货地址成功！',U("addressAddList"),1);
                    exit;
                }
            }
            
            $this->error($addressModel->getError());
        }
        //获取当前用户的所有收货地址信息
        $address_info = $addressModel->where("member_id = $member_id")->select();
        $this->assign("address_info",$address_info);
        $this->setHeadInfo("易达在线订餐会员中心", "易达在线订餐会员中心", "易达在线订餐会员中心",0,array('user',"address","home"),array("home"));
        $this->display();
    }
    /**
     * 修改收货地址
     */
    public function addressEdit(){
        if (!session("member_id")){
            cookie("return_url",U("addressEdit"));
            $this->error("请先登录！",U("login"),1);
        }
        $member_id = session("member_id");
        $address_id = I("get.address_id");
        
        $addressModel = D("Home/MemberAddress");
        if (IS_POST){
            $data = $addressModel->validate($addressModel->_address_validate)->create(I('post.'), 2);
            
            if ($data){
                //修改收货地址前，须判断当前用户收货地址是否拥有唯一默认值
                
                $address_default = $addressModel->field("is_default")->where("member_id = $member_id")->select();
                foreach ($address_default as $key => $val){
                    if ($val['is_default'] == $data['is_default']) {
                        //把数据表中所有的is_default字段设置为0
                        $res = $addressModel->where("member_id = $member_id")->setField("is_default","0");
                        if ($res) {
                            break;
                        }
                    }
                }
                if($addressModel->save() !== FALSE ) {
                    $this->success('修改收货地址成功！',U("addressAddList"),1);
                    exit;
                }
            }
           
            $this->error($addressModel->getError());
            
        }
        //获取当前用户的所选中收货地址信息
        $address_one = $addressModel->where("address_id = $address_id and member_id = $member_id")->find();
        $this->assign("address_one",$address_one);
        //获取当前用户的所有收货地址信息
        $address_info = $addressModel->where("member_id = $member_id")->select();
        $this->assign("address_info",$address_info);
        $this->setHeadInfo("易达在线订餐会员中心", "易达在线订餐会员中心", "易达在线订餐会员中心",0,array('user',"address","home"),array("home"));
        $this->display();
    }
    /**
     * 删除收货地址
     */
    public function addressDelete(){
        if (!session("member_id")){
            cookie("return_url",U("addressAddList"));
            $this->error("请先登录！",U("login"),1);
        }
        $member_id = session("member_id");
        $address_id = I("get.address_id");
        
        $addressModel = M("MemberAddress");
        $res = $addressModel->where("member_id = $member_id and address_id = $address_id")->delete();
        if ($res !== false){
            $this->redirect("addressAddList");
        }
    }
    /**
     * 设置默认收货地址
     */
    public function changeAddressDefault(){
        if (!session("member_id")){
            cookie("return_url",U("changeAddressDefault"));
            $this->error("请先登录！",U("login"),1);
        }
        //接受参数
        $member_id = session("member_id");
        $address_id = I("get.address_id");
        
        $addressModel = M("MemberAddress");
        $addressModel->where("member_id = $member_id")->setField("is_default","0");
        $res = $addressModel->where("member_id = $member_id and address_id = $address_id")->setField("is_default","1");
        if ($res !== false){
            $this->redirect("addressAddList");
        }
    }
    /**
     * 我的留言
     */
    public function memberGuestbook(){
		 if (!session("member_id")){
            cookie("return_url",U("changeAddressDefault"));
            $this->error("请先登录！",U("login"),1);
        }
        //显示留言信息
        $guestbookModel = D('Home/Guestbook');
        $guestbook_info = $guestbookModel -> showGuestBook(0,session("member_id"));
        
        $this->assign("guestbook_info",$guestbook_info);
        $this->setHeadInfo("我的留言", "易达在线订餐会员中心", "易达在线订餐会员中心",0,array('user',"address","home",'restaurant','membermessageleave'),array("home","jquery.cookie"));
       
        $this->display();
    }
    /**
     * 我的评论
     */
    public function memberComment(){
		 if (!session("member_id")){
            cookie("return_url",U("changeAddressDefault"));
            $this->error("请先登录！",U("login"),1);
        }
        $member_id = session("member_id");
        $p = empty(I("get.p")) ? 1 : I("get.p");
        $commentModel = D("Home/Comment");
        $member_comment_info = $commentModel -> getCommentByMemberId($member_id,$p);
        //dump($member_comment_info);
        $this->assign("member_comment_info",$member_comment_info);
        $this->setHeadInfo("我的评论", "易达在线订餐会员中心", "易达在线订餐会员中心",0,array('user',"address","home",'restaurant','membermessageleave'),array("home","jquery.cookie"));
        $this->display();
    }
}