<?php
/*
 * smtp.php @(#) $Header: /home/mlemos/cvsroot/smtp/smtp.php,v 1.39 2006/06/11
 * 02:33:21 mlemos Exp $
 */
require_once ("Internet_Email.class.php");

$to = "shijp@gmail.com";
$subject = "test email";
$content = "新邮件测试";
$contentType = "text/plain;";

$email = new Internet_Email();
return $email->sendmail2($to, $subject, $content, $contentType); // 发送邮件

?>