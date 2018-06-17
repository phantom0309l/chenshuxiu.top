<?php
$pagetitle = "99健康网——医院列表 Jkw_hospitals";
$cssFiles = []; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = []; //填写完整地址
?>
<?php include_once $tpl . '/_header.new.tpl.php'; ?>
    <div class="col-md-12">
        <section class="col-md-12">
            <div class="col-md-12" style="padding-left: 0px;padding-right: 0px;">
                <!-- <div class="col-sm-1 col-xs-3 success" style="float: left; padding: 0px; line-height: 2.5;">
                    <a class="btn btn-sm btn-primary" target="_blank" href="/jkw_hospitalmgr/add">
                        <i class="fa fa-plus push-5-r"></i>医院新建
                    </a>
                </div> -->

                <div class="col-sm-12 col-xs-12">
                    <div class="col-sm-3 col-xs-12 pull-right">
                        <form class="form-horizontal push-5-t" action="/jkw_hospitalmgr/list" method="get">
                            <div class="form-group">
                                <div class="input-group">
                                    <input type="text" placeholder="搜索医院名" name="jkw_hospital_name" class="input-search form-inline form-control" value="<?=$jkw_hospital_name?>">
                                    <span class="input-group-btn" style="width: 1%; line-height: 35px;">
                                        <button type="submit" class="btn btn-primary">
                                            <span type="submit" aria-hidden="true" class="glyphicon glyphicon-search">
                                            </span>
                                        </button>
                                    </span>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="clear">

                </div>
            </div>
            <div class="col-md-12">
            <div class="table-responsive">
                <table class="table table-bordered">
                <thead>
                    <tr>
                        <td width="50">id</td>
                        <td>创建日期</td>
                        <td>
                            医院名称
                            <br />
                            <span class="gray">医院别名</span>
                        </td>
                        <td>医院性质</td>
                        <td>医院等级</td>
                        <td>医院地址</td>
                        <td>医护人数</td>
                        <td style="width:10px;">数据来源</td>
                        <td>操作</td>
                    </tr>
                </thead>
                <tbody>
                <?php
                foreach ($jkw_hospitals as $a) {
                    ?>
                    <tr>
                        <td><?= $a->id ?></td>
                        <td><?= $a->getCreateDay() ?></td>
                        <td><?= $a->name?>
                            <br />
                            <span class="gray"><?= $a->shortname ?></span>
                        </td>
                        <td><?= $a->type ?></td>
                        <td><?= $a->levelstr ?></td>
                        <td title="<?= nl2br($a->getJkw_hospitalAddressStr()) ?>"><?= mb_substr(nl2br($a->getJkw_hospitalAddressStr()), 0, 3) ?></td>
                        <td><?= $a->employee_cnt ?>个</td>
                        <td><a href="<?= $a->from_url ?>" target="_blank"><?= mb_substr($a->from_url, 0, 10)."..." ?></a></td>
                        <td>
                            <?php $cond = ' and name=:name ';
                            $bind = array(':name' => $a->name);
                            $hospital = Dao::getEntityByCond('Hospital', $cond, $bind);
                            if ($hospital instanceof Hospital) { ?>
                                <a class="btn btn-sm btn-primary" href="/hospitalmgr/modify?hospitalid=<?= $hospital->id ?>">已创建</a>
                            <?php }else{ ?>
                                <a class="btn btn-sm btn-primary" href="/hospitalmgr/addFromJkw_hospital?jkw_hospitalid=<?= $a->id ?>">去建医院</a>
                            <?php } ?>
                            <a class="btn btn-sm btn-primary" href="/jkw_hospitalmgr/modify?jkw_hospitalid=<?= $a->id ?>">修改</a>
                        </td>
                    </tr>
                <?php } ?>
                    <tr>
                        <td colspan=10>
    <?php include $dtpl . "/pagelink.ctr.php"; ?>
                        </td>
                    </tr>
                </tbody>
            </table>
            </div>
            </div>
                <?php if(count($jkw_hospitals) == 0){ ?>
                    <div class="col-sm-1 col-xs-3 success" style="float: left; padding: 0px; line-height: 2.5;">
                        <a class="btn btn-sm btn-primary" target="_blank" href="/hospitalmgr/add">
                            <i class="fa fa-plus push-5-r"></i>医院新建
                        </a>
                    </div>
                <?php } ?>
        </section>
    </div>
    <div class="clear"></div>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
