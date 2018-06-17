<?php

class ErrorAction extends BaseAction
{

    function __construct () {
        parent::__construct();
    }

    public function do404 () {
        return self::SUCCESS;
    }

    public function doError () {
        $u = XRequest::getValue("u", 0);
        $e = XRequest::getValue("e", '');
        $error_title = XRequest::getValue("error_title", '');
        $msgstr = "[线上异常]";
        $msgstr .= "\nu= {$u}";
        $msgstr .= "\n\n{$e}";

        $errorMsg = $e ? $e : '系统错误,请重试.';
        $errorTitle = $error_title ? $error_title : '错误页';

        XContext::setValue('errorMsg', $errorMsg);
        XContext::setValue('errorTitle', $errorTitle);

        return self::SUCCESS;
    }
}
