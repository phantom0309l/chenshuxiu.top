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
                        <span>患者主动提问的活跃度（第11页）</span>
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
        'echarts/chart/line', // 使用柱状图就加载line模块，按需加载
        'echarts/chart/bar', // 使用柱状图就加载bar模块，按需加载
    ],
    function (ec) {
        var getOption = function (data) {
            var dateList = data.map(function (item) {
                return item[0];
            });
            var value1List = data.map(function (item) {
                return item[1];
            });
            var value2List = data.map(function (item) {
                return item[2];
            });
            var option = {
                    tooltip: {
                    trigger: 'axis',
                    axisPointer: {
                        type: 'cross',
                        crossStyle: {
                            color: '#999'
                        }
                    }
                },
                toolbox: {
                    show: true,
                    feature: {
                        saveAsImage: {show: true},
                        dataView: {show: true},
                    }
                },
                legend: {
                    data:['患者总数','微信提问率']
                },
                xAxis: [
                    {
                        type: 'category',
                        data: dateList,
                        axisPointer: {
                            type: 'shadow'
                        },
                        splitLine: {show: false},
                        axisTick: {
                            alignWithLabel: true
                        }
                    }
                ],
                yAxis: [
                    {
                        type: 'value',
                        name: '数量',
                        min: 0,
                        // max: 250,
                        interval: 50,
                        axisLabel: {
                            formatter: '{value}'
                        }
                    },
                    {
                        type: 'value',
                        name: '比值',
                        min: 0,
                        // max: 25,
                        interval: 5,
                        axisLabel: {
                            formatter: '{value} %'
                        }
                    }
                ],
                series: [
                    {
                        name:'患者总数',
                        type:'bar',
                        data:value1List,
                        itemStyle : {
                            normal: {
                                label : {
                                    show: true
                                }
                            }
                        },
                    },
                    {
                        name:'微信提问率',
                        type:'line',
                        yAxisIndex: 1,
                        data:value2List,
                        itemStyle : {
                            normal: {
                                label : {
                                    show: true,
                                    formatter: '{c} %',
                                }
                            }
                        },
                    },
                ]
            };

            return option;
        };
        lillyreport.init(
            '/lillyReportMgr/page11json',
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
