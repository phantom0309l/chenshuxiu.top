<style>
    .filter-box .block-title {
        font-weight: 400;
        font-size: 14px;
        color: #646464;
    }

    .filter-box .block {
        margin-bottom: 15px;
    }

    .filter-box .block-header {
        padding: 10px 10px;
    }

    .filter-box .block-content {
        padding: 15px 15px 0px;
    }

    .filter-box .nice-copy {
        margin-bottom: 15px;
    }

    .filter-box .tab-pane {
        margin-bottom: 15px;
    }

    .ui-autocomplete {
        max-height: 300px;
        overflow-y: auto;
        /* 防止水平滚动条 */
        overflow-x: hidden;
      }
</style>
<?php
$configs = json_decode($optaskfilter->filter_json, true);
?>
<!--<h2 class="content-heading" style="margin-top: 10px; padding-left: 5px;"> 过滤器</h2>-->
<input type="hidden" id="optaskfilterid" name="optaskfilterid" value="<?= $optaskfilter->id ?>">
<div class="bg-white">
    <div class="block" style="margin-bottom: 0px;">
        <ul class="nav nav-tabs nav-tabs-alt" data-toggle="tabs">
            <li class="active">
                <a href="#btabs-alt-static-current" data-toggle="tooltip" title="" data-original-title="当前选中过滤器">当前</a>
            </li>
            <li class="">
                <a href="#btabs-alt-static-lock" data-toggle="tooltip" title=""
                   data-original-title="锁定过滤器">锁定(<span
                            class="span_lock_patient_cnt"><?= $myauditor->getLock_patient_cnt(); ?></span>)</a>
            </li>
            <li class="">
                <a href="#btabs-alt-static-private" data-toggle="tooltip" title="" data-original-title="个人过滤器">个人</a>
            </li>
            <li class="">
                <a href="#btabs-alt-static-public" data-toggle="tooltip" title="" data-original-title="公共过滤器">公共</a>
            </li>

            <li class="pull-right">
                <a href="#btabs-alt-static-edit" data-toggle="tooltip" title="" data-original-title="修改当前过滤器"><i
                            class="fa fa-pencil"></i> 修改</a>
            </li>
        </ul>
        <div class="block-content tab-content">
            <div class="tab-pane active" id="btabs-alt-static-current">
                <h3 class="block-title" style="margin-top: 0px; margin-bottom: 10px;">以下为 <a href="javascript:void(0)"><?= $optaskfilter->title ? $optaskfilter->title : '临时' ?></a>的过滤条件</h3>
                <?php
                $shows = $configs['showstr'];
                $shows = OpTaskFilterService::FixShows($configs['showstr'], $myauditor);
                if (is_array($shows)) { ?>
                    <p class="nice-copy">
                        <?php
                        foreach ($shows as $value) {
                            $showstr = implode(',', $value);
                            if ($showstr) {
                                echo "<span class='label label-default push-5-r push-10' style='background-color: #959595;'> {$showstr}</span> ";
                            }
                        } ?>
                    </p>
                <?php } ?>
            </div>
            <div class="tab-pane" id="btabs-alt-static-lock">
                <?php
                foreach ($public_optaskfilters as $public_filter) {
                    if ($public_filter->title == '') {
                        continue;
                    }

                    if ($public_filter->id > 2000001) {
                        continue;
                    }

                    $color = 'default';
                    $flag = "";
                    if ($public_filter->id == $optaskfilter->id) {
                        $color = 'primary';
                        $flag = "<i class='fa fa-check'></i>";
                    }
                    echo "<a class='btn btn-xs btn-{$color} mr5 mb5' href='/optaskmgr/listnew?optaskfilterid={$public_filter->id}'>{$flag} {$public_filter->title}</a> ";
                }
                ?>

                <div class="border-top-dashed mt10 pt10">
                    <div class="form-group" style="margin: 0px; padding: 0px">
                        <label class="col-xs-5 col-md-2 control-label" style="width: 55px; padding-left: 0;"
                               for="is_auto_lock_patient">自动锁定</label>
                        <div class="col-xs-7">
                            <input class="form-control" type="hidden" id="is_auto_lock_patient"
                                   name="is_auto_lock_patient"
                                   value="<?= $myauditor->is_auto_lock_patient ?>">
                            <?php
                            if ($myauditor->is_auto_lock_patient == 1) {
                                $checkedstr = 'checked';
                                $text = '开';
                            } else {
                                $checkedstr = '';
                                $text = '关';
                            }
                            ?>
                            <label class="css-input switch switch-success">
                                <input type="checkbox" id="toggle_is_auto_lock_patient" <?= $checkedstr ?>>
                                <span></span>
                                <span id="text_is_auto_lock_patient"><?= $text ?></span>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="border-top-dashed mt10 pt10">
                    <button class="btn btn-default" id="unlock_all" type="button">
                        <i class="fa fa-unlock"></i>
                        释放我锁定的患者
                    </button>
                </div>
            </div>
            <div class="tab-pane" id="btabs-alt-static-private" style="margin-bottom: 5px;">
                <!--                <h4 class="font-w300 push-15">by -->
                <? //= $optaskfilter->create_auditor->name ?><!--</h4>-->
                <p class="nice-copy remove-margin" id="private-filter">
                    <?php
                    foreach ($private_optaskfilters as $private_filter) {
                        $color = 'default';
                        $flag = "";
                        $title = $private_filter->title ? $private_filter->title : '临时';
                        if ($private_filter->id == $optaskfilter->id) {
                            $color = 'primary';
                            $flag = "<i class='fa fa-check'></i>";
                        }
                        echo "<a class='btn btn-xs btn-{$color} push-5-r push-10' href='/optaskmgr/listnew?optaskfilterid={$private_filter->id}'>{$flag} {$title}</a> ";
                    }
                    ?>
                </p>
            </div>
            <div class="tab-pane" id="btabs-alt-static-public" style="margin-bottom: 5px;">
                <!--                <h4 class="font-w300 push-15">by -->
                <? //= $optaskfilter->create_auditor->name ?><!--</h4>-->
                <p class="nice-copy remove-margin" id="public-filter">
                    <?php
                    foreach ($public_optaskfilters as $public_filter) {

                        if ($public_filter->id < 2000001) {
                            continue;
                        }

                        if ($public_filter->title == '') {
                            continue;
                        }

                        $color = 'default';
                        $flag = "";
                        if ($public_filter->id == $optaskfilter->id) {
                            $color = 'primary';
                            $flag = "<i class='fa fa-check'></i>";
                        }
                        echo "<a class='btn btn-xs btn-{$color} push-5-r push-10' href='/optaskmgr/listnew?optaskfilterid={$public_filter->id}'>{$flag} {$public_filter->title}</a> ";
                    }
                    ?>
                </p>
            </div>
            <div class="tab-pane" id="btabs-alt-static-edit">
                <!--                <h4 class="font-w300 push-15">by -->
                <? //= $optaskfilter->create_auditor->name ?><!--</h4>-->
                <div class="block-content remove-padding" id="filter-now-content">
                    <?php
                    $all_configs = OpTaskFilter::getAllConfig();
                    foreach ($all_configs as $config_title) {
                        include_once dirname(__FILE__) . "/_{$config_title}.tpl.php";
                    }

                    include_once dirname(__FILE__) . "/_submit.tpl.php";
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="bg-white mt20">
    <div class="p10">
        <form action="/optaskmgr/listnew" method="get">
	    <input type="hidden" id="patientid" name="patientid" value="">
            <div class="form-group" style="margin-bottom: 0;">
                <div class="col-xs-12">
                    <div class="input-group">
                        <input type="text" class="form-control" name="patient_name" id="optask-listnew-word"
                               value="<?= $patient_name ?>" placeholder="患者姓名/拼音/ID/手机">
                        <span class="input-group-btn"><button class="btn btn-default" type="submit"><i
                                        class="fa fa-search"></i> 搜索</button></span>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    //读Cookie
    function getCookie(objName) {//获取指定名称的cookie的值
        var arrStr = document.cookie.split("; ");
        for (var i = 0; i < arrStr.length; i++) {
            var temp = arrStr[i].split("=");
            if (temp[0] == objName) return unescape(temp[1]);
        }
        return "";
    }

    //设置cookie的值
    function setCookie(cname, cvalue, exdays) {
        var d = new Date();
        console.log(cname + '/' + cvalue + '/' + exdays)
        d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
        var expires = "expires=" + d.toGMTString();
        document.cookie = cname + "=" + escape(cvalue) + "; " + expires;
        console.log(document.cookie);
    }

    function initAutoComplete() {
        $("#optask-listnew-word").autocomplete({
	    source: function( request, response ) {
		$.ajax({
		  url: "/commonservice/suggest/patient/",
                  type: 'get',
                  dataType: 'json',
		  data: {
		    k: request.term,
		  },
		  success: function( d ) {
		    response( $.map( d, function( item ) {
		      return {
			label: item.name + " (" + item.disease_name + ") <span class='text-gray'>" + item.id + "</span>",
			value: item.name,
                        id: item.id
		      }
		    }));
                  },
                  complete: function(d) {
                      $('#patientid').val('');
                  }
		});
	    },
            minLength: 1,
            select: function( event, ui ) {
                $('#patientid').val(ui.item.id);
            }
        }).focus(function(){
            $(this).autocomplete("search");
        }).data("ui-autocomplete")._renderItem = function (ul, item) {
            return $("<li></li>")
             .data("item.autocomplete", item)
             .append("<div>" + item.label + "</div>")
             .appendTo(ul);
     	};
    }

    $(function () {
        initAutoComplete();

        $('.J_filter_search_form').submit(function () {
            var word = $('#optask-listnew-word').val();
            if (word == '') {
                alert("关键词不能为空!");
                return false;
            }
        });

        $('#toggle_is_auto_lock_patient').on('click', function () {
            var me = $(this);
            var url = "";

            if (me.context.checked == true) {
                url = "/patientmgr/auto_lock_patient_openJson";
            } else {
                url = "/patientmgr/auto_lock_patient_closeJson";
            }

            $.ajax({
                url: url,
                type: 'get',
                dataType: 'json',
                data: {},
                "success": function (data) {
                    if (data.errno != '0') {
                        alert(data.errmsg);
                        return;
                    }

                    if (me.context.checked == true) {
                        $('#is_auto_lock_patient').val(1);
                        $('#text_is_auto_lock_patient').text('开');
                    } else {
                        $('#is_auto_lock_patient').val(0);
                        $('#text_is_auto_lock_patient').text('关');
                    }
                }
            });
        });

        $('.btn-filter-list').on('click', function () {
            if ($("#filter-list").hasClass('block-opt-hidden')) {
                setCookie('div_filter_list_hidden', 1, 100);
            } else {
                setCookie('div_filter_list_hidden', 0, 100);
            }
        });

        $('#unlock_all').on('click', function () {

            if (!confirm('确认释放所有锁定的患者吗?')) {
                return;
            }

            $.ajax({
                url: '/patientmgr/unlock_allJson',
                type: 'get',
                dataType: 'json',
                data: {},
                "success": function (data) {
                    if (confirm(data.errmsg + "\n刷新一下页面?")) {
                        window.location.reload();
                        return;
                    }
                }
            });
        });

        var div_filter_list_hidden = getCookie('div_filter_list_hidden');
        if (div_filter_list_hidden == 1) {
            $("#filter-list").addClass('block-opt-hidden');
            $(".btn-filter-list").children('.si').removeClass('si-arrow-up');
            $(".btn-filter-list").children('.si').addClass('si-arrow-down');
        }
    });
</script>
