<?php
$timeSpan1 = Debug::getCostTimeFromStart();
$timeSpan1 = XUtility::trimTimeSpan($timeSpan1);
$sqltimesum1 = Debug::getSqltimesum();
?>
<div class="footer">
    @<?= date("Y") ?> 方寸医生
    [ MethodEnd : <?=$sqltimesum0?> / <?=$timeSpan?> ]
    [ PageEnd : <?=$sqltimesum1 ?> / <?=$timeSpan1?> ]
</div>
