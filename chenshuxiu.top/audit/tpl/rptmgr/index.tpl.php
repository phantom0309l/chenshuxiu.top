<?php
$pagetitle = "报表统计首页";
$cssFiles = []; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = []; //填写完整地址
$pageStyle = <<<STYLE
.box{
    border-left : solid 1px #ccc;
    text-align:center;
    margin: 0 auto 10px;
}
STYLE;
$pageScript = <<<SCRIPT

SCRIPT;
$sideBarMini = false;

include_once $tpl . '/_header.new.tpl.php'; ?>
    <div class="col-md-12">
        <section class="col-md-12">
            <div class="col-md-4 box">
                <p>平台统计</p>
                <p>
                    <a class="btn btn-default" href="/rptmgr/adhd_kpi">持续服药率统计</a>
                </p>
                <p>
                    <a class="btn btn-default" href="/rptmgr/huanjieRatio">报到30-180天患者缓解率</a>
                </p>
                <!-- <p>
                    <a class="btn btn-default" href="/rptmgr/patientdrugstate">分阶段管理(桑基图)</a>
                </p> -->
                <p>
                    <a class="btn btn-default" href="/rptmgr/pipelevel">消息任务平均处理时长统计</a>
                </p>
                <p>
                    <a class="btn btn-default" href="/rptmgr/optaskdataforadhd">限时回复数据监控</a>
                </p>
                <p>
                    <a class="btn btn-default" href="/rptmgr/shoporder">复购率(下单明细)</a>
                </p>
                <p>
                    <a class="btn btn-default" href="/rptmgr/patientedubyadhdtag">患教文章被查看次数统计</a>
                </p>
            </div>
            <div class="col-md-4 box">
                <p>sunflower项目统计</p>
                <p>
                    <a class="btn btn-default" href="/rptmgr/sunflowerforpatient">入组患者维度统计</a>
                </p>
                <p>
                    <a class="btn btn-default" href="/rptmgr/sunflowerformarketer">礼来市场维度统计</a>
                </p>
                <p>
                    <a class="btn btn-default" href="/rptmgr/sunflowerfordoctor">合作医生维度统计</a>
                </p>
                <p>
                    <a class="btn btn-default" href="/rptmgr/stopdrugdata">礼来项目停药患者统计</a>
                </p>
                <p>
                    <a class="btn btn-default" href="/rptmgr/drugradio?week=4">4周遵医嘱服药率</a>
                </p>
                <p>
                    <a class="btn btn-default" href="/rptmgr/drugradio?week=12">12周遵医嘱服药率</a>
                </p>
                <p>
                    <a class="btn btn-default" href="/rptmgr/drugradio?week=24">24周遵医嘱服药率</a>
                </p>
            </div>
            <div class="col-md-4 box">
                <p>任务节点统计</p>
                <p>
                    <a class="btn btn-default" href="/rptmgr/opnodelogdata">任务节点统计</a>
                </p>
            </div>
        </section>
    </div>
    <div class="clear"></div>
<?php
$footerScript = <<<STYLE

STYLE;
?>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
