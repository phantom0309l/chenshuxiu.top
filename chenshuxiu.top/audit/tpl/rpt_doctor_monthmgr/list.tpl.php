<?php
$pagetitle = "当月新增患者及活跃医生统计";
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
            <div class="searchBar">
                <?php
                $yeararr = XDateTime::getYearArrToNew();
                foreach($yeararr as $year_one){ ?>
                <a class="btn <?= $year_one == $year ? 'btn-primary' : 'btn-default' ?>" href="/rpt_doctor_monthmgr/list?year=<?= $year_one ?>"><?= $year_one ?>年</a>
                <?php } ?>
                <input class="year hidden" value="<?=$year?>">
                <br/><br/>(开通医生数:之前没有进患者，从本月开始 进/报到 第一个患者，视为开通医生。)
            </div>
            <div class="table-responsive">
                <table class="table table-bordered">
                <thead>
                <tr>
                    <td></td>
                    <?php for($i=1; $i<13; $i++){?>
                        <td><?=$i?>月</td>
                    <?php } ?>
                </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>进患者>=1医生数</td>
                        <?php foreach($doctorsArr["doctors_hascome"] as $k => $v){ ?>
                            <td><?= $v?></td>
                        <?php } ?>
                    </tr>
                    <tr>
                        <td>进患者>=5医生数</td>
                        <?php foreach($doctorsArr["doctors_active"] as $k => $v){ ?>
                            <td><?= $v?></td>
                        <?php } ?>
                    </tr>
                    <tr>
                        <td>开通医生数(本月/总计)</td>
                        <?php foreach($doctorsArr["doctors_all"] as $k => $v){ ?>
                            <td><?= $doctorsArr["doctors_month"][$k] ?>／<?= $v?></td>
                        <?php } ?>
                    </tr>
                    <tr>
                        <td>进患者医生率<br/>(进患者>=1医生数/开通医生数总计)</td>
                        <?php foreach($doctorsArr["doctors_hascome_rate"] as $k => $v){ ?>
                            <td><?= $v?>%</td>
                        <?php } ?>
                    </tr>
                    <tr>
                        <td>活跃医生率<br/>(进患者>=5医生数/开通医生数总计)</td>
                        <?php foreach($doctorsArr["doctors_active_rate"] as $k => $v){ ?>
                            <td><?= $v?>%</td>
                        <?php } ?>
                    </tr>
                </tbody>
            </table>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered">
                <thead>
                <tr>
                    <td></td>
                    <?php for($i=1; $i<13; $i++){?>
                        <td><?=$i?>月</td>
                    <?php } ?>
                </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>当月报到服药患者数</td>
                        <?php foreach($patientsArr["patientcnt_drug"] as $k => $v){ ?>
                            <td><?= $v?></td>
                        <?php } ?>
                    </tr>
                    <tr>
                        <td>当月报到患者数</td>
                        <?php foreach($patientsArr["patientcnt"] as $k => $v){ ?>
                            <td><?= $v?></td>
                        <?php } ?>
                    </tr>
                    <tr>
                        <td>报到患者服药率<br/>(当月报到服药患者数/当月报到患者数)</td>
                        <?php foreach($patientsArr["patientcnt_rate"] as $k => $v){ ?>
                            <td><?= $v?>%</td>
                        <?php } ?>
                    </tr>
                </tbody>
            </table>
            </div>
            <section class="col-md-10">
                <div class="table-responsive">
                    <table class="table table-bordered">
                    <thead>
                    <tr>
                        <td></td>
                        <?php for($i=1; $i<13; $i++){?>
                            <td><?=$i?>月</td>
                        <?php } ?>
                    </tr>
                    </thead>
                    <tbody>
                        <?php foreach($doctorCntArr as $k => $v){
                            $doctor = Doctor::getById($v["doctorid"]);
                            ?>
                            <tr>
                                <td><?=$doctor->name?></td>
                                    <?php for($i=0; $i<12; $i++){?>
                                        <td><?=$v["column_".$i]?></td>
                                    <?php } ?>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
                </div>
            </section>
            <section class="col-md-2">
                <div class="table-responsive">
                    <table class="table table-bordered">
                    <thead>
                    <tr>
                        <td>患者数汇总</td>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach($doctorCntArr as $k => $v){
                        $doctor = Doctor::getById($v["doctorid"]);
                        $themonth = date('Y-m', strtotime(date('Y-m') . ' -1 month '));
                        $rpt_doctor_month = Rpt_doctor_monthDao::getByDoctoridAndDateYm($doctor->id, $themonth);
                        ?>
                        <tr>
                            <?php if($rpt_doctor_month instanceof rpt_doctor_month){ ?>
                                <td><?= $rpt_doctor_month->patient_cnt_all_scan ?></td>
                            <?php }else { ?>
                                <td>0</td>
                            <?php } ?>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
                </div>
            </section>
        </section>
    </div>
    <div class="clear"></div>
<?php
$footerScript = <<<STYLE

STYLE;
?>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
