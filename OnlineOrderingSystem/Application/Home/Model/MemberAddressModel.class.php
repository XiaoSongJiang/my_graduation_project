<?php
namespace Home\Model;
use Think\Model;
class MemberAddressModel extends Model{
    protected $insertFields = array('receiver','address','receiver_mobile',"member_id","is_default");
    protected $updateFields = array('address_id','member_id','receiver','address','receiver_mobile',"is_default");
     
   
    // 添加和修改收货地址时的规则
    public $_validate = array(
        array('member_id', 'require', '你尚未登录！', 1),
        array('receiver', 'require', '收货人姓名不能为空！', 1),
        array('address', 'require', '收货地址不能为空！', 1),
        array('receiver_mobile', '11', '手机号码长度不正确', 1, 'length'),
        array('receiver_mobile', 'checkMobile', '手机号码格式不正确', 1, 'callback'),
    );
    
    public function checkMobile($mobile){
        if(strlen($mobile) == 11)
        {
            
            //上面部分判断长度是不是11位
            $n = preg_match_all('/13[0123569]{1}\d{8}|15[01235689]\d{8}|18[0123569]{1}\d{8}/',$mobile,$array);
            
            if ($n){
                return true;
            } else {
                return false;
            }
        }else
        {
            $this->error = "手机号码长度不正确";
            return false;
        }
    }
    /**
     * 获取订餐用户会员收货地址
     * @param int $member_id 会员id
     * @return array $address_info 地址信息(二位数组)
     */
    public function getMemberAddress($member_id){
        $address_info = $this->where("member_id = $member_id")->select();
        return $address_info;
    }
}

?>