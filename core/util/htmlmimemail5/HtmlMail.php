<?php

class HtmlMail
{

    private $toAddress;

    private $toName;

    private $mail;

    public function __construct ($hostName, $fromAddress, $fromName, $charset, $user = null, $pass = null) {
        $this->mail = new HtmlMimeMail5();

        $auth = isset($user);
        $this->mail->setSMTPParams($hostName, null, null, $auth, $user, $pass);
        $this->mail->setHTMLCharset($charset);
        $this->mail->setTextCharset($charset);
        $this->mail->setHeadCharset($charset);
        $this->mail->setFrom("\"$fromName\" <$fromAddress>");
    }

    public function build ($toAddress, $toName, $subject, $htmlBody, $imgDir = null) {
        $this->toAddress = $toAddress;
        $this->toName = $toName;

        $this->mail->setSubject($subject);
        $this->mail->setText('This is an HTML message. Please use an HTML capable mail program to read this message.');
        $this->mail->setHTML($htmlBody, $imgDir);

        $this->mail->build();
    }

    public function getMailAsString () {
        return $this->mail->getRFC822(array(
            "{$this->toName}<{$this->toAddress}>"), 'smtp');
    }

    public function save ($emlName) {
        return file_put_contents($emlName, $this->getMailAsString());
    }

    public function send () {
        return $this->mail->send(array(
            "\"{$this->toName}\" <{$this->toAddress}>"), 'smtp');
    }

    public function multiSend ($toArray) {
        return $this->mail->send($toArray);
    }

    public function getErrorInfo () {
        return $this->mail->getErrorInfo();
    }
}

?>
