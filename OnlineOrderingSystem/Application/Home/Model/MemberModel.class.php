<?php
namespace Home\Model;
use Think\Model;
class MemberModel extends Model{
    public $is_manager_add = false;
    
    protected $insertFields = array('email','password','cpassword',"checkcode",'receiver','address','receiver_mobile',"member_id");
    protected $updateFields = array('address_id','member_id','nickname','password','cpassword',"mobile","mem_face",'address','receiver','address','receiver_mobile');
     
    // 登录时表单验证的规则
    public $_login_validate = array(
        array('email', 'require', '用户名不能为空！', 1),
        array('password', 'require', '密码不能为空！', 1),
        array('checkcode', 'require', '验证码不能为空！', 1),
        array('checkcode', 'checkVerify', '验证码不正确！', 1, 'callback'),
    );
    // 添加和修改收货地址时的规则
    public $_address_validate = array(
        array('member_id', 'require', '你尚未登录！', 1),
        array('receiver', 'require', '收货人姓名不能为空！', 1),
        array('address', 'require', '收货地址不能为空！', 1),
        array('receiver_mobile', 'checkMobile', '手机号码格式不正确', 1, 'callback'),
    );
    // 添加和会员时的规则
    public $_validate = array(
        //array(验证字段1,验证规则,错误提示,[验证条件,附加规则,验证时间]),
        // 添加修改订餐用户会员时用
        array('checkcode', 'require', '验证码不能为空！', 0, 'regex', 1),
        array('checkcode','checkVerify','验证码不正确',0,'callback',1),
        array('email', 'require', '账号不能为空！', 1, 'regex', 1),
        array('email', '', '账号已存在！', 1, 'unique', 1),
        array('nickname', 'require', '昵称不能为空！', 1, 'regex', 2),
        array('email', 'email', '账号格式不正确！', 1,'regex', 1),
        array('password', 'require', '密码不能为空！', 1, 'regex', 1),
        array('password', '6,20', '密码必须是6-20位的字符！', 2, 'length',3),
        array('cpassword', 'password', '前后密码不一致', 1, 'confirm', 3),
        array('mobile', 'require', '手机号码不能为空', 1, 'regex', 2),
        array('mobile', '11', '手机号码长度不正确', 1, 'length', 2),
        array('mobile', 'checkMobile', '手机号码格式不正确', 1, 'callback', 2),
    );
    /**
     * 
     * @param unknown $code 用户验证码
     * @return boolean 用户验证码是否正确
     */
    public function checkVerify($code){
        $verify = new \Think\Verify();
        
        return $verify->check($code);
    }
    public function checkMobile($mobile){
        if(strlen($mobile) == 11){
            
            //上面部分判断长度是不是11位
            $n = preg_match_all('/13[123569]{1}\d{8}|15[1235689]\d{8}|18[123569]{1}\d{8}/',$mobile,$array);
            
            if ($n){
                return true;
            } else {
                return false;
            }
        }else{
            
            return false;
        }
    }
    /**
     * 注册会员插入数据库之前
     */
    protected function _before_insert(&$data, $options){
        $data['addtime'] = time();  // 注册的当前时间
        // 生成验证email用的验证码
        $data['email_code'] = md5(uniqid());
        // 先把会员的密码加密
        $data['password'] = md5($data['password'] . C('MD5_KEY'));
    }
    /**
     * 会员插入数据库之后（未激活）
     */
    protected function _after_insert($data, $options){
       
        //如果管理员在后台直接添加的订餐用户会员，则不向会员邮箱发送激活码
        if ($this->is_manager_add){
            
            $this->where(array('member_id'=>array('eq', $data['member_id'])))->setField('email_code', '');
            
        }else{
            
            $content = <<<str
            <p>欢迎您成为本站会员，请点击以下链接地址完成email验证。</p>
            <p><a href="http://www.ydors.com/index.php/Home/Member/emailForRegister/code/{$data['email_code']}">点击完成验证</a></p>
str;
            // 把生成的验证码发到会员的邮箱中
            sendmail($data['email'], '易达在线订餐网email验证', $content);
        }
        
    }
    protected function _before_update(&$data, $options){
        //上传头像图片
        if(isset($_FILES['face']) && $_FILES['face']['error'] == 0){
            $res = uploadOne("face", "Member",array(
                array(50,50)
            ));
        
            if($res["ok"] == 1){
                $ori_face_name = ltrim($res["images"][0],'.');
                $sm_face_name = ltrim($res["images"][1],'.');
                
                $data['face'] = $ori_face_name;
                $data['sm_face'] = $sm_face_name;
                //删除原图片
                $face = $this->field("face,sm_face")->find($options['where']['member_id']);
                deleteImage(array($face['face'],$face["sm_face"]
                ));
                 
            }else{
                $this->error = $res["error"];
                 
                return false;
            }
        } else {
            $data['face'] = $data['sm_face'] = I("post.mem_face");
        }
       
        // 密码为空则不修改密码
        if (empty($data['password'])){
    	        unset($data['password']);
    	} else {
    	        $data['password'] = md5($data['password'].C("MD5_KEY"));
    	}
    }
    public function loginConfirm(){
        $eamil = $this->email;
        $password = $this->password;
        $has_eamil = $this -> where(array("email"=>array('eq',$eamil))) ->find();
        if ($has_eamil){
            //判断用户是否激活
            if (empty($has_eamil['email_code'])){
                //账号已经激活
                //判断是否被禁用
                if ($has_eamil['is_use']){
                    //判断用户密码是否正确
                    if ($has_eamil['password'] == md5($password.C('MD5_KEY'))){
                        //登录成功后，把该会员的信息存入session
                        session('member_id', $has_eamil['member_id']);
                        session('email', $has_eamil['email']);
                        session("points",$has_eamil['points']);
                        session("xp",$has_eamil['xp']);
                        cookie("member_id",$has_eamil['member_id'],"/");
                        //redis
                        $redis = new \Redis();
                        $redis->connect('127.0.0.1',6379);
                        $redis->select(5);
                        $redis->lpush('newlogin',$has_eamil['email']);
                        $redis->ltrim('newlogin', 0, 4);
                        
                        $cartModel = D("Home/Cart");
                        $cartModel->moveDataToDb();
                        return TRUE;
                        }else {
                            $this->error = '密码错误！';
                            return FALSE;
                        }
                
                }else{
                    $this->error = '账号被禁用！';
                    return FALSE;
                }
            }else{
                $this->error = '账号还没有通过email验证激活！';
                return FALSE;
            }
        }else{
            $this->error = '账号不存在';
            return FALSE;
        }
    }
    /**
     * 订餐用户会员以某种形式展示展示
     * $all 表示是否显示全部会员  0：显示所有订餐用户  1~.. 表示显示那个餐厅的所有会员
     */
    public function showlist($all = 0){
        $where = array();
        //按订餐用户会员账号搜索
        $email = I("get.email");
        if ($email){
            $where['email'] = array("eq","$email");
        }
        //按订餐用户会员是否禁用搜索
        $is_use = I("get.is_use");
        if($is_use != '' && $is_use != '-1')
            $where['is_use'] = array('eq', $is_use);
        //翻页实现
    
        $totalRows = $this->where($where)->count();
    
        if ($totalRows > 0){
            $Page = setPage($totalRows,5);
            $show = $Page->show();// 分页显示输出
            // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
            $list = $this
            ->where($where)
            ->limit($Page->firstRow . ',' . $Page->listRows)
            ->select();
    
            return array(
                'data' => $list,
                'page' => $show
            );
        }
    }
    /**
     * 支付订单
     */
    public function memberPayForOrder($order_id){
        //先查出订单的总价
        $orderModel = M("Order");
        $member_id = session("member_id");
        $order_total_price = $orderModel -> find($order_id)['total_price'];
        //查出会员的可用资金
        $member_money = $this->find($member_id)['surplus_money'];
        if ($member_money < $order_total_price){
            $this->error = "剩余资金不足，无法购买！";
            return ;
        }
        //启动事务
        mysqli_query('START TRANSACTION');
        //增加餐厅余额
        $restaurant_info = $orderModel->field("c.restaurant_id,b.foods_id,b.foods_price,b.foods_num")
                                      ->alias('a')->join("left join yd_order_foods b on a.order_id = b.order_id")
                                      ->join("left join yd_foods c on b.foods_id = c.foods_id")
                                      ->where("a.order_id = $order_id")
                                      ->select();
        
        $restaurantModel = M("Restaurant");
        foreach ($restaurant_info as $key => $val){
            //循环更新多个餐厅
            $houston = $val['foods_num'] * $val['foods_price'] +10;
            $sql = "update yd_restaurant set houston=houston+$houston,totalsales=totalsales+{$val['foods_num']} where  restaurant_id={$val['restaurant_id']}";
            $res = $restaurantModel->execute($sql);
            if ($res === FALSE){
                mysqli_query('ROLLBACK');
                return FALSE;
            }
        } 
        //减少会员余额
        $surplus_money = $member_money - $order_total_price - 10*count($restaurant_info);
        $ret = $this->where("member_id = $member_id")->setField("surplus_money",$surplus_money);
        if ($ret === FALSE){
            mysqli_query('ROLLBACK');
            return FALSE;
        }
        mysqli_query('COMMIT'); // 提交事务
        return TRUE;
    }
}
?>