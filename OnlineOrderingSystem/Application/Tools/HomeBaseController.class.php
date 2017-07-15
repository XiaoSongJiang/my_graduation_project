<?php

namespace Tools;
use Think\Controller;
class HomeBaseController extends Controller{
    
    public function  __construct(){
        parent::__construct();
        //由于前台的所有页面都可能会涉及到菜品分类，故取出
        $cateModel = D("Admin/FoodsCate");
        $category_data = $cateModel->getNavCateInfo();
       
        $this->assign("category_info",$category_data);
    }
    /**
     * 
     * @param string $title 网页标题
     * @param string $keywords seo关键词
     * @param string $description seo描述
     * @param number $showNav 是否显示 首页的分类导航 1：显示  0：不显示
     * @param array  $css 当前脚本需要的css
     * @param array  $js 当前脚本需要的js
     */
    public function setHeadInfo($title, $keywords, $description, $showNav=0, $css=array(), $js=array()){
    	$this->assign('page_keywords', $keywords);
    	$this->assign('page_description', $description);
    	$this->assign('page_title', $title);
    	$this->assign('show_nav', $showNav);
    	$this->assign('page_css', $css);
    	$this->assign('page_js', $js);
    }
    /**
     *  生成验证码的图片
     */
    public function chkcode(){
		ob_clean();
        $Verify = new \Think\Verify(array(
            'length' => 4,
            'useNoise' => true,
            'fontSize' =>15,
            'fontttf' => '4.ttf',
            'imageH'    => 40,
            'imageW'    => 120
        ));
        $Verify->entry();
    }
    public function setPageBtn($title, $btnName='', $btnLink='',$net_title="")
    {
        $this->assign('page_title', $title);
        $this->assign('page_btn_name', $btnName);
        $this->assign('page_btn_link', $btnLink);
        $this->assign('net_title', $net_title);
    }
}
?>