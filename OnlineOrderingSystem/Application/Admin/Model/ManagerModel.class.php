<?php

 namespace Admin\Model;
 use Think\Model;
 class ManagerModel extends Model{
     protected $insertFields = array('manager_name','password','cpassword','is_use');
     protected $updateFields = array('manager_id','manager_name','cpassword','password','is_use');
     
     protected $patchValidate    =   false; //批量验证
    // 登录时表单验证的规则 
	public $login_validate = array(
		array('manager_name', 'require', '用户名不能为空！', 1),
		array('password', 'require', '密码不能为空！', 1),
		array('verifycode', 'require', '验证码不能为空！', 1),
		array('verifycode', 'chk_verifycode', '验证码不正确！', 1, 'callback'),
	);
	//修改已登录管理员密码
	public $modify_validate = array(
	    array('manager_name', 'require', '账号不能为空！', 1, 'regex'),
	    array('manager_name', '', '账号已存在！', 1, 'unique'),
		array('manager_name', '1,30', '账号的值最长不能超过 30 个字符！', 1, 'length'),
		array('password', 'require', '密码不能为空！', 1, 'regex'),
	    array('password', '6,20', '密码应为6-20的字符', 1, 'length'),
		array('cpassword', 'password', '前后密码不一致', 1, 'confirm'),
	);
	
	// 添加和修改管理员时的规则
	public $_validate = array(
	    //array(验证字段1,验证规则,错误提示,[验证条件,附加规则,验证时间]),
	    // 添加修改管理员时用
	    array('manager_name', 'require', '账号不能为空！', 1, 'regex', 3),
	    array('manager_name', '', '账号已存在！', 1, 'unique', 1),
		array('manager_name', '1,30', '账号的值最长不能超过 30 个字符！', 1, 'length', 3),
		array('password', 'require', '密码不能为空！', 1, 'regex', 1),
	    array('password', '6,20', '密码应为6-20的字符', 1, 'length'),
		array('cpassword', 'password', '前后密码不一致', 1, 'confirm', 3),
		array('is_use', 'number', '是否启用 1：启用0：禁用必须是一个整数！', 2, 'regex', 3),
	);
	/**
	 * 回调函数
	 * @string $verifycode 用户输入的验证码
	 * @return boolean
	 */
	public function chk_verifycode($verifycode){
	    $verify = new \Think\Verify();
	    return $verify->check($verifycode);
	}
	/**
	 * 验证管理员登录信息的正确性
	 * @return boolean
	 */
	public function chk_login(){
		// 获取表单中的用户名密码
		$manager_name = $this->manager_name;
		$password = $this->password;
		// 先查询数据库有没有这个账号
		$user = $this->where(array(
			'manager_name' => array('eq', $manager_name),
		))->find();
		// 判断有没有账号
		if($user){
			// 判断是否启用(超级管理员不能禁用）
			if($user['id'] == 1 || $user['is_use'] == 1){
				// 判断密码
				if($user['password'] == md5(C("MD5_KEY").$password)){
					// 把ID和用户名存到session中
					session('manager_id', $user['manager_id']);
					session('manager_name', $user['manager_name']);
					return TRUE;
				}
				else {
					$this->error = '密码不正确！';
					return FALSE;
				}
			}
			else {
				$this->error = '账号被禁用！';
				return FALSE;
			}
		}
		else {
			$this->error = '用户名不存在！';
			return FALSE;
		}
	}
	// 添加前
	protected function _before_insert(&$data, $options){
	    $data['password'] = md5(C("MD5_KEY").$data['password']);
	}
	//添加后
	protected function _after_insert($data, $options){
	    //把管理员对应的角色插入管理员角色表中
	    $role_ids = I("post.role_id");
	
	    if ($role_ids){
	        $mrModel = M("ManagerRole");
	        foreach ($role_ids as $key=>$val){
	            $mrModel->add(array(
	                "manager_id"=>$data['manager_id'],
	                "role_id"=>$val
	            ));
	        }
	
	    }
	}
	// 修改前
	protected function _before_update(&$data, $options){
	   
	    if (empty($data['password'])){
	        unset($data['password']);
	    }else{
	        $data['password'] = md5(C("MD5_KEY").$data['password']);
	    }
	    //超级管理员不能被禁用
	    if ($options['where']['manager_id'] == 1 && $data['is_use'] == 0){
	        $data['is_use'] = 1;
	        $this->error = "超级管理员不能被禁用!";
	        return false;
	    }
	    //修改之前将此管理员原来所属角色删除
	    $mrModel = M("ManagerRole");
	    $res = $mrModel->where(array(
	        "manager_id"=>array("eq",$options['where']['manager_id'])
	    ))->delete();
	     
	    //添加将要修改的角色
	    $role_ids = I("post.role_id");
	    if ($role_ids){
	
	        foreach ($role_ids as $key => $val){
	            $mrModel->add(array(
	                "manager_id"=>$options['where']['manager_id'],
	                "role_id"=>$val
	            ));
	        }
	         
	    }
	     
	}
	protected function _before_delete($options){
	    //超级管理员不能被删除
	    if ($options['where']['manager_id'] == 1){
	        $this->error = "超级管理员不能被删除！";
	        return FALSE;
	    }
	    //删除管理员之前将管理员角色表中的数据也删掉
	    $mrModel = M("ManagerRole");
	    $mrModel->where(array(
	        "manager_id"=>array("eq",$options['where']['manager_id'])
	    ))->delete();
	}
	/**
	 * 管理员以某种形式展示展示
	 */
	public function showlist(){
	    $where = array();
	    //按管理员名称搜索
	    $manager_name = I("get.manager_name");
	    if ($manager_name){
	        $where['manager_name'] = array("like","%$manager_name%");
	    }
	    //按管理员是否禁用搜索
	    $is_use = I("get.is_use");
	    if($is_use != '' && $is_use != '-1')
	        $where['is_use'] = array('eq', $is_use);
	    //翻页实现
	
	    $totalRows = $this->where($where)->count();
	
	    if ($totalRows > 0){
	        $Page = setPage($totalRows,10);
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
 }
?>