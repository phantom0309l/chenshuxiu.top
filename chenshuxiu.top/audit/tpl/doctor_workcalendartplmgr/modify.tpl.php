<?php
$pagetitle = "修改工作日历模板";
$cssFiles = []; // 也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = []; // 填写完整地址
$pageStyle = <<<STYLE
STYLE;
$pageScript = <<<SCRIPT
SCRIPT;
$sideBarMini = false;
?>
<?php include_once $tpl . '/_header.new.tpl.php'; ?>
<div class="col-md-12">
    <section class="col-md-12">
        <form action="/doctor_workcalendartplmgr/modifypost" method="post">
            <input type="hidden" value="<?= $workcalendartpl->id ?>" name="workcalendartplid"/>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <tbody>
                    <tr>
                        <td style="width: 120px;">医生</td>
                        <td><?= $workcalendartpl->doctorid == 0 ? '全部医生' : $workcalendartpl->doctor->name ?></td>
                    </tr>
                    <tr>
                        <td>疾病</td>
                        <td><?= $workcalendartpl->diseaseid == 0 ? '全部疾病' : $workcalendartpl->disease->name ?></td>
                    </tr>
                    <tr>
                        <td>CODE</td>
                        <td>
                            <input class="form-control" type="text" name="code" placeholder="请填写CODE"
                                   value="<?= $workcalendartpl->code ?>"/>
                        </td>
                    </tr>
                    <tr>
                        <td>标题</td>
                        <td>
                            <input class="form-control" type="text" name="title" placeholder="请填写标题"
                                   value="<?= $workcalendartpl->title ?>"/>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            模板配置
                            <br>
                            <br>
                            <a target="_blank" href="https://www.bejson.com/">go BeJson</a>
                        </td>
                        <td>
                            <textarea class="form-control" name="content" rows="6"
                                      placeholder="请编辑模板内容"><?= $workcalendartpl->content ?></textarea>
                        </td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>
                            <input type="submit" class="btn btn-success" value="提交"/>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </form>
    </section>
</div>
<div class="clear"></div>
<?php
$footerScript = <<<STYLE

STYLE;
?>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
