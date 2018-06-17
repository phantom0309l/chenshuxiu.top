<?php
class Request {
    private $request;
    public function getRequest($key, $default = null, $except = null) {
        if ($this->request == null)
            $this->request = array_diff_assoc($_REQUEST, $_COOKIE);
        $unSafeData = $default;
        if (isset($this->request[$key]))
            $unSafeData = $this->request[$key] != $except ? $this->request[$key] : $default;
        return self::filter($unSafeData);
    }
    public function setRequest($key, $value = null) {
        if (is_array($key)) {
            $this->request = $key;
        } else {
            if ($this->request == null) {
                $this->request = array_diff_assoc($_REQUEST, $_COOKIE);
            }
            if (empty($value) == false)
                $this->request[$key] = urldecode($value);
        }
        return $this;
    }
    public function getPost($key, $default = null, $except = null) {
        return $_POST[$key] != $except ? $_POST[$key] : $default;
    }
    public function setPost($key, $value = null) {
        if (is_array($key)) {
            $_POST = $key;
        } else {
            $_POST[$key] = $value;
        }
        return $this;
    }
    public function getQuery($key, $default = null, $except = null) {
        return $_GET[$key] != $except ? $_GET[$key] : $default;
    }
    public function setQuery($key, $value = null) {
        if (is_array($key)) {
            $_GET = $key;
        } else {
            $_GET[$key] = $value;
        }
        return $this;
    }
    public function getCookie($key, $default = null, $except = null) {
        $_COOKIE[$key] = isset($_COOKIE[$key])?$_COOKIE[$key]:'';
        return $_COOKIE[$key] != $except ? $_COOKIE[$key] : $default;
    }
    public function setCookie($key, $value) {
        $_COOKIE[$key] = $value;
    }
    public function getHeader($key) {
    	$header = getallheaders();
    	return $header[$key];
    }
    public function getEnv($key, $default = null, $except = null) {
        return getenv($key) != $except ? getenv($key) : $default;
    }
    public function setEnv($key, $value = null) {
        if (is_array($key)) {
            $_ENV = $key;
        } else {
            $_ENV[$key] = $value;
        }
        return $this;
    }
    public function __get($key) {
        return self::filter($this->getRequest($key));
        //return ($this->getRequest($key));
    }

    public function isPost(){
        return ('post' == strtolower(getenv('REQUEST_METHOD')));
    }

    public function getAllUnsafeRequest()
    {
        return array_diff_assoc($_GET, $_POST);
    }

    public function getAllRequest()
    {
        return self::filter($this->getMergedRequest());
    }

    private function getMergedRequest()
    {/*{{{*/
        if ($this->request == null)
        {
            $this->request = array_diff_assoc($_REQUEST, $_COOKIE);
        }
        return $this->request;
    }/*}}}*/

    static public function filter($sets)
    {/*{{{*/
        if (is_array($sets))
        {
            foreach ($sets as $key=>$set)
            {
                if (is_array($set))
                {
                    $set = self::filter($set);
                }
                else
                {
                    //��"<>&�Լ�ASCIIֵ��32���µ��ַ�����ת�� ����ҳ�渻�ı����Զ�������ת�� ���Բ��������������ת��
                    //$set = filter_var($set, FILTER_SANITIZE_SPECIAL_CHARS);
                    $set = self::replace($set);
                    $set = trim(filter_var($set, FILTER_SANITIZE_STRIPPED));
                }
                $sets[$key] = $set;
            }
        }
        else if (null !== $sets)
        {
            //$sets = filter_var($sets, FILTER_SANITIZE_SPECIAL_CHARS);
            $sets = self::replace($sets);
            $sets = trim(filter_var($sets, FILTER_SANITIZE_STRIPPED));
        }
        return $sets;
    }/*}}}*/

    static private function replace($value)
    {/*{{{*/
        $replaceMap = array('<' => '&#60;', '>'=> '&#62;');
        foreach($replaceMap as $key=>$code)
        {
            $value = str_replace($key, $code, $value);
        }
        return $value;
    }/*}}}*/

    public function collectAllRequest()
    {/*{{{*/
        $args = array_merge($_GET, $_POST);
        return self::filter($args);
    }/*}}}*/

    public function convertToGBK()
    {/*{{{*/
        if($this->request == null)
        {
            $this->request = array_diff_assoc($_REQUEST, $_COOKIE);
        }
        mb_convert_variables('gbk', 'auto', $this->request);
    }/*}}}*/

    public function getUnSafeData($key, $default = null, $except = null)
    {/*{{{*/
        if ($this->request == null)
            $this->request = array_diff_assoc($_REQUEST, $_COOKIE);
        if (false == isset($this->request[$key]))
            return $default;
        return $this->request[$key] != $except ? $this->request[$key] : $default;
    }/*}}}*/

    public function getAllSafePost()
    {/*{{{*/
        return self::filter($_POST);
    }/*}}}*/

    public function getAllSafeGet()
    {/*{{{*/
        return self::filter($_GET);
    }/*}}}*/

    public function unsetRequest($key)
    {/*{{{*/
        if (isset($this->request[$key]))
        {
            unset($this->request[$key]);
            return true;
        }
        return false;
    }/*}}}*/

    public function isQueryFromOtherHost()
    {/*{{{*/
        $refer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
        if (empty($refer)) return true;

        $parseRefer = parse_url($refer);
        $host = $parseRefer['host'].((isset($parseRefer['port'])&&$parseRefer['port'])?':'.$parseRefer['port']:'');
        return ($_SERVER['HTTP_HOST'] != $host);
    }/*}}}*/

    public function isQueryFromHDF()
    {/*{{{*/
        $refer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
        if (empty($refer)) return true;

        $parseRefer = parse_url($refer);
        $host = $parseRefer['host'].((isset($parseRefer['port'])&&$parseRefer['port'])?':'.$parseRefer['port']:'');
        return (false !== strpos($host, 'haodf.com'));
    }/*}}}*/

    public function isFromIphone()
    {/*{{{*/
        $agent = getenv('HTTP_USER_AGENT');
        return (stristr($agent, 'iphone') || stristr($agent, 'ios') || stristr($agent, 'ipod'));
    }/*}}}*/

    public function isFromAndroid()
    {/*{{{*/
        $agent = getenv('HTTP_USER_AGENT');
        return (stristr($agent, 'Android') || stristr($agent, 'Adr'));
    }/*}}}*/

    public function isFromIpad()
    {/*{{{*/
        $agent = getenv('HTTP_USER_AGENT');
        return (stristr($agent, 'ipad'));
    }/*}}}*/

    public function isFromQQBrowser()
    {/*{{{*/
        $agent = getenv('HTTP_USER_AGENT');
        return (stristr($agent, 'QQBrowser'));
    }/*}}}*/
}
?>
