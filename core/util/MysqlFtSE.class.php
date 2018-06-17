<?php

/*
 * 本搜索引擎为 mysql全文检索引擎 操作步骤如下（见附录） 1.创建辅助函数 2.创建表 3.数据导入 4.创建全文索引 5.增加和删除数据行 6.检索
 */
class MysqlFtSE
{

    private $dbExecuter;

    private $table;

    public function __construct ($dbExecuter, $table) {
        $this->dbExecuter = $dbExecuter;
        $this->table = $table;
    }

    // 输入关键词数组
    public function matchImp ($keywords, $isAnd, $groupByPid) {
        $hexs = array();
        foreach ($keywords as $keyword) {
            $hexs[] = self::getHEX($keyword, $isAnd);
        }
        $cond = implode(" ", $hexs);

        $cond = "'$cond' IN BOOLEAN MODE";

        if ($groupByPid) {
            $sql = "select pid,id,count(*) as cnt,sum(score) as sumscore
			from
			(
			select id,pid,ft_text,match(ft_code) against($cond) as score
			from {$this->table}
			where match(ft_code) against($cond)
			) tt group by pid order by sumscore desc limit 500";
        } else {
            $sql = "select id,pid,ft_text,match(ft_code) against($cond) as score
				from {$this->table}
				where match(ft_code) against($cond)
				order by match(ft_code) against($cond) desc limit 500";
        }

        // echo $sql;
        return $this->dbExecuter->query($sql);
    }

    public static function getHEX ($keyword, $isAnd) {
        $len = mb_strlen($keyword, "utf-8");
        $chars = array();
        for ($i = 0; $i < $len; $i ++) {
            $chars[] = strtoupper(bin2hex(mb_substr($keyword, $i, 1, "utf-8")));
        }
        if ($isAnd)
            return '+"' . implode(" ", $chars) . '"';
        else
            return '"' . implode(" ", $chars) . '"';

        // $sql = "select fungetsplitchinese('$keyword')";
        // return $this->dbExecuter->queryValue($sql);
    }

    public static function getHexStr ($str) {
        $len = mb_strlen($str, "utf-8");
        $hexStr = "";
        for ($i = 0; $i < $len; $i ++) {
            $char = mb_substr($str, $i, 1, "utf-8");
            $hexChar = strtoupper(bin2hex($char));
            $hexStr .= $hexChar;
            $hexStr .= " ";
        }
        return $hexStr;
    }

    // 输入搜索字符串，空格分隔
    // $isAnd=true 求交集
    public function match ($str, $isAnd = true, $groupByPid = true) {
        $keywords = explode(" ", $str);
        $keywords = array_unique($keywords);
        $keywords = array_diff($keywords, array(
            " ",
            ""));
        return $this->matchImp($keywords, $isAnd, $groupByPid);
    }

    // 输入搜索字符串，空格分隔
    // $isAnd=true 求交集
    public function match2 ($str, $isAnd = true, $groupByPid = true, $step = 2) {
        $keywords = explode(" ", $str);
        $kws = array();

        foreach ($keywords as $keyword) {
            $len = mb_strlen($keyword, "utf-8");
            $step;

            if ($len <= $step) {
                $kws[] = $keyword;
            } else {
                for ($i = 0; $i < $len - ($step - 1); $i ++) {
                    $kw = mb_substr($keyword, $i, $step, "UTF-8");
                    $kws[] = $kw;
                }
            }
        }

        $kws = array_unique($kws);
        $kws = array_diff($kws, array(
            " ",
            ""));
        return $this->matchImp($kws, $isAnd, $groupByPid);
    }
}

/*
 * 附录 下面两个函数为辅助函数，需要定义进自己的mysql 数据库中 -- 把汉字拆为以空格分割的编码的字符串的函数 CREATE FUNCTION
 * funsplitchinese(s varchar(1024000)) RETURNS text BEGIN declare rptindex int;
 * declare paramlen int; declare retstr text; set rptindex = 1; set paramlen =
 * char_length(s); set retstr = ''; REPEAT set retstr = concat(retstr, ' ',
 * HEX(substr(s,rptindex,1))); SET rptindex = rptindex + 1; UNTIL rptindex>
 * paramlen END REPEAT; return ltrim(rtrim(retstr)); END CREATE FUNCTION
 * fungetsplitchinese(s varchar(1024000)) RETURNS text BEGIN declare rptindex
 * int; declare paramlen int; declare retstr text; set rptindex = 1; set
 * paramlen = char_length(s); set retstr = ''; REPEAT set retstr =
 * concat(retstr, ' ', HEX(substr(s,rptindex,1))); SET rptindex = rptindex + 1;
 * UNTIL rptindex> paramlen END REPEAT; return concat('\"',
 * ltrim(rtrim(retstr)), '\"'); END --创建全文索引表 drop table ft_member_sellinfos;
 * CREATE TABLE `ft_member_sellinfos` ( `id` decimal(30,0) NOT NULL default '0',
 * `pid` decimal(30,0) NOT NULL default '0', `ft_text` varchar(1025) NOT NULL
 * default '', `ft_code` text NOT NULL default '', PRIMARY KEY (`id`), KEY
 * `ft_member_sellinfos_pid` (`pid`) ) ENGINE=MyISAM DEFAULT CHARSET=utf8;
 * --导入数据 insert into ft_member_sellinfos(id,pid,ft_text,ft_code) select
 * a.id,a.corporationid,a.caption,funsplitchinese(a.caption) from sellinfos a
 * left join corporations b on a.corporationid=b.id where a.status=1 and
 * b.level>0; --导入商吧msgs insert into ft_msgs(id,pid,ft_text,ft_code) select
 * id,topicid,title,funsplitchinese(concat(title,' ',tags,' ',content)) from
 * msgs -- 创建全文索引 alter table ft_member_sellinfos add FULLTEXT
 * `ftidx_ft_member_sellinfos` (`ft_code`); -- 简单搜索 （一个词:钢管） set @s =
 * concat('\'', fungetsplitchinese('钢管'), '\'') ; select * from
 * ft_member_sellinfos where match(ft_code) against(@s IN BOOLEAN MODE); -- 复杂搜索
 * -- 汽车 = E6B1BD E8BDA6 -- 配件 = E9858D E4BBB6 -- 加工 = E58AA0 E5B7A5 -- 包含：汽车
 * 配件，排除：加工 select id,pid,ft_text,match(ft_code) against('"E6B1BD E8BDA6"
 * "E9858D E4BBB6" -"E58AA0 E5B7A5"' IN BOOLEAN MODE) as score from
 * ft_member_sellinfos where match(ft_code) against('"E6B1BD E8BDA6" "E9858D
 * E4BBB6" -"E58AA0 E5B7A5"' IN BOOLEAN MODE) order by match(ft_code)
 * against('"E6B1BD E8BDA6" "E9858D E4BBB6" -"E58AA0 E5B7A5"' IN BOOLEAN MODE)
 * desc
 */
?>