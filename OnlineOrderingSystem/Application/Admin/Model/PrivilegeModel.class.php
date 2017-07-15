<?php

namespace Admin\Model;
use Think\Model;
class PrivilegeModel extends Model{
    // 在添加时调用create方法时允许接收的字段
    protected $insertFields = array('pri_name','parent_id','module_name','controller_name','action_name');
    // 在更新时调用create方法时允许接收的字段
    protected $updateFields = array('pri_id','pri_name','parent_id','module_name','controller_name','action_name');
    // 自动验证
    protected $_validate = array(
        //array(验证字段1,验证规则,错误提示,[验证条件,附加规则,验证时间]),
        array('pri_name', 'require', '权限名称不能为空！', 1),
        array('pri_name', '1,30', '权限名称的值最长不能超过 30 个字符！', 1, 'length', 3),
		array('module_name', 'require', '模块名称不能为空！', 1, 'regex', 3),
		array('module_name', '1,30', '模块名称的值最长不能超过 30 个字符！', 1, 'length', 3),
		array('controller_name', 'require', '控制器名称不能为空！', 1, 'regex', 3),
		array('controller_name', '1,30', '控制器名称的值最长不能超过30 个字符！', 1, 'length', 3),
		array('action_name', 'require', '方法名称不能为空！', 1, 'regex', 3),
		array('action_name', '1,30', '方法名称的值最长不能超过 30 个字符！', 1, 'length', 3),
		array('parent_id', 'number', '上级权限的ID，0：代表顶级权限必须是一个整数！', 2, 'regex', 3),
        
    );
    /**
     * 树状显示权限列表
     * @return multitype:number
     */
    public function tree(){
        $data = $this->select();
         
        return $this->sortByTree($data);
    }
    /**
     * 
     * @param array $data 所有权限列表
     * @param number $parent_id 父级权限id
     * @param number $level 权限等级 例 :权限管理 level：0 权限列表 ：1 添加权限：1
     * @return array $arr 树状的列表
     */
    private function sortByTree($data,$parent_id = 0,$level = 0){
        static $arr = array();
        foreach ($data as $key=>$val){
            if ($val['parent_id'] == $parent_id){
                $val['level'] = $level;
                $arr[] = $val;
                $this->sortByTree($data, $val['pri_id'], $level + 1);
            }
        }
        return $arr;
    }
    /**
     * 获得当前权限的子权限
     * @param int $id 当前权限id
     * @return array  子权限列表
     */
    public  function getChildren($id){
        $data = $this->select();
        return $this->children($data,$id);
    }
    /**
     * 根据处理子权限动作
     * @param array $data 所有权限
     * @param number $parent_id 父级权限id
     * @return array $arr 子级权限列表
     */
    private function children($data,$parent_id=0){
        static $arr = array();
        foreach ($data as $key=>$val){
            if ($val['parent_id'] == $parent_id){
                $arr[] = $val['pri_id'];
                $this->children($data, $val['pri_id']);
            }
        }
        return $arr;
    }
    protected function _before_update(&$data, $options){
        //找出所要修改权限的子分类
        
        $sub_pri_ids = $this->getChildren($options['where']['pri_id']);
        $sub_pri_ids[] = (string)$options['where']['pri_id'];
        //修改的分类不能将自身或者为其的子分类作为上级分类
        if (in_array($data["parent_id"],$sub_pri_ids)){

            $this->error = "不能将当前权限及其子权限作为上级权限";
            //redirect(U("Privilege/privilegeList"),3,"不能将当前权限及其子权限作为上级权限");
            return FALSE;
        }

    }
    protected function _before_delete($options){
         
        //找出所要修改商品分类的子分类
        $sub_pri_ids = $this->getChildren($options['where']['pri_id']);
        //删除的分类需要将自身或者为其的子分类删掉
        if($sub_pri_ids)
        {
            $sub_pri_ids = implode(',', $sub_pri_ids);
            //为什么这里不能直接用DELETE而是要自己拼SQL执行？
            // $this->delete($children);  -->  错误  --> 死循环
            // 在调用delete方法里TP会先调用_before_delete钩子函数这样就死循环了
            $this->execute("DELETE FROM yd_privilege WHERE pri_id IN($sub_pri_ids)");
        }
    }
}
?>