<?php
$pagetitle = "方寸运营后台管理系统";
$cssFiles = []; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = [
    $img_uri . "/v5/plugin/echarts/echarts.js",
    $img_uri . "/v5/common/lillyreport.js",
]; // 填写完整地址
$pageStyle = <<<STYLE
    .title{font-size:18px; color:#337ab7;}
STYLE;
$pageScript = <<<SCRIPT

SCRIPT;
$sideBarMini = false;

include_once $tpl . '/_header.new.tpl.php'; ?>
    <div class="col-md-12">
        <section class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <div class="title">
                        <span class="glyphicon glyphicon-edit" aria-hidden="true"></span>
                        <span>医生入组数/周（第4页）</span>
                    </div>
                </div>
                <div class="panel-body">
                    <div id="chart" style="height: 400px"></div>
                    <div class="col-md-12">
                        <div class="col-md-4 form-group">
                            <label>统计截止日期</label>
                            <input type="text" class="form-control calendar" id="thedate" value='<?= date("Y-m-d", time()) ?>'>
                        </div>
                        <div class="col-md-4 form-group">
                            <label>高</label>
                            <input type="text" class="form-control" id="height" value='400'>
                        </div>
                        <div class="col-md-4 form-group">
                            <label>宽</label>
                            <input type="text" class="form-control" id="width" value='600'>
                        </div>
                        <button class="btn btn-primary btn-block draw" style="width : 120px; margin-left : 20px;">绘制</button>
                    </div>
                </div>
            </div>
        </section>
    </div>
    <div class="clear"></div>
<?php
$footerScript = <<<STYLE
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
    ],
    function (ec) {
        var getOption = function (data) {
            var dateList = data.map(function (item) {
                return item[0];
            });
            var valueList = data.map(function (item) {
                return item[1];
            });
            var option = {
                color: ['#3398DB'],
                // Make gradient line here
                visualMap: [{
                    show: false,
                    type: 'continuous',
                    seriesIndex: 0,
                    min: 0,
                    max: 400
                }, {
                    show: false,
                    type: 'continuous',
                    seriesIndex: 1,
                    dimension: 0,
                    min: 0,
                    max: dateList.length - 1
                }],
                tooltip: {
                    trigger: 'axis'
                },
                toolbox: {
                    show: true,
                    feature: {
                        saveAsImage: {show: true},
                        dataView: {show: true},
                    }
                },
                xAxis: [{
                    data: dateList,
                    splitLine:{
                        show:false
                    },
                }],
                yAxis: [{
                    splitLine: {show: false}
                }, {
                    splitLine: {show: false},
                    gridIndex: 1
                }],
                grid: [{
                    bottom: '60%'
                }, {
                    top: '60%'
                }],
                series: [{
                    type: 'line',
                    showSymbol: false,
                    data: valueList,
                    itemStyle : {
                        normal: {
                            label : {
                                show: true
                            }
                        }
                    },
                }]
            };

            return option;
        };
        lillyreport.init(
            '/lillyReportMgr/page4fordoctorjson',
            function (data) {
                var h = $("#height").val();
                var w = $("#width").val();
                $("#chart").height(h);
                $("#chart").width(w);
                var option = getOption(data);
                setTimeout(function () {
                    var myChart = ec.init(document.getElementById('chart'));
                    myChart.setOption(option);
                }, 0);
            }
        );
    }
);
STYLE;
?>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
