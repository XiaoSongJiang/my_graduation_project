<?php

namespace Tools;
use Think\Controller;
class AdminBaseController extends Controller{
    
    public function  __construct(){  
        
    
        // 验证登录
        $manager_id = session('manager_id');
        if(empty($manager_id)){
            $url = U('Admin/Loginer/login');
            $str = <<<eof
            <script type="text/javascript">
               
                window.top.location.href = "$url";
            </script>
eof;
            echo $str;
            exit();
        }
            
        parent::__construct();
        //随时判断当前管理员是否被禁用
        $model = M("Manager");
        $now_manager_is_use = $model->find($manager_id)['is_use'];
        if($now_manager_is_use == 0){
            $url = U('Admin/Loginer/login');
            $str = <<<eof
            <script type="text/javascript">
                alert('你的账号被禁用');
                window.top.location.href = "$url";
            </script>
eof;
            echo $str;
            exit();
        }
        
        //所有的管理员都可以访问主页面
        if (CONTROLLER_NAME == "Index"){
            return TRUE;
        }
        //先获取当前管理员所拥有的访问权限
        // 查询数据库判断当前管理员有没有访问这个页面的权限
        $where = 'module_name="'.MODULE_NAME.'" AND controller_name="'.CONTROLLER_NAME.'" AND action_name="'.ACTION_NAME.'"';
        //超级管理员拥有所有权限
        if ($manager_id == 1){
            $sql = "select count(*) has from yd_privilege where $where";
        }else {
            $sql = "select count(a.pri_id) has
                    from yd_role_privilege a
                    left join yd_privilege b on a.pri_id = b.pri_id
                    left join yd_manager_role c on a.role_id = c.role_id
                    where c.manager_id = $manager_id and $where";
        }
       
        $model = M();
        $res = $model->query($sql);
       
        if ($res[0]['has']<1){
            
            $this->error("无权访问","",2);
        }
        
        
        
    }
    public function setPageBtn($title, $btnName, $btnLink,$net_title="管理中心")
    {
        $this->assign('page_title', $title);
        $this->assign('page_btn_name', $btnName);
        $this->assign('page_btn_link', $btnLink);
        $this->assign('net_title', $net_title);
    }
}
?>