<?php

ini_set("arg_seperator.output", "&amp;");
ini_set("magic_quotes_gpc", 0);
ini_set("magic_quotes_sybase", 0);
ini_set("magic_quotes_runtime", 0);
ini_set('display_errors', 1);
ini_set("memory_limit", "2048M");
include_once (dirname(__FILE__) . "/../../sys/PathDefine.php");
include_once (ROOT_TOP_PATH . "/cron/Assembly.php");
include_once (ROOT_TOP_PATH . "/../core/util/email/class.phpmailer.php");
mb_internal_encoding("UTF-8");

TheSystem::init(__FILE__);

XContext::setValue("dtpl", ROOT_TOP_PATH . "/domain/tpl");

echo "\n\n-----begin----- " . XDateTime::now();
Debug::trace("=====[cron][beg][fill_base_msg.php]=====");

class MailSender
{

    public $host = "smtp.ym.163.com";

    public $username = "qiaotaojin@fangcunyisheng.com";

    public $password = "txj1987";

    public $from_address_alias = "方寸课堂";

    public $from_address = "qiaotaojin@fangcunyisheng.com";

    public $to_address = "747969377@qq.com";

    public $subject = "《ISPY视觉大发现》免费送，请关注微信公众号『方寸课堂』";

    public $maillist = null;

    public function doWork () {
        $this->getToMaillist();
        foreach ($this->maillist as $i => $v) {
            echo "[{$i}][$v]\n";
            $this->sendOne($v);
            sleep(2);
        }
    }

    public function getToMaillist () {
        $filename = "email.txt";
        $fp = fopen($filename, "r");
        $contents = fread($fp, filesize($filename));
        $this->maillist = explode("|", $contents);
        fclose($fp);
    }

    public function sendOne ($to_address) {
        $to_address = trim($to_address) . "@qq.com";
        $mail = new PHPMailer();
        $mail->IsSMTP(); // send via SMTP
        $mail->Host = $this->host; // SMTP servers
        $mail->SMTPAuth = true; // turn on SMTP authentication
        $mail->Username = $this->username; // SMTP username 注意：普通邮件认证不需要加 @域名
        $mail->Password = $this->password; // SMTP password

        $mail->SetFrom($this->from_address, $this->from_address_alias);

        $mail->CharSet = "UTF8";
        $mail->Encoding = "base64";

        $mail->AddAddress($to_address, ""); // 收件人邮箱和姓名

        $mail->IsHTML(true); // send as HTML
        $mail->Subject = $this->subject;

        // 邮件内容 可以直接发送html文件
        $mail->Body = <<<EOT
    <html>
    <head>
    <title>方寸课堂</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <style type="text/css">
    <!--
    .STYLE1 {color: #009900}
    .STYLE3 {color: #000000}
    a:link {
        color: #333333;
    }
    .STYLE4 {color: #666666}
    body,td,th {
        font-family: 宋体;
        font-size: 12px;
    }
    .STYLE6 {color: #FFFFFF}
    .STYLE9 {color: #DCB003}
    .bp{ color:#ff6666; font-weight:bold;}
    .notice{ font-size:16px; font-weight:bold; }

    -->
    </style>
    </head>
    <body bgcolor="#FFFFFF" style="width:660px;" >
<div style="width:660px;">
    <p>微信公众号『方寸课堂』上线以来，通过《家长课程》、《注意力游戏》等方式，帮助家长：</p>
<p class="bp" style="color:#ff6666;">1. 改善教育方式</p>
<p class="bp" style="color:#ff6666;">2. 改善小朋友学习质量</p>
<p class="bp" style="color:#ff6666;">3. 提升小朋友的注意力</p>
<p class="bp" style="color:#ff6666;">4. 改善亲子关系 </p>

<p class="notice" style="color:#ff6666; font-weight:bold; font-size:16px;">收获家长一致好评，关注后点击『课堂交流』菜单，查看当前课程家长的感悟与孩子的成长！,感受孩子真正进步的喜悦！ </p>
<p>
值此开学伊始之际，推出<span class="notice" style="color:#ff6666; font-weight:bold; font-size:16px;">“我为方寸课堂代言”</span>活动，长按文章底部二维码，关注方寸课堂。关注后，回复“代言”，将会收到自己专属的代言卡片，将卡片分享出去，让更多的人来关注了解『方寸课堂』。通过你的专属卡片关注『方寸课堂』的家长，都会记录在你的名下。</p>

<p><span class="notice" >代言人前5名</span>将会获得由方寸课堂送出的，提升小朋友注意力的丛书《I SPY 视觉大发现》一套。本次活动只是前期预热活动，马上将进行新一轮的活动，<span class="bp" style="color:#ff6666; font-weight:bold; font-size:16px;">奖品会更多更广（会有乐高玩具等）</span>，希望大家持续关注到时候参加！</p>

<p><img src="http://mmbiz.qpic.cn/mmbiz/6yvoELN4gK2TiaS9AKaItgNnXicEvbNDmf5D6fDvWo9haficz9rVdUoppepD6wCb8VU2Qg4wDtttne0ZdY43sl8ZQ/640?wx_fmt=jpeg&tp=webp&wxfrom=5&wx_lazy=1"/></p>

<p style="color:#ff6666; font-weight:bold; font-size:16px;">微信搜索『方寸课堂』或者微信扫描下方二维码：</p>
<p><img src="http://mmbiz.qpic.cn/mmbiz/6yvoELN4gK3hAQhygUkqcbfIxWOmlPVt7uDOciarfEW2CsibsSHv1pQicJibXCdTBllf1TbM8GHmabWt5tU1Pm9xKw/640?wx_fmt=png&tp=webp&wxfrom=5&wx_lazy=1"/></p>
</div>

    </body>
    </html>
EOT;
        $mail->AltBody = "text/html";

        $mail->Send();
    }

}

$process = new MailSender();
$process->doWork();
