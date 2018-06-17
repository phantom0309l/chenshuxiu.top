<table width="100%" border="0" cellspacing="0" cellpadding="0" class="pages">
    <tr>
        <td align="right" valign="middle" style="padding-right: 15px;">
            <?php if ($pagelink && $pagelink->getTotalPage() > 1) { ?>

                <?php

                if ($pagelink->getTotalPage() > 0) {
                    ?>
                    [ 共 <?php echo $pagelink->getTotalRows(); ?> 条，<?php echo $pagelink->getPagesize(); ?> 条 / 页，第 <?php echo $pagelink->getPageNum(); ?>/<?php echo $pagelink->getTotalPage(); ?> 页 ] &nbsp; &nbsp;
                <?php } else { ?>
                    第 0/0 页
                <?php } ?>
                <?php
                echo $pagelink->getFirstPage() . " ";
                echo $pagelink->getPrePage() . " ";
                echo $pagelink->getNextPage() . " ";
                echo $pagelink->getLastPage();
                echo $pagelink->getSelectPage() . " ";
                echo $pagelink->getGotoPage($ctr_id_fix);
                ?>
                <?php
            } elseif ($pagelink && $pagelink->getTotalPage() < 2) {
                echo "共1页";
            } else {
                echo "无分页";
            }
            ?>
        </td>
    </tr>
</table>