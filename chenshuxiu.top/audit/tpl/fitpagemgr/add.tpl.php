<?php
$pagetitle = "实例录入  Of " . $fitpagetpl->name;
$cssFiles = []; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = []; //填写完整地址
?>
<?php include_once $tpl . '/_header.new.tpl.php'; ?>

<div class="col-md-12">
    <section class="col-md-12">
        <form class="form-horizontal" method="post" action="/fitpagemgr/addpost">
            <input type="hidden" name="fitpagetplid" value="<?=$fitpagetpl->id ?>">
            <input type="hidden" name="diseaseid" value="<?=$diseaseid ?>">

            <div class="table-responsive">
                <table class="table table-bordered">
                <tr>
                    <td width="90">code</td>
                    <td class="">
                        <?=$fitpagetpl->code?>
                    </td>
                </tr>
                <tr>
                    <td width="90">名称</td>
                    <td class="">
                        <?=$fitpagetpl->name?>
                    </td>
                </tr>
                <tr>
                    <td>疾病</td>
                    <td>
                    <div class="col-md-6 col-xs-12 remove-padding">
                    <select id="select-disease" class="form-control">
                        <option value="0">不指定疾病</option>
                        <?php foreach (CtrHelper::getDiseaseCtrArray(false) as $key => $value) {?>
                            <option value="<?=$key?>" <?php if($diseaseid == $key){ ?>selected=""<?php }?>><?=$value?></option>
                        <?php } ?>
                    </select>
                    </div>
            </td>
                </tr>
                <tr>
                    <td>医生</td>
                    <td>
            <?php
            if ($diseaseid == 0) {
                echo '<span class="text-danger">如要选择医生，必须先选择疾病</span>';
            } else {
                echo '<div class="col-md-6 col-xs-12 remove-padding">';
                echo HtmlCtr::getSelectCtrImp($doctorCtrArr, 'doctorid', 0, 'form-control');
                echo '</div>';
            }
            ?>
                    </td>
                </tr>
                <tr>
                    <td>备注</td>
                    <td>
                        <div class="col-md-6 col-xs-12 remove-padding">
                            <textarea class="form-control" name="remark" rows="10" cols="50"></textarea>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td>
                        <input class="btn btn-success btn-minw" type="submit" value="提交">
                    </td>
                </tr>
            </table>
            </div>
        </form>
    </section>
</div>
<div class="clear"></div>

<?php
$footerScript = <<<SCRIPT
$(function(){
    $(document). on('change', '#select-disease', function(){
        var diseaseid = $(this).val();
        location.href = "/fitpagemgr/add?fitpagetplid={$fitpagetpl->id}&diseaseid=" + diseaseid;
    })
})
SCRIPT
?>

<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
