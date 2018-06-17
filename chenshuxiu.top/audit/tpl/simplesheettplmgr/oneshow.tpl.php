<?php
$pagetitle = "[{$simplesheettpl->title}]问卷展示 SimpleSheetTpls";
$cssFiles = [
]; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = [
]; //填写完整地址
?>
<?php include_once $tpl . '/_header.new.tpl.php'; ?>
<div class="col-md-12" xmlns="http://www.w3.org/1999/html">
    <section class="col-md-12">
        <div class="col-md-12"  style="overflow-x:auto">
            <form action="/simplesheetmgr/addpost" method="post">
                <input type="hidden" id="patientid" name="patientid" value="510005016">
                <input type="hidden" id="simplesheettplid" name="simplesheettplid" value="<?=$simplesheettpl->id?>">

                <?php
                $questions = explode("\n", $simplesheettpl->content);
                foreach ($questions as $question) {
                    list($must, $title, $type, $option) = explode(',', $question);

                    if ($type == '文本') {
                        ?>
                        <div style="width: 530px;">
                            <div class="form-group">
                                <label class="" for="<?=$title?>"><?=$title?></label>
                                <div class="">
                                    <input class="form-control" data-must="<?=$must?>" type="text" id="content[<?=$title?>]" name="content[<?=$title?>]" placeholder="<?=$title?>">
                                    <?php
                                        if ($must == '*') {
                                            ?>
                                                <span class="must" style="color: red">必填*</span>
                                            <?php
                                        }
                                    ?>
                                </div>
                            </div>
                        </div>
                        <?php
                    }

                    if ($type == '下拉单选') {
                        ?>
                        <div style="width: 530px;margin-bottom: 20px;">
                            <label class="" for="<?=$title?>"><?=$title?></label>
                            <?php
                            $options = explode('|', $option);
                            $list = [];
                            foreach ($options as $a) {
                                $list["{$a}"] = $a;
                            }

                            echo HtmlCtr::getSelectCtrImp($list, "content[{$title}]", '', "form-control");
                            ?>
                            <?php
                            if ($must == '*') {
                                ?>
                                    <span class="must" style="color: red">必填*</span>
                                <?php
                            }
                            ?>
                        </div>
                        <?php
                    }

                    if ($type == '文本域') {
                        ?>
                        <div style="width: 530px;">
                            <div class="form-group">
                                <label class="" for="<?=$title?>"><?=$title?></label>
                                <div class="">
                                    <textarea class="form-control" id="content[<?=$title?>]" name="content[<?=$title?>]" rows="4" placeholder="<?=$title?>"></textarea>
                                    <?php
                                    if ($must == '*') {
                                        ?>
                                            <span class="must" style="color: red">必填*</span>
                                        <?php
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                        <?php
                    }
                }
                ?>

                <input type="submit" id="submit" class="btn btn-sm btn-primary" value="提交">
            </form>
        </div>
    </section>
</div>

<div class="clear"></div>

<script>
    $(function () {
        $('#submit').on('click', function () {
            var flag = 0;
            $('.must').each(function () {
                var checkEl = $(this).prev();
                var title = checkEl.attr('id');

                if (checkEl.val() == '') {
                    alert(title + "不能为空");
                    flag = 1;
                    return false;
                }
            });

            if (flag == 1) {
                return false;
            }
        });
    });
</script>

<?php
$footerScript = <<<XXX
$(function() {
});
XXX;
?>

<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
