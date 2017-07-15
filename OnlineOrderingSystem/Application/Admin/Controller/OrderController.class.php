<?php
namespace Admin\Controller;
use Tools\AdminBaseController;
class OrderController extends AdminBaseController {
    /**
     * 订单列表
     */
    public function orderList()
    {
        $orderModel = D("Home/Order");
        $order_list_info = $orderModel -> showOrderList(-1);
	    $order_info = $order_list_info['order_all'];
	    
	    foreach ($order_info as $key => $val){
	        $logos = explode(",", $val['logos']);
	        $foods_names = explode(",", $val['foods_names']);
	        $foods_ids = explode(",", $val['foods_ids']);
	        $foods_nums = explode(",", $val['foods_nums']);
	        $foods_prices = explode(",", $val['foods_prices']);
	      
            $order_info[$key]['logos'] = $logos;
            $order_info[$key]['foods_names'] = $foods_names;
            $order_info[$key]['foods_ids'] = $foods_ids;
            $order_info[$key]['foods_nums'] = $foods_nums;
            $order_info[$key]['foods_prices'] = $foods_prices;
	    }
	    $arr = array();
	    $arr1 = array();
	    foreach ($order_info as $k => $v){
	            if (count($v['foods_prices']) > 1){
	                $arr['receiver_name'] = $v['receiver_name'];
	                $arr['receiver_mobile'] = $v['receiver_mobile'];
	                $arr['receiver_address'] = $v['receiver_address'];
	                foreach ($v['foods_prices'] as $k1 => $v1){
	                    $arr['foods_price'] = $v1;
	                    $arr['foods_num'] = $v['foods_nums'][$k1];
	                    $arr['foods_name'] = $v['foods_names'][$k1];
	                    $arr['foods_id'] = $v['foods_ids'][$k1];
	                    $arr['logo'] = $v['logos'][$k1];
	                    $order_info[$k]['foods_list'][] = $arr;
	                }
	            }else{
	                $arr1['receiver_name'] = $v['receiver_name'];
	                $arr1['receiver_mobile'] = $v['receiver_mobile'];
	                $arr1['receiver_address'] = $v['receiver_address'];
	                $arr1['foods_price'] = $v['foods_prices'][0];
	                $arr1['foods_num'] = $v['foods_nums'][0];
	                $arr1['foods_name'] = $v['foods_names'][0];
	                $arr1['foods_id'] = $v['foods_ids'][0];
	                $arr1['logo'] = $v['logos'][0];
	                $order_info[$k]['foods_list'][] = $arr1;
	            }
	            
	            
	        
	    }
	   
	    $this->assign("order_list_info",$order_info);
	    $this->assign("pages",$order_list_info['pages']);
        $this->setPageBtn('订单列表', '', U("orderList"));
        $this->display();
    }
    /**
     * 修改
     */
    public function OrderEdit(){
        // 建立数据处理模型
        $model = D("Home/Member");
        if (IS_POST){
           
            if ($model->create(I("post."),2)) {
                // 插入数据库
                if (false !== $model->save()) { //save()返回的是受影响的记录数，没有修改则返回0 错误返回false
                    // 提示信息
                   
                    $this->success('修改成功！',U('memberList?p='.I("get.p")));
                    // 停止执行后面的代码
                    exit();
                }
            }
            // 错误处理
            $error = $model->getError();
            $this->error($error);
        }
        //获得修改订单的id
        $member_id = I("get.member_id");
        $member_info = $model->find($member_id);
        $this->assign("member_info",$member_info);
        $this->setPageBtn('订单编辑', '订单列表', U("memberList"));
        $this->display();
    }
    /**
     * 订单删除
     */
    public function orderDelete(){
        $model = D("Home/Order");
        
        $res = $model -> delete(I("get.order_id"));
        if($res !== false){
            
            $this->success("删除成功！",U("orderList?p=".I("get.p")));
        }else {
            $this->error($model->getError());
        }
       
    }
    /**
     * 菜品销售情况
     */
    public function foodsSalesRecord(){
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
                        ->where(array("sales_time"=>array("between","$tmp[$i],$last_itime")))
                        ->select()['0']['total'];
        }
        file_put_contents("C:/test.txt", $restaurantsalesModel->getLastSql());
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
    
}














