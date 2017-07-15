<?php
namespace Home\Controller;
use Tools\HomeBaseController;
class CommentController extends HomeBaseController {
    /**
     * 添加评论
     */
    public function commentAdd(){
        // 判断有没有登录
        $member_id = session('member_id');
        if(!$member_id){
            echo json_encode(array(
                'ok' => 0,
                'error' => '必须先登录',
            ));
            exit;
        }
        if(IS_POST){
            //判断用户是否购买过菜品该
            $foods_id = I("post.foods_id");
            $orderfoodsModel = M("OrderFoods");
            $buy_record = $orderfoodsModel->alias("a")->join("left join yd_order b on a.order_id = b.order_id")->where("b.pay_status=1 and a.foods_id=$foods_id and a.member_id=$member_id")->select();
            if(!$buy_record){
                echo json_encode(array(
                    'ok' => 0,
                    'error' => "你还未购买过此菜品，不能评论",
                ));
                exit();
            }
            $model = D('Home/Comment');
            // 根据表单并根据模型中定义的规则验证表单
            if($model->create(I('post.'), 1)){
                if(!!$last_id = $model->add()){
                    // 取出会员的头像
                    
                    $memberModel = M('Member');
                    $face = $memberModel->field('face')->find($member_id);
                    // 如果没有设置头像就返回默认头像
                    $realFace = !$face['face'] ? '/Public/Home/images/face.jpg' : $face['face'];
                    //查询印象信息
                    $impressionModel = M("Impression");
                    $foods_id = I("post.foods_id");
                    
                    $impression_info = $impressionModel->where("foods_id=$foods_id")->select();
                    
                    echo json_encode(array(
                        'ok' => 1,
                        'content' => I('post.content'), // 过滤之后的内容
                        'addtime' => date('Y-m-d H:i'),
                        'star' => I('post.star'),
                        'email' => session('email'),
                        'face' => $realFace,
                        'comment_id' => $last_id,
                        'impression' => $impression_info
                    ));
                    exit;
                }
            }
            echo json_encode(array(
                'ok' => 0,
                'error' => $model->getError(),
            ));
        }
    }
    /**
     * ajax展示评论
     */
    public function showCommentByAjax(){
        $foods_id = I("get.foods_id");
        $p = empty(I("get.page")) ? 1 : I("get.page");
        $commentModel = D("Home/Comment");
        $comment_info = $commentModel->showList($foods_id,$p);
        $impressionModel = M("Impression");
        $impression_info = $impressionModel->where("foods_id = $foods_id")->select();
        //dump($impression_info);
        $comment_info["impression"] = $impression_info;
        //dump($comment_info);
        echo json_encode($comment_info);
    }
    /**
     * 点赞
     */
    public function thumbsUp(){
        
        $comment_id = I("get.comment_id");
        $foods_id = I("get.foods_id");
        if (!session("member_id")){
            cookie("return_url",U("Home/Index/foods?foods_id=$foods_id"));
            $this->error("请先登录！",U("Home/Member/login"),1);
        }
        $member_id = session("member_id");
        $clickeduseModel = M("ClickedUse");
        $is_clik = $clickeduseModel -> where(
            array(
                "member_id"=>array("eq",$member_id),
                "comment_id" => array("eq",$comment_id)
                
            )) -> find();
        if (!$is_clik){
            //点赞加一
            $commentModel = M("Comment");
            $sql = "update yd_comment set used=used+1 where comment_id=$comment_id";
            $res = $commentModel -> execute($sql);
            
            $data['member_id'] = $member_id;
            $data['comment_id'] = $comment_id;
            $inset_id = $clickeduseModel->add($data);
            if(FALSE !== $res && $inset_id){
                $this->redirect("Home/Index/foods?foods_id=$foods_id");
            }
        }else{
            $this->error("你已经点赞了");
        }
    }
    /**
     * 评论回复
     */
    public function commentReply(){
        $foods_id = I("get.foods_id");
        $comment_id = I("get.comment_id");
        $commenter = I("get.commenter");
        if (!session("member_id")){
            cookie("return_url",U("Home/Index/foods?foods_id=$foods_id"));
            $this->error("请先登录！",U("Home/Member/login"),1);
        }
        $commentModel = M("Comment");
        $replyModel = M("Reply");
        if (IS_POST){
            $data = I("post.");
            $data['addtime'] = time();
            $data['member_id'] = session('member_id');
            if ($data['commenter_id'] == session('member_id') ){
                $this->error("自己不能回复自己");
                exit;
            }
            if ($replyModel->add($data)){
                $this->success("回复成功!");
                exit;
            }
        }
        //评论者信息
        $comment_one = $commentModel->alias('a')->join("left join yd_member b on a.member_id = b.member_id")->where("a.comment_id=$comment_id")->find();
        $this->assign("comment_one",$comment_one);
        //所有回复信息
        
        $reply_all = $replyModel -> field("a.*,c.email")->alias('a')->join("left join yd_comment b on a.comment_id = b.comment_id")->join('left join yd_member c on a.member_id = c.member_id')->where("a.comment_id = $comment_id")->select();
        
        
        $this->assign("reply_all",$reply_all);
        $this->setHeadInfo("回复评论", "回复评论", "回复评论",0);
        $this->display();
    }
}