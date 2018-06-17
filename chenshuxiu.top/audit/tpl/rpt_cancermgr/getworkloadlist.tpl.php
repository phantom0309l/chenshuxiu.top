<?php
$pagetitle = "运营有效操作统计";
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
table{
    table-layout:fixed;
}
.searchBar .form-group label {
    font-weight: 500;
    width: 12%;
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
            <form class="form-horizontal" action="/rpt_cancermgr/getworkloadlist" method="get">
                <div class="form-group mt10" >
                    <label class="control-label col-md-2">选择日期:</label>
                    <div class="col-md-3">
                        <input class="form-control" type="text" id="thedate" name="thedate"
                               value="<?= $thedate ?>" placeholder="选择日期">
                    </div>
                    <label class="control-label col-md-2">选择运营人员：</label>
                    <div class="col-md-3">
                        <select name="auditorid" id="auditorid" class="form-control">
                            <?php
                            $auditorids = [
                                '0' => '全部',
                                '10057' => '未国霞',
                                '10048' => '赖雪梅',
                                '10067' => '张绍政',
                                '10097' => '王琳琳',
                                '10112' => '侯锦'
                            ];
                            foreach ($auditorids as $k => $v) {
                                ?>
                                <option value="<?= $k ?>" <?= $auditorid == $k ? 'selected' : '' ?>><?= $v ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="mt10">
                    <input type="submit" class="btn btn-success"  value="筛选"/>
                </div>
            </form>
        </div>

    </section>
    <section class="col-md-12">
        <div>
            <div class="panel panel-default">
                <div class="panel-heading" style="padding:10px 10px">
                    按小时统计
                </div>
                <div class="panel-body" id="hourCount" style="height:600px;">
                </div>
            </div>
        </div>
    </section>
</div>
<div class="clear"></div>
<script>
    var echart_data = <?= $json ?>;

    $(function () {
        App.initHelper('select2');

        //日期范围选择
        laydate.render({
            elem: '#thedate',
        });

        //以下代码为EChart控件代码，
        // 不会可自学（http://echarts.baidu.com/echarts2/doc/start.html）
        // 路径配置
        require.config({
            paths: {
                echarts: "<?= $img_uri ?>/v5/plugin/echarts"
            }
        });

        // 使用
        require(
            [
                'echarts',
                'echarts/chart/pie',
            ],
            function (ec) {
                var myChart = ec.init(document.getElementById('hourCount'));

                var option = {
                    title: {
                        text: '',
                        subtext: '',
                        x: 'center'
                    },
                    tooltip: {
                        trigger: 'item',
                        formatter: "{a} <br/>{b} : {c}  ({d}%)"
                    },
                    legend: {
                        orient: 'vertical',
                        x: 'left'
                    },
                    series: [
                        {
                            name: '有效操作',
                            type: 'pie',
                            radius: '55%',
                            center: ['50%', '60%'],
                            data: [
                            ],
                            itemStyle: {
                                emphasis: {
                                    shadowBlur: 10,
                                    shadowOffsetX: 0,
                                    shadowColor: 'rgba(0, 0, 0, 0.5)'
                                }
                            }
                        }
                    ]
                };

                option.legend.data = Object.keys(echart_data).map(function (key) {
                    return key + '时 (' + echart_data[key] + ')';
                }).sort();
                option.series[0].data = Object.keys(echart_data).map(function (key) {
                    var obj = {};
                    obj.name = key + '时 (' + echart_data[key] + ')';
                    obj.value = echart_data[key];
                    return obj
                });

                myChart.setOption(option);
            }
        );
    });
</script>
<?php
$footerScript = <<<STYLE

STYLE;
?>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
