<?php
$pagetitle = "分享用户列表";
$cssFiles = []; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = [
    "{$img_uri}/v5/plugin/echarts/echarts.js",
]; //填写完整地址
$pageStyle = <<<STYLE
    .chartShell {
        width: 100%;
        overflow: auto;
    }
STYLE;
$pageScript = <<<SCRIPT

SCRIPT;
$sideBarMini = false;

include_once $tpl . '/_header.new.tpl.php'; ?>
<div class="col-md-12">
    <section class="col-md-12 content-item">
        <p>历史周活跃率</p>
        <div class="chartShell">
            <div id="activityhistory" style="height: 250px; width: auto;"></div>
        </div>
        <p>家长进度比例</p>
        <div class="chartShell">
            <div id="weekpartition" style="height: 400px; width: auto;"></div>
        </div>
        <p>历史用户增加</p>
        <div class="chartShell">
            <div id="addhistory" style="height: 250px; width: auto;"></div>
        </div>
    </section>
</div>
<div class="clear"></div>
<?php
$footerScript = <<<STYLE
    $(function () {
        $(".courseuserref-one").on("click", function (e) {
            var node = $(this);
            var courseuserrefid = node.data("courseuserrefid");
            $.ajax({
                "type": "get",
                "data": {courseuserrefid: courseuserrefid},
                "dataType": "html",
                "url": "/fbt/hwkhtml",
                "success": function (data) {
                    $(".content-right").html(data);
                    $(".content-right").show();
                }
            });
        });

        $(document).on("click", '.answer-one', function () {
            var me = $(this);
            var answerid = me.data("answerid");
            $.ajax({
                "type": "get",
                "data": {answerid: answerid},
                "dataType": "html",
                "url": "/fbt/answer2commentofcourseJson",
                "success": function (data) {
                    alert(data);
                }
            });
        });
    });
    // 路径配置
    require.config({
        paths: {
            echarts: '{$img_uri}/v5/plugin/echarts'
        }
    });

    // 使用
    require(
        [
            'echarts',
            'echarts/chart/pie', // 使用柱状图就加载bar模块，按需加载
            'echarts/chart/bar' // 使用柱状图就加载bar模块，按需加载
        ],
        function (ec) {
            // 基于准备好的dom，初始化echarts图表

            // 历史周活跃率
            var ahChart = ec.init(document.getElementById('activityhistory'));

            var ahChartoption = {
                tooltip: {
                    show: true
                },
                legend: {
                    data: ['历史周活跃率']
                },
                xAxis: [
                    {
                        type: 'category',
                        axisTick: {
                            show: true
                        },
                        data: {$activityhistory['week']}
                    }
                ],
                yAxis: [
                    {
                        type: 'value',
                        splitNumber: 1,

                    }
                ],
                series: [
                    {
                        "name": "历史周活跃率",
                        "type": "bar",
                        itemStyle: {
                            normal: {
                                label: {
                                    show: true,
                                    position: 'top'
                                }
                            }
                        },
                        "data": {$activityhistory['value']}
                    }
                ]
            };

            ahChart.setOption(ahChartoption);

            // 历史用户增加
            var ahChart = ec.init(document.getElementById('addhistory'));

            var ahChartoption = {
                tooltip: {
                    show: true
                },
                legend: {
                    data: ['历史用户增加']
                },
                xAxis: [
                    {
                        type: 'category',
                        axisTick: {
                            show: true,
                        },

                        data: {$addhistory['week']}
                    }
                ],
                yAxis: [
                    {
                        type: 'value',
                        splitNumber: 1,
                    }
                ],
                series: [
                    {
                        "name": "历史用户增加",
                        "type": "bar",
                        itemStyle: {
                            normal: {
                                label: {
                                    show: true,
                                    position: 'top',
                                }
                            }
                        },
                        "data": {$addhistory['value']}
                    }
                ]
            };

            ahChart.setOption(ahChartoption);

            // 家长进度比例
            var ahChart = ec.init(document.getElementById('weekpartition'));

            var ahChartoption = {
                tooltip: {
                    trigger: 'item',
                    formatter: "{a} <br/>{b} : {c} ({d}%)"
                },
                legend: {
                    orient: 'vertical',
                    x: 'left',
                    data: ['1', '2', '3', '4', '5', '6', '7', '8', '9', '10']
                },

                calculable: true,
                series: [
                    {
                        "name": "家长进度比例",
                        "type": "pie",
                        radius: '55%',
                        center: ['50%', '60%'],
                        "data": {$weekpartition}
                    }
                ]
            };

            ahChart.setOption(ahChartoption);
        }
    )
STYLE;
?>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
