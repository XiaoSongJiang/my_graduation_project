<?php
namespace Tools;

class Page
{

    public  $limits;     //分页偏移量
    private $total;      // 总共有多少条记录
    private $pagenum;    // 分成多少页
    public  $pagesize;   // 每页多少条记录
    private $current;    // 当前所在页
    private $p= 'p';     //分页参数名
    private $url;        // url
    private $first;      // 首页
    private $last;       // 末页
    private $prev;       // 上一页
    private $next;       // 下一页

    private $records = 4;  // 一个页面显示的页数为5
    private $serval;       // 1,2,3,4
    private $gourl;// 跳转
    /**
     * 构造函数
     * 
     * @access public
     * @param $total number
     *            总的记录数
     * @param $pagesize number
     *            每页的记录数
     * @param $current number
     *            当前所在页
     */
    public function __construct($total, $pagesize)
    {
        
       
        $this->total = $total;
        $this->pagesize = $pagesize;
        $this->current = empty($_GET[$this->p]) ? 1 : intval($_GET[$this->p]);
        $this->pagenum = $this->getNum();
        $this->current    = $this->current>0 ? min(array($this->current,$this->pagenum)) : 1;
        $this->limits   = $this->pagesize * ($this->current - 1);
        // 设置url
        $this->url = __ACTION__ . "/$this->p/";
        
        $this->first = $this->getFirst();
        $this->last = $this->getLast();
        $this->prev = $this->getPrev();
        $this->serval = $this->getServral();
        $this->next = $this->getNext();
        $this->gourl = $this->gorul($pagesize, $total);
    }

    /**
     * 获得总页数
     * 
     * @return number
     */
    private function getNum()
    {
        return ceil($this->total / $this->pagesize);
    }

    private function gorul($pagesize, $total)
    {
        $action = __ACTION__;
        $gourlinfo = <<<eof
	      <input type='text' style='width:30px' id='page'/>
	      <input type='button' value='跳转' onclick='jump()'/>
	      <script type="text/javascript">
	        function jump(){
	        	
	        	if(page.value==""){
	        		alert("你没有输入任何值");
	        	}
	        	else if($pagesize*(page.value-1) > $total){
	        		alert("你输入的页码有误");
	        		page.value="";
	        		page.focus();
	        	}
	        	else{
	        	    
	        		window.location.href="$action/$this->p/"+page.value;
	        	}
	        }
        </script>
eof;
        return $gourlinfo;
    }

    /**
     * 一个页面显示的页数
     */
    private function getServral()
    {
        $naviagte = "";
        $start = floor(($this->current - 1) / $this->records) * $this->records + 1;
        $index = $start;
       
        for (;$start<$index+$this->records&$start<=$this->pagenum;$start++){
           
            if ($this->current == $start){
                $naviagte .= "<span class='current' ><a  href='{$this->url}$start'>&nbsp;$start&nbsp;</a></span>";
            }else{
                $naviagte .= "<a  href='{$this->url}$start'>&nbsp;[$start]&nbsp;</a>";
            }
             
        }
        
        return $naviagte;
    }

    /**
     * 首页
     * 
     * @return string
     */
    private function getFirst()
    {
        if ($this->current == 1) {
            return '[首页]';
        } else {
            return "<a href='{$this->url}1'>[首页]<a/>";
        }
    }

    /**
     * 尾页
     * 
     * @return string
     */
    private function getLast()
    {
        if ($this->current == $this->pagenum) {
            return '末页';
        } else {
            return "<a href='{$this->url}{$this->pagenum}'>[末页]</a>";
        }
    }

    /**
     * 上一页
     * 
     * @return string
     */
    private function getPrev()
    {
        if ($this->current == 1) {
            return '[上一页]';
        } else {
            return "<a href='{$this->url}" . ($this->current - 1) . "'>[上一页]</a>";
        }
    }

    /**
     * 下一页
     * 
     * @return string
     */
    private function getNext()
    {
        if ($this->current == $this->pagenum) {
            return '[下一页]';
        } else {
            return "<a href='{$this->url}" . ($this->current + 1) . "'>[下一页]</a>";
        }
    }

    /**
     * showPage方法，得到分页信息
     * 
     * @access public
     * @return string 分页信息字符串
     */
    public function showPage()
    {
        if ($this->pagenum > 1) {
            return "共有 [{$this->total}] 条记录,每页显示[ {$this->pagesize}]条记录， 当前为 {$this->current}/{$this->pagenum} {$this->first} {$this->prev} {$this->serval} {$this->next}  {$this->last} {$this->gourl}";
        } else {
            return "共有 {$this->total} 条记录";
        }
    }
}
