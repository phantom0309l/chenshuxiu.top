<?php
$pagetitle = "疑似无效患者列表统计";
$cssFiles = []; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = []; //填写完整地址
$pageStyle = <<<STYLE
        div.chart {
            height: 300px
        }

        div.detail {
            height: 40px;
        }

        a.showmore {
            float: right;
        }
STYLE;
$pageScript = <<<SCRIPT
SCRIPT;
$sideBarMini = false;
?>
<?php include_once $tpl . '/_header.new.tpl.php'; ?>

<div class="col-md-12">
    <section class="col-md-12">
        <form action="/patientmgr/listfordoubt" method="get" class="pr">
            <div class="mt12 searchBar">
                <label>按患者报到时间: </label>
                从
                <input type="text" class="calendar" style="width: 100px" name="fromdate" value="<?= $fromdate ?>"/>
                到
                <input type="text" class="calendar" style="width: 100px" name="todate" value="<?= $todate ?>"/>
                (左闭右闭)
                <br>
                <label>按市场筛选: </label>
                <?php echo HtmlCtr::getSelectCtrImp(CtrHelper::getMarketAuditorCtrArray(true),'auditorid_market',$auditorid_market); ?>
                <label>按医生筛选: </label>
                <?php echo HtmlCtr::getSelectCtrImp(CtrHelper::getDoctorCtrArray(1),'doctorid',$doctorid); ?>
                <input type="submit" class="btn btn-success" value="查看">
            </div>
        </form>
        <div class="table-responsive">
            <table class="table table-bordered">
            <tbody>
            <tr>
                <td>市场负责人</td>
                <?php foreach($arr[0] as $k=>$v ){ ?>
                    <td><?= $v ?></td>
                <?php } ?>
                <td>总计</td>
            </tr>
            <tr>
                <td>患者数量</td>
                <?php
                $count = 0;
                foreach($arr[1] as $k=>$v ){ ?>
                    <td><?= $v ?></td>
                <?php
                    $count += $v;
                } ?>
                <td><?= $count ?></td>
            </tr>
            </tbody>
        </table>
        </div>
        <?php
        $pagetitle = "疑似无效患者列表列表";
        include $tpl . "/_pagetitle.php";
        ?>
        <div class="table-responsive">
            <table class="table table-bordered">
            <thead>
            <tr>
                <td>报到时间</td>
                <td>关注时间</td>
                <td>患者姓名</td>
                <td>微信名</td>
                <td>第一次扫码医生</td>
                <td>现在所属医生</td>
                <td>市场负责人</td>
                <td>操作</td>
            </tr>
            </thead>
            <tbody>
            <?php
            foreach ($patients as $patient) {
                    ?>
                    <tr>
                        <td><?= $patient->createtime ?></td>
                        <td><?= $patient->getMasterWxUser()->createtime ?></td>
                        <td><?= $patient->getMaskName(); ?></td>
                        <td><?= $patient->getMasterWxUser()->nickname ?></td>
                        <td><?= $patient->doctor->name ?></td>
                        <td><?= $patient->first_doctor->name ?></td>
                        <td><?= $patient->doctor->marketauditor->name ?></td>
                        <td>
                            <a href="/patientmgr/list?keyword=<?= $patient->name ?>" target="_blank">查看</a>
                        </td>
                    </tr>
                <?php
            } ?>
            <tr>
                <td colspan=10>
                    <?php include $dtpl . "/pagelink.ctr.php"; ?>
                </td>
            </tr>
            </tbody>
        </table>
        </div>
    </section>
</div>
<div class="clear"></div>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
