<?php
namespace Admin\Controller;
use Tools\HomeBaseController;
class RestaurantController extends HomeBaseController{
    public function  __construct(){
        parent::__construct();
        
    }
    /**
     * 餐厅主页面
     */
    public function index(){
        $this->restaurantIsLogin();
       
        $this->display();
    }
    public function menu(){
        $this->restaurantIsLogin();
        $this->display();
    }
    public function top(){
        $this->restaurantIsLogin();
        $this->display();
    }
    public function main(){
        $this->restaurantIsLogin();
        $this->display();
    }
    /**
     * 餐厅登录
     */
    public function login(){
        
        if (IS_POST){
           
           
            $restauModel = D('Admin/Restaurant');
            if($restauModel->validate($restauModel->_login_validate)->create(I('post.'), 9))
            {
                if($restauModel->loginConfirm())
                {
        
                   
                    $this->success('登录成功！',"index",3);
                    exit;
                }
            }
            $this->error($restauModel->getError());
        }
        $this->setHeadInfo("易达在线订餐餐厅登录", "易达在线订餐餐厅登录", "易达在线订餐餐厅登录",0,array("login"));
        $this->display();
    }
    /**
     * 餐厅用户登出
     */
    public function logout(){
        session("restaurant_id",null);
        session("restaurant_name",null);
        session("restaurant_email",null);
        session("restaurant_logo",null);
        
        cookie("restaurant_id",null,"/");
    
        $this->success("成功退出","login",2);
    }
    /**
     * 设置餐厅登录cookie
     */
    public function setCookie(){
        $restaurant_name = I("get.restaurant_name");
        $password = I("get.password");
        cookie('restaurant_name',$restaurant_name,3600);
        cookie('password',$password,3600);
    }
    /**
     * 餐厅注册
     */
    public function register(){
        
        if(IS_POST)
        {
           
            $restaurModel = D('Admin/Restaurant');
            if($restaurModel->create(I('post.'), 1))
            {
                
                if($restaurModel->add())
                {
                    $this->success('注册成功，请登录到您的邮件中完成验证！');
                    exit;
                }
            }
            $this->error($restaurModel->getError());
        }
        $this->setHeadInfo("易达订餐餐厅注册", "易达订餐餐厅注册", "易达订餐餐厅注册",0,array("login"));
        
        $this->display();
    }
    /**
     * 发送email至注册餐厅注册人用户邮箱
     */
    public function emailForRegister(){
        // 接收传回来的验证码
        $code = I('get.code');
        
        if($code)
        {
            // 把这个验证码到数据库中比较一下即可
            $model = D('Admin/Restaurant');
            $email = $model->where(array('email_code'=>array('eq', $code)))->find();
            if($email)
            {
              
                // 设置这个餐厅账号为已验证
                $res = $model->where(array('restaurant_id'=>array('eq', $email['restaurant_id'])))->setField(array('restaurant_state'=>"2",'email_code'=>''));
                
                $this->success('已经完成餐厅用户验证激活，等待管理员验证后就可以去登录', U('/'));
                exit;
            }
        }
    }
    
    
    /**
     * 餐厅信息修改
     */
    public function restaurantEdit(){
       $this->restaurantIsLogin();
        if (IS_POST){
           
            $restaurantModel = D('Admin/Restaurant');
            if($restaurantModel->create(I('post.'), 2)){
                 
                if (FALSE !== $restaurantModel->save()) {
                    $this->success('修改成功！');
                     
                    // 停止执行后面的代码
                    exit();
                }
            }
            $this->error($restaurantModel->getError());
        }
        
        $restaurant_id = session("restaurant_id");
        $restaurantModel = D("Admin/Restaurant");
        $restaurant_info = $restaurantModel->where("restaurant_id = $restaurant_id")->find(); 
        $this->assign("rest_info",$restaurant_info);
        $this->assign('page_title', "餐厅信息中心");
        $this->assign('net_title', "餐厅信息中心");
        $this->display();
       
    }
    /**
     * 展示菜品分类列表
     */
    function foodsCateList(){
        $this->restaurantIsLogin();
        $cateModel = D("Admin/FoodsCate");
        $category_data = $cateModel->tree();
        $this->assign("category_data",$category_data);
        $this->setPageBtn('菜品分类列表', '', '');
        $this->display("Restaurant/foodsCateList");
    }
    
    /**
     * 判断是否有餐厅用户登录或者管理员
     */
    public function restaurantIsLogin(){
        if (!session("restaurant_id") && empty(session('manager_id'))) {
            
            $url = U('login');
            $str = <<<eof
            <script type="text/javascript">
                window.top.location.href = "$url";
            </script>
eof;
            echo $str;
            exit();
        }
    }
    /**
     * 订餐用户会员列表
     */
    public function memberList(){
        
        $Model = D("Home/Member");
        $member_info = $Model->showlist();
         
        $this->assign(array(
            "data" => $member_info['data'],
            "page" => $member_info['page']
        ));
        $this->setPageBtn('订餐用户会员列表','','','订餐用户会员列表');
        $this->display();
    
    }
    /**
     * 留言列表
     */
    public function restaurantGuestBookList(){
        $restaurant_id = session("restaurant_id");
        $guestbookModel = D("Home/Guestbook");
        $guestbook_info = $guestbookModel -> showGuestBook($restaurant_id);
        
        $this->assign("guestbook_info",$guestbook_info);
        $this->setPageBtn('订餐用户留言列表','','','订餐用户留言列表');
        $this->display();
    }
    /**
     * 编辑回复
     */
    public function guestBookEdit(){
       $gb_id = I("get.gb_id");
       $guestbookModel = D('Home/Guestbook');
       if (IS_POST){
           
            if($guestbookModel->create(I('post.'), 2))
            {
                
                if(FALSE !== $guestbookModel->save())
                {
                     $this->success('回复成功！',U("restaurantGuestBookList"));
                     exit;
                 }
            }
            $this->error($guestbookModel->getError());
        }
        $guestbook_one = $guestbookModel->find($gb_id);
        $this->assign("guestbook_one",$guestbook_one);
        //dump($guestbook_one);
        $this->setPageBtn('订餐用户留言回复编辑','','','订餐用户留言回复编辑');
        $this->display();
    }
    /**
     * 删除留言
     */
    public function guestBookDelete(){
        $gb_id = I("get.gb_id");
        $guestbookModel = D('Home/Guestbook');
        $res = $guestbookModel -> delete($gb_id);
        if($res !== false){
        
            $this->success("删除成功！",U("restaurantGuestBookList?p=".I("get.p")));
        }
    }
    /**
     * 获取餐厅所有菜品的评论信息
     */
    public function restaurantFoodsCommentList() {
       $restaurant_id = session("restaurant_id");
       $p = empty(I("get.p")) ? 1 : I("get.p");
       $commentModel = D("Home/Comment");
       $comment_foods_info =  $commentModel->getCommentByRestaurantId($restaurant_id,$p);
       $this->assign("comment_foods_info",$comment_foods_info);
       
       $this->setPageBtn('订餐用户购买菜品评论','','','订餐用户购买菜品评论');
       $this->display();
    }
    public function restaurantFoodsCondition(){
        
        vendor('Jpgraph.jpgraph');
        vendor('Jpgraph.jpgraph_bar');
        $restaurantsalesModel = M("RestaurantSales");
        $temp = array();
        $tmp = array();
        $datas = array();
        for ($i = 1; $i <= 7; $i++){
            $temp[$i-1] = date('Y-m-d', strtotime("-$i days"));
            $tmp[$i-1] = strtotime(date('Y-m-d', strtotime("-$i days")));
        }
        $tmp = array_reverse($tmp);
        for ($i=0; $i<count($tmp); $i++){
            $last_itime = $tmp[$i]+24*3600;
            $datas[$i] = $restaurantsalesModel
            ->field("sum(num) total")
            ->where(array("sales_time"=>array("between","$tmp[$i],$last_itime"),"restaurant_id"=>array('eq',session("restaurant_id"))))
            ->select()['0']['total'];
        }
        //file_put_contents("C:/test.txt", $restaurantsalesModel->getLastSql());
        $datay = $datas;
        $datax = array_reverse($temp);
        
        // Setup the graph.
        Vendor('Jpgraph.jpgraph');
        $graph = new \Graph(1100,500);
        $graph->img->SetMargin(40,20,35,55);
        $graph->SetScale("textlin");
        $graph->SetMarginColor("lightblue:1.1");
        $graph->SetShadow();
        $graph->SetMarginColor('#DDEEF2');
        $graph->xaxis->title->Set(iconv("UTF-8","GB2312//IGNORE","The Date of FoodsSales"));
        $graph->yaxis->title->Set(iconv("UTF-8","GB2312//IGNORE","The TotalSales"));
        // Set up the title for the graph
         
        $graph->title->Set(iconv("UTF-8","GB2312//IGNORE","菜品前七天销售统计图"));
        $graph->title->SetMargin(12);
        $graph->title->SetFont(FF_SIMSUN,FS_BOLD,12);
        $graph->title->SetColor("darkred");
        
        // Setup font for axis
        $graph->xaxis->SetFont(FF_SIMSUN,FS_NORMAL,10);
        $graph->yaxis->SetFont(FF_SIMSUN,FS_NORMAL,10);
        
        // Show 0 label on Y-axis (default is not to show)
        $graph->yscale->ticks->SupressZeroLabel(false);
        
        // Setup X-axis labels
        $graph->xaxis->SetTickLabels($datax);
        $graph->xaxis->SetLabelAngle(0);
        
        // Create the bar pot
        Vendor('Jpgraph.jpgraph_bar');
        $bplot = new \BarPlot($datay);
        $bplot->SetWidth(0.4);
         
        // Setup color for gradient fill style
        $bplot->SetFillGradient("#BBDDE5","#BBDDE5",GRAD_LEFT_REFLECTION);
        
        // Set color for the frame of each bar
        $bplot->SetColor("white");
        
        $graph->Add($bplot);
        // Finally send the graph to the browser
        $graph->Stroke();
        
    }
    /**
     * 售卖详情
     */
    public function restaurantFoodsSaleDetail(){
        $restaurantsalesModel = M("RestaurantSales");
        $temp = array();
        $tmp = array();
        $datas = array();
        for ($i = 1; $i <= 7; $i++){
            $temp[$i-1] = date('Y-m-d', strtotime("-$i days"));
            $tmp[$i-1] = strtotime(date('Y-m-d', strtotime("-$i days")));
        }
        $tmp = array_reverse($tmp);
        for ($i=0; $i<count($tmp); $i++){
            $last_itime = $tmp[$i]+24*3600;
            $datas[$i] = $restaurantsalesModel
            ->field("b.foods_name,b.logo,a.sales_time,a.num")
            ->alias("a")
            ->join("left join yd_foods b on a.foods_id = b.foods_id")
            ->where(array("a.sales_time"=>array("between","$tmp[$i],$last_itime"),"a.restaurant_id"=>array('eq',session("restaurant_id"))))
            ->select();
        }
        $this->setPageBtn('餐厅前七天售卖情况','','','前七天售卖情况');
        
        $this->assign("detail",$datas);
        $this->display();
    }
    
}
?>