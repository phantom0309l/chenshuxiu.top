<?php
$pagetitle = '用药详情';
$cssFiles = []; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = [
    $img_uri . "/v5/page/audit/patientmgr/drugdetail/drugdetail.js?v=20170720",
]; //填写完整地址
$pageStyle = <<<STYLE
    .drugsheetRemark{
        border:1px solid #ccc;
        background: #f7f7f7;
        padding: 10px;
        margin: 20px 0px 0px;
    }
    .title{
        font-size:18px; color:#337ab7;
    }
STYLE;
$pageScript = <<<SCRIPT
SCRIPT;
$sideBarMini = false;
?>
<?php include_once $tpl . '/_header.new.tpl.php'; ?>

<div class="col-md-12">
    <?php include_once $tpl . "/patientmgr/_menu.tpl.php"; ?>
    <div class="content-div">
        <input type="hidden" name="patientid" value="<?= $patient->id ?>" id="patientid" />
        <section class="col-md-12">
        <div class="panel panel-default">
          <div class="panel-heading">
              <div class="title">
                  <span class="glyphicon glyphicon-user" aria-hidden="true"></span>
                  <span>患者基本信息</span>
              </div>
          </div>
          <div class="panel-body">
            <p>
                <span>患者姓名：<?= $patient->name ?></span>
                <span>所属医生:<?= $patient->doctor->name?></span>
            </p>
            <p>
                <span>具体疾病：<?= $patient->disease->name ?></span>
            </p>
            <p>
                <span>性别：<?= $patient->getSexStr()?></span>
                <span>年龄：<?= $patient->getAgeStr()?> 岁</span>
                <span>城市：<?= $patient->getXprovinceStr(); ?> <?= $patient->getXcityStr(); ?></span>
            </p>
          </div>
        </div>
        <?php if($patient->isEverDruging()){ ?>
          <div style="margin-bottom:10px;" class="title">
              <span class="glyphicon glyphicon-time" aria-hidden="true"></span>
              <span>当前用药</span>
          </div>
            <div class="table-responsive">
                <table class="table table-hover table-radius" style="text-align:center;">
              <thead>
                  <tr>
                      <th>药名</th>
                      <th>首次服药时间</th>
                      <th>最后一次更新时间</th>
                      <th>剂量</th>
                      <th>频次</th>
                      <th>状态</th>
                      <th>操作</th>
                  </tr>
              </thead>
              <tbody>
          <?php foreach ($patientmedicinerefs as $a) { ?>
                  <tr>
                      <td><?= $a->medicine->name?></td>
                      <td>
                          <?= $a->first_start_date ?>
                      </td>
                      <td>
                          <?= substr($a->last_drugchange_date,0,10) ?>
                      </td>
                      <td>
                          <?= $a->getDrugDose() ?>
                      </td>
                      <td>
                          <?= $a->drug_frequency ?>
                      </td>
                      <td>
                          <?php if(1 == $a->status){ ?>
                              <span class="green">用药中</span>
                          <?php } ?>
                          <?php if(0 == $a->status){ ?>
                              <span class="red">已停药</span>
                          <?php } ?>
                      </td>
                      <td>
                          <button data-medicineid="<?= $a->medicine->id ?>" data-patientid="<?=$patient->id?>" class="triggerAddDrugItem btn btn-default" data-toggle="modal" data-target="#drugItemAdd">新增一条记录</button>
                          <button data-medicineid="<?= $a->medicine->id ?>" data-patientid="<?=$patient->id?>" class="triggerstopDrug btn btn-default" data-toggle="modal" data-target="#drugStop">停药</button>
                      </td>
                  </tr>
          <?php } ?>
              </tbody>
          </table>
            </div>
          <div style="margin-bottom:10px;" class="title clearfix">
              <span class="pull-right triggerAddDrugBtn">
                  <span class="btn btn-primary">添加用药</span>
              </span>
          </div>

        <?php include_once $tpl . "/patientmgr/_adddrug_box_shell.php"; ?>

        <div class="panel panel-default">
            <div class="panel-heading">
                <div class="title">
                    <span class="glyphicon glyphicon-calendar" aria-hidden="true"></span>
                    <span>用药记录<span>
                </div>
            </div>
            <div class="panel-body">
                <?php foreach ($drugdata as $medicine_name => $drugitem_arr) {
                    if(count($drugitem_arr)==0){
                        continue;
                    }
                ?>
                <div class="mt20">
                    <h4><?= $medicine_name ?></h4>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>服药日期</th>
                                <th>剂量</th>
                                <th>频率</th>
                                <th>备注</th>
                                <th>运营</th>
                                <th>删除</th>
                            </tr>
                        </thead>
                        <tbody>
                    <?php foreach ($drugitem_arr as $a) {
                        $item = $a["item"];
                    ?>
                            <tr class="<?= $a['keypoint']==1 ? 'success' : ''?>">
                                <td><?= substr($item->record_date,0,10) ?></td>
                                <td>
                                    <?= $item->getDrugDose() ?>
                                </td>
                                <td>
                                    <?= $item->drug_frequency ?>
                                </td>
                                <td>
                                    <?= $item->content ?>
                                </td>
                                <td>
                                    <?= $item->auditor->name ?>
                                </td>
                                <td class="text-center">
                                    <button class="deleteDrugItemBtn btn btn-default" data-drugitemid="<?= $item->id ?>">删除</button>
                                </td>
                            </tr>
                    <?php } ?>
                        </tbody>
                    </table>
                    </div>
                </div>
                <?php } ?>
            </div>
        </div>
        <?php }else{ ?>
            <?php include_once $tpl . "/patientmgr/_drug_choice.php"; ?>
        <?php } ?>
        </section>
    </div>

    <div class="modal fade" id="drugItemAdd" tabindex="-1" role="dialog" aria-labelledby="drugItemAddLabel" aria-hidden="true"></div>
    <div class="modal fade" id="drugStop" tabindex="-1" role="dialog" aria-labelledby="drugStopLabel" aria-hidden="true"></div>

    <div class="clear"></div>
    </div>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
