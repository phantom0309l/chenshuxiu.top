<?php
$pagetitle = "分阶段管理 桑基图";
$cssFiles = []; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = [
    $img_uri . "/v5/plugin/echarts/echarts.js",
]; //填写完整地址
$pageStyle = <<<STYLE
STYLE;
$pageScript = <<<SCRIPT
SCRIPT;
$sideBarMini = false;
include_once $tpl . '/_header.new.tpl.php'; ?>
    <div class="col-md-12">
        <section class="col-md-12">
            <div style="height:200px">
            <section class="col-md-6">
                <div class="panel panel-default" style="height:200px">
                  <div class="panel-heading">
                      <div class="title">
                          <span class="glyphicon glyphicon-edit" aria-hidden="true"></span>
                          <span>需求描述</span>
                      </div>
                  </div>
                  <div class="panel-body">
                    <h4>分阶段管理(桑基图)</h4>
                    <div class="">
                    <p><a href="http://doc.fangcunyisheng.cn/issues/<?= $redmine ?>">详情参见redmine</a></p>
                    </div>
                    <div class="btn-box">
                        <div class="btn btn-default type <?= "all"==$type ? "btn-primary" : "" ?>" data-type="all">全部</div>
                        <div class="btn btn-default type <?= "sunflower"==$type ? "btn-primary" : "" ?>" data-type="sunflower">sunflower</div>
                        <div class="btn btn-default type <?= "notsunflower"==$type ? "btn-primary" : "" ?>" data-type="notsunflower">非sunflower</div>
                    </div>
                  </div>
                </div>
            </section>
            <section class="col-md-6">
                <div class="panel panel-default" style="height:200px">
                  <div class="panel-heading">
                      <div class="title">
                          <span class="glyphicon glyphicon-circle-arrow-down" aria-hidden="true"></span>
                          <span>查询</span>
                      </div>
                  </div>
                  <div class="panel-body">
                      <div class="col-md-12">
                        <section class="col-md-6">
                            <div class="form-group">
                              <label>开始日期</label>
                              <input type="text" class="form-control calendar" id="startdate" value="<?= $startdate ?>">
                            </div>
                        </section>
                        <section class="col-md-6">
                            <div class="form-group">
                              <label>结束日期</label>
                              <input type="text" class="form-control calendar" id="enddate" value="<?= $enddate ?>">
                            </div>
                        </section>
                        <button class="btn btn-primary btn-block outdata">查询</button>
                    </div>
                  </div>
                </div>
            </section>
            </div>
            <div id="echart" class="chart"></div>
        </section>
    </div>

<div class="clear"></div>
<?php

$footerScript = <<<XXX
//以下代码为EChart控件代码，
// 不会可自学（http://echarts.baidu.com/echarts2/doc/start.html）
// 路径配置
require.config({
    paths: {
        echarts: '$img_uri/v5/plugin/echarts'
    }
});

// 使用
require(
    [
        'echarts/echarts.min',
    ],
    function (ec) {
        var getOption = function (data) {

            var option = {
                title: {
                    text: ''
                },
                tooltip: {
                    trigger: 'item',
                    triggerOn: 'mousemove'

                },
                series: [
                    {
                        type: 'sankey',
                        layout: 'none',
                        right: 100,
                        data: data.nodes,
                        links: data.links,
                        itemStyle: {
                            normal: {
                                borderWidth: 1,
                                borderColor: '#aaa'
                            }
                        },
                        lineStyle: {
                            normal: {
                                curveness: 0.5
                            }
                        }
                    }
                ]
            };
            return option;
        };

        var startdate = $("#startdate").val();
        var enddate = $("#enddate").val();
        var type = $(".type.btn-primary").data("type");
        $("#echart").height(800);
        // 为echarts对象加载数据
        $.ajax({
            url: "/rptmgr/patientdrugstatejson?startdate=" + startdate + "&enddate=" + enddate + "&type=" +type,
            dataType: 'json',
            success: function (data) {
                var option = getOption(data);
                setTimeout(function () {
                    var myChart = ec.init(document.getElementById('echart'));
                    myChart.setOption(option);
                }, 0);
            }
        });
    }
);

$(function(){
    $(".outdata").on("click", function(){
        var me = $(this);
        if(me.hasClass('process')){
            return
        }
        me.addClass('process');
        me.text('正在导出，请稍等....');
        var startdate = $("#startdate").val();
        var enddate = $("#enddate").val();
        var type = $(".type.btn-primary").data("type");
        window.location.href = "/rptmgr/patientdrugstate?startdate=" + startdate + "&enddate=" + enddate + "&type=" +type;
    });
    $(".type").on("click", function(){
        var me = $(this);
        me.addClass('btn-primary').siblings().removeClass("btn-primary");
    });
})

XXX;
?>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
