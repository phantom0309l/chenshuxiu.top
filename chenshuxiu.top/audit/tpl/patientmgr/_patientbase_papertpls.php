
<!-- 医助代填量表 begin -->
<div class="TriggerBox">
    <div class="grayBgColorBox contentBoxTitle">
        <span class="patient_name_title">医助代填量表</span>
        <button class="TriggerBtn">展开量表列表</button>
    </div>
    <div class="TriggerContent none" id="papertpl_list">
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>分组</th>
                    <th>标题</th>
                    <th>填写</th>
                </tr>
            </thead>
            <tbody>
        <?php foreach($papertpls as $a){ ?>
                <tr>
                    <td><?=$a->groupstr ?></td>
                    <td><?=$a->title ?></td>
                    <td>
                        <a target="_blank" href="/papertplmgr/one4patient?papertplid=<?=$a->id ?>&patientid=<?=$patient->id ?>">填写</a>
                    </td>
                </tr>
        <?php } ?>
                </tbody>
        </table>
    </div>
</div>
<!-- 医助代填量表 end -->
