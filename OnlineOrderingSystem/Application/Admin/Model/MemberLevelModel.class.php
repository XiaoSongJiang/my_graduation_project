<?php

namespace Admin\Model;
use Think\Model;
class MemberLevelModel extends Model{
    protected $insertFields = array('level_name','bottom_num','top_num','rate');
	protected $updateFields = array('level_id','level_name','bottom_num','top_num','rate');
	protected $_validate = array(
		array('level_name', 'require', '级别名称不能为空！', 1, 'regex', 3),
		array('level_name', '1,30', '级别名称的值最长不能超过 30 个字符！', 1, 'length', 3),
		array('bottom_num', 'require', '积分下限不能为空！', 1, 'regex', 3),
		array('bottom_num', 'number', '积分下限必须是一个整数！', 1, 'regex', 3),
		array('top_num', 'require', '积分上限不能为空！', 1, 'regex', 3),
		array('top_num', 'number', '积分上限必须是一个整数！', 1, 'regex', 3),
		array('rate', 'number', '折扣率，以百分比，如：9折=90必须是一个整数！', 2, 'regex', 3),
	);
    //在调用add方法之前会自动调用这个方法，我们可以把在插入数据库之前要执行的代码写到这里
    // 第一个参数：就是表单中的数据（要插入到数据库中的数据）是一个一维数组
    // 第二个参数：额外信息，：当前模型对应的实际的表名是什么
    // 说明：在这个函数中要改变这个函数外部的$data，需要按钮引用传递，否则修改也无效
    // 说明：如果return false是指控制器中的add方法返回了false
    protected function _before_insert(&$data, $option)
    {
         
    }
    protected function _before_delete($options){
        
    }
    /**
     * 类型以某种形式展示展示
     */
    public function showlist(){
        $where = array();
        //翻页实现
        $totalRows = $this->where($where)->count();
        if ($totalRows>0){
            $Page = setPage($totalRows,10);
            $show = $Page->show();// 分页显示输出
            // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
            $list = $this->where($where)
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