<style>
    .block_width{
        display: inline-block !important;
        width: 40% !important;
    }
</style>

<?php
if ($scheduletpl->getRevisitTktCntGtToday() > 0 || $scheduletpl->getScheduleCntGtToday() > 0) {
    include $tpl . "/scheduletplmgr/_modify_simple.php";
} else {
    include $tpl . "/scheduletplmgr/_modify.php";
}
?>
