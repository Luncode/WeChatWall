<?php
error_reporting(1);
header('Content-type:text/html; Charset=utf-8');
/* 配置开始 */
$appid = '';  //换成微信公众号的AppID
$appKey = '';   //微信公众号AppSecret
/* 配置结束 */

//①、获取用户openid
$wxPay = new WxService($appid,$appKey);
$data = $wxPay->GetOpenid();      //获取openid
if(!$data['openid']) exit('获取openid失败');
//②、获取用户信息
$user = $wxPay->getUserInfo($data['openid'],$data['access_token']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="renderer" content="webkit" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge,Chrome=1" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />
    <title>签到抽奖</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <script src="js/jquery.min.js"></script>
    <style type="text/css">
        body{
            background-image: url('image/2.jpg');
            background-repeat: no-repeat;
            background-size: 100%;
        }
        .messagebox{
            width: 100%;
            position: absolute;
            background-repeat: no-repeat;
            top:33%;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="row">
        <h1 align="center" style="color: aliceblue;"><img src="image/sign.png" style="max-width: 100%;"></h1>
        <div class="container" style="margin-top: -60px;">
            <img src="image/targer.png" class="center-block" style="position: relative; top: 140px;">
            <img src="<?=$user['headimgurl']?>" id="userface" class="center-block" style="width: 120px;border-radius: 50%;" alt="">
            <h4 style="color: #FFFFFF;margin-top: 20px;" align="center" >昵称:<span id="nickname"><?=$user['nickname']?></span></h4>
            <p id="openid" style="display: none;text-align: center;"><?=$user['openid']?></p>
            <div class="container" align="center" id="truename">
                <div class="input-group">
                    <span class="input-group-addon" id="basic-addon3">真实姓名</span>
            <input type="text" name="name" placeholder="兑奖时使用" class="form-control">
            </div>
            </div>
            <button style="background:none;outline: none;border: 0; margin-top: 30px;" class="center-block" onclick="submit();" id="submitbtn"><img id="switchbutton" src="image/button.png"></button>
            <h5 style="color: #FFFFFF;margin-top: 50px;" align="center">BY:Luncode</h5>
        </div>
    </div>
</div>
<div class="messagebox" class="center-block" style="display: none;z-index: 99999;">
    <h4 style="letter-spacing: 5px;margin-top: 0px;color: #FFFFFF;position: relative;top: 100px;" id="message" align="center"></h4>
    <img src="image/messagebox.png" class="center-block">
    <span><button style="background:none;outline: none;border: 0;position: relative;top:-100px;" class="center-block" onclick="$('.messagebox').fadeOut();$('#submitbtn').attr('disabled','true');">
        <img  src="image/qd.png"style="max-width: 75%;" >
    </button></span>
</div>
</body>
</html>
<script type="text/javascript">
    //load();
    // function load(){
    //   // alert('asd');
    //         $.ajax({
    //         url:'load.php',
    //         type:"POST",
    //         data:"openid="+$('#openid').text(),
    //         success:function(){
    //             $('#switchbutton').attr('src','image/yqd.png');
    //             $('#submitbtn').attr('disabled','true');
    //             //$('#truename').hide();
    //         }
    //     });
    // }
    function submit(){
         $('#submitbtn').attr('disabled','true');
        $('#truename').hide();
        $.ajax({
            url:'enter.php',
            type:"POST",
            //data: '{"openid":"asdadsada","nickname":"saltfish","userface":"http://localhost/luck/img/1.jpg","name":"李"}',
            data:"openid="+$('#openid').text()+"&nickname="+$('#nickname').text()+"&userface="+$('#userface').attr('src')+"&name="+$('input[name=name]').val(),
            success:function(code){
                
               // alert(code);
                if(code==200){ 
                    $('#message').text("签到成功"); 
                    $('.messagebox').fadeIn();
                    $('#switchbutton').attr('src','image/yqd.png');
                }else if(code==201){
                    $('#message').text("您已签过到了"); 
                    $('.messagebox').fadeIn();
                    //alert('您已签过到了');
                    $('#switchbutton').attr('src','image/yqd.png');
                }else{
                    alert("ERROR");
                }
            }
        });
    }
</script>

<?php
class WxService
{
    protected $appid;
    protected $appKey;

    public $data = null;

    public function __construct($appid, $appKey)
    {
        $this->appid = $appid; //微信支付申请对应的公众号的APPID
        $this->appKey = $appKey; //微信支付申请对应的公众号的APP Key
    }

    /**
     * 通过跳转获取用户的openid，跳转流程如下：
     * 1、设置自己需要调回的url及其其他参数，跳转到微信服务器https://open.weixin.qq.com/connect/oauth2/authorize
     * 2、微信服务处理完成之后会跳转回用户redirect_uri地址，此时会带上一些参数，如：code
     *
     * @return 用户的openid
     */
    public function GetOpenid()
    {
        //通过code获得openid
        if (!isset($_GET['code'])){
            //触发微信返回code码
            $baseUrl = $this->getCurrentUrl();
            $url = $this->__CreateOauthUrlForCode($baseUrl);
            Header("Location: $url");
            exit();
        } else {
            //获取code码，以获取openid
            $code = $_GET['code'];
            $openid = $this->getOpenidFromMp($code);
            return $openid;
        }
    }

    public function getCurrentUrl()
    {
        $scheme = $_SERVER['HTTPS']=='on' ? 'https://' : 'http://';
        $uri = $_SERVER['PHP_SELF'].$_SERVER['QUERY_STRING'];
        if($_SERVER['REQUEST_URI']) $uri = $_SERVER['REQUEST_URI'];
        $baseUrl = urlencode($scheme.$_SERVER['HTTP_HOST'].$uri);
        return $baseUrl;
    }

    /**
     * 通过code从工作平台获取openid机器access_token
     * @param string $code 微信跳转回来带上的code
     * @return openid
     */
    public function GetOpenidFromMp($code)
    {
        $url = $this->__CreateOauthUrlForOpenid($code);        
        $res = self::curlGet($url);
        $data = json_decode($res,true);
        $this->data = $data;
        return $data;
    }

    /**
     * 构造获取open和access_toke的url地址
     * @param string $code，微信跳转带回的code
     * @return 请求的url
     */
    private function __CreateOauthUrlForOpenid($code)
    {
        $urlObj["appid"] = $this->appid;
        $urlObj["secret"] = $this->appKey;
        $urlObj["code"] = $code;
        $urlObj["grant_type"] = "authorization_code";
        $bizString = $this->ToUrlParams($urlObj);
        return "https://api.weixin.qq.com/sns/oauth2/access_token?".$bizString;
    }

    /**
     * 构造获取code的url连接
     * @param string $redirectUrl 微信服务器回跳的url，需要url编码
     * @return 返回构造好的url
     */
    private function __CreateOauthUrlForCode($redirectUrl)
    {
        $urlObj["appid"] = $this->appid;
        $urlObj["redirect_uri"] = "$redirectUrl";
        $urlObj["response_type"] = "code";
        $urlObj["scope"] = "snsapi_userinfo";
        $urlObj["state"] = "STATE";
        $bizString = $this->ToUrlParams($urlObj);
        return "https://open.weixin.qq.com/connect/oauth2/authorize?".$bizString;
    }

    /**
     * 拼接签名字符串
     * @param array $urlObj
     * @return 返回已经拼接好的字符串
     */
    private function ToUrlParams($urlObj)
    {
        $buff = "";
        foreach ($urlObj as $k => $v)
        {
            if($k != "sign") $buff .= $k . "=" . $v . "&";
        }
        $buff = trim($buff, "&");
        return $buff;
    }

    /**
     * 获取用户信息
     * @param string $openid 调用【网页授权获取用户信息】接口获取到用户在该公众号下的Openid
     * @return string
     */
    public function getUserInfo($openid,$access_token)
    {

        $response = self::curlGet('https://api.weixin.qq.com/sns/userinfo?access_token='.$access_token.'&openid='.$openid.'&lang=zh_CN');
        return json_decode($response,true);
        
    }

    public static function curlGet($url = '', $options = array())
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        if (!empty($options)) {
            curl_setopt_array($ch, $options);
        }
        //https请求 不验证证书和host
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }

    public static function curlPost($url = '', $postData = '', $options = array())
    {
        if (is_array($postData)) {
            $postData = http_build_query($postData);
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30); //设置cURL允许执行的最长秒数
        if (!empty($options)) {
            curl_setopt_array($ch, $options);
        }
        //https请求 不验证证书和host
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }

}