<?php
$pagetitle = "肿瘤相关统计";
$cssFiles = [
    $img_uri . "/vendor/oneui/js/plugins/select2/select2.min.css",
    $img_uri . "/vendor/oneui/js/plugins/select2/select2-bootstrap.css",
]; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = [
    $img_uri . "/vendor/oneui/js/plugins/select2/select2.full.min.js",
    $img_uri . "/v5/plugin/echarts/echarts.js",
//    $img_uri . "/v5/plugin/echarts/echarts.js",
]; //填写完整地址
$pageStyle = <<<STYLE
.searchBar .form-group label {
    font-weight: 500;
    width: 9%;
    text-align: left;
}
.padding-left0{
    padding-left: 0px;
}

STYLE;
$pageScript = <<<SCRIPT

SCRIPT;
$sideBarMini = false;

include_once $tpl . '/_header.new.tpl.php'; ?>
    <div class="col-md-12">
        <section class="col-md-12">
            <div class="searchBar">
                <form class="form-horizontal" action="/rpt_cancermgr/list" method="get">
                    <div class="form-group mt10">
                        <label class="control-label col-md-2">医生组</label>
                        <div class="col-md-3">
                            <?= HtmlCtr::getSelectCtrImp(CtrHelper::getDoctorGroupsCtrArray(), "doctorgroupid", $doctorgroupid, 'js-select2 form-control levelBox'); ?>
                        </div>
                        <label class="control-label col-md-2">日期范围</label>
                        <div class="col-md-3">
                            <input class="form-control" type="text" id="date_range" name="date_range"
                                   value="<?= $date_range ?>" placeholder="日期范围">
                        </div>
                    </div>
                    <div class="mt10">
                        <input type="submit" class="btn btn-success" value="组合筛选"/>
                    </div>
                </form>
            </div>
            <div class="content bg-white border-b">
                <div class="row items-push text-uppercase">
                    <div class="col-xs-6 col-sm-3">
                        <div class="font-w700 text-gray-darker animated fadeIn">患者数（已关注：上线+下线）</div>
                        <div class="text-muted animated fadeIn">
                            <small><i class="si si-calculator"></i> 不包含医生导入且未报到患者</small>
                        </div>
                        <a class="h2 font-w300 text-primary animated flipInX"
                           href="javascript:void(0);"><?= $subscribe_patient_cnt ?></a>
                        <small> = <?= $subscribe_patient_online_cnt ?> + <?= $subscribe_patient_offline_cnt ?></small>
                    </div>
                    <div class="col-xs-6 col-sm-3">
                        <div class="font-w700 text-gray-darker animated fadeIn">患者数（未关注）</div>
                        <div class="text-muted animated fadeIn">
                            <small><i class="si si-calculator"></i> 医生导入且未报到患者</small>
                        </div>
                        <a class="h2 font-w300 text-primary animated flipInX"
                           href="javascript:void(0);"><?= $notsubscribe_cnt ?></a>
                    </div>
                    <div class="col-xs-6 col-sm-3">
                        <div class="font-w700 text-gray-darker animated fadeIn">患者数（合并被丢弃）</div>
                        <div class="text-muted animated fadeIn">
                            <small><i class="si si-calculator"></i> 合并丢弃</small>
                        </div>
                        <a class="h2 font-w300 text-primary animated flipInX"
                           href="javascript:void(0);"><?= $merge_rubbish_cnt ?></a>
                    </div>
                </div>
            </div>
            <div class="content bg-white border-b">
                <div class="row items-push text-uppercase">
                    <div class="col-xs-6 col-sm-3">
                        <div class="font-w700 text-gray-darker animated fadeIn">患者数（失访）</div>
                        <div class="text-muted animated fadeIn">
                            <small><i class="si si-calculator"></i> 被标记为失访的患者</small>
                        </div>
                        <a class="h2 font-w300 text-primary animated flipInX" target="_blank"
                           href="/rpt_cancermgr/loselist"><?= $lose_cnt ?></a>
                    </div>
                    <div class="col-xs-6 col-sm-4">
                        <div class="font-w700 text-gray-darker animated fadeIn">失访率</div>
                        <div class="text-muted animated fadeIn">
                            <small><i class="si si-calculator"></i> 失访患者数/已关注患者数,四舍五入保留两位小数</small>
                        </div>
                        <a class="h2 font-w300 text-success animated flipInX" target="_blank"
                           href="/rpt_cancermgr/loselist">
                            <?php if ($subscribe_patient_cnt > 0) {
                                echo round($lose_cnt / $subscribe_patient_cnt * 100, 2) . "%";
                            } else {
                                echo "0%";
                            } ?>
                        </a>
                        <small> = <?= $lose_cnt ?> / <?= $subscribe_patient_cnt ?></small>
                    </div>
                </div>
            </div>
            <div class="content bg-white border-b">
                <div class="row items-push text-uppercase">
                    <div class="col-xs-6 col-sm-3">
                        <div class="font-w700 text-gray-darker animated fadeIn">患者数（取消关注）</div>
                        <div class="text-muted animated fadeIn">
                            <small><i class="si si-calculator"></i> 取消关注的患者</small>
                        </div>
                        <a class="h2 font-w300 text-primary animated flipInX"
                           href="javascript:void(0);"><?= $unsubscribe_cnt ?></a>
                    </div>
                    <div class="col-xs-6 col-sm-4">
                        <div class="font-w700 text-gray-darker animated fadeIn">取消关注率</div>
                        <div class="text-muted animated fadeIn">
                            <small><i class="si si-calculator"></i> 取消关注数/已关注患者数,四舍五入保留两位小数</small>
                        </div>
                        <a class="h2 font-w300 text-success animated flipInX" href="javascript:void(0);">
                            <?php if ($subscribe_patient_cnt > 0) {
                                echo round($unsubscribe_cnt / $subscribe_patient_cnt * 100, 2) . "%";
                            } else {
                                echo "0%";
                            } ?>
                        </a>
                        <small> = <?= $unsubscribe_cnt ?> / <?= $subscribe_patient_cnt ?></small>
                    </div>
                </div>
            </div>
            <div class="content bg-white border-b">
                <div class="row items-push text-uppercase">
                    <div class="col-xs-6 col-sm-3">
                        <div class="font-w700 text-gray-darker animated fadeIn">患者数（死亡）</div>
                        <div class="text-muted animated fadeIn">
                            <small><i class="si si-calculator"></i> 被标记为死亡的患者（已关注的患者）</small>
                        </div>
                        <a class="h2 font-w300 text-primary animated flipInX"
                           href="javascript:void(0);"><?= $dead_cnt ?></a>
                    </div>
                    <div class="col-xs-6 col-sm-4">
                        <div class="font-w700 text-gray-darker animated fadeIn">死亡患者率</div>
                        <div class="text-muted animated fadeIn">
                            <small><i class="si si-calculator"></i> 死亡患者数/已关注患者数,四舍五入保留两位小数</small>
                        </div>
                        <a class="h2 font-w300 text-success animated flipInX" href="javascript:void(0);">
                            <?php if ($subscribe_patient_cnt > 0) {
                                echo round($dead_cnt / $subscribe_patient_cnt * 100, 2) . "%";
                            } else {
                                echo "0%";
                            } ?>
                        </a>
                        <small> = <?= $dead_cnt ?> / <?= $subscribe_patient_cnt ?></small>
                    </div>
                </div>
            </div>
            <div class="content bg-white border-b">
                <div class="row items-push text-uppercase">
                    <div class="col-xs-6 col-sm-3">
                        <div class="font-w700 text-gray-darker animated fadeIn">微信数（未报到）</div>
                        <div class="text-muted animated fadeIn">
                            <small><i class="si si-calculator"></i> 关注过但未报到的微信</small>
                        </div>
                        <a class="h2 font-w300 text-primary animated flipInX"
                           href="javascript:void(0);"><?= $nopatient_cnt ?></a>
                    </div>
                    <div class="col-xs-6 col-sm-3">
                        <div class="font-w700 text-gray-darker animated fadeIn">报到率</div>
                        <div class="text-muted animated fadeIn">
                            <small><i class="si si-calculator"></i> 已关注患者数/(已关注患者数+未报到微信数),四舍五入保留两位小数</small>
                        </div>
                        <a class="h2 font-w300 text-success animated flipInX" href="javascript:void(0);">
                            <?php if ($subscribe_patient_cnt > 0) {
                                echo round($subscribe_patient_cnt / ($subscribe_patient_cnt + $nopatient_cnt) * 100, 2) . "%";
                            } else {
                                echo "0%";
                            } ?>
                        </a>
                        <small> = <?= $subscribe_patient_cnt ?> / (<?= $subscribe_patient_cnt ?> + <?= $nopatient_cnt ?>
                            )
                        </small>
                    </div>
                    <div class="col-xs-6 col-sm-3">
                        <div class="font-w700 text-gray-darker animated fadeIn">微信数（关注中但未报到）</div>
                        <div class="text-muted animated fadeIn">
                            <small><i class="si si-calculator"></i> 关注过但未报到且仍在关注的微信</small>
                        </div>
                        <a class="h2 font-w300 text-primary animated flipInX"
                           href="javascript:void(0);"><?= $nopatient_subscribe_cnt ?></a>
                    </div>
                    <div class="col-xs-6 col-sm-3">
                        <div class="font-w700 text-gray-darker animated fadeIn">微信数（取消关注且未报到）</div>
                        <div class="text-muted animated fadeIn">
                            <small><i class="si si-calculator"></i> 关注过但未报到，又取消关注的微信</small>
                        </div>
                        <a class="h2 font-w300 text-primary animated flipInX"
                           href="javascript:void(0);"><?= $nopatient_unsubscribe_cnt ?></a>
                    </div>
                </div>
            </div>
            <div class="content bg-white border-b">
                <div class="row items-push text-uppercase">
                    <div class="col-xs-10 col-sm-6">
                        <div class="left">
                        <div class="font-w700 text-gray-darker animated fadeIn">运营任务</div>
                        <div class="text-muted animated fadeIn">
                            <small><i class="si si-calculator"></i>创建和完成数量</small>
                        </div>
                        </div>
                            <div class="right" style="float: right;margin-bottom: 20px">
                                <a href="/rpt_cancermgr/getworkloadlist">
                                    <input class="btn btn-success" type="button" value="按小时汇总">
                                </a>
                        </div>
                        <div>
                            <table class="table">
                                <tr>
                                    <td>运营</td>
                                    <td>创建</td>
                                    <td>关闭</td>
                                    <td>进行中</td>
                                    <td>挂起</td>
                                    <td>有效操作</td>
                                </tr>
                                <?php
                                foreach ($optask_list as $item) {
                                    ?>
                                    <tr>
                                        <td><?= $item['auditor'] ?></td>
                                        <td><?= $item['create'] ?></td>
                                        <td><?= $item['status1'] ?></td>
                                        <td><?= $item['status0'] ?></td>
                                        <td><?= $item['status2'] ?></td>
                                        <td><?php echo $item['status1'] + $optask_kv_effect_list["{$item['auditor']}"]; ?></td>
                                    </tr>
                                    <?php
                                }
                                ?>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="content bg-white border-b">
                <div class="row items-push text-uppercase">
                    <div class="col-xs-10 col-sm-6">
                        <div class="font-w700 text-gray-darker animated fadeIn">运营【定期随访】任务阶段统计</div>
                        <div class="text-muted animated fadeIn">
                            <small><i class="si si-calculator"></i>统计(从2018-04-18开始)</small>
                        </div>
                        <div>

                            <table class="table">
                                <tr>
                                    <td>总计</td>
                                    <td>无阶段</td>
                                    <td>其他</td>
                                    <td>化疗</td>
                                    <td>靶向</td>
                                    <td>手术</td>
                                </tr>
                                <?php
                                foreach ($optask_regular_follow_list as $item) {
                                    ?>
                                    <tr>
                                        <td><?= $item['all'] ?></td>
                                        <td><?= $item['all'] > 0 ? round($item['无阶段'] / $item['all'] * 100, 2) . "%" : "0%"; ?></td>
                                        <td><?= $item['all'] > 0 ? round($item['其他'] / $item['all'] * 100, 2) . "%" : "0%"; ?></td>
                                        <td><?= $item['all'] > 0 ? round($item['化疗'] / $item['all'] * 100, 2) . "%" : "0%"; ?></td>
                                        <td><?= $item['all'] > 0 ? round($item['靶向'] / $item['all'] * 100, 2) . "%" : "0%"; ?></td>
                                        <td><?= $item['all'] > 0 ? round($item['手术'] / $item['all'] * 100, 2) . "%" : "0%"; ?></td>
                                    </tr>
                                    <?php
                                }
                                ?>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="content bg-white border-b">
                <div class="row items-push text-uppercase">
                    <div class="col-xs-12 col-sm-12">
                        <div class="font-w700 text-gray-darker animated fadeIn">运营任务完成率趋势图</div>
                        <div class="text-muted animated fadeIn">
                            <small><i class="si si-calculator"></i>趋势图</small>
                        </div>
                        <div>
                            <div id="optask_clone_echart" style="height: 400px; width: 80%"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="content bg-white border-b">
                <div class="row items-push text-uppercase">
                    <div class="col-xs-12 col-sm-10">
                        <div class="font-w700 text-gray-darker animated fadeIn">运营任务完成率统计</div>
                        <div class="text-muted animated fadeIn">
                            <small><i class="si si-calculator"></i>统计</small>
                        </div>
                        <div>

                            <table class="table">
                                <tr>
                                    <td width="200px">任务类型</td>
                                    <td width="100px">任务总数</td>
                                    <td width="100px">进行中数</td>
                                    <td width="100px">挂起数</td>
                                    <td width="100px">关闭数</td>
                                    <td width="300px">完成率(关闭)</td>
                                    <td width="300px">未完成率(关闭)</td>
                                    <td width="300px">其他节点率(关闭)</td>
                                    <td width="300px">空节点率(关闭)</td>
                                </tr>
                                <?php
                                foreach ($optask_tpl_list as $item) {
                                    $optasktpl = OpTaskTpl::getById($item['optasktplid']);
                                    ?>
                                    <tr>
                                        <td><?= $optasktpl->title ?></td>
                                        <td><?= $item['all_cnt'] ?></td>
                                        <td><?= $item['status_0_cnt'] ?></td>
                                        <td><?= $item['status_2_cnt'] ?></td>
                                        <td><?= $item['status_1_cnt'] ?></td>
                                        <td>
                                            <a class="h2 font-w300 text-success animated flipInX" href="javascript:void(0);">
                                                <?php
                                                if ($item['finish_unfinish_cnt'] > 0) {
                                                    echo round($item['status_1_finish_cnt'] / $item['finish_unfinish_cnt'] * 100, 2) . "%";
                                                } else {
                                                    echo "0";
                                                }
                                                ?>
                                            </a>
                                            <small> = <?= $item['status_1_finish_cnt'] ?> / <?= $item['finish_unfinish_cnt'] ?>
                                            </small>
                                        </td>
                                        <td>
                                            <a class="h2 font-w300 text-danger animated flipInX" href="javascript:void(0);">
                                                <?php
                                                if ($item['finish_unfinish_cnt'] > 0) {
                                                    echo round($item['status_1_unfinish_cnt'] / $item['finish_unfinish_cnt'] * 100, 2) . "%";
                                                } else {
                                                    echo "0";
                                                }
                                                ?>
                                            </a>
                                            <small> = <?= $item['status_1_unfinish_cnt'] ?> / <?= $item['finish_unfinish_cnt'] ?>
                                            </small>
                                        </td>
                                        <td>
                                            <a class="h2 font-w300 text-warning animated flipInX" href="javascript:void(0);">
                                                <?php
                                                if ($item['status_1_cnt'] > 0) {
                                                    echo round($item['status_1_opnode_other_cnt'] / $item['status_1_cnt'] * 100, 2) . "%";
                                                } else {
                                                    echo "0";
                                                }
                                                ?>
                                            </a>
                                            <small> = <?= $item['status_1_opnode_other_cnt'] ?> / <?= $item['status_1_cnt'] ?>
                                            </small>
                                        </td>
                                        <td>
                                            <a class="h2 font-w300 text-info animated flipInX" href="javascript:void(0);">
                                                <?php
                                                if ($item['status_1_cnt'] > 0) {
                                                    echo round($item['status_1_opnode_0_cnt'] / $item['status_1_cnt'] * 100, 2) . "%";
                                                } else {
                                                    echo "0";
                                                }
                                                ?>
                                            </a>
                                            <small> = <?= $item['status_1_opnode_0_cnt'] ?> / <?= $item['status_1_cnt'] ?>
                                            </small>
                                        </td>
                                    </tr>
                                    <?php
                                }
                                ?>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
    <div class="clear"></div>
<?php
$footerScript = <<<STYLE
$(function() {
    App.initHelper('select2');
    
    //日期范围选择
    laydate.render({ 
        elem: '#date_range',
        range: '至' //或 range: '~' 来自定义分割字符
    });
    
    //以下代码为EChart控件代码，
    // 不会可自学（http://echarts.baidu.com/echarts2/doc/start.html）
    // 路径配置
    require.config({
        paths: {
            echarts: "$img_uri/v5/plugin/echarts"
        }
    });
    
    
    // 使用
    require(
        [
            'echarts',
            'echarts/chart/line', // 使用柱状图就加载bar模块，按需加载
            'echarts/chart/bar'
        ],
        function (ec) {
            // 基于准备好的dom，初始化echarts图表
            var myChart = ec.init(document.getElementById('optask_clone_echart'));
            
            var date_range = $("input[name='date_range']").val();
            console.log(date_range);
            
            $.ajax({
                url : '/rpt_cancermgr/optaskdatajson',
                type : 'get',
                dataType : 'json',
                data : {
                    date_range : date_range
                },
                success : function (data) {
                    console.log(data);
                    
                    var option = {
                        tooltip : {
                            trigger: 'axis'
                        },
                        legend: {
                            data:data.legend
                        },
                        toolbox: {
                            show : false,
                            feature : {
                                mark : {show: true},
                                dataView : {show: true, readOnly: false},
                                magicType : {show: true, type: ['line']},
                                restore : {show: true},
                                saveAsImage : {show: true}
                            }
                        },
                        calculable : true,
                        xAxis : [
                            {
                                type : 'category',
                                boundaryGap : false,
                                data : data.xAxis
                            }
                        ],
                        yAxis : [
                            {
                                type : 'value'
                            }
                        ],
                        series : data.series
                    };
                    
                    myChart.setOption(option); 
                }
            });
        }
    );
});


STYLE;
?>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>