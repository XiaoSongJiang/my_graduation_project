<?php
/**
 * 用于过滤用户输入字符串
 * @param string $val 用户输入的字符串
 * @return Ambigous <string, boolean>
 */
function removeXSS($val)
{
    static $obj = null;
    if($obj === null)
    {
        require('./Public/htmlpurifier-4.8.0/HTMLPurifier.includes.php');
        $config = HTMLPurifier_Config::createDefault();
        // 保留a标签上的target属性
        $config->set('HTML.TargetBlank', TRUE);
        $obj = new HTMLPurifier($config);
    }
    return $obj->purify($val);
}
/**
 *
 * @param string $imgName 表单中上传文件名
 * @param string $dirName 保存路径名（控制器名） 例如goods category
 * @param array $thumb 缩略图
 */
function uploadOne($imgName, $dirName, $thumb = array()){

    if(isset($_FILES[$imgName]) && $_FILES[$imgName]['error'] == 0){
         
        $cfg = array(
            'maxSize'       =>  (int)C('IMG_MAX_SIZE')*1024*1024, //上传的文件大小限制
            'exts'          =>  C('IMG_EXT'), //允许上传的文件后缀
            'rootPath'      =>  C('IMG_ROOTPATH'), //保存根路径
            'savePath'      => "$dirName/",
        );
        $upload = new \Think\Upload($cfg);// 实例化上传类
        // 上传文件
       
        $info = $upload->upload(array($imgName=>$_FILES[$imgName]));
   
        if(!$info) {
            // 上传错误提示错误信息
            return array(
                'ok' => 0,
                'error' => $upload->getError(),
            );
        }else{// 上传成功
            $ret['ok'] = 1;
            $ret['images'][0] = $ori_logo_name = $upload->rootPath.$info[$imgName]['savepath'].$info[$imgName]['savename'];
            //判断是否生成缩略图
            if($thumb){
                $img = new \Think\Image();
                //循环生成缩略图
                foreach ($thumb as $key => $val){
                    $ret['images'][$key+1] = $sm_logo_name = $upload->rootPath.$info[$imgName]['savepath']."small_$key-".$info[$imgName]['savename'];
                     
                    $img->open($ori_logo_name);
                    $img->thumb($val[0], $val[1])->save($ret['images'][$key+1]);
                }

            }
            return $ret;
        }

    }

}
/**
 * 对脚本的运行计时
 * @return number
 */
function microtime_float()
{
    list($usec, $sec) = explode(" ", microtime());
    return ((float)$usec + (float)$sec);
}
/**
 *
 * @param 总记录数  $count
 * @param 分页大小 $pagesize
 * @return \Think\Page 设置参数后的Page类
 */
function setPage($count, $pagesize = 10) {
    $p = new \Think\Page($count, $pagesize);
    $p->setConfig('header', '<li class="rows">共<b>%TOTAL_ROW%</b>条记录&nbsp;第<b>%NOW_PAGE%</b>页/共<b>%TOTAL_PAGE%</b>页</li>');
    $p->setConfig('prev', '上一页');
    $p->setConfig('next', '下一页');
    $p->setConfig('last', '末页');
    $p->setConfig('first', '首页');
    $p->setConfig('theme', '%FIRST%%UP_PAGE%%LINK_PAGE%%DOWN_PAGE%%END%%HEADER%');
    $p->rollPage = 3;
    $p->lastSuffix = false;//最后一页不显示为总页数
    return $p;
}
/**
 * 删除图片
 * @param array $images 图片所在路径
 */
function deleteImage($images)
{
    // 先取出图片所在目录

    foreach ($images as $v)
    {
        // @错误抵制符：忽略掉错误,一般在删除文件时都添加上这个
        @unlink(".".$v);
    }
}
// 显示图片
function showImage($url, $width='', $height='')
{

    if($width)
        $width = "width='$width'";
    if($height)
        $height = "height='$height'";
    echo "<img src='$url' $width $height />";
}

/**
 * 判断是否有图片
 * @string $img_name 表单中file的名称
 */
function hasImage($img_name){
    foreach ($_FILES[$img_name]['error'] as $v)
    {
        if($v == 0)
            return TRUE;
    }
    return FALSE;
}

/**
 * 用phpmailer发送邮件
 * @param unknown $to 邮件接收人
 * @param unknown $title 邮件标题
 * @param unknown $content 邮件内容
 * @return boolean
 */
function sendmail($to, $title, $content)
{
    require_once('./Public/PHPMailer_v5.1/class.phpmailer.php');
    $mail = new PHPMailer();
    // 设置为要发邮件
    $mail->IsSMTP();
    // 是否允许发送HTML代码做为邮件的内容
    $mail->IsHTML(TRUE);
    // 是否需要身份验证
    $mail->SMTPAuth=TRUE;
    $mail->CharSet='UTF-8';
    /*  邮件服务器上的账号是什么 */
    $mail->From=C('MAIL_ADDRESS');
    $mail->FromName=C('MAIL_FROM');
    $mail->Host=C('MAIL_SMTP');
    $mail->Username=C('MAIL_LOGINNAME');
    $mail->Password=C('MAIL_PASSWORD');
    // 发邮件端口号默认25
    $mail->Port = 25;
    // 收件人
    $mail->AddAddress($to);
    // 邮件标题
    $mail->Subject=$title;
    // 邮件内容
    $mail->Body=$content;
    return($mail->Send());
}

function customredict($url_str,$message){
    $url = U($url_str);
    $str = <<<eof
            <script type="text/javascript">
                alert("$message");
                window.top.location.href = "$url";
            </script>
eof;
    echo $str;
    exit();
}
?>