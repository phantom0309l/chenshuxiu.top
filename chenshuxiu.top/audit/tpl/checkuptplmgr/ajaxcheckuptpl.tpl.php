<form class="form-horizontal" action="/checkuptplmgr/addofdoctorpost" method="post">
    <input type="hidden" name="checkuptplid" value="<?= $checkuptpl->id ?>">
    <input type="hidden" name="doctorid" value="<?= $doctor->id ?>">
    <input type="hidden" name="diseaseid" value="<?= $disease->id ?>">
    <div class="block block-bordered" id="tpl_block">
        <div class="block-header bg-gray-lighter">
            <h3 class="block-title"><?= $checkuptpl->title ?></h3>
        </div>
        <div class="block-content scroll-y">
            <div class="table-responsive">
                <table class="table table-bordered">
                <thead>
                <tr>
                    <th>问题</th>
                    <th class="tc" style="width: 130px;">类型</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $questions = [];
                if ($xquestionsheet instanceof XQuestionSheet) {
                    $questions = $xquestionsheet->getQuestions();
                }
                foreach ($questions as $key => $question) {
                    $index = $key + 1
                    ?>
                    <tr>
                        <td><?= $question->getTitle() ?></td>
                        <td class="tc" style="width: 130px;"><?= $question->getTypeDesc() ?></td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
            </div>
        </div>
        <div class="block-footer tc">
            <button class="btn btn-minw btn-success J_submit" type="submit">保存</button>
        </div>
    </div>
</form>