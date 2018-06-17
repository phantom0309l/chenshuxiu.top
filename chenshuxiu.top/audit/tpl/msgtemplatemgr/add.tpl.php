<?php
$pagetitle = "自动回复消息新建";
$cssFiles = [
]; // 也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = [
]; // 填写完整地址
?>
<?php include_once $tpl . '/_header.new.tpl.php'; ?>

<script>
    $(function () {
        $('#doctor-word').autoComplete({
            type: 'doctor',
            partner: '#doctorid',
        });
    })
</script>

<div class="col-md-12">
        <section class="col-md-12">
        <form action="/msgtemplatemgr/addpost" method="post">
                <input type="hidden" name="diseaseid" value="<?=$diseaseid ?>" />
            <div class="table-responsive">
                <table class="table table-bordered">
                    <tr>
                        <th width='140'>疾病</th>
                        <td>
                            <?php
                            if (0 == $diseaseid) {
                                echo "<span class='f16'>全部</span>";
                            } else {
                                echo "<span><a href='/msgtemplatemgr/add?diseaseid=0'>全部</a></span> ";
                            }
                            ?>
                           &nbsp;&nbsp;
                            <?php
                            foreach ($diseases as $a) {
                                if ($a->id == $diseaseid) {
                                    echo "<span class='f16'>{$a->name}</span>";
                                } else {
                                    echo "<span><a href='/msgtemplatemgr/add?diseaseid={$a->id}'>{$a->name}</a></span> ";
                                }

                                echo "&nbsp;&nbsp;";
                            }
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <th>使用的医生</th>
                        <td>
                            <div class="col-xs-2">
                                <?php include_once $tpl . '/_select_doctor.tpl.php'; ?>
                            </div>
                            <span>非必填，如果医生有特别的要求可填</span>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            ename(类型)
                            <br />
                            (必填)
                        </th>
                        <td>
                        <?php $enameArr = MsgTemplate::getEnameArr();?>
                        <input class="enameInput" type="hidden" name="ename" value="" />
                            <div>
                            <?php foreach( $enameArr['ename_type'] as $arr_temp ){?>
                                <div class="enameBtn btn btn-default btn-primary" style="margin: 5px 0px;" data-ename="<?=$arr_temp['ename']?>" data-description="<?=$arr_temp['description']?>">
                                    <?=$arr_temp['title']?>
                                </div>
                            <?php }?>
                        </div>
                            <p>如果有需要添加类型，可以提出需求</p>
                        </td>
                    </tr>
                    <tr>
                        <th>标题</th>
                        <td>
                            <input type="text" name="title" value="" style="width: 30%;"/>
                            <p>只是给运营看的</p>
                        </td>
                    </tr>
                    <tr>
                        <th>模板文案</th>
                        <td>
                            <textarea name="content" style="width: 50%; height: 200px;"></textarea>
                            <p>
                                <span class="blue">通用占位符, 需要做测试</span>
                                <?php foreach( $enameArr['common_str'] as $k => $v ){?>
                                    <br /><?= $k ?> => <?= $v ?>
                                <?php }?>
                            </p>
                            <span class="blue">特殊占位符</span>
                            <p class="enameDescription">无</p>
                        </td>
                    </tr>
                    <tr>
                        <th></th>
                        <td>
                            <input type="submit" class="btn btn-success" value="提交" />
                        </td>
                    </tr>
                </table>
            </div>
            </form>
        </section>
    </div>
    <div class="clear"></div>


<?php
$footerScript = <<<XXX
$(function(){

    $(".enameBtn").on("click", function(){
        var me = $(this);
        $(".enameBtn").each(function(){
            var me = $(this);
            if( false == me.hasClass('btn-primary')){
                me.addClass('btn-primary');
            }
        });

        me.parents('td').find('.enameInput').val(me.data('ename'));
        $('.enameDescription').empty();
        $('.enameDescription').append(me.data('description'));
        me.removeClass('btn-primary');
    })
})
XXX;
?>

<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
