<?php

class HtmlCtr
{

    // 问诊单选
    public static function getWenzhenRadioCtrImp($arr, $name, $selectid = 0, $otherstr = "") {
        $optionstr = "";
        $num = count($arr);
        $select_value = "";
        foreach ($arr as $key => $value) {
            $activeClass = ($selectid == $key) ? " radio-itemActive" : "";
            if ($selectid == $key) {
                $select_value = $key;
            }
            $optionstr .= "<div data-optionid=\"{$key}\" class=\"clearfix radio-item {$activeClass}\"><div class=\"fl radio-item-l\"><span class=\"radio-item-tip\"></span></div>
            <div class=\"radio-item-r\">{$value}</div>
            </div>";
        }
        $hiddenstr = "<input type=\"hidden\" name=\"{$name}\" class=\"hiddenItem\" value=\"$select_value\"/>";

        return <<< HTML
        {$hiddenstr}
        {$optionstr}
        {$otherstr}
HTML;
    }

    // 下拉
    public static function getSelectCtrImp($arr, $name, $selectid = 0, $class = "", $style = "", $fixs=[]) {
        $str = "\n<select autocomplete=\"off\" name=\"$name\" id=\"$name\" class=\"$class\" style=\"$style\"> ";

        foreach ($arr as $key => $value) {
            $fix = isset($fixs[$key]) ? $fixs[$key] : " class=\"\" ";
            $str .= "\n <option value=\"$key\" {$fix}";
            // echo $key."=>".$value;
            if ($key == "{$selectid}") {
                $str .= " selected=\"selected\" ";
            }

            $str .= "> $value </option>";
        }

        $str .= "\n</select>";
        // exit;
        return $str;
    }

    //下拉多选
    public static function getMultiSelectCtrImp($arr, $name, $selectid = [], $class = "") {
        $str = "\n<select autocomplete=\"off\" name=\"$name\" id=\"$name\" class=\"$class\" multiple=\"multiple\">";
        //$str .= "<option disabled selected value></option>";
        foreach ($arr as $key => $value) {
            $str .= "\n <option value=\"$key\"";
            // echo $key."=>".$value;
            if (in_array($key, $selectid)) {
                $str .= " selected=\"selected\" ";
            }

            $str .= "> $value </option>";
        }

        $str .= "\n</select>";
        // exit;
        return $str;
    }

    // 单选
    public static function getRadioCtrImp($arr, $name, $selectid = 0, $br = "<br/>", $class = '', $fixs = array(), $start = '', $end = '') {
        $rand = rand(1000, 9999);
        $str = "";

        $i = 0;
        foreach ($arr as $key => $value) {
            $i++;
            if ($i == count($arr)) {
                $br = ' ';
            }

            $fix = isset($fixs[$key]) ? $fixs[$key] : " class=\"$class\" ";

            $str .= $start . ("\n<input id=\"id_{$rand}_{$key}\" type=\"radio\" name=\"$name\" {$fix} value=\"$key\" " . ($selectid == $key ? ' checked="checked"' : '') .
                    " > <label " . ($selectid == $key ? ' ' : '') . " for=\"id_{$rand}_{$key}\">$value</label> $br") . $end;
        }
        return $str;
    }

    // button 的单选
    public static function getButtonRadioCtrImp($arr, $name, $selectid = 0, $br = "<br/>", $class = '', $fixs = array(), $start = '', $end = '') {
        $rand = rand(1000, 9999);
        $str = "";

        $i = 0;
        foreach ($arr as $key => $value) {
            $i++;
            if ($i == count($arr)) {
                $br = ' ';
            }

            $xoption = XOption::getById($selectid);
            $xoptionContent = $xoption->content;

            $activeClass = '';
            switch ($xoptionContent) {
                case '合格':
                    $activeClass = 'btn-success';
                    break;
                case '不合格':
                    $activeClass = 'btn-danger';
                    break;
                case '不适用':
                    $activeClass = 'btn-info';
                    break;
                default:
                    $activeClass = 'btn-warning';
            }

            $fix = isset($fixs[$key]) ? $fixs[$key] : " class=\"$class\" ";

            $str .= $start . ("\n<input id=\"{$key}\" class=\"btn btn-minw btn-default ".($selectid==$key? $activeClass:'')."\" type='button' name=\"$name\" {$fix} value=\"$value\" data-type=\"$value\" data-index=\"$i\" > $br") . $end;
        }
        return $str;
    }

    // OneUi样式单选
    public static function getRadioCtrImp4OneUi($arr, $name, $selectid = 0, $class = 'css-radio-warning', $start = '', $end = '') {
        $rand = rand(1000, 9999);
        $str = "";
        foreach ($arr as $key => $value) {
            $str .= $start . ("\n<label class=\"css-input css-radio {$class} push-10-r\"> <input id=\"id_{$rand}_{$key}\" type=\"radio\" name=\"$name\" value=\"$key\" " . ($selectid == $key ? ' checked="checked"' : '') .
                    " ><span></span> $value</label>") . $end;
        }
        return $str;
    }

    // OneUi样式多选
    public static function getCheckboxCtrImp4OneUi($arr, $name, $selectids = [], $br = "<br />", $class = 'css-checkbox-warning', $start = '', $end = '') {
        $rand = rand(1000, 9999);
        $str = "";
        /*
            <label class="css-input css-checkbox css-checkbox-success">
                <input type="checkbox" checked=""><span></span> Success
            </label>
         */

        $i = 0;
        foreach ($arr as $key => $value) {
            $str .= $start . ("\n<label class=\"css-input css-checkbox {$class} push-10-r\"> <input id=\"id_{$rand}_{$key}\" type=\"checkbox\" data-value=\"$value\" name=\"$name\" value=\"$key\" " . (in_array($key, $selectids) ? ' checked="checked"' : '') .
                    " ><span></span> $value</label>{$br}") . $end;
        }
        return $str;
    }

    // 按钮单选
    public static function getBtnRadioCtrImp($arr, $name, $selectid = 0, $otherstr = "") {
        $optionstr = "";
        $num = count($arr);
        $select_value = "";
        foreach ($arr as $key => $value) {
            if ($selectid == $key) {
                $select_value = $key;
            }
            $activeClass = ($selectid == $key) ? " option-active" : "";
            $optionstr .= "<label class=\"option {$activeClass} option-center options-c{$num}\" data-optionid=\"{$key}\">{$value}</label>";
        }
        $hiddenstr = "<input type=\"hidden\" name=\"{$name}\" class=\"hiddenItem\" value=\"$select_value\"/>";

        return <<< HTML
            <div class="clearfix options">
                {$hiddenstr}
                <span class='fl clearfix options-full'>{$optionstr}</span>
                {$otherstr}
            </div>
HTML;
    }

    public static function getAddressCtr4New($name, $xprovinceid = 0, $xcityid = 0, $xcountyid = 0) {
        $xprovinceid = $xprovinceid ?? 0;
        $xcityid = $xcityid ?? 0;
        $xcountyid = $xcountyid ?? 0;

        return <<< HTML
        <style>
            .padding-left-right{
                padding-left: 4px;
                padding-right: 4px;
            }
        </style>

        <div class="col-xs-10 form-group" style="margin-bottom: 0px;padding-left: 10px;">
            <div class="col-xs-4 padding-left-right">
                <select class="form-control" id="{$name}_xprovince" name="{$name}[xprovinceid]">
                </select>
            </div>

            <div class="col-xs-4 padding-left-right">
                <select class="form-control" id="{$name}_xcity" name="{$name}[xcityid]">
                </select>
            </div>

            <div class="col-xs-4 padding-left-right">
                <select class="form-control" id="{$name}_xcounty" name="{$name}[xcountyid]">
                </select>
            </div>
        </div>

        <script>
            $(function () {
                init();

                $("#{$name}_xprovince").on('change', function(){
                    var me = $(this);

                    var xprovinceid = me.val();

                    $.ajax({
                        "type" : "get",
                        "data" : {
                            xprovinceid : xprovinceid
                        },
                        "dataType" : "json",
                        "url" : "/xcitymgr/getxcitys",
                        "success" : function(data) {
                            var htmlstr = "";
                            $.each(data['data'], function (index, info) {
                                if ($xcityid == info['id']) {
                                    htmlstr += "<option value=\"" + info['id'] + "\" selected>" + info['name'] + "</option>";
                                } else {
                                    htmlstr += "<option value=\"" + info['id'] + "\">" + info['name'] + "</option>";
                                }
                            });

                            $("#{$name}_xcity").html(htmlstr);
                        }
                    });

                    var htmlstr = "<option value='0'>请选择</option>";

                    $("#{$name}_xcounty").html(htmlstr);
                });

                $("#{$name}_xcity").on('change', function(){
                    var me = $(this);

                    var xcityid = me.val();

                    $.ajax({
                        "type" : "get",
                        "data" : {
                            xcityid : xcityid
                        },
                        "dataType" : "json",
                        "url" : "/xcountymgr/getxcountys",
                        "success" : function(data) {
                            var htmlstr = "";
                            $.each(data['data'], function (index, info) {
                                if ($xcountyid == info['id']) {
                                    htmlstr += "<option value=\"" + info['id'] + "\" selected>" + info['name'] + "</option>";
                                } else {
                                    htmlstr += "<option value=\"" + info['id'] + "\">" + info['name'] + "</option>";
                                }
                            });

                            $("#{$name}_xcounty").html(htmlstr);
                        }
                    });
                });
            });
            function init () {
                // init xprovince
                $.ajax({
                    "type" : "get",
                    "data" : {},
                    "dataType" : "json",
                    "url" : "/xprovincemgr/getxprovinces",
                    "success" : function(data) {
                        var htmlstr = "";
                        $.each(data['data'], function (index, info) {
                            if ($xprovinceid == info['id']) {
                                htmlstr += "<option value=\"" + info['id'] + "\" selected>" + info['name'] + "</option>";
                            } else {
                                htmlstr += "<option value=\"" + info['id'] + "\">" + info['name'] + "</option>";
                            }
                        });

                        $("#{$name}_xprovince").html(htmlstr);
                    }
                });

                // init xcity
                if ($xprovinceid == 0 && $xcityid == 0) {
                    var htmlstr = "<option value='0'>请选择</option>";

                    $("#{$name}_xcity").html(htmlstr);
                } else if ($xprovinceid > 0) {
                    $.ajax({
                        "type" : "get",
                        "data" : {
                            xprovinceid : $xprovinceid
                        },
                        "dataType" : "json",
                        "url" : "/xcitymgr/getxcitys",
                        "success" : function(data) {
                            var htmlstr = "";
                            $.each(data['data'], function (index, info) {
                                if ($xcityid == info['id']) {
                                    htmlstr += "<option value=\"" + info['id'] + "\" selected>" + info['name'] + "</option>";
                                } else {
                                    htmlstr += "<option value=\"" + info['id'] + "\">" + info['name'] + "</option>";
                                }
                            });

                            $("#{$name}_xcity").html(htmlstr);
                        }
                    });
                }

                // init xcounty
                if ($xcityid == 0 && $xcountyid == 0) {
                    var htmlstr = "<option value='0'>请选择</option>";

                    $("#{$name}_xcounty").html(htmlstr);
                } else if ($xcityid > 0) {
                    $.ajax({
                        "type" : "get",
                        "data" : {
                            xcityid : $xcityid
                        },
                        "dataType" : "json",
                        "url" : "/xcountymgr/getxcountys",
                        "success" : function(data) {
                            var htmlstr = "";
                            $.each(data['data'], function (index, info) {
                                if ($xcountyid == info['id']) {
                                    htmlstr += "<option value=\"" + info['id'] + "\" selected>" + info['name'] + "</option>";
                                } else {
                                    htmlstr += "<option value=\"" + info['id'] + "\">" + info['name'] + "</option>";
                                }
                            });

                            $("#{$name}_xcounty").html(htmlstr);
                        }
                    });
                }
            }
        </script>
HTML;
    }

    // 病史单选
    public static function getHistoryRadioCtrImp($arr, $name, $selectid = 0, $fixs = array(), $otherstr = "") {
        $optionstr = "";
        $select_value = "";
        foreach ($arr as $key => $value) {
            $fix = isset($fixs[$key]) ? $fixs[$key] : "";
            if ($selectid == $key) {
                $select_value = $key;
            }
            $activeClass = ($selectid == $key) ? " radio-itemActive" : "";
            $optionstr .= "<div data-optionid=\"{$key}\" class=\"clearfix radio-item {$activeClass}  {$fix}  \"><div class=\"fl radio-item-l\"></div>
                            <div class=\"radio-item-r\">{$value}</div>
                        </div>";
        }
        $hiddenstr = "<input type=\"hidden\" name=\"{$name}\" class=\"hiddenItem\" value=\"$select_value\"/>";

        return <<< HTML
                {$hiddenstr}
                {$optionstr}
                {$otherstr}
HTML;
    }

    // 多选
    public static function getWenzhenCheckboxCtrImp($arr, $name, $selectids = array(), $otherstr = "") {
        if (empty($selectids)) {
            $selectids = array();
        }

        $optionstr = "";
        $hiddenstr = "";
        foreach ($arr as $key => $value) {
            $select_value = '';
            if (in_array($key, $selectids)) {
                $select_value = $key;
            }
            $hiddenstr .= "<input type=\"hidden\" class=\"hiddenItem\" name=\"$name\" value=\"$select_value\" />";
            $activeClass = in_array($key, $selectids) ? " checkbox-itemActive" : "";

            $optionstr .= "<div data-optionid=\"{$key}\" class=\"clearfix checkbox-item {$activeClass}\"><div class=\"fl checkbox-item-l\"><span class=\"checkbox-item-tip\"></span></div>
                    <div class=\"checkbox-item-r\">{$value}</div>
                </div>";
        }

        return <<< HTML
        {$hiddenstr}
        {$optionstr}
        {$otherstr}
HTML;
    }

    // 多选
    public static function getCheckupTplCheckboxCtrImp($arr, $name, $selectids = array(), $otherstr = "") {
        if (empty($selectids)) {
            $selectids = array();
        }

        $optionstr = "";
        $hiddenstr = "";
        foreach ($arr as $key => $value) {
            $select_value = "";
            if (in_array($key, $selectids)) {
                $select_value = $key;
            }
            $hiddenstr .= "<input type=\"hidden\" class=\"hiddenItem\" name=\"$name\" value=\"$select_value\" />";
            $activeClass = in_array($key, $selectids) ? " checkbox-itemActive" : "";

            $optionstr .= "<div data-optionid=\"{$key}\" class=\"clearfix checkbox-item {$activeClass}\"><div class=\"fl checkbox-item-l\"><span class=\"checkbox-item-tip\"></span></div>
                    <div class=\"checkbox-item-r\">{$value}</div>
                </div>";
        }

        return <<< HTML
        {$hiddenstr}
        {$optionstr}
        {$otherstr}
HTML;
    }

    // 多选
    public static function getCheckboxCtrImp($arr, $name, $selectids = array(), $br = "<br/>", $class = '', $fixs=[], $start = '', $end = '') {

        if (empty($selectids)) {
            $selectids = array();
        }

        $rand = rand(1000, 9999);
        $str = "";
        $i = 0;
        foreach ($arr as $key => $value) {
            $i++;
            if ($i == count($arr)) {
                $br = ' ';
            }
            $fix = isset($fixs[$key]) ? $fixs[$key] : " class=\"$class\" ";
            $str .= $start . ("\n<input id=\"id_{$rand}_{$key}\" type=\"checkbox\" name=\"$name\" {$fix} value=\"$key\" " .
                    (in_array($key, $selectids) ? ' checked="checked"' : '') . " > <label for=\"id_{$rand}_{$key}\">$value</label> $br") . $end;
        }

        return $str;
    }

    public static function getCheckboxCtrImp5($arr, $name, $selectids = array(), $br = "<br/>", $class = '') {
        if (empty($selectids)) {
            $selectids = array();
        }

        $rand = rand(1000, 9999);
        $str = "";
        $i = 0;
        foreach ($arr as $key => $value) {
            $i++;
            if ($i == count($arr)) {
                $br = ' ';
            }
            if ($i % 5 == 0) {
                $enter = "<br>";
            } else {
                $enter = ' ';
            }
            $str .= "\n<span><input id=\"id_{$rand}_{$key}\" type=\"checkbox\" class=\"$class\" name=\"$name\" value=\"$key\" " .
                (in_array($key, $selectids) ? ' checked="checked"' : '') . " > <label for=\"id_{$rand}_{$key}\">$value</label></span> $br $enter ";
        }

        return $str;
    }

    // 多选
    public static function getCheckboxCtrImpTable4($arr, $name, $selectids = array(), $br = "<br/>", $class = '') {

        if (empty($selectids)) {
            $selectids = array();
        }

        $str = "<table class='col-md-12 col-sm-12 col-xs-12'>";
        $i = 0;
        $tdcount = 0;
        foreach ($arr as $key => $value) {
            $i++;
            if ($i == count($arr)) {
                $br = ' ';
            }
            if (($i - 1) % 3 == 0) {
                $tdcount = 0;
                $str .= "<tr>";
            }
            $tdcount++;
            $img_uri = Config::getConfig("img_uri");
            $str .= ("\n
                <td class='col-md-4 col-sm-4 col-xs-4' height='40px'>
                <span class=\"\">
                <img  class=\"item-selected itemid-$key\" data-id=\"$key\" style=\"display: none;margin-bottom: 2px;margin-left: 0px;margin-right: 0px\" src=\"$img_uri/admin/image/selected.png\">
                <input class='item-noselected id-$key' data-id=\"$key\" type='text' readonly=\"readonly\" name='' style='margin-bottom: 2px;width: 18px;height: 18px;border: 1px solid #666;'>
                <input style=\"display: none\" id=\"input-$key\" data-id=\"$key\" type=\"checkbox\" class=\"$class\" name=\"$name\" value=\"$key\" " .
                (in_array($key, $selectids) ? ' checked="checked"' : '') . " >
                <span class='text' data-id=\"$key\" id='text-$key'>$value</span>
                </span>
                </td>
                $br ");
            if ($tdcount == 4) {
                $str .= "</tr>";
            }
        }
        $str .= "</table>";

        return $str;
    }

    // 多选
    public static function getCheckboxCtrTableImp($arr, $selectids = array()) {

        if (empty($selectids)) {
            $selectids = array();
        }

        $str = " ";
        foreach ($arr as $key => $value) {
            if (in_array($key, $selectids)) {
                $str .= $value . " ";
            }
        }

        return $str;
    }

    // 单选
    public static function getCheckboxCtrImp4single($arr, $name, $selectid = array(), $br = "<br/>", $class = '') {

        $str = "";
        $i = 0;
        foreach ($arr as $key => $value) {
            $i++;
            if ($i == count($arr)) {
                $br = ' ';
            }
            $str .= ("\n<input type=\"checkbox\" class=\"$class\" name=\"$name\" value=\"$key\" " .
                ($selectid == $key ? ' checked="checked"' : '') . " > $value $br");
        }

        return $str;
    }

    // 表格
    public static function getTableCtrImp ($name='',$className,$header=array(),$body=array(),$delimiter='***') {
        $headerStr = "<thead><tr>{$delimiter}</tr></thead>";
        $bodyStr = "<tbody>{$delimiter}</tbody>";

            // 生成 <thead></thead> 标签
        $headerTd = "";
        foreach($header as $td){
            $headerTd .= "<td>{$td}</td>";
        }
        $headerStr = self::pushSonDom($headerStr,$headerTd,$delimiter);

        // 生成 <tbody></tbody> 标签
        $bodyData = "";
        if(!empty($body) && is_array($body)){
            foreach($body as $trKey=>$tr){
                if(is_array($tr)){
                    $trStr = "<tr>{$delimiter}</tr>";

                    $count = 0;
                    foreach($tr as $tdKey=>$td){
                        $tdStr = "<td><input type='text' name='{$tdKey}' value='{$td}'  autocomplete='off'></td></td>";
                        $trStr = self::pushSonDom($trStr,$tdStr,$delimiter);
                        $count++;
                        if($count != count($tr)){
                            $num = strrpos($trStr,'</td>');
                            $trStr = substr_replace($trStr,$delimiter,$num,0);
                        }
                    }
                    $bodyData .= $trStr;
                }else {
                    $tdStr = "<td><input type='text' name='{$trKey}' value='{$tr}'  autocomplete='off'></td>";
                    $bodyData .= $tdStr;
                }
            }
        }
        $bodyStr = self::pushSonDom($bodyStr,$bodyData,$delimiter);

        $tableStr = "<table name='{$name}' class='{$className}'>{$headerStr}{$bodyStr}</table>";
        return $tableStr;
    }

    // 向父标签中插入子标签
    private static function pushSonDom ($parent,$son,$delimiter){
        $num = strpos($parent , $delimiter);
        $html = substr_replace($parent,$son,$num,strlen($delimiter));

        return $html;
    }

    // textarea
    public static function  getCheckBtnBootstrap ($arr, $name, $selectid = array(), $class = '') {
        $str = '';



        return $str;
    }
}