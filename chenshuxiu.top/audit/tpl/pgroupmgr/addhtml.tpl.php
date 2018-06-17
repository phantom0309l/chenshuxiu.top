<?php
/**
 * Created by PhpStorm.
 * User: lijie
 * Date: 16-5-23
 * Time: 下午7:11
 */
?>
<div id="showAddShell">
    <?php
    if (file_exists(dirname(__FILE__) . "/_" . $addtype . ".php")) {
        include $tpl . "/pgroupmgr/_{$addtype}.php";
    }
    ?>
</div>
