<?php
namespace Admin\Model;
use Think\Model;
class RestaurantModel extends Model{
    protected $insertFields = array("restaurant_name","restaurant_email","password","cpassword","checkcode");
    protected $updateFields = array("restaurant_id","restaurant_name","restaurant_email","password","cpassword","person_leader","restaurant_mobile","restaurant_tel","restaurant_address");
     
    // 登录时表单验证的规则
    public $_login_validate = array(
        array('restaurant_name', 'require', '餐厅用户名不能为空！', 1),
        array('password', 'require', '密码不能为空！', 1),
        array('checkcode', 'require', '验证码不能为空！', 1),
        array('checkcode', 'checkVerify', '验证码不正确！', 1, 'callback'),
    );
    // 添加和修改餐厅时的规则
    public $_validate = array(
        array('checkcode', 'require', '验证码不能为空！', 1, 'regex', 1),
        array('checkcode','checkVerify','验证码不正确',1,'callback',1),
        array('restaurant_name', 'require', '餐厅名能为空', 1,"regex",3),
        array('restaurant_name', '', '餐厅名已存在', 1,"unique",3),
        array('restaurant_name', '3,20', '餐厅名长度不正确', 1, 'length', 2),
        array('restaurant_name', 'checkResName', '餐厅名不符合要求', 1,"callback",3),
        array('restaurant_email', 'email', '注册人账号格式不正确！', 1,'regex', 3),
        array('password', 'require', '密码不能为空！', 1, 'regex', 1),
        array('password', '6,20', '密码必须是6-20位的字符！', 2, 'length',3),
        array('cpassword', 'password', '前后密码不一致', 1, 'confirm', 3),
        array('restaurant_address', 'require', '餐厅地址不能为空！', 1,'regex',2),
        array('restaurant_mobile', 'checkMobile', '手机号码格式不正确', 1, 'callback',2),
        array('restaurant_tel', '7', '餐厅固定号长度不正确', 1, 'length', 2),
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
    /**
     * 验证餐厅名的回调函数
     * @param unknown $restaurant_name
     * @return boolean
     */
    public function checkResName($restaurant_name){
        
            $n = preg_match_all('/^[_a-zA-Z0-9\x{4e00}-\x{9fa5}]{2,10}$/u',$restaurant_name,$array);
        
            if ($n){
                return true;
            } else {
                return false;
            }
       
    }
    /**
     * 验证手机号码
     * @param string $mobile 用户手机号码
     * @return boolean
     */
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
       * 餐厅账号插入数据库之前
       */
      protected function _before_insert(&$data, $options){
          //插入数据库前检验餐厅注册人是否为本网站会员
          $memModel = D("Home/Member");
          $is_exist = $memModel->where("email = '{$data['restaurant_email']}'")->find();
          if (!$is_exist){
              $this->error = "餐厅注册人不是本网站会员，不能注册餐厅!";
              return FALSE;
          }
          $data['addtime'] = time();  // 注册的当前时间
          // 生成验证email用的验证码
          $data['email_code'] = md5(uniqid());
          // 先把餐厅账户的密码加密
          $data['password'] = md5($data['password'] . C('MD5_KEY'));
      }
      /**
       * 餐厅账号插入数据库之后（未激活）
       */
      protected function _after_insert(&$data, $options){
          //向会员发送激活码
          $content = <<<str
        <p>恭喜你，请点击以下链接地址完成email验证以激活餐厅</p>
        <p><a href="http://www.ydors.com/index.php/Admin/Restaurant/emailForRegister/code/{$data['email_code']}">点击完成验证激活</a></p>
str;
          // 把生成的验证码发到会员的邮箱中
          sendmail($data['restaurant_email'], '易达在线订餐网email验证激活餐厅用户', $content);
      }
      protected function _before_update(&$data, $options){
          //上传头像图片
          if(isset($_FILES['restaurant_logo']) && $_FILES['restaurant_logo']['error'] == 0){
              $res = uploadOne("restaurant_logo", "Restaurant",array(
                  array(50,50)
              ));
      
              if($res["ok"] == 1){
                  $ori_restaurant_logo_name = ltrim($res["images"][0],'.');
                  $sm_restaurant_logo_name = ltrim($res["images"][1],'.');
      
                  $data['restaurant_logo'] = $ori_restaurant_logo_name;
                  $data['sm_restaurant_logo'] = $sm_restaurant_logo_name;
                  //删除原图片
                  $face = $this->field("restaurant_logo,sm_restaurant_logo")->find($options['where']['restaurant_id']);
                  deleteImage(array($face['restaurant_logo'],$face["sm_restaurant_logo"]
                  ));
                   
              }else{
                  $this->error = $res["error"];
                   
                  return false;
              }
          } 
           
          // 密码为空则不修改密码
          if (empty($data['password'])){
              unset($data['password']);
          } else {
              $data['password'] = md5($data['password'].C("MD5_KEY"));
          }
      }
      /**
       * 餐厅登录验证
       * @return boolean
       */
      public function loginConfirm(){
          $restaurant_name = $this->restaurant_name;
          $password = $this->password;
          
          $has_restaurant = $this -> where(array("restaurant_name"=>array('eq',$restaurant_name))) ->find();
          if ($has_restaurant){
              //判断用户是否激活并且管理员验证通过
              if (empty($has_restaurant['email_code'])){
                  //餐厅账号已经激活
                  if ($has_restaurant['restaurant_state'] == 1){
                      //判断餐厅密码是否正确
                      if ($has_restaurant['password'] == md5($password.C('MD5_KEY'))){
                          //登录成功后，把该餐厅的信息存入session
                          session('restaurant_id', $has_restaurant['restaurant_id']);
                          session('restaurant_email', $has_restaurant['restaurant_email']);
                          session('restaurant_name', $has_restaurant['restaurant_name']);
                          session("restaurant_logo",$has_restaurant['restaurant_logo']);
                      
                          cookie("restaurant_id",$has_restaurant['restaurant_id'],"index");
                      
                          return TRUE;
                       }else {
                           $this->error = '密码错误！';
                           return FALSE;
                       }
                  
                  }else{
                      $this->error = '餐厅账号还没有通过管理员验证激活！';
                      return FALSE;
                  }
              }else{
                  $this->error = '餐厅账号还没有通过email验证激活！';
                  return FALSE;
              }
          }else{
              $this->error = '餐厅账号不存在';
              return FALSE;
          }
      }
      /**
       * 餐厅用户以某种形式展示展示
       */
      public function showlist($rest_name=''){
          $where = array();
          //按餐厅用户名搜索
          $restaurant_name = I("get.restaurant_name");
          if ($restaurant_name){
              $where['restaurant_name'] = array("like","%$restaurant_name%");
          }
          if($rest_name){
              $where['restaurant_name'] = array("like","%$rest_name%");
          }
          //按餐厅销量
          $orderbyfield = 'totalsales';  // 默认排序字段
          $orderway = 'desc'; // 默认排序方式
          $orderbyway = I("get.orderby");
          if ($orderbyway){
              $orderway = $orderbyway;
          }
          //翻页实现
      
          $totalRows = $this->where($where)->count();
          
          if ($totalRows>0){
              $Page = setPage($totalRows,2);
              $show = $Page->show();// 分页显示输出
              // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
              $list = $this
              ->where($where)
              ->order("$orderbyfield $orderway")
              ->limit($Page->firstRow . ',' . $Page->listRows)
              ->select();
      
              return array(
                  'data' => $list,
                  'page' => $show
              );
          }
      }
      /**
       * 获得本店热销菜品 
       */
      public function getHotFoods($restaurant_id){
          $foodsModel = D("Admin/Foods");
          
          return $foodsModel->field('foods_id,foods_name,shop_price,logo')->where(array(
              'restaurant_id' => array('eq', $restaurant_id),
              'is_on_sale' => array('eq', 1),  // 售卖中
              'is_delete' => array('eq', 0),   // 不在回收站
              'is_hot' => array('eq', 1),  // 热卖
          ))->limit(6)->order('sort_num ASC')->select();
      }
      

}
     
?>