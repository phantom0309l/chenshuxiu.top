<?php if(is_array($optasks_history)) {?>
<?php foreach( $optasks_history as $i => $optask ){?>
<div class="optask"  data-page="<?= $page ?>" data-totalPage="<?=$totalPage?>">
    <div style='height : auto' class="optask-t
        <?php if($isadhd){?>
            <?= $myauditor->hasBindPgroup($optask->pgroupid) ? "" : "optask-tNo"?>
        <?php }?>
        ">
        <?php if ($optask->status == 1) { ?>
        	<span class="text-danger">关闭：</span><?= substr($optask->donetime,0,10) ?>
    	<?php }else{?>
    		<span class="text-success">正在进行 </span>
		<?php }?>
		<span data-optaskid="<?=$optask->id?>"> [<?= $optask->auditor->name ?>]</span>
        </br>
        计划时间：<?= substr($optask->plantime,0,10) ?><span>[<?=$optask->getCreateAuditorName()?>]</span>
        <?= $optask->optasktpl->title ?>
        <span>[<?= $optask->user->shipstr ?>]</span>
        <span class="pull-right push-20-r"><i class="fa fa-angle-down angle"></i></span>
        <span class="collapse"><?=$optask->optasktplid?></span>
    </div>
    <div class="optask-c none" style="border-bottom:1px solid #e9e9e9;">
        <div class="optask-handlebox">
            <?php if( $optask->pipeid ){ ?>
            <span class="btn btn-default btn-sm trackPipe push-10-l" data-pipeid="<?= $optask->pipeid ?>">查看流中内容</span>
            <?php } ?>
        </div>
        <div class="tab">
            <ul class="tab-menu">
                <li class="active">内容</li>
                <li>备注</li>
            </ul>
            <div class="tab-content">
                <div class="tab-content-item">
                    <div class="optask-innershell">
                        <?php
                        $typestr = $optask->optasktpl->getTypestr();

                        if( file_exists(dirname ( __FILE__ )."/_optask/_".$typestr.".php") ){
                            $filename = $typestr;
                        }else {
                            $filename = "OpTaskBase";
                        }
                        include dirname ( __FILE__ )."/_optask/_".$filename.".php";
                        ?>

                        <div class="" style="border-top:1px dashed #5c90d2; margin-bottom: 20px;"></div>

                        <div class="pl10">
                            <textarea class="textarea_optlog_content textarea_two_rows"></textarea>
                            <br/>
                            <span class="btn btn-default nextfollowBtn" data-optaskid="<?= $optask->id ?>">提交新备注</span>
                        </div>
                        <?php
                        $optlogs = $optask->getOptLogs();
                        ?>
                        <div class="border-top-dashed mt10 p10">
                            <div>备注列表</div>
                            <div class="optlogTableShell mt10">
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                        <tr>
                                            <td>时间(逆序)</td>
                                            <td>内容</td>
                                        </tr>
                                        </thead>
                                        <tbody class="tbody_optlog">
                                        <?php foreach ($optlogs as $optlog) {
                                            ?>
                                            <tr>
                                                <td class="gray f12">
                                                    <?= $optlog->getCreateDayHi() ?>
                                                    <?= $optlog->auditor->name ?>
                                                </td>
                                                <td>
                                                    <?= $optlog->content ?>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-content-item none">
                    <div class="optask-innershell">
                        <div class="pl10">
                            <textarea class="textarea_optlog_content textarea_two_rows"></textarea>
                            <br/>
                            <span class="btn btn-default nextfollowBtn" data-optaskid="<?= $optask->id ?>">提交新备注</span>
                        </div>
                        <?php
                        $optlogs = $optask->getOptLogs();
                        ?>
                        <div class="border-top-dashed mt10 p10">
                            <div>备注列表</div>
                            <div class="optlogTableShell mt10">
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                        <tr>
                                            <td>时间(逆序)</td>
                                            <td>内容</td>
                                        </tr>
                                        </thead>
                                        <tbody class="tbody_optlog">
                                        <?php foreach ($optlogs as $optlog) {
//                                                                 $jsoncontent = json_decode($optlog->jsoncontent);
                                            ?>
                                            <tr>
                                                <td class="gray f12">
                                                    <?= $optlog->getCreateDayHi() ?>
                                                    <?= $optlog->auditor->name ?>
                                                </td>
                                                <td>
                                                    <?= $optlog->content ?>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php }} ?>
