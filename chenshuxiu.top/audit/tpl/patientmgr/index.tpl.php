<?php
$pagetitle = '运营系统首页';
$cssFiles = []; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = []; //填写完整地址
$pageStyle = <<<STYLE
STYLE;
$pageScript = <<<SCRIPT
SCRIPT;
$sideBarMini = false;
?>
<?php include_once $tpl . '/_header.new.tpl.php'; ?>
    <nav class="navbar navbar-inverse collapse">
        <div id="navbar" class="navbar-collapse collapse">
            <ul class="nav navbar-nav">
                <li>
                    <a href="#">
                    	<?php
                    	   $pcard = $patient->getMasterPcard();
                    	?>
                        <span style="font-size: 18px;"><?=$patient->name ?></span> <?=$patient->getSexStr() ?> <?=$patient->getAgeStr() ?>岁
                        <span><?= $patient->getXprovinceXcityStr()?> , </span>
                        <span><?= $patient->getMobiles() ?> , </span>
                        <span><?=$pcard->getYuanNeiStr() ?></span>
                        <span>疾病：<?= $pcard->disease->name ?> , </span>
                        <span>距离上次复发：<?= $pcard->getDescStrOfLast_incidence_date2Today()?> , </span>
                    </a>
                </li>
                <li></li>
            </ul>
        </div>
    </nav>
    <div class="col-md-12">
        <aside class="col-md-2">
            <div class="sub_menu">
                <div class="sub_menu_body">
                    <div class="sub_menu_title">患者首页</div>
                    <div class="sub_menu_item sub_menu_itemActive">基本信息</div>
                    <div class="sub_menu_item sub_menu_itemActive">病历总览</div>
                    <div class="sub_menu_item sub_menu_itemActive">随访流</div>
                    <div class="sub_menu_item sub_menu_itemActive">就诊记录</div>
                    <div class="sub_menu_item sub_menu_itemActive">开药</div>
                    <div class="sub_menu_item sub_menu_itemActive">约复诊</div>
                    <div class="sub_menu_item sub_menu_itemActive">诊疗医嘱?</div>
                    <div class="sub_menu_item sub_menu_itemActive">发作史</div>
                    <div class="sub_menu_item sub_menu_itemActive">EDSS</div>
                    <div class="sub_menu_item sub_menu_itemActive">血清检查</div>
                    <div class="sub_menu_item sub_menu_itemActive">血常规</div>
                    <div class="sub_menu_item sub_menu_itemActive">- 肝功能</div>
                    <div class="sub_menu_item sub_menu_itemActive">- 肾功能</div>
                    <div class="sub_menu_item sub_menu_itemActive">- ...</div>
                    <div class="sub_menu_item sub_menu_itemActive">脑脊液</div>
                    <div class="sub_menu_item sub_menu_itemActive">影像检查</div>
                    <div class="sub_menu_item sub_menu_itemActive">基因筛查</div>
                    <div class="sub_menu_item sub_menu_itemActive">眼科检查</div>
                    <div class="sub_menu_item sub_menu_itemActive">- 视力</div>
                    <div class="sub_menu_item sub_menu_itemActive">- 视野</div>
                    <div class="sub_menu_item sub_menu_itemActive">口腔检查</div>
                    <div class="sub_menu_item sub_menu_itemActive">- 唇腺活检</div>
                </div>
            </div>
        </aside>
        <section class="col-md-10">
            <div class="list-group">
                <a href="#" class="list-group-item disabled">
                    <span style="font-size: 18px;"><?=$patient->name ?></span>
                </a>
                <a href="#" class="list-group-item">
                    <?=$patient->getSexStr() ?> <?=$patient->getAgeStr() ?>岁
                    <span><?= $patient->getXprovinceXcityStr()?></span>
                    <span><?= $patient->getMobiles() ?></span>
                </a>
                <a href="#" class="list-group-item">
                    <span><?= $pcard->getYuanNeiStr() ?></span>
                    <span>疾病：<?= $pcard->disease->name ?></span>
                    <span>距离上次复发：<?= $pcard->getDescStrOfLast_incidence_date2Today()?></span>
                </a>
            </div>
            <div class="list-group">
                <span style="font-size: 18px;">上次开药</span>
                <span>[这里没实现]</span>
                <a href="#" class="btn btn-success">开药</a>
                 <?php
                $pmRefs = $patient->getAllPatientMedicineRefs();
                if (false == empty($pmRefs)) {
                    ?>
                <div class="table-responsive">
                    <table class="table table-bordered tdcenter mt10">
                    <thead>
                        <tr class="blue">
                            <td>药品</td>
                            <td>剂量</td>
                            <td>用药状态</td>
                            <td>首次服药</td>
                            <td>最后变更</td>
                        </tr>
                    </thead>
                    <tbody>
                <?php
                    foreach ($pmRefs as $pmRef) {
                        $drugItem = $patient->getLastDrugItem($pmRef->medicine);
                        ?>
                         <tr>
                            <td>
            	<?= $pmRef->medicine->name?>
         	                </td>
                            <td>
                <?= $drugItem->value ?> <?= $pmRef->medicine->unit?>
         	                </td>
                            <td>
                 <?= $pmRef->typetrans()?>
                 <?php if( $pmRef->stopdate != '0000-00-00' ){ ?>
                     (<?= $pmRef->stopdate ?>)
                 <?php } ?>
                            </td>
                            <td>
            	<?= substr($pmRef->first_start_date, 0,10)?>
         	                </td>
                            <td>
            	<?= substr($pmRef->last_drugchange_date, 0,10)?>
         	                </td>
                        </tr>
            <?php }?>
	                </tbody>
                </table>
                </div>
        <?php }?>

            </div>
            <div class="list-group">
                <span style="font-size: 18px;">就诊记录</span>
                <a href="#" class="btn btn-success">添加就诊记录</a>
                <a href="#" class="btn btn-success">约下次复诊</a>
                <pre class="mt10 red">以下信息为示例,别当真!!!</pre>
                <div class="table-responsive">
                    <table class="table table-bordered tdcenter mt10">
                    <thead>
                        <tr class="blue">
                            <td width=120>就诊日期</td>
                            <td style="text-align: left">主诉</td>
                            <td width=120>操作</td>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>2016-08-01</td>
                            <td style="text-align: left">验血发现,血小板过少</td>
                            <td>
                                <a href='#'>修改</a>
                            </td>
                        </tr>
                        <tr>
                            <td>2016-06-01</td>
                            <td style="text-align: left">药物副反应严重,脱发</td>
                            <td>
                                <a href='#'>修改</a>
                            </td>
                        </tr>
                        <tr>
                            <td>2016-04-01</td>
                            <td style="text-align: left">上周发作了一次,持续3天才好</td>
                            <td>
                                <a href='#'>修改</a>
                            </td>
                        </tr>
                        <tr>
                            <td>2016-02-01</td>
                            <td style="text-align: left">油腻东西吃多了,近期失眠</td>
                            <td>
                                <a href='#'>修改</a>
                            </td>
                        </tr>
                    </tbody>
                </table>
                </div>
            </div>
        </section>
    </div>
    <div class="clear"></div>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
