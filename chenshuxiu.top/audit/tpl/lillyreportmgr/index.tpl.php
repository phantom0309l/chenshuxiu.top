<?php
$pagetitle = "报表统计首页";
$cssFiles = []; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = []; //填写完整地址
$pageStyle = <<<STYLE

STYLE;
$pageScript = <<<SCRIPT

SCRIPT;
$sideBarMini = false;

include_once $tpl . '/_header.new.tpl.php'; ?>
    <div class="col-md-12">
        <section class="col-md-12">
            <div>
                <p>
                    <a class="btn btn-default" href="/lillyreportmgr/page4fordoctor">医生入组数/周（第4页）</a>
                    <a class="btn btn-default" href="/lillyreportmgr/page4forpatient">患者入组数/周（第4页）</a>
                </p>
                <p>
                    <a class="btn btn-default" href="/lillyreportmgr/page5fordoctor">入组医生总数（第5页）</a>
                    <a class="btn btn-default" href="/lillyreportmgr/page5forpatient">入组患者总数（第5页）</a>
                </p>
                <p>
                    <a class="btn btn-default" href="/lillyreportmgr/page6forpie">新老患者比例（第6页）</a>
                    <a class="btn btn-default" href="/lillyreportmgr/page6forbar">入组新老患者分布（第6页）</a>
                </p>
                <p>
                    <a class="btn btn-default" href="/lillyreportmgr/page7forpie">医生活跃性分析（第7页）</a>
                    <a class="btn btn-default" href="/lillyreportmgr/page7forbar">活跃医生入组患者数分析（第7页）</a>
                </p>
                <p>
                    <a class="btn btn-default" href="/lillyreportmgr/page9">患者完成自评量表（第9页）</a>
                </p>
                <p>
                    <a class="btn btn-default" href="/lillyreportmgr/page10">患者完成行为训练（第10页）</a>
                </p>
                <p>
                    <a class="btn btn-default" href="/lillyreportmgr/page11">患者通过微信端主动提问（第11页）</a>
                </p>
                <p>
                    <a class="btn btn-default" href="/lillyreportmgr/page13foroutradio">出组率（第13页）</a>
                    <a class="btn btn-default" href="/lillyreportmgr/page13foroutradiodetail">出组率明细（第13页）</a>
                </p>
                <p>
                    <a class="btn btn-default" href="/lillyreportmgr/page14">每月依从性（第14页）</a>
                </p>
                <p>
                    <a class="btn btn-default" href="/lillyreportmgr/page19">AE 上报率（第19页）</a>
                    <a class="btn btn-default" href="/lillyreportmgr/page19detail">AE 上报率明细（第19页）</a>
                </p>
                <p>
                    <a class="btn btn-default" href="/lillyreportmgr/responsestatistic">响应速度和响应率</a>
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
