<?php
$pagetitle = '患者流类型分布页';
$cssFiles = []; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = [
    $img_uri . "/v5/plugin/echarts/echarts.js",
    $img_uri . "/v5/common/setMedicineBreakDate.js?v=20171019",
]; //填写完整地址
$pageStyle = <<<STYLE
.not-point{
    color:#666;
}
.white{
    color:white;
}
.btn-rounded{
    margin:3px 0px;
}
STYLE;
$pageScript = <<<SCRIPT
SCRIPT;
$sideBarMini = false;
?>
<?php include_once $tpl . '/_header.new.tpl.php'; ?>
    <div class="col-md-12 contentShell">
        <?php include_once $tpl . "/patientmgr/_menu.tpl.php"; ?>
        <div class="content-div">
            <section class="col-md-12 remove-padding">
            <input type="hidden" id="patientid" value="<?= $patient->id ?>" />
            <div class="col-md-8">
                <!-- 患者交互信息柱状图 -->
                <div id="pipes-chart" style="margin-top: 8px;">
                </div>
            </div>
            </section>
        </div>
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
        'echarts/chart/bar', // 使用柱状图就加载bar模块，按需加载
    ],
    function (ec) {
        var getOption = function (data) {
            var dateList = data.map(function (item) {
                return item['key'];
            });
            var valueList = data.map(function (item) {
                return item['value'];
            });
            var option = {
                title: {
                    text: '患者交互信息分布',
                },
                color: ['#3398DB'],
                tooltip : {
                    trigger: 'axis',
                    axisPointer : {            // 坐标轴指示器，坐标轴触发有效
                        type : 'shadow'        // 默认为直线，可选为：'line' | 'shadow'
                    }
                },
                legend: {
                    data: ['数量']
                },
                toolbox: {
                    show: true,
                    feature: {
                        saveAsImage: {show: true},
                        dataView: {show: true},
                    }
                },
                grid: {
                    x : '30%',
                    left: '3%',
                    right: '4%',
                    bottom: '3%',
                    containLabel: true
                },
                xAxis: {
                    type: 'value',
                    splitLine: {show: false},
                    boundaryGap: [0, 0.01]
                },
                yAxis: {
                    type: 'category',
                    splitLine: {show: false},
                    data: dateList
                },
                series: [
                    {
                        name: '数量',
                        type: 'bar',
                        data: valueList,
                        itemStyle : {
                            normal: {
                                label : {
                                    show: true
                                }
                            }
                        },
                    }
                ]
            };

            return option;
        };

        var patientid = $('#patientid').val();
        $.ajax({
            url: 'getPipesChartJson',
            timeout: 200000,
            type: 'get',
            dataType: 'json',
            data: {patientid: patientid,}
        })
        .done(function(data) {
            $("#pipes-chart").height('600');
            // $("#pipes-chart").width(w);
            var option = getOption(data);
            setTimeout(function () {
                var myChart = ec.init(document.getElementById('pipes-chart'));
                myChart.setOption(option);
            }, 0);
        })
        .fail(function() {
            console.log("error");
        })
    }
);
STYLE;
?>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
