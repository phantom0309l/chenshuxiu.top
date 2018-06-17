<?php
$pagetitle = "标签关系新建";
$cssFiles = []; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = []; //填写完整地址
$pageStyle = <<<STYLE

STYLE;
$pageScript = <<<SCRIPT

SCRIPT;
$sideBarMini = false;

include_once $tpl . '/_header.new.tpl.php'; ?>
    <div class="col-md-12">
        <section class="col-md-12">
            <form action="/tagrefmgr/addpost" method="post">
                <input type="hidden" name="typestr" value="<?=$typestr ?>" />
                <div class="table-responsive">
                    <table class="table table-bordered">
                    <tr>
                        <th width='120'>目标类型</th>
                        <td>
                            <input type="hidden" name="objtype" value="<?=$objtype ?>" />
                        	<?= $objtype?>
                        </td>
                    </tr>
                    <tr>
                        <th>目标id</th>
                        <td>
                            <input type="hidden" name="objid" value="<?=$objid ?>" />
                        	<?= $objid?>
                        </td>
                    </tr>
                    <tr>
                        <th>标签分组</th>
                        <td>
                    		<?php
                    foreach (Tag::getTypeStrDefines() as $key => $value) {
                        if ($typestr == $key) {
                            ?>
                    		      	<a class="btn btn-success" href="/tagrefmgr/add?objtype=<?=$objtype ?>&objid=<?=$objid ?>&typestr=<?=$key ?>"><?=$key ?>:<?=$value ?></a>
                    		      <?php
                            continue;
                        }
                        ?>
                    		      <a class="tab-btn-highlight" href="/tagrefmgr/add?objtype=<?=$objtype ?>&objid=<?=$objid ?>&typestr=<?=$key ?>"><?=$key ?>:<?=$value ?></a>
                    		      <?php
                    }
                    ?>
                    	</td>
                    </tr>
                    <tr>
                        <th>标签</th>
                        <td>
                            <?= HtmlCtr::getSelectCtrImp($tagidnameArr, "tagid",66)?>
                        </td>
                    </tr>
                    <tr>
                        <th></th>
                        <td>
                            <input type="submit" class="btn btn-success" value="提交" />
                        </td>
                    </tr>
                </table>
                </div>
            </form>
        </section>
    </div>
    <div class="clear"></div>
<?php
$footerScript = <<<STYLE

STYLE;
?>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>