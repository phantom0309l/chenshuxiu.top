<?php
/**
 * Created by PhpStorm.
 * User: lijie
 * Date: 16-5-23
 * Time: 上午10:53
 */
?>
<div class="col-md-12" style="border: 1px solid #CCC">
    <div style="display: block">
        <?php
        $pagetitle = "添加";
        if ($pgroup->inpapertplid) {
            $pagetitle = "更改绑定";
        }
        $pagetitle .= "量表（出组任务）";
        include $tpl . "/_pagetitle.php";
        ?>
    </div>
    <div style="display: block">
        <form action="/pgroupmgr/addoutpapertplpost" method="post" onsubmit="return check()">
            <input style="display: none" name="pgroupid" value="<?= $pgroup->id?>">
            <?php
            $num = 0;
            foreach ($papertpls as $a) {
                ?>
                <div class="clearfix" style="background-color:<?= $num % 2 ? "#FFF":"#F2F2F2"?>;">
                <input id="modify_checked" style="margin-left: 15px; width: 20px; height: 20px" type="radio" name="papertplid" value="<?=$a->id?>">
                <label><?=$a->title?></label>
                <a href="/xquestionsheetmgr/one?xquestionsheetid=<?=$a->xquestionsheetid?>" class="btn btn-success" style="float: right">查看</a>
            </div>
            <?php
                $num ++;
            }
            ?>
            <br />
            <br />
            <br />
            <div style="text-align: center; margin: 8px 0 8px 0">
                <input class="btn btn-success" type="submit" value="确定" style="font-size: 16px; width: 60px" />
            </div>
        </form>
    </div>
</div>
