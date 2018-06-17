<?php

include_once "simplehtmldom/simple_html_dom.php";

/*
 * 版权所有者 sjp & wzb 算法思路：找出最长的文字节点，再去噪声，再归并
 */
class HtmlExtract
{

    // 干扰项
    private static $noises = array();

    private static $tree0 = array(); // 本节点数据长度,包括嵌套节点
    private static $tree1 = array(); // 本节点纯数据长度,不包括嵌套节点
    private static $tree2 = array(); // 保存节点
    public static function doWork ($str, $uri = "") {

        self::$noises = array();
        self::$tree0 = array(); // 本节点数据长度,包括嵌套节点
        self::$tree1 = array(); // 本节点纯数据长度,不包括嵌套节点
        self::$tree2 = array(); // 保存节点

        $search = array(
            "/>[\s]+/",
            "/[\s]+</",
            "/[\s]+/",
            "/<[\s\/]*br[^>]*>/i",
            "/<[\s\/]*input[^>]*>/i",
            "/<script(.|\s)*?>(.|\s)*?<\/script>/i",
            "/<style(.|\s)*?>(.|\s)*?<\/style>/i",
            "/<iframe(.|\s)*?>(.|\s)*?<\/iframe>/i",
            "/&nbsp;/i");
        $replace = array(
            ">",
            "<",
            " ",
            "!@!",
            " ",
            " ",
            " ",
            " ",
            " ");
        $str = preg_replace($search, $replace, $str);

        // $search = array("/<[\s]*script[^>]*>.*<[\s\/]*[\s]*script[\s]*>/i");
        // $replace = array (" ");
        // $str = preg_replace($search,$replace,$str);

        $html = str_get_html($str);

        $body = $html->find("body", 0);

        // 递归节点导出
        self::dumpNode($body, 0);

        // 按得分大小排序
        arsort(self::$tree1);

        // 截取最长的10个节点
        $arr = array_slice(self::$tree1, 0, 10);

        // 保留 len>50 的节点
        $tops = array();
        foreach ($arr as $key => $len) {
            if ($len > 50)
                $tops[$key] = self::$tree0[$key];
        }

        // *
        foreach ($tops as $key => $len) {
            $len0 = self::$tree0[$key];
            $len1 = self::$tree1[$key];
            $node = self::$tree2[$key];
            // echo "\r\n $key => $len1 => $len0 => ".$node->innertext;
        }
        // */

        // 清除无效段落
        $tops = self::trimTops($tops);
        // print_r($tops);

        // 找出最小公约数节点[比喻]
        $subkey = self::maxSubkey($tops);
        // echo $subkey;

        $node = self::$tree2[$subkey];
        $maxtxt = $node->innertext;

        return str_replace("!@!", "<br>", $maxtxt);
    }

    // 递归遍历dom树，计算各节点的内签文字长度
    private static function dumpNode ($node, $prefix = "0") {
        if (empty($node)) {
            return 0;
        }

        $children = $node->children();
        $plaintext = $node->plaintext;

        // 无效字符不计数
        // $plaintext = preg_replace("/&#\d+;/","",$plaintext);
        // $plaintext = preg_replace("/\d+/","",$plaintext);
        // $plaintext = preg_replace("/\w+/i","",$plaintext);
        $plaintext = preg_replace("/\s+/i", " ", $plaintext);
        $plaintext = preg_replace("/\t+/i", " ", $plaintext);

        // 包含所有子节点的正文长度
        $len0 = $len = mb_strlen($plaintext, "utf-8");

        self::$tree0[$prefix] = $len0;

        if (empty($children)) {
            self::$tree1[$prefix] = $len;
            self::$tree2[$prefix] = $node;
            return $len;
        }

        $i = 0;
        foreach ($children as $child) {
            // 如果没有内文，则跳过
            $len -= self::dumpNode($child, $prefix . "," . $i);
            $i ++;
        }

        self::$tree1[$prefix] = $len;
        self::$tree2[$prefix] = $node;

        return $len0;
    }

    // 过滤掉无效的段落
    private static function trimTops ($tops) {
        $arr = array();
        $i = 0;
        foreach ($tops as $key => $len) {
            if ($i == 0) {
                $p = preg_replace("/(\d+,\d+,\d+,).*/", "\${1}", $key);
                // echo "\r\np=$p\r\n";
            }

            if (0 === strpos($key, $p)) {
                $arr[$key] = $len;
            }

            $i ++;
        }

        return $arr;
    }

    // 最小公共子节点
    private static function maxSubkey ($tops) {
        foreach ($tops as $key => $len) {
            $str = $key;
            break;
        }

        if (empty($str)) {
            return 0;
        }

        $arr = array();

        $pos = 0;
        while (true) {
            $pos = strpos($str, ",", $pos + 1);
            if ($pos === false)
                break;
            $arr[] = substr($str, 0, $pos);
        }
        $arr[] = $str;
        $arr = array_reverse($arr);

        // 需要归并的项目
        $cnt = count($tops);
        foreach ($arr as $str) {
            $i = 0;
            foreach ($tops as $key => $len) {
                $pos = strpos($key, $str);
                if ($pos === 0) {
                    $i ++;
                }
            }
            // 都已归并
            if (($cnt - $i) == 0)
                return $str;

                // 已归并的数>3,且未归并的<2,则可中止
            if ($i > 3 && ($cnt - $i) < 2) {
                return $str;
            }
        }

        return $str;
    }
}
?>