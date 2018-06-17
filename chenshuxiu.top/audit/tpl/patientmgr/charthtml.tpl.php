<div style="margin-top: 10px">
<?php
if ($writers) {
    foreach ($writers as $k => $v) {
        ?>
        <button class="adhdwriter btn btn-default <?= $writer == $v ? "btn-primary" : "" ?> " data-writer='<?= $v ?>' data-patientid="<?= $patientid ?>" style="left: auto; width: 50px; height: 30px"><?= $v ?></button>
        <?php
    }
    ?>
    <button class="adhdwriter btn btn-default <?= $writer == "all" ? "btn-primary" : "" ?> " data-patientid="<?= $patientid ?>" style="left: auto; width: 50px; height: 30px">全部</button>
    <?php
}
?>
</div>
<div id="chart" style="height: 400px"></div>
<?php if(0){ ?>
<div id="connersChart" style="height: 400px"></div>
<?php } ?>
<script>
$('.adhdwriter').on("click", function(){
    var me = $(this);
    var writer = me.data("writer");
    var patientid = me.data("patientid");

    $.ajax({
        "type" : "get",
        "data" : {
            writer : writer,
            patientid : patientid
        },
        "dataType" : "html",
        "url" : "/patientmgr/chartHtml",
        "success" : function(data) {
            $("#chartShell").html(data);
        }
    });
});

//以下代码为EChart控件代码，
// 不会可自学（http://echarts.baidu.com/echarts2/doc/start.html）
// 路径配置
require.config({
    paths: {
        echarts: '<?=$img_uri?>/v5/plugin/echarts'
    }
});

// 使用
require(
    [
        'echarts',
        'echarts/chart/bar', // 使用柱状图就加载bar模块，按需加载
        'echarts/chart/line' // 使用柱状图就加载bar模块，按需加载
    ],
    function (ec) {
        var getOption = function (data) {

            var option = {
                tooltip: {
                    show: true,
                    trigger: 'axis'
                },
                toolbox: {
                    show: true,
                    feature: {
                        mark: {show: false},
                        dataView: {show: false, readOnly: false},
                        magicType: {show: false, type: ['line', 'bar']},
                        restore: {show: false},
                        saveAsImage: {show: true}
                    }
                },
                calculable: true,
                legend: {
                    data: data.legend
                },
                xAxis: [
                    {
                        type: 'category',
                        data: data.created_at,
                        boundaryGap: true,
                        axisLabel: {
                            rotate: -0.01
                        }
                    }
                ],
                yAxis:data.yAxis,
                series: [
                    {
                        name: data.legend[0],
                        type: data.item1_type,
                        barWidth: 30,
                        barCategoryGap:'40%',
                        itemStyle: {
                            normal: {
                                color: "#b8EDFA",

                                label: {
                                    show: true,
                                    textStyle: {
                                        color: "#337ab7"
                                    }
                                }
                            }
                        },
                        data: data.item1
                    },
                    {
                        name: data.legend[1],
                        type: data.item2_type,
                        barWidth: 30,
                        barCategoryGap:'40%',
                        itemStyle: {
                            normal: {
                                color: "#f33",

                                label: {
                                    show: true
                                }
                            }
                        },
                        data: data.item2
                    },
                    {
                        name: '总分',
                        type: data.scores_type,
                        barCategoryGap:'40%',
                        itemStyle: {
                            normal: {
                                label: {
                                    show: true
                                }
                            }
                        },
                        yAxisIndex: data.scores_yAxisIndex,
                        data: data.scores
                    }
                ]
            };

            if( data.item3_type ){
                option.legend.data = data.legend;
                var obj = {
                    name: data.legend[2],
                    type: data.item3_type,
                    barWidth: 30,
                    barCategoryGap:'40%',
                    itemStyle: {
                        normal: {
                            color: "#f90",
                            label: {
                                show: true
                            }
                        }
                    },
                    data: data.item3
                };
                option.series.splice(2,0,obj);
            }
            return option;
        };

        // 为echarts对象加载数据
        $.ajax({
            url: "/patientmgr/get_adhd_dataJson?patientid=<?= $patientid?>&writer=<?= $writer?>",
            dataType: 'json',
            success: function (data) {
                var baseW = 120;
                var l = data.scores.length;
                var w = baseW * l > 650 ? baseW * l : 650;
                $("#chart").width(w);
                var option = getOption(data);
                setTimeout(function () {
                    var myChart = ec.init(document.getElementById('chart'));
                    myChart.setOption(option);
                }, 0);
            }
        });
    }
);
</script>
