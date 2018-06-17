<?php

/*
 * @desc 此类目前只支持单个redis，以后扩展到redis集群需要重新编写此类
 */
class WxNoticer extends Noticer
{

    public function send($unitofworkId, $brief, $content) {

        $str = '';
        $env = Config::getConfig('env');
        $pwd = dirname(__FILE__);
        $tmp = explode('/', $pwd);
        $user = $tmp[2];

        $host = getenv('HTTP_HOST');
        if (empty($host)) {
            $xdomain = 'cron';
        } else {
            $pos = strpos($host, '.');
            $xdomain = substr($host, 0, $pos);
        }

        if ($env == 'production') {
            $str = "[线上-{$user}-{$xdomain}";
        } else {
            $str = "[开发-{$user}-{$xdomain}";
        }

        $action = XContext::getValueEx('action', '');
        $method = XContext::getValueEx('method', '');
        $cronName = Debug::getCronName();

        if ($action || $method) {
            $str .= " {$action}/{$method}]";
        } else {
            $str .= " {$cronName}]";
        }

        $brief = str_replace("'", ' ', $brief);
        $brief = str_replace('"', ' ', $brief);

        $data = array(
            'unitofworkid' => $unitofworkId,
            'env' => $str,
            'brief' => $brief,
            'time' => time());

        $job = Job::getInstance();
        $job->doBackground('send_warning_msg', json_encode($data, JSON_UNESCAPED_UNICODE));
    }
}
