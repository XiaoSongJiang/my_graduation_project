<?php
namespace Home\Model;
use Think\Model;
class CommentModel extends Model 
{
	// 发表评论时表单中允许提交的字段
	protected $insertFields = array('content','star','foods_id');
	protected $updateFields = array('content','star','comment_id');
	protected $_validate = array(
		array('content', 'require', '评论的内容不能为空！或者你输入了违规内容', 1, 'regex', 3),
		array('star', '/^[1-5]$/', '分值必须是1-5之间的数字！', 1,'regex', 3), 
	);
	protected function _before_insert(&$data, $options){
		$data['addtime'] = time();
		$data['member_id'] = session('member_id');
		// 处理印象的数据
		$impression = I('post.impression');
		if($impression)
		{
			// 先统计字符串中的，号都用英文的
			$impression = str_replace('，', ',', $impression);
			// 先把印象根据，号转化成数组
			$impression = explode(',', $impression);
			$impModel = M('Impression');
			foreach ($impression as $k => $v)
			{
				// 判断这个菜品有没有这个印象
				$has = $impModel->field('imp_id')->where(array(
					'foods_id' => array('eq', $data['foods_id']),
					'imp_name' => array('eq', $v),
				))->find();
				// 这件菜品已经有这个印象就把印象的数字加1
				if($has)
					$impModel->where('imp_id='.$has['imp_id'])->setInc('imp_count');
				else 
					$impModel->add(array(
						'foods_id' => $data['foods_id'],
						'imp_name' => $v,
					));
			}
		}
	}
	/**
	 * 根据菜品id显示评论
	 * @param unknown $foods_id
	 * @param unknown $p
	 * @return multitype:string
	 */
	public function showList($foods_id,$p){
	    $where['foods_id'] = array("eq",$foods_id);
	    $totalRows = $this->where($where)->count();
	    $page_size = 4;
	    $offset = ($p-1)*$page_size;
	    if ($totalRows>0){
	        $Page = new \Tools\Page1($totalRows,4);
	        $show = $Page->showpage();// 分页显示输出
	        // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
	        $data = $this->field('a.*,b.email,b.face,COUNT(c.comment_id) reply_count')
	        ->alias('a')
	        ->join('LEFT JOIN yd_member b ON a.member_id = b.member_id LEFT JOIN yd_reply c ON a.comment_id=c.comment_id')
	        ->where(array('a.foods_id'=>array('eq', $foods_id)))
	        ->limit($offset . ',' . $page_size)
	        ->group('a.comment_id')
	        ->order('a.comment_id DESC')
	        ->select();
	        foreach ($data as $k => $v)
	        {
	            $data[$k]['face'] = $v['face'] ? $v['face'] : '/Public/Home/images/face.jpg';
	            $data[$k]['addtime'] = date('Y-m-d H:i', $v['addtime']);
	        }
	        return array(
	            'info' => $data,
	            'page' => $show,   
	        );
	    }
	}
	/**
	 * 根据会员id显示评论信息
	 */
	public function getCommentByMemberId($member_id,$p){
	    $where['member_id'] = array("eq",$member_id);
	    $totalRows = $this->where($where)->count();
	   
	    if ($totalRows>0){
	        $Page = setPage($totalRows,4);
	        $show = $Page->show();// 分页显示输出
	        
	        $data = $this->field('a.*,b.foods_name,b.logo,b.foods_id')
	        ->alias('a')
	        ->join('LEFT JOIN yd_foods b ON a.foods_id = b.foods_id')
	        ->where(array('a.member_id'=>array('eq', $member_id)))
	        ->limit($Page->firstRow . ',' . $Page->listRows)
	        ->order('a.comment_id DESC')
	        ->select();
	        foreach ($data as $k => $v)
	        {
	            $data[$k]['face'] = $v['face'] ? $v['face'] : '/Public/Home/images/face.jpg';
	            $data[$k]['addtime'] = date('Y-m-d H:i', $v['addtime']);
	        }
	        return array(
	            'data' => $data,
	            'page' => $show,
	        );
	    }
	}
	/**
	 * 根据餐厅id显示评论信息
	 */
	public function getCommentByRestaurantId($restaurant_id,$p){
	    //根据餐厅id查出所有菜品，并在评论表中筛选
	    $restaurantModel = M("Restaurant");
	    $foods_info = $this->field("a.foods_id")->alias("a")->join("left join yd_foods b on a.foods_id=b.foods_id left join yd_restaurant c on b.restaurant_id = c.restaurant_id")->where("c.restaurant_id = $restaurant_id")->select();
	    foreach ($foods_info as $key => $val) {
	        $foods_ids[] = $val['foods_id'];
	    }
	   
	    $totalRows = count($foods_ids);
	    if ($totalRows>0){
	        $Page = setPage($totalRows,4);
	        $show = $Page->show();// 分页显示输出
	         
	        $data = $this->field('a.*,b.foods_name,b.logo')
	        ->alias('a')
	        ->join('LEFT JOIN yd_foods b ON a.foods_id = b.foods_id left join yd_restaurant c on b.restaurant_id = c.restaurant_id')
	        ->where(array('c.restaurant_id'=>array('eq', $restaurant_id)))
	        ->limit($Page->firstRow . ',' . $Page->listRows)
	        ->order('a.comment_id DESC')
	        ->select();
	        foreach ($data as $k => $v)
	        {
	            $data[$k]['face'] = $v['face'] ? $v['face'] : '/Public/Home/images/face.jpg';
	            $data[$k]['addtime'] = date('Y-m-d H:i', $v['addtime']);
	        }
	       
	        return array(
	            'data' => $data,
	            'page' => $show,
	        );
	    }
	    
	}
    /**
     * 菜品评价 好评 差评 中评
     * @param int $foods_id 菜品id
     * @return array $arr 包含评价的一维数组
     */
    public function getFoodsEvaluate($foods_id){
        $arr = array();
        //所有评价
        $comment_all = $this->field("count(*) alls")->where("foods_id = $foods_id")->select();
        
        //好评数
        $praise = $this->field("count(*) praise")->where("foods_id = $foods_id and star>3")->select();
        //中评数
        $middle_praise = $this->field("count(*) middle")->where("foods_id = $foods_id and star between 2 and 3")->select();
        //差评数
        $bad = $this->field("count(*) bad")->where("foods_id = $foods_id and star = 1")->select();
        //包含评价率的数组
        $arr[] = round(($praise[0]['praise']/$comment_all[0]['alls'])*100);
        $arr[] = round(($middle_praise[0]['middle']/$comment_all[0]['alls'])*100);
        $arr[] = round(($bad[0]['bad']/$comment_all[0]['alls'])*100);
        $maxs = max($arr);
        $sum = 0;
        foreach ($arr as $key => $val){
            $sum += $val;
            if($maxs == $val){
                $temp = $key;
            }
        }
        if ($sum == 0){
            $arr['buy'] = 0;
        }else{
            $arr['buy'] = 1;
        }
        $arr['compre'] = $temp;
        $persons = $this->field("member_id,count(*) ")->where("foods_id = $foods_id")->group("member_id")->select();
        $arr['persons'] = count($persons);
        
        return $arr;
    } 
    /**
     * 根据餐厅id显示评论信息
     */
    public function getAllComment(){
        $totalRows = $this->count();
        if ($totalRows>0){
            $Page = setPage($totalRows,10);
            $show = $Page->show();// 分页显示输出
    
             $data = $this->field('a.*,b.foods_name,b.logo')
	        ->alias('a')
	        ->join('LEFT JOIN yd_foods b ON a.foods_id = b.foods_id left join yd_restaurant c on b.restaurant_id = c.restaurant_id')
            ->limit($Page->firstRow . ',' . $Page->listRows)
            ->order('comment_id DESC')
            ->select();
            return array(
                'data' => $data,
                'page' => $show,
            );
        }
         
    }
}
















