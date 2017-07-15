<?php
namespace Admin\Model;
use Think\Model;
class RoleModel extends Model{
    // 在添加时调用create方法时允许接收的字段
    protected $insertFields = array('role_name');
    // 在更新时调用create方法时允许接收的字段
    protected $updateFields = array('role_id','role_name');
    // 自动验证
    protected $_validate = array(
        //array(验证字段1,验证规则,错误提示,[验证条件,附加规则,验证时间]),
        array('role_name', 'require', '角色名称不能为空！', 1, 'regex', 3),
		array('role_name', '1,30', '角色名称的值最长不能超过 30 个字符！', 1, 'length', 3),
        array('role_name', '', '角色名称已存在！', 1, 'unique', 1),

    );
    protected function _before_update(&$data, $options){
        //先删除以前的权限
        $rpModel = M(RolePrivilege);
        $rpModel->where(array(
            "role_id"=>array("eq",$options['where']['role_id'])
        ))->delete();
        //重新添加
        $pri_id = I("post.pri_id");
        if($pri_id){
            foreach ($pri_id as $k => $v)
            {
                $rpModel->add(array(
                    'pri_id' => $v,
                    'role_id' => $options['where']['role_id'],
                ));
            }
        }
    }
    protected  function _after_insert($data, $options){
        //添加角色后把角色相对应的权限插入权限角色表
        //获得权限id
        $pri_id = I('post.pri_id');
        if($pri_id)
        {
            $rpModel = M('RolePrivilege');
           
          
            foreach ($pri_id as $k => $v)
            {
                $rpModel->add(array(
                    'pri_id' => $v,
                    'role_id' => $data['role_id'],
                ));
            }
        }
    }
    protected function _before_delete($options){
        //判断将要删除的角色是否为某个管理员所属
        $arModel = M('AdminRole');
        $is_has = $arModel->where(array('role_id'=>array('eq', $options['where']['role_id'])))->count();
        if($is_has > 0)
        {
            $this->error = '有管理员属于这个角色，无法删除！';
            return FALSE;
        }
        // 把这个角色所拥有的权限的数据也一起删除
        $rpModel = M('RolePrivilege');
        $rpModel->where(array('role_id'=>array('eq', $options['where']['role_id'])))->delete();
        
    }
    /**
     * 角色以某种形式展示展示
     */
    public function showlist(){
        $where = array();
        //按商品名称搜索
        $role_name = I("get.role_name");
        if ($role_name){
            $where['role_name'] = array("like","%$role_name%");
        }
        //翻页实现
        
        $totalRows = $this->where($where)->count();
        
        if ($totalRows>0){
           
            $Page = setPage($totalRows,10);
            $Page->lastSuffix = true; // 最后一页显示为总页数
            $show = $Page->show();// 分页显示输出
            
            
            // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
            $list = $this->field("a.*,group_concat(c.pri_name) pri_names")
            ->alias('a')
            ->join("left join yd_role_privilege b on a.role_id = b.role_id
               left join yd_privilege c on b.pri_id=c.pri_id")
            ->where($where)
            ->group("a.role_id")
            ->order("a.role_id")
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