<?php
$pagetitle = "方寸运营后台管理系统";
$cssFiles = []; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = [
    $img_uri . "/v5/plugin/echarts/echarts.js",
]; //填写完整地址
$pageStyle = <<<STYLE
    .title{font-size:18px; color:#337ab7;}
    .pie{height:400px;width:450px;display:block;}
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
                      <span>需求描述</span>
                  </div>
              </div>
              <div class="panel-body">
                <h4>消息任务平均处理时长统计(支持疾病筛选)</h4>
              <p><a href="http://doc.fangcunyisheng.cn/issues/<?= $redmine ?>">详情参见redmine</a></p>
              </div>
            </div>

            <div class="panel panel-default">
                <div class="panel-heading">
                    <div class="title">
                        <span class="glyphicon glyphicon-edit" aria-hidden="true"></span>
                        <span>数据</span>
                    </div>
                </div>
                <div class="panel-body">
                <div class="col-md-10 datashow">
                    <div id="radio-data">
                        <?php if(isset($data["pipelevel_data"])){ ?>
                            <?= $data["pipelevel_data"]["is_urgent_cnt"] ?>条患者消息判别为紧急。<br/>
                            <?= $data["pipelevel_data"]["not_is_urgent_cnt"] ?>条患者消息判别为不紧急。<br/>
                            <?= $data["pipelevel_data"]["is_urgent_fix_cnt"] ?>条患者消息，运营由紧急反标为不紧急。<br/>
                            <?= $data["pipelevel_data"]["not_is_urgent_fix_cnt"] ?>条患者消息，运营由不紧急反标为紧急。<br/><br/>
                            紧急召回率 = <?= $data["pipelevel_data"]["recall"] ?>%<br/>
                            紧急精确率 = <?= $data["pipelevel_data"]["precision"] ?>%<br/>
                            算法的准确率 = <?= $data["pipelevel_data"]["accuracy"] ?>%<br/><br/><br/>
                        <?php } ?>
                    </div>
                    <?php if($mydisease instanceof Disease && 1 == $mydisease->id){ ?>
                    <span class="red">紧急：红色</span>   <span class="green">不紧急：绿色</span></br>
                    <?php } ?>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <td>类别 \ 时间</td>
                                <td class="bg-primary-lighter">工作日[10点, 19:30点)</td>
                                <td>工作日[0点, 10点)</td>
                                <td>工作日[19:30点, 24点)</td>
                                <td>工作日</td>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>创建时间～首次回复</br>(只统计当天回复的任务)</br>平均处理时长</td>
                                <?php foreach ($data["optask_data"]['firstreplytime'] as $k => $v) { ?>
                                    <td class="<?= 'worktime' == $k ? 'bg-primary-lighter' : '' ?>">
                                        <?php if($mydisease instanceof Disease && 1 == $mydisease->id){ ?>
                                        <a href="/rptmgr/getOptaskDetail?fromdate=<?=$fromdate?>&todate=<?=$todate?>&type_str=firstreplytime&worktime_str=<?=$k?>&level_str=urgent">
                                        <span class='red' data-toggle="tooltip" title="" data-original-title="点击下载明细">
                                            <?= $v["urgent"] ?>
                                        </span>
                                        </a>
                                        ／
                                        <?php } ?>
                                        <a href="/rptmgr/getOptaskDetail?fromdate=<?=$fromdate?>&todate=<?=$todate?>&type_str=firstreplytime&worktime_str=<?=$k?>&level_str=not_urgent">
                                        <span class='green' data-toggle="tooltip" title="" data-original-title="点击下载明细">
                                            <?= $v["not_urgent"] ?>
                                        </span>
                                        </a>
                                        小时
                                    </td>
                                <?php } ?>
                            </tr>
                            <tr>
                                <td>创建时间～任务关闭</br>平均处理时长</td>
                                <?php foreach ($data["optask_data"]['donetime'] as $k => $v) { ?>
                                    <td class="<?= 'worktime' == $k ? 'bg-primary-lighter' : '' ?>">
                                        <?php if($mydisease instanceof Disease && 1 == $mydisease->id){ ?>
                                        <a href="/rptmgr/getOptaskDetail?fromdate=<?=$fromdate?>&todate=<?=$todate?>&type_str=donetime&worktime_str=<?=$k?>&level_str=urgent">
                                        <span class='red' data-toggle="tooltip" title="" data-original-title="点击下载明细">
                                            <?= $v["urgent"] ?>
                                        </span>
                                        </a>
                                        ／
                                        <?php } ?>
                                        <a href="/rptmgr/getOptaskDetail?fromdate=<?=$fromdate?>&todate=<?=$todate?>&type_str=donetime&worktime_str=<?=$k?>&level_str=not_urgent">
                                        <span class='green' data-toggle="tooltip" title="" data-original-title="点击下载明细">
                                            <?= $v["not_urgent"] ?>
                                        </span>
                                        </a>
                                        小时
                                    </td>
                                <?php } ?>
                            </tr>
                            <tr>
                                <td>当天未回复数</td>
                                <?php foreach ($data["optask_data"]['notdonecnt_data'] as $k => $v) { ?>
                                    <td class="<?= 'worktime' == $k ? 'bg-primary-lighter' : '' ?>">
                                        <?php if($mydisease instanceof Disease && 1 == $mydisease->id){ ?>
                                        <a href="/rptmgr/getOptaskDetail?fromdate=<?=$fromdate?>&todate=<?=$todate?>&type_str=notdonecnt_data&worktime_str=<?=$k?>&level_str=urgent">
                                        <span class='red' data-toggle="tooltip" title="" data-original-title="点击下载明细">
                                            <?= $v["urgent"] ?>
                                        </span>
                                        </a>
                                        ／
                                        <?php } ?>
                                        <a href="/rptmgr/getOptaskDetail?fromdate=<?=$fromdate?>&todate=<?=$todate?>&type_str=notdonecnt_data&worktime_str=<?=$k?>&level_str=not_urgent">
                                        <span class='green' data-toggle="tooltip" title="" data-original-title="点击下载明细">
                                            <?= $v["not_urgent"] ?>
                                        </span>
                                        </a>
                                        条
                                    </td>
                                <?php } ?>
                            </tr>
                            <tr>
                                <td>未关闭数</td>
                                <?php foreach ($data["optask_data"]['notreplycnt_data'] as $k => $v) { ?>
                                    <td class="<?= 'worktime' == $k ? 'bg-primary-lighter' : '' ?>">
                                        <?php if($mydisease instanceof Disease && 1 == $mydisease->id){ ?>
                                        <a href="/rptmgr/getOptaskDetail?fromdate=<?=$fromdate?>&todate=<?=$todate?>&type_str=notreplycnt_data&worktime_str=<?=$k?>&level_str=urgent">
                                        <span class='red' data-toggle="tooltip" title="" data-original-title="点击下载明细">
                                            <?= $v["urgent"] ?>
                                        </span>
                                        </a>
                                        ／
                                        <?php } ?>
                                        <a href="/rptmgr/getOptaskDetail?fromdate=<?=$fromdate?>&todate=<?=$todate?>&type_str=notreplycnt_data&worktime_str=<?=$k?>&level_str=not_urgent">
                                        <span class='green' data-toggle="tooltip" title="" data-original-title="点击下载明细">
                                            <?= $v["not_urgent"] ?>
                                        </span>
                                        </a>
                                        条
                                    </td>
                                <?php } ?>
                            </tr>
                        <tbody>
                    </table>
                    <?php $urge_pie = json_encode($data["optask_data"]["urge_pie"]); ?>
                    <?php $noturge_pie = json_encode($data["optask_data"]["noturge_pie"]); ?>
                    <?php if(1 == $mydisease->id){ ?>
                    <div id="chart-urge" class="col-md-8 pie"></div>
                    <div id="chart-noturge" class="col-md-8 pie"></div>
                    <?php } ?>
                </div>
                </div>
            </div>

            <div class="panel panel-default">
              <div class="panel-heading">
                  <div class="title">
                      <span class="glyphicon glyphicon-circle-arrow-down" aria-hidden="true"></span>
                      <span>查询数据(按照消息任务生成时间筛选左闭右闭)</span>
                  </div>
              </div>
              <div class="panel-body">
                <div class="col-md-4">
                    <form action="/rptmgr/pipelevel" method="get" id="pipelevelForm">
                    <div class="form-group">
                        <label>开始日期</label>
                        <input type="text" class="form-control calendar" name="fromdate" id="fromdate" value="<?= $fromdate ?>">
                    </div>
                    <div class="form-group">
                        <label>截止日期</label>
                        <input type="text" class="form-control calendar" name="todate" id="todate" value="<?= $todate ?>">
                    </div>
                    <button type="submit" class="btn btn-primary btn-block outdata">查询</button>
                    </form>
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
        'echarts/chart/pie', // 使用柱状图就加载bar模块，按需加载
    ],
    function (ec) {
        var getOption = function (title, data) {
            var option = {
                title : {
                    text: title,
                    x:'center'
                },
                tooltip: {
                    trigger: 'item',
                    formatter: "{b}<br/>{c} ({d}%)"
                },
                toolbox: {
                    show: true,
                    feature: {
                        // saveAsImage: {show: true},
                        // dataView: {show: true},
                    }
                },
                legend: {
                    orient: 'vertical',
                    left: 'left',
                    data: [],
                },
                series: [
                    {
                       type: 'pie',
                       radius : '55%',
                       center: ['50%', '60%'],
                       data: data,
                       itemStyle: {
                           emphasis: {
                               shadowBlur: 10,
                               shadowOffsetX: 0,
                               shadowColor: 'rgba(0, 0, 0, 0.5)'
                           },
                           normal : {
                               label : {
                                   show:true,
                                   textStyle : {
                                       fontFamily: 'Microsoft YaHei',
                                       fontSize : '12',
                                       fontWeight : 'bold'
                                   },
                                   formatter: "{b}"//在区域名字后显示值及其百分比
                               },
                               labelLine:{show:true}
                           },
                       }
                   }
                ]
            };

            return option;
        };

        var option_urge = getOption("任务首次回复时间分布(紧急)", $urge_pie);
        setTimeout(function () {
            var urgenode = document.getElementById('chart-urge');
            if(urgenode){
                var myChart_urge = ec.init(urgenode);
                myChart_urge.setOption(option_urge);
            }
        }, 0);

        var option_noturge = getOption("任务首次回复时间分布(不紧急)", $noturge_pie);
        setTimeout(function () {
            var noturgenode = document.getElementById('chart-noturge');
            if(noturgenode){
                var myChart_noturge = ec.init(noturgenode);
                myChart_noturge.setOption(option_noturge);
            }
        }, 0);
    }
);
STYLE;
?>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
