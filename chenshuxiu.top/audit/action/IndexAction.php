<?php

class IndexAction extends AuditBaseAction
{

    // 运营后台首页
    public function doIndex () {
        //加一行测试注释
        $auditor = $this->myauditor;
        $auditroleidarr = explode(',', $auditor->auditroleids);
        $sql = "select am.*
            from auditmenus am
            left join auditresources ar on am.auditresourceid=ar.id
            where 1=1 ";

        $cond = '';

        // 无法进行 bind
        if (false == empty($auditroleidarr)) {
            $cond .= " and ( 1=0 ";
            foreach ($auditroleidarr as $auditroleid) {
                $cond .= " or ar.auditroleids like '%{$auditroleid}%' ";
            }
            $cond .= " ) ";
        }

        $cond .= ' order by am.parentmenuid asc, am.pos asc';

        $sql .= $cond;

        // echo $cond;exit;
        $auditmenus = Dao::loadEntityList('AuditMenu', $sql, []);

        $auditmenutree = array();

        foreach ($auditmenus as $auditmenu) {
            if ($auditmenu->parentmenuid) {
                $auditmenutree[$auditmenu->parentmenuid]['subs'][] = $auditmenu;
            } else {
                $auditmenutree[$auditmenu->id]['self'] = $auditmenu;
            }
        }
        XContext::setValue('auditmenutree', $auditmenutree);

        return self::SUCCESS;
    }

    public function doSetDiseaseidCookieJson () {
        $diseaseid = XRequest::getValue("diseaseid", 0);
        XCookie::set0("_diseaseid_", $diseaseid);

        echo "ok";
        return self::BLANK;
    }

    public function doGetPinyinJson () {
        $words = XRequest::getValue('words', '');
        DBC::requireNotEmpty($words, 'words is null');
        if (!is_array($words)) {
            $words = [$words];
        }
        $pinyins = [];
        foreach ($words as $word) {
            $py = PinyinUtilNew::Word2PY($word);
            $pinyin = PinyinUtilNew::Word2PY($word, false);
            $pinyins[$word] = [$pinyin, $py];
        }
        $this->result['data'] = $pinyins;
        return self::TEXTJSON;
    }

    public function doTestJson () {
        // $row = array (
        // 'media_id' => '123',
        // 'media_type' => 'image',
        // 'created_at' => time(),
        // 'expire_seconds' => 2000000,
        // 'objtype' => 'WxUser',
        // 'objid' => null,
        // 'objcode' => 'Share[DY]',
        // );
        // Media::createByBiz($row);
        // die('xxxxx');

        $this->result['errno'] = - 1;
        $this->result['errmsg'] = '错啦错啦';
        $this->result['data'] = array(
            'id' => 222,
            'name' => '许喆',
            'ename' => 'xuzhe',
            'nickname' => 'honey喆',
            'sexual_orientation' => array(
                '男',
                '女',
                '中性人'));
        return self::TEXTJSON;
    }

    public function doTestRedis() {
        $redis = XRedis::getConnect();
        $redis->set('a', '你好方寸医生');
        $ret = $redis->get('a');
        $this->result['data'] = $ret;
        $redis->close();
        return self::TEXTJSON;
    }
}
