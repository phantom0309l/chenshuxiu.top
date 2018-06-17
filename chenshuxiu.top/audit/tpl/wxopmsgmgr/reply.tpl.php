<div class="contentBoxTitle">患者:<?=$patient->name ?></div>
<div class="flow-time" align="left">
    <h4 class="flow-item-title">
    	<?=$myauditor->name ?>医助
        <span class="flow-time"></span>
        <span class="flow-writer">回复
<?php
echo $patient->doctor->name . "医生";
?>
        </span>
    </h4>
    <br />
    <textarea id="content" rows="4" cols="50"></textarea>
    <br />
    <a class="btn btn-primary reply-btn-wxopmsg mt10" href="#" data-patientid="<?=$patient->id ?>" data-doctorid="<?=$patient->doctorid ?>">回复</a>
</div>
