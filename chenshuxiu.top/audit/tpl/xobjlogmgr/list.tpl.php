<?php
$pagetitle = "实体日志列表 XObjLog";
$cssFiles = []; // 也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = []; // 填写完整地址
?>
<?php include_once $tpl . '/_header.new.tpl.php'; ?>
<div class="col-md-12">
    <section class="col-md-12">
        <div class="searchBar">
            <form action="/xobjlogmgr/list" method="get">
                <div class="mt10">
                    <label>按objtype筛选:</label>
                        <?= HtmlCtr::getSelectCtrImp(XObjLog::getObjtypeStr(),'objtype',$objtype, "f18"); ?>
                        <label>按objid:</label>
                    <input type="text" name="objid" value="<?= $objid == 0 ? '' : $objid ?>">
                    <input type="submit" class="btn btn-success" value='筛选' />
                </div>
            </form>
        </div>
        <div class="searchBar">
            <form action="/xobjlogmgr/list" method="get">
                <div class="mt10">
                    <label> xunitofworkid:</label>
                    <input style="width: 200px" type="text" name="xunitofworkid" value="<?= $xunitofworkid == 0 ? '' : $xunitofworkid ?>">
                    <input type="submit" class="btn btn-success" value='筛选' />
                </div>
            </form>
        </div>
            <?php
            if ($xunitofwork instanceof XUnitOfWork) {
                ?>
        <div class="searchBar row">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <tr>
                        <td>client_ip</td>
                        <td>dev_user</td>
                        <td>sub_domain</td>
                        <td>action_name</td>
                        <td>method_name</td>
                        <td>load</td>
                        <td>insert</td>
                        <td>update</td>
                        <td>delete</td>
                        <td>method_end</td>
                        <td>commit_end</td>
                        <td>page_end</td>
                    </tr>
                    <tr>
                        <td><?= $xunitofwork->client_ip ?></td>
                        <td><?= $xunitofwork->dev_user ?></td>
                        <td><?= $xunitofwork->sub_domain ?></td>
                        <td><?= $xunitofwork->action_name ?></td>
                        <td><?= $xunitofwork->method_name ?></td>
                        <td><?= $xunitofwork->commit_load_cnt ?></td>
                        <td><?= $xunitofwork->commit_insert_cnt ?></td>
                        <td><?= $xunitofwork->commit_update_cnt ?></td>
                        <td><?= $xunitofwork->commit_delete_cnt ?></td>
                        <td><?= $xunitofwork->method_end ?></td>
                        <td><?= $xunitofwork->commit_end ?></td>
                        <td><?= $xunitofwork->page_end ?></td>
                    </tr>
                    <tr>
                        <th>url</th>
                        <td colspan=11><?= $xunitofwork->url?></td>
                    </tr>
                    <tr>
                        <th>cookie</th>
                        <td colspan=11><?= $xunitofwork->cookie?></td>
                    </tr>
                    <tr>
                        <th>posts</th>
                        <td colspan=11><?= $xunitofwork->posts?></td>
                    </tr>
                    <tr>
                        <th>referer</th>
                        <td colspan=11><?= $xunitofwork->referer?></td>
                    </tr>
                </table>
            </div>
            <div class="mt10" width="60%" style="word-wrap: break-word; word-break: break-all;">
                <?php
                foreach ($objtypeobjids as $b) {
                    $tableName = strtolower($b['objtype']) . 's';
                    $where = 'id ' . (count(explode(',', $b['objids'])) > 1 ? " IN ({$b['objids']}) " : " = {$b['objids']} ");
                    echo "{$b['objtype']} : ({$b['objids']}) | SELECT * FROM {$tableName} WHERE {$where}; | <span style='color: red;'>DELETE FROM {$tableName} WHERE {$where};</span><br>";
                }
                ?>
            </div>
        </div>
                    <?php
            }
            ?>
            <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>
                            id
                            <span class="gray">createtime</span>
                        </th>
                        <th>xunitofworkid</th>
                        <th>type</th>
                        <th>objtype</th>
                        <th>objid</th>
                        <th>objver</th>
                        <th>content</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $i = 0;
                    foreach ($xobjlogs as $a) {
                        $i ++;
                        ?>
                    <tr>
                        <td><?= $i ?></td>
                        <td><?= $a->id ?><br />
                            <span class="gray"><?= $a->createtime ?></span>
                        </td>
                        <td>
                            <a target="_blank" href="/xobjlogmgr/list?xunitofworkid=<?= $a->xunitofworkid ?>"><?= $a->xunitofworkid ?></a>
                        </td>
                        <td>
                            <?= $a->getTypeStr(); ?>
                        </td>
                        <td>
                            <?= $a->objtype?>
                        </td>
                        <td>
                            <a target="_blank" href="/xobjlogmgr/list?objtype=<?= $a->objtype ?>&objid=<?= $a->objid ?>"><?= $a->objid ?></a>
                        </td>
                        <td><?= $a->objver ?></td>
                        <td width="50%" style="word-wrap: break-word; word-break: break-all;">
                        <?php
                        $contentarr = json_decode($a->content, true);
                        foreach ($contentarr as $k => $v) {
                            echo " [" . $k . "] => [" . $v . "]<br/>";
                        }
                        // print_r($contentarr);
                        ?>
                        </td>
                    </tr>
                <?php
                    }
                    ?>
                </tbody>
            </table>

            <?php
            if ($objtype != 'all' && $objid > 0 && false == $entity instanceof EntityBase) {
                ?>
                    <a class="btn btn-danger" href="/xobjlogmgr/reCreateEntityPost?objtype=<?=$objtype ?>&objid=<?=$objid ?>">实体[ <?=$objtype ?> <?=$objid ?> ]不存在, 点击还原</a>
            <?php
            }
            ?>
        </div>
    </section>
</div>
<div class="clear"></div>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
