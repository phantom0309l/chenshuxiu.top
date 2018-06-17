<?php
// OpTaskFilterMgrAction
class OpTaskFilterMgrAction extends AuditBaseAction
{

    public function doList () {
        return self::SUCCESS;
    }

    public function doOne () {
        return self::SUCCESS;
    }

    public function doModify () {
        return self::SUCCESS;
    }

    public function doModifyPost () {
        return self::SUCCESS;
    }

    public function doAddOrModifyJson () {
        $type = XRequest::getValue('type', '');
        $optaskfilterid = XRequest::getValue('optaskfilterid', '');
        $optaskfilter_selected = OpTaskFilter::getById($optaskfilterid);
        $title = XRequest::getValue('title', '');
        $optaskfilters = XRequest::getValue('optaskfilters', []);
        $is_public = XRequest::getValue('is_public', 0);
        $create_auditorid = $this->myauditor->id;
        $remark = XRequest::getValue('remark', '');

        if ($type == 'add' || ($type == 'modify' && $optaskfilter_selected->title != $title)) {
            // 检验title
            $cond = " and (create_auditorid = :create_auditorid or is_public = 1) and title = :title ";
            $bind = [
                ':create_auditorid' => $create_auditorid,
                ':title' => $title
            ];
            $optaskfilter_title = Dao::getEntityByCond('OpTaskFilter', $cond, $bind);
            if ($optaskfilter_title instanceof OpTaskFilter) {
                $this->result['errno'] = -1;
                $this->result['errmsg'] = '过滤器名称已存在!';

                return self::TEXTJSON;
            }
        }

        $list = [];
        $shows = [];
        foreach ($optaskfilters as $optaskfilter) {
            $k = $optaskfilter['filter_key'];
            $v = $optaskfilter['filter_value'];
            $values = [];
            if ($v) {
                $values = explode(',', $v);
            }
            $list["{$k}"] = $values;

            $k_str = $optaskfilter['filter_key_str'];
            $v_str = $optaskfilter['filter_value_str'];

            $values = [];
            if ($v_str) {
                $values = explode(',', $v_str);
            }
            $shows["{$k_str}"] = $values;
        }
        $list['showstr'] = $shows;
        $filter_json = json_encode($list, JSON_UNESCAPED_UNICODE);

        if ($type == 'add') {
            $row = [];
            $row["title"] = $title;
            $row["filter_json"] = $filter_json;
            $row["is_public"] = $is_public;
            $row["create_auditorid"] = $create_auditorid;
            $row["remark"] = $remark;
            $optaskfilter = OpTaskFilter::createByBiz($row);

            $optaskfilterid = $optaskfilter->id;
        } else {
            $optaskfilter_selected->title = $title;
            $optaskfilter_selected->filter_json = $filter_json;
            $optaskfilter_selected->is_public = $is_public;

            $optaskfilterid = $optaskfilter_selected->id;
        }

        $this->result['data'] = [
            'optaskfilterid' => $optaskfilterid
        ];

        return self::TEXTJSON;
    }

    public function doModifyTempJson () {
        $optaskfilters = XRequest::getValue('optaskfilters', []);

        $optaskfilter_temp = OpTaskFilterService::getTempByCreate_auditorid($this->myauditor->id);

        $list = [];
        $shows = [];
        foreach ($optaskfilters as $optaskfilter) {
            $k = $optaskfilter['filter_key'];
            $v = $optaskfilter['filter_value'];
            $list["{$k}"] = explode(',', $v);

            $k_str = $optaskfilter['filter_key_str'];
            $v_str = $optaskfilter['filter_value_str'];
            $shows["{$k_str}"] = explode(',', $v_str);
        }
        $list['showstr'] = $shows;
        $filter_json = json_encode($list, JSON_UNESCAPED_UNICODE);

        $optaskfilter_temp->filter_json = $filter_json;

        $this->result['data'] = [
            'optaskfilterid' => $optaskfilter_temp->id
        ];

        return self::TEXTJSON;
    }

    public function doDeleteJson () {
        $optaskfilterid = XRequest::getValue('optaskfilterid', 0);

        $optaskfilter = OpTaskFilter::getById($optaskfilterid);

        if ($optaskfilter instanceof OpTaskFilter && $optaskfilter->create_auditorid == $this->myauditor->id) {
            $optaskfilter->remove();

            echo 'success';
        } else {
            echo 'fail';
        }

        return self::BLANK;
    }
}
