<?php
namespace Home\Model;
use Think\Model;
class GuestbookModel extends Model{
    
    protected $insertFields = array('title','name','email',"qq","mood",'body','code','face');
    protected $updateFields = array('gb_id','reply','ifreply');
     
   
    // 留言时的规则
    public $_validate = array(
        
        array('title', 'require', '留言标题不能为空！', 1,'regex',1),
        array('name', 'require', '留言者姓名不能为空！', 1,'regex',1),
        array('email', 'email', '邮箱账号格式不正确！', 1,'regex', 1),
        array('qq', '6,11', 'qq号码长度不正确', 1, 'length',1),
        array('qq', 'checkQQ', 'qq号码格式不正确！', 1, 'callback',1),
        array('mood', 'require', '心情不能为空', 1, 'regex',1),
        array('body', 'require', '留言内容不能为空！', 1,'regex',1),
        array('reply', 'require', '回复内容不能为空！', 1,'regex',2),
        array('code', 'require', '验证码不能为空！', 1,'regex',1),
        
        array('code', 'checkVerify', '验证码不正确！', 1, 'callback',1),
        
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
    
    public function checkQQ($qq){
            $n = preg_match_all('/^[1-9][0-9]{4,9}$/',$qq,$array);
            
            if ($n){
                return true;
            } else {
                return false;
            }
        
    }
    /**
     * 插入数据库之前
     */
    protected function _before_insert(&$data, $options){
        $data['addtime'] = time();  // 留言的当前时间
        $data['restaurant_id'] = I("post.restaurant_id");
        $data['member_id'] = I("post.member_id");
        $data['ip'] = $_SERVER["REMOTE_ADDR"];
    }
    /**
     * 插入数据库之前
     */
    protected function _before_update(&$data, $options){
        $data['replytime'] = time();  // 留言回复的当前时间
        
    }
    public function showGuestBook($rest_id = 0,$member_id = 0){
        $where = array();
        if ($rest_id != 0){
            $where['restaurant_id'] = $rest_id;
        }
        if ($member_id != 0){
            $where['member_id'] = $member_id;
        }
        //翻页实现     
        $totalRows = $this->where($where)->count();
        
        if ($totalRows > 0){
            $Page = setPage($totalRows,3);
            $show = $Page->show();// 分页显示输出
            // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
            $list = $this
            ->where($where)
            ->order("gb_id DESC")
            ->limit($Page->firstRow . ',' . $Page->listRows)
            ->select();
        
            return array(
                'data' => $list,
                'page' => $show
            );
        }
       
    }
}

?>