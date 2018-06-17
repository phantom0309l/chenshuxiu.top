<div class="optaskOneShell">
	<div class="optaskContent">
        <div style="padding: 20px">
        	<?php
                $patientcollection = $optask->obj;
                $data = json_decode($patientcollection->json_content, true);
            ?>
            <?php if ($patientcollection instanceof PatientCollection) { ?>
                <table class="table table-bordered " style="text-align: center;">
                    <tr>
                        <th width="150px">诊断日期</th>
                        <td><?= $data['step1']['thedate'] ?></td>
                    </tr>
                    <tr>
                        <th width="150px">诊断</th>
                        <td><?= $data['step1']['content'] ?></td>
                    </tr>
                    <tr>
                        <th>医嘱用药(图片)</th>
                        <td>
                            <div class="patientcollection_picbox">
                                <?php
                                   $basicpictures = $patientcollection->getBasicPictures();
                                   foreach ($basicpictures as $basicpicture) {
                                       ?>
                                            <div class="col-md-6">
                                                <div class="pipe-img" style="max-width:200px;overflow:hidden">
                                                    <img class="img-responsive viewer-toggle" data-url="<?= $basicpicture->picture->getSrc() ?>" src="<?=$basicpicture->picture->getSrc(800, 800)?>" alt="">
                                                </div>
                                            </div>
                                       <?php
                                   }
                            	?>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th>期望</th>
                        <td><?= $data['step3']['content']?></td>
                    </tr>
                </table>
            <?php } ?>
        </div>
    </div>
</div>
<script>
$(function(){
    $('.patientcollection_picbox').viewer({
        inline: false,
        url: 'data-url',
        class: 'viewer-toggle',
        navbar: false,
        scalable: false,
        fullscreen: false,
        shown: function (e) {
        }
    })
    $('.patientcollection_picbox').viewer('update');
});
</script>