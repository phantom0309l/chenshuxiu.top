<?php
$pagetitle = "列表 doctor_hezuos";
$cssFiles = []; // 也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = []; // 填写完整地址
?>
<?php include_once $tpl . '/_header.new.tpl.php'; ?>
<div class="col-md-12">
    <section class="col-md-12">
        <div class="searchBar">
            <form action="/doctor_hezuomgr/list" method="get" class="pr">
                <div class="mt10">
                    <label>按状态：</label>
                    <select autocomplete="off" name="status">
                        <option value="-1" <?= $status == -1 ? "selected" : ""?>>全部</option>
                        <option value="0" <?= $status == 0 ? "selected" : ""?>>未开通</option>
                        <option value="1" <?= $status == 1 ? "selected" : ""?>>开通</option>
                        <option value="2" <?= $status == 2 ? "selected" : ""?>>未绑定 有同名的方寸医生</option>
                        <option value="3" <?= $status == 3 ? "selected" : ""?>>已绑定 hospital_name_2 = ''</option>
                        <option value="4" <?= $status == 4 ? "selected" : ""?>>已绑定 hospital_name_2 != ''</option>
                        <option value="5" <?= $status == 5 ? "selected" : ""?>>未绑定 hospital_name_2 != ''</option>
                    </select>
                </div>
                <div class="mt10">
                    <label for="">按医生：</label>
                    <input type="text" name="doctor_name" value="<?= $doctor_name ?>" />
                </div>
                <div class="mt10">
                    <input type="submit" class="btn btn-success" value="筛选" />
                </div>
            </form>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <td>id</td>
                        <td>合作方</td>
                        <td>from_hezuo_doctor</td>
                        <td>from_fangcun_doctor</td>
                        <td>操作</td>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($doctor_hezuos as $a) { ?>
                    <tr>
                        <td><?= $a->id ?></td>
                        <td><?= $a->company ?></td>
                        <td>
                            <p class="blue"><?= $a->name ?></p>
                            <p><?= $a->hospital_name ?></p>
                            <p class="gray"><?= $a->hospital_name_2 ?></p>
                            <p><?= $a->department ?> <?= $a->title1 ?></p>
                        </td>
                        <td>
                    <?php
                    $doctor = $a->doctor;
                    if ($doctor instanceof Doctor) {
                        ?>
                            <p>
                                <span class="blue"><?= $doctor->name ?></span> <?= $doctor->id ?> <?= $doctor->user->username ?></p>
                            <p><?= $doctor->hospital->name ?></p>
                            <p class='gray'><?= $doctor->hospital->shortname ?></p>
                            <p><?= $doctor->department ?> <?= $doctor->title ?></p>
                    <?php } ?>

                        </td>
                        <td>
                            <p>
                                <a class="btn btn-default" href="/doctor_hezuomgr/relation?doctor_hezuoid=<?= $a->id ?>" target="_blank">关联fangcun库医生( <?=$a->getSameNameDoctorCnt(); ?> )</a>
                                <a class="btn btn-default" href="/doctor_hezuomgr/modify?doctor_hezuoid=<?= $a->id ?>" target="_blank">修改</a>
                            </p>
                                <?php if( $a->isPassed() ){ ?>
                            <p>
                                <span class="closeHezuoBtn btn btn-primary" data-doctorhezuoid="<?= $a->id ?>">开通合作</span>
                            </p>
                                <?php }else{ ?>
                             <p>
                                <span class="passHezuoBtn btn btn-default" data-doctorhezuoid="<?= $a->id ?>">开通合作</span>
                            </p>
                                <?php } ?>

                    <?php
                    if ($doctor instanceof Doctor) {
                        ?>
                            <p>
                                <a target="_blank" href="/doctor_hezuomgr/lilly_zhuoka?doctor_hezuoid=<?= $a->id ?>">桌卡图片</a>
                                <a target="_blank" href="/doctor_hezuomgr/lilly_patient_page_back?doctor_hezuoid=<?= $a->id ?>">患者页背面</a>
                                <a target="_blank" href="/doctor_hezuomgr/lilly_patient_page_back_20170919?doctor_hezuoid=<?= $a->id ?>">患者页背面0919</a>
                            </p>
                    <?php } ?>

                        </td>
                    </tr>
                <?php } ?>
                    <tr>
                        <td colspan=5><?php include $dtpl . "/pagelink.ctr.php"; ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>
</div>
<div class="clear"></div>
<?php
$footerScript = <<<XXX
    $(function(){
        var app = {
            init : function(){
                var self = this;
                self.handlePassHezuo();
                self.handleCloseHezuo();
            },
            handlePassHezuo : function(){
                $(document).on("click", ".passHezuoBtn", function(){
                    var me = $(this);
                    var doctor_hezuoid = me.data("doctorhezuoid");
                    $.ajax({
                        url: '/doctor_hezuomgr/passHezuoJson',
                        type: 'post',
                        dataType: 'text',
                        data: {doctor_hezuoid: doctor_hezuoid}
                    })
                    .done(function() {
                        alert("已开通");
                        me.addClass('btn-primary').addClass('closeHezuoBtn').removeClass('passHezuoBtn').removeClass("btn-default");
                    })
                    .fail(function() {
                    })
                    .always(function() {
                    });

                })
            },
            handleCloseHezuo : function(){
                $(document).on("click", ".closeHezuoBtn", function(){
                    var me = $(this);
                    var doctor_hezuoid = me.data("doctorhezuoid");
                    $.ajax({
                        url: '/doctor_hezuomgr/closeHezuoJson',
                        type: 'post',
                        dataType: 'text',
                        data: {doctor_hezuoid: doctor_hezuoid}
                    })
                    .done(function() {
                        alert("已关闭");
                        me.addClass('btn-default').addClass('passHezuoBtn').removeClass('closeHezuoBtn').removeClass("btn-primary");
                    })
                    .fail(function() {
                    })
                    .always(function() {
                    });

                })
            }
        }
        app.init();
    })
XXX;
?>

<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
