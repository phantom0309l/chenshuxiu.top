<div class="optaskOneShell">
    <?php
    $patientname = $optask->patient->name;
    $pagetitle = "{$patientname}{$optask->optasktpl->title}详情";
    include $tpl . "/_pagetitle.php"; ?>
    <?php if( 5 == $optask->level ){ ?>
        <div class="optaskContent">
            <?= $optask->content ?>
            <p>先选择是否加入 sunflower 项目，加入 项目的患者再选择课程，然后关闭任务；</p>
        </div>
        <div>
            <p style="margin-top:10px;">患者标签：</p>
            <div>
                <?php
                $patientid = $optask->patient->id;
                 $patient_hezuo = Patient_hezuoDao::getOneByCompanyPatientid("Lilly", $patientid); ?>
                <span class="sunflowerBtn btn btn-default <?= $patient_hezuo instanceof Patient_hezuo ? 'btn-primary' : ''?>" data-patientid=<?= $patientid ?>>
                    sunflower项目
                </span>
                <span class="notSunflowerBtn btn btn-default" data-patientid=<?= $patientid ?>>
                    非sunflower项目
                </span>
            </div>
            <div class="choicePgroup-block <?= $patient_hezuo instanceof Patient_hezuo ? '' : 'none'?>">
                <?php
                $pgroup_subtypestrs = "";
                $drug_monthcnt_when_create = 1;
                $pgroup_arr = [];
                if($patient_hezuo instanceof Patient_hezuo){
                    $pgroup_subtypestrs = $patient_hezuo->pgroup_subtypestrs;
                    $drug_monthcnt_when_create = $patient_hezuo->drug_monthcnt_when_create;
                }
                $pgroup_arr = explode(",", $pgroup_subtypestrs);
                 ?>
                <p style="margin-top:10px;">可选课程：</p>
                <span class="choicePgroups btn btn-default <?= in_array("ABCTraining", $pgroup_arr)? "btn-primary":""?>" data-subtypestr="ABCTraining" data-patientid="<?= $patientid ?>">入门练习</span>
                <span class="choicePgroups btn btn-default <?= in_array("AdvancedTraining", $pgroup_arr)? "btn-primary":""?>" data-subtypestr="AdvancedTraining" data-patientid="<?= $patientid ?>">进阶练习</span>
                <span class="choicePgroups btn btn-default <?= in_array("PracticalTraining", $pgroup_arr)? "btn-primary":""?>" data-subtypestr="PracticalTraining" data-patientid="<?= $patientid ?>">实战应对</span>
                <div class="monthcntShell grayBgColorBox">
                    <label for="">已服择思达时长（月）：</label>
                    <select class="monthcnt js-select2" style="width:80px;">
                        <?php for ($i = 1; $i <= 60; $i++) { ?>
                            <option value="<?= $i ?>" <?= $drug_monthcnt_when_create == $i ? "selected" : ""?>>
                                <?= $i ?>
                            </option>
                        <?php } ?>
                    </select>
                    <button class="btn drugMonthcntBtn btn-success" data-patientid=<?= $patientid ?>><i class="fa fa-pencil"></i>设置</button>
                </div>
            </div>
            <div class="notSunflowerTag-block none">
                <?php
                $ptag1 = TagRefDao::getByObjtypeObjidTagid("Patient", $patientid, "176");
                $ptag2 = TagRefDao::getByObjtypeObjidTagid("Patient", $patientid, "177");
                $ptag3 = TagRefDao::getByObjtypeObjidTagid("Patient", $patientid, "178");
                $ptag4 = TagRefDao::getByObjtypeObjidTagid("Patient", $patientid, "179");
                 ?>
                <p style="margin-top:10px;">不加入sunflower项目的原因：</p>
                <span class="patientTagBtn btn btn-default push-5 btn-sm <?= $ptag1 instanceof TagRef ? 'btn-primary' : ''?>" data-tagid="176">电话失联</span>
                <span class="patientTagBtn btn btn-default push-5 btn-sm <?= $ptag2 instanceof TagRef ? 'btn-primary' : ''?>" data-tagid="177">非ADHD</span>
                <span class="patientTagBtn btn btn-default push-5 btn-sm <?= $ptag3 instanceof TagRef ? 'btn-primary' : ''?>" data-tagid="178">非择思达</span>
                <span class="patientTagBtn btn btn-default push-5 btn-sm <?= $ptag4 instanceof TagRef ? 'btn-primary' : ''?>" data-tagid="179">拒绝加入</span>
            </div>
        </div>
    <?php }else{ ?>
        <div class="optaskContent">
            <?= $optask->content ?>
        </div>
    <?php } ?>
</div>
