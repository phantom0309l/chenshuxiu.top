<div class="mt10" style="background:#eee;padding:5px;border-radius:2px">
    <?php foreach($patient->getPcards() as $pcard){
        $color = "#fcfcea";
        if ($patient->doctorid == $pcard->doctorid && $patient->diseaseid == $pcard->diseaseid) {
            $color = "#fbd9a8";
        }
        ?>
        <div class='border1' style="background-color: <?=$color?>;padding:10px;">
            <span class='blue'><a target="_blank" href="/pcardmgr/modifydisease?pcardid=<?=$pcard->id?>"><?= $pcard->disease->name ?><i class="fa fa-pencil"></i></a></span>
            <span class='red'><?= $pcard->doctor->name ?></span>
            <span class="gray"><?=  $pcard->doctor->hospital->name ?> - <?=  $pcard->doctor->department ?></span>
            <br/> <span class='black'><?= $pcard->getYuanNeiStr() ?></span>
            <?php
            if ($pcard->doctor->service_remark) {
                ?>
                <div class="span-blue">
                    <?php echo $pcard->doctor->service_remark; ?>
                </div>
                <?php
            }
            ?>
            <br/> <span>显示疾病[ <span class='blue' id="diseasename_show_edit-<?=$pcard->id?>"><?=$pcard->diseasename_show?></span> ]<button class="btn btn-info btn-xs push-10-l" data-toggle="modal" data-target="#modify_diseasename_show-<?=$pcard->id?>" type="button">修改</button></span>
        </div>

        <div class="modal" id="modify_diseasename_show-<?=$pcard->id?>" tabindex="-1" role="dialog" aria-hidden="true" style="display: none; padding-right: 17px;">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="block block-themed block-transparent remove-margin-b">
                        <div class="block-header bg-primary">
                            <ul class="block-options">
                                <li>
                                    <button data-dismiss="modal" type="button"><i class="si si-close"></i></button>
                                </li>
                            </ul>
                            <h3 class="block-title">修改显示疾病(仅仅用于显示for方寸管理端)</h3>
                        </div>
                        <div class="block-content">
                            <input style="margin-bottom: 20px;" class="form-control" type="text" id="diseasename_show-<?=$pcard->id?>" name="diseasename_show" placeholder="请输入疾病 " value="<?=$pcard->diseasename_show?>">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-sm btn-default" type="button" data-dismiss="modal">关闭</button>
                        <button class="btn btn-sm btn-primary edit_diseasename_show" data-pcardid="<?=$pcard->id?>" type="button" data-dismiss="modal"><i class="fa fa-check"></i>修改</button>
                    </div>
                </div>
            </div>
        </div>
    <?php } ?>
</div>

<div class="clearfix">

</div>

<script type="text/javascript">
    $(function(){
        $(document).on('click', '.edit_diseasename_show', function(event) {
            event.preventDefault();

            var pcardid = $(this).data('pcardid');
            var diseasename_show = $("#diseasename_show-" + pcardid).val();

            $.ajax({
                url: '/pcardmgr/modifydiseasenameshowJson',
                type: 'get',
                dataType: 'text',
                data: {
                    pcardid : pcardid,
                    diseasename_show : diseasename_show
                }
            })
            .done(function() {
                console.log("success");

                $("#diseasename_show_edit-" + pcardid).text(diseasename_show);
            })
            .fail(function() {
                console.log("error");
            })
            .always(function() {
                console.log("complete");
            });

        });;
    });
</script>
