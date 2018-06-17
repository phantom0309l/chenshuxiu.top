<?php
$pagetitle = "请求日志列表 XUnitOfWork";
$cssFiles = []; // 也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = []; // 填写完整地址
?>
<?php include_once $tpl . '/_header.new.tpl.php'; ?>
<div class="col-md-12">
    <section class="col-md-12">
        <div class="searchBar">
            <form action="/xunitofworkmgr/list" method="get">
                <div class="mt10">
                    <label>tableno:</label>
                    <?php
                    echo HtmlCtr::getSelectCtrImp(CtrHelper::getXUnitOfWorkTablenoCtrArray(), 'tableno', $tableno, "f18");
                    ?>
                    <br />
                    <label>client_ip:</label>
                    <input type="text" name="client_ip" value="<?=$client_ip ?>">
                    <br />
                    <label>sub_domain:</label>
                        <?php
                        $arr = array(
                            'all' => 'all',
                            'admin' => 'admin',
                            'api' => 'api',
                            'audit' => 'audit',
                            'cron' => 'cron',
                            'da' => 'da',
                            'dapi' => 'dapi',
                            'dm' => 'dm',
                            'doctor' => 'doctor',
                            'domain' => 'domain',
                            'dwx' => 'dwx',
                            'ipad' => 'ipad',
                            'sys' => 'sys',
                            'www' => 'www',
                            'wwwroot' => 'wwwroot',
                            'wx' => 'wx');
                        echo HtmlCtr::getSelectCtrImp($arr, 'sub_domain', $sub_domain, "f18");
                        ?>
                        <br />
                    <label>action_name:</label>
                    <input style="width: 250px" type="text" name="action_name" value="<?=$action_name ?>">
                    <label>method_name:</label>
                    <input style="width: 250px" type="text" name="method_name" value="<?=$method_name ?>">
                    <br />
                    <label>sql类型: </label>
                        <?php
                        $arr = array(
                            'insert' => 'insert',
                            'update' => 'update',
                            'delete' => 'delete');
                        echo HtmlCtr::getCheckboxCtrImp($arr, 'sqltypes[]', $sqltypes, '');
                        ?>
                    </div>
                <div class="mt10">
                    <input type="submit" class="btn btn-success" value='筛选' />
                </div>
            </form>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered col-md-10">
                <thead>
                    <tr>
                        <td>id</td>
                        <td>createtime</td>
                        <td>client_ip</td>
                        <td>dev_user</td>
                        <td>sub_domain</td>
                        <td>action</td>
                        <td>method</td>
                        <td>加载实体数量</td>
                        <td>insert语句数量</td>
                        <td>update语句数量</td>
                        <td>delete语句数量</td>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($xunitofworks as $a) {
                        ?>
                            <tr>
                        <td>
                            <a target="_blank" href="/xobjlogmgr/list?xunitofworkid=<?= $a->id ?>"><?= $a->id ?></a>
                        </td>
                        <td><?= $a->createtime ?></td>
                        <td>
                            <a href="/xunitofworkmgr/list?tableno=<?= $a->randno ?>&client_ip=<?= $a->client_ip ?>"><?= $a->client_ip ?></a>
                        </td>
                        <td><?= $a->dev_user ?></td>
                        <td>
                            <a href="/xunitofworkmgr/list?tableno=<?= $a->randno ?>&sub_domain=<?= $a->sub_domain ?>"><?= $a->sub_domain ?></a>
                        </td>
                        <td style="word-wrap: break-word; word-break: break-all;">
                            <a href="/xunitofworkmgr/list?tableno=<?= $a->randno ?>&sub_domain=<?= $a->sub_domain ?>&action_name=<?= $a->action_name ?>"><?= $a->action_name ?></a>
                        </td>
                        <td style="word-wrap: break-word; word-break: break-all;">
                            <a href="/xunitofworkmgr/list?tableno=<?= $a->randno ?>&sub_domain=<?= $a->sub_domain ?>&action_name=<?= $a->action_name ?>&method_name=<?= $a->method_name ?>"><?= $a->method_name ?></a>
                        </td>
                        <td><?= $a->commit_load_cnt ?></td>
                        <td><?= $a->commit_insert_cnt ?></td>
                        <td><?= $a->commit_update_cnt ?></td>
                        <td><?= $a->commit_delete_cnt ?></td>
                    </tr>
                            <?php
                    }
                    ?>
                    <tr>
                        <td colspan="12" class="pagelink"><?php include $dtpl . "/pagelink.ctr.php"; ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>
</div>
<div class="clear"></div>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
