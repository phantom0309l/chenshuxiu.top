<?php
$pagetitle = "手工修正单";
$cssFiles = []; // 也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = []; // 填写完整地址
$pageStyle = <<<STYLE
STYLE;
$pageScript = <<<SCRIPT
SCRIPT;
$sideBarMini = false;
?>
<?php include_once $tpl . '/_header.new.tpl.php'; ?>
<div class="col-md-12">
    <section class="col-md-12">
        <form action="/fixordermgr/addpost" method="post">
            <input type="hidden" name="accountid" value="<?= $account->id; ?>" />
            <div class="table-responsive">
                <table class="table table-bordered">
                <tr>
                    <th width='140'>账户ID</th>
                    <td>
                        <?=$account->id?>
                    </td>
                </tr>
                <tr>
                    <th>账户类型</th>
                    <td>
                        <?=$account->getCodeDesc();?> (<?=$account->code;?>)
                    </td>
                </tr>
                <tr>
                    <th>账户所属人</th>
                    <td>
                        <?=$account->user->name;?> | <?=$account->user->patient->name;?>
                    </td>
                </tr>
                <tr>
                    <th>账户余额(元)</th>
                    <td class="red">
                        <?=$account->getBalance_yuan();?>
                    </td>
                </tr>
                <tr>
                    <th>操作人(auditor)</th>
                    <td>
                        <?=$myauditor->name?>
                    </td>
                </tr>
                <tr>
                    <th>金额</th>
                    <td>
                        <input type="text" name="amount_yuan" value="0" />
                        元 (正值为充值, 负值为扣款)
                    </td>
                </tr>
                <tr>
                    <th>原因</th>
                    <td>
                        <textarea rows="2" cols="80" name="reason"></textarea>
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
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
