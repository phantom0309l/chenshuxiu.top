<?php
$pagetitle = "自动回复消息修改";
$cssFiles = []; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = []; //填写完整地址
?>
<?php include_once $tpl . '/_header.new.tpl.php'; ?>

<div class="col-md-12">
        <section class="col-md-10">
        <form action="/msgtemplatemgr/modifypost" method="post">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <tr>
                        <th width='140'>疾病</th>
                        <td>
                            <input type="hidden" name="msgtemplateid" value="<?=$msgtemplate->id?>" />
                            <p><?=$msgtemplate->disease->name?></p>
                        </td>
                    </tr>
                    <tr>
                        <th>使用的医生</th>
                        <td>
                        <?php
                        if ($msgtemplate->doctor instanceof Doctor) {
                            echo "{$msgtemplate->doctor->name} {$msgtemplate->doctor->id}";
                        } else {
                            echo "无特定医生";
                        }
                        ?>
                    </td>
                    </tr>
                    <tr>
                        <th>
                            ename
                        </th>
                        <td>
                        <p><?=$msgtemplate->ename; ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            ename(类型)
                        </th>
                        <td>
                        <?php $enameArr = MsgTemplate::getEnameArr();?>
                        <div>
                            <?= $enameArr['ename_type'][$msgtemplate->ename]['title']?>
                        </div>
                        </td>
                    </tr>
                    <tr>
                        <th>标题</th>
                        <td>
                            <input type="text" name="title" value="<?= $msgtemplate->title ?>"  style="width: 30%;"/>
                            <p>只是给运营看的</p>
                        </td>
                    </tr>
                    <tr>
                        <th>展示在用户端的格式</th>
                        <td>
                        <?php
                            $user = $myauditor->user;
                            $wxuser = $user->getMasterWxUser();

                            if($enameArr['ename_type'][$msgtemplate->ename]['send_by_custom']){
                        ?>
                                <p>文本消息</p>
                                <textarea name="content" cols="80" rows="10"><?= $msgtemplate->content ?></textarea>
                        <?php
                            }
                            if($enameArr['ename_type'][$msgtemplate->ename]['send_by_template']){
                                if($wxuser instanceof WxUser){
                                    foreach ($enameArr['ename_type'][$msgtemplate->ename]['wxtemplate_enames'] as $k => $wxtemplate_ename) {
                                        $wxtemplate = WxTemplateDao::getByEname($wxuser->wxshopid, $wxtemplate_ename);
                                        if($wxtemplate instanceof WxTemplate){
                        ?>
                                            <p>模版消息</p>
                                            <textarea name="content" cols="80" rows="10"><?= $wxtemplate->content ?></textarea>
                        <?php
                                        }
                                    }
                                }
                            }
                        ?>
                        </td>
                    </tr>
                    <tr>
                        <th>通知内容文案</th>
                        <td>
                            <textarea id="content" name="content" style="width: 50%; height: 200px;"><?= $msgtemplate->content ?></textarea>
                            <p class="enameDescription"><?=$enameArr['ename_type'][$msgtemplate->ename]['description']?></p>
                        </td>
                    </tr>
                    <tr>
                        <th></th>
                        <td>
                            <span class='btn btn-success btn-test' data-msgtemplateid="<?= $msgtemplate->id ?>">发送给自己测试</span>
                            <p> <?= $enameArr['ename_type'][$msgtemplate->ename]['send_by_custom'] ? '文本消息' : '' ?> </p>
                            <p> <?= $enameArr['ename_type'][$msgtemplate->ename]['send_by_template'] ? '模版消息' : '' ?> </p>
                        </td>
                    </tr>
                    <tr>
                        <th></th>
                        <td>
                            <input type="submit" class="btn btn-success" value="修改" />
                        </td>
                    </tr>
                </table>
            </div>
                <a class='btn btn-danger' href="/msgtemplatemgr/deletepost?msgtemplateid=<?= $msgtemplate->id ?>">删除</a>
            </form>
        </section>
    </div>
    <div class="clear"></div>
    <script>
        $(function() {
            $(".btn-test").on("click",function () {
                var me = $(this);
                var msgtemplateid = me.data('msgtemplateid');
                var contentNode = $("#content");
                var content = $.trim(contentNode.val());

                $.ajax({
                    type: "post",
                    url: "/msgtemplatemgr/testjson",
                    data: {
                        "msgtemplateid": msgtemplateid,
                        "content": content,
                    },
                    dataType: "text",
                    success: function (data){
                        if(data == 'ok'){
                            alert("发送成功");
                        }
                    }
                });
            });
        });
    </script>
    <?php include_once $tpl . '/_footer.new.tpl.php'; ?>
