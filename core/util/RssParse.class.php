<?PHP

/**
 * ***************************
 * RSS文档解析类-(lovered.GV)
 * sjp 进行了修改
 * *****************************
 */
class RssParse
{

    var $url; // rss文件的地址
    var $data; // rss文件的内容
    var $version; // rss文件的版本号
    var $channel; // rss文件中的频道信息
    var $image;

    var $items;

    // 与XML解析有关的属性####################
    var $xml_parser; // xml解析器句柄
    var $depth; // XML当前解析深度
    var $tag; // 当前正在解析的XML元素
    var $marker; // 用来标记制定的深度
    var $event; // 实践名称:CHANNEL and ITEM
    var $item_index; // item元素索引

    var $eventstack = array();

    public function __construct ($rss_url) {
        $this->url = $rss_url;
    }

    public function doWork ($proxy = "") {
        /*
         * 旧方式 $h=@fopen($this->url,"r"); if(empty($h)) { echo("\r\nfopen
         * error,{$this->url}\r\n"); return false; } stream_set_timeout($h, 60);
         * while(!feof($h)) $this->data.=fgets($h,4096); fclose($h);
         */

        $httpRequest = XHttpRequest::getInstance();
        $httpRequest->setProxy($proxy);
        $content = $httpRequest->getUrlContents($this->url, $err = '');

        if ($content) {
            $this->data = $content;
        } else {
            // echo("\r\nfopen error,{$this->url} err=$err\r\n");
            return false;
        }

        $this->data = ereg_replace("/^$/i", "", $this->data);

        // 初始化xml解析器
        $this->xml_parser = xml_parser_create("UTF-8");
        xml_set_object($this->xml_parser, $this);
        xml_parser_set_option($this->xml_parser, XML_OPTION_CASE_FOLDING, 1);
        xml_set_element_handler($this->xml_parser, "startElement", "endElement");
        xml_set_character_data_handler($this->xml_parser, "characterData");
        // 开始解析数据
        if (! @xml_parse($this->xml_parser, $this->data)) {
            // echo("XML error:
            // ".xml_error_string(xml_get_error_code($this->xml_parser))." at
            // line ".xml_get_current_line_number($this->xml_parser));
            // echo("\r\nXML error,{$this->url}\r\n");
            // $code = xml_get_error_code($this->xml_parser);
            // echo xml_error_string($code);
            return false;
        }

        // echo "<pre>";
        // var_dump($this);
        // exit;

        return true;
    }

    function getData () {
        return $this->data;
    }

    // 返回所有ITEM信息#######################
    function GetItems () {
        return $this->items;
    }

    // 返回频道信息###########################
    function GetChannel () {
        return $this->channel;
    }

    // 返回IMAGE信息###########################
    function GetImage () {
        return $this->image;
    }

    // 返回RSS文件的版本信息##################
    function GetVersion () {
        return $this->version;
    }

    // 开始解析XML元素########################
    function startElement ($parser, $name, $attribs) {
        $this->depth ++;
        $this->tag = $name;

        // echo $name;
        // echo "<br>";

        switch ($name) {
            case "RSS":
                $this->version = $attribs["VERSION"];
                $this->event = $name;
                array_push($this->eventstack, $name);
                break;
            case "CHANNEL":
            case "FEED":
            case "IMAGE":
                $this->marker = $this->depth + 1;
                $this->event = $name;
                array_push($this->eventstack, $name);
                break;
            case "ITEM":
            case "ENTRY":
                $this->item_index ++;
                $this->marker = $this->depth + 1;
                $this->event = $name;
                array_push($this->eventstack, $name);
                break;
            default:
                return NULL;
        }

    }

    // 结束某个元素解析时#####################
    function endElement ($parser, $name) {
        if ($this->event == $name) {
            array_pop($this->eventstack);
            $this->event = array_pop($this->eventstack);
            $this->marker --;
        }

        $this->depth --;
        return;
    }

    // 处理数据###############################
    function characterData ($parser, $data) {
        $data = trim($data);
        // $data = strip_tags($data);
        // $data=iconv("utf-8","gb2312",trim($data));

        // 当数据为chanel下的数据时执行, FEED 是针对译言做的处理
        if (($this->event == "CHANNEL" || $this->event == "FEED") && $this->marker == $this->depth) {
            $this->channel[$this->tag] .= $data;
        }

        // 当数据为image下的数据时执行
        if ($this->event == "IMAGE" && $this->marker == $this->depth) {
            $this->image[$this->tag] .= $data;
        }

        // 当数据为item下的数据时执行, ENTRY 是针对译言做的处理
        if (($this->event == "ITEM" || $this->event == "ENTRY") && $this->marker == $this->depth) {
            $this->items[$this->item_index][$this->tag] .= $data;
        }
    }

    // 是否是一个有效的RSS地址################
    function IsRss ($rss_url) {
        $rss_url = trim($rss_url);

        if ($rss_url == "")
            return false;

        if ($h = @fopen($rss_url, "r")) {
            $text = @fread($h, 512);
            @fclose($h);

            if (eregi("<RSS", $text))
                return true;
            elseif (eregi("<FEED", $text))
                return true;
            else
                return false;
        } else
            return false;
    }
}

// $rss = new
// RssParse("http://blog4.eastmoney.com/rss2.asp?dcuser_name=falcon1985");
// $result = $rss->doWork();
// var_dump($rss->GetItems());
?>