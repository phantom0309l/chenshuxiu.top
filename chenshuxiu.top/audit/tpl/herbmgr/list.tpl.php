<?php
$pagetitle = "中药药材列表 Herbs";
$cssFiles = [
    $img_uri . "/v5/page/audit/herbmgr/list/list.css?v=2016123001",
]; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
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
            <div class="searchBar">
                <div class="mt10">
                  <a class="btn btn-success" target="_blank" href="/herbmgr/add">中药药材新建</a>
                </div>
            </div>
            <div class="col-md-1">
                <div class="herbIndexPanel">
                <?php foreach ( $herbarr as $p => $herbs ){?>
                    <a class="herbIndexBox" href="#herbPanel_<?= $p ?>">
                        <?= $p ?>(<?= count($herbs)?>)
                    </a>
                    <br />
                <?php }?>
                <div class="clear"></div>
                </div>

            </div>
            <div class="col-md-11">
                <?php foreach ( $herbarr as $p => $herbs ){?>
                    <div class="herbPanel">
                        <p class="herbPanelTitle" id="herbPanel_<?= $p ?>">
                            <?= $p ?> 部分 (<?= count($herbs)?>)
                        </p>

                        <div class="herbPanelBox">
                            <?php foreach ( $herbs as $herb ){?>
                                <a target="_blank" href="/herbmgr/modify?herbid=<?= $herb->id ?>">
                                    <div class="herbBox">
                                        <?= $herb->name ?>
                                        <br />
                                        <?= $herb->pinyin ?>
                                        <br />
                                        <?= $herb->py ?>
                                    </div>
                                </a>
                            <?php }?>
                            <div class="clear"></div>
                        </div>
                    </div>
                <?php }?>
            </div>

        </section>
    </div>
    <div class="clear"></div>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
