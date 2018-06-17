<?php
$pagetitle = "批量上传";
$cssFiles = []; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = []; //填写完整地址
$pageStyle = <<<STYLE
STYLE;
$pageScript = <<<SCRIPT
SCRIPT;
$sideBarMini = false;
?>
<?php include_once $tpl . '/_header.new.tpl.php'; ?>
    <div class="col-md-12">
        <section class="col-md-12">
            <div class="patientDetails searchBar">
                <span>患者姓名：<?=$patient->name?></span>
                <span>所属医生：<?=$patient->doctor->name?></span>
            </div>
            <form action="/wxpicmsgmgr/batuploadcasePost/">
                <input name="patientid" type="hidden" value="<?=$patient->id?>" />
            <?php
            $picWidth = 150;
            $picHeight = 150;
            $pictureInputName = "pictureids";
            $isCut = false;
            $objtype = "Auditor";
            $objid = $myauditor->id;
            $objsubtype = "WxPicMsg";
            require_once ("$dtpl/mult_picture.ctr.php");
            ?>
                <input type="submit" value="提交" />
            </form>
            <div class="clear"></div>
        </section>
    </div>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
