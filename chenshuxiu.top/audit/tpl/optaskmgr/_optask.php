<div class="colorBox" style="line-height: 150%">
    <?php
    $patientid = $patient->id;
    ?>
    <div>
        <button class="btn btn-default" data-toggle="modal" data-target="#optaskBox">添加跟进任务</button>
        <?php if ($patient->diseaseid != 1) { ?>
            <a href="/pmsideeffectmgr/add?patientid=<?= $patientid ?>" target="_blank" class="btn btn-default">添加药物副反应任务</a>
        <?php } ?>
        <a href="/pmsideeffectmgr/list?patientid=<?= $patientid ?>" target="_blank" class="btn btn-default">[列表]</a>
    </div>
</div>

<div class="modal fade" id="optaskBox" tabindex="-1" role="dialog"
     aria-labelledby="optaskBoxLabel" aria-hidden="true">

    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                    &times;
                </button>

                <h4 class="modal-title" id="optaskBoxLabel">
                    添加跟进任务
                </h4>
            </div>

            <div class="modal-body">
                <form class="optaskBox">
                    <div class="form-group">
                        <label>设置任务类型</label>
                        <?php
                        $optasktpl_order = [];
                        foreach ($optasktpls as $a) {
                            if ($a->isInOpTaskTplDiseaseids($patient->diseaseid)) {
                                if ($a->is_can_handcreate == 1) {
                                    if ($a->title == '跟进[定期随访]') {
                                        $first_optasktpl = $a;
                                    } elseif ($a->title == '跟进[其他]') {
                                        $last_optasktpl = $a;
                                    } else {
                                        $optasktpl_order[] = $a;
                                    }
                                }
                            }
                        }
                        if ($first_optasktpl instanceof OpTaskTpl) {
                            array_unshift($optasktpl_order, $first_optasktpl);
                        }
                        if ($last_optasktpl instanceof OpTaskTpl) {
                            array_push($optasktpl_order, $last_optasktpl);
                        }

                        ?>
                        <select class="typestr form-control">
                            <?php foreach ($optasktpl_order as $k => $a) { ?>
                                <option value="<?= $a->id ?>" <?= $k == 0 ? 'selected' : '' ?>>
                                    <?= $a->title ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>下次跟进时间</label>
                        <input type="text" class="form-control calendar optaskBox-plantime"/>
                    </div>
                    <div class="form-group">
                        <label>任务等级</label>
                        <div style="width:100px; display:block;">
                            <?php
                            echo HtmlCtr::getSelectCtrImp(CtrHelper::getOptaskLevelCtrArray(), "level", 2, 'form-control level');
                            ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>跟进内容</label>
                        <textarea class="form-control optaskBox-content" rows="7"></textarea>
                    </div>
                    <p class="optaskBox-notice text-success none text-right"></p>
                </form>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">
                    关闭
                </button>

                <button type="button" class="btn btn-primary optask-btn">
                    提交
                </button>
            </div>

        </div>
    </div>
</div>
