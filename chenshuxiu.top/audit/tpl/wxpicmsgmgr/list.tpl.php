<?php
$pagetitle = '病历图';
$cssFiles = [
    $img_uri . "/static/css/jquery.fileupload.css",
    $img_uri . "/v3/audit_wxpicmsgmgr_list.css",
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
        <section class="container col-md-12">
            <div class="patientDetails searchBar">
                <span>患者姓名：<?=$patient->name?></span>
                <span>所属医生：<?=$patient->doctor->name?></span>
            </div>
            <div class="tagBox searchBar">
                添加标签：
                <input type="text" value="" name="name" id="tagName" />
                <button id="addTag">确定</button>
                <a href="#simplePicBox" class="btn btn-default" id="uploadcase">上传单张病历</a>
                <a href="/wxpicmsgmgr/batuploadcase?patientid=<?=$patient->id?>" class="btn btn-default" id="batuploadcase">批量上传病历</a>
            </div>
            <div class="tagClass">
                <a class="<?= ($tagid == 0) ? 'active':''; ?>" href="/wxpicmsgmgr/list?patientid=<?=$patient->id ?>">全部</a>

            <?php foreach($tags as $a){ ?>
                <a class="<?= ($a->id == $tagid) ? 'active':''; ?>" href="/wxpicmsgmgr/list?tagid=<?=$a->id ?>&patientid=<?=$patient->id ?>"><?=$a->name ?></a>
            <?php } ?>

            </div>
            <div class="picturesBox border1" id="single">
            <?php

            foreach ($wxpicmsgs as $a) {
                if (false == $a->picture instanceof Picture) {
                    continue;
                }
                ?>
                <div class="picturesBox-list clearfix">
                    <span class="fl wp20 pictureBox-cell">
                        <span class="preview">
                            <a target="_blank" href="<?= $a->picture->getSrc() ?>" title="<?= $a->picture->getSrc() ?>" data-gallery-txj="">
                                <img src="<?= $a->picture->getSrc(150,150,false); ?>">
                            </a>
                        </span>
                        <p class="name">
                            <a target="_blank" href="<?= $a->picture->getSrc() ?>" data-gallery-txj="">查看图片</a>
                        </p>
                    </span>
                    <span class="fl wp40 pictureBox-cell">
                        <ul class="tags">
                        <?php  foreach($tags as $tag){ ?>
                            <li class="<?= $a->isTagBy($tag->id)?"active":""; ?>" data-tagid="<?= $tag->id ?>" data-wxpicmsgid="<?= $a->id ?>"><?= $tag->name ?></li>
                        <?php  } ?>
                    </ul>
                    </span>
                    <span class="fl wp20 pictureBox-cell">
                        <button class="btn btn-danger delete" data-type="DELETE" data-id="<?= $a->id ?>">
                            <i class="glyphicon glyphicon-trash"></i>
                            <span>删除</span>
                        </button>
                        <input type="checkbox" name="delete" value="1" class="toggle">
                    </span>
                </div>
            <?php  } ?>
            </div>
            <div>
                <script src="<?=$img_uri?>/dist/js/showcase.min.js"></script>
                <div style="margin: 10px auto 20px auto;">
                    <span>上传单张图片</span>
                    <form action="/wxpicmsgmgr/uploadcasePost">
                        <input name="patientid" type="hidden" value="<?=$patient->id?>">
<?php
$picWidth = 150;
$picHeight = 150;
$pictureInputName = "pictureid";
$isCut = false;
$picture = null;
$objtype = "Auditor";
$objid = $myauditor->id;
$objsubtype = "WxPicMsg";
require_once ("$dtpl/picture.ctr.php");
?>
                        <input type="submit" value="提交" style="font-size: 16px; width: 150px;">
                    </form>
                </div>
                <a name="simplePicBox" id="simplePicBox"></a>
            </div>
        </section>
    </div>
    <div class="clear"></div>
<?php
$footerScript = <<<XXX
$(function(){
    $(".delete").on("click",function(){
        var me = $(this);
        var id = me.data("id");
        var url = "/wxpicmsg/removeJson";
        $.ajax({
            "type" : "post",
            "url" : url,
            "data" : "wxpicmsgid=" + id
        }).done(function(json){
            me.parents(".picturesBox-list").remove();
        });

    });

    //新增标签
    var tagName = $("#tagName");
    $("#addTag").on("click", function(){
        var tagVal = $.trim( tagName.val() );
        if( tagVal === ""){
            alert("请输入标签名");
            return;
        }
        $.ajax({
            "type" : "post",
            "url" : "/tagmgr/addJson",
            "data" : "name=" + tagVal
        }).done(function(json){
            window.location.href = window.location.href;
        });

    });

    //图片打标签
    $(".tags").on("click", 'li', function(){
        var me = $(this);
        var tagid = me.data("tagid");
        var wxpicmsgid = me.data("wxpicmsgid");
        if( me.hasClass("active") ){
            $.ajax({
                "type" : "post",
                "url" : "/wxpicmsg/removeTagRefJson",
                "data" : "tagid=" + tagid + "&wxpicmsgid=" + wxpicmsgid
            }).done(function(json){
                me.removeClass("active");
            }).fail(function(){
            });
            return;
        }
        me.addClass("active");
        $.ajax({
            "type" : "post",
            "url" : "/wxpicmsg/addTagRefJson",
            "data" : "tagid=" + tagid + "&wxpicmsgid=" + wxpicmsgid
        }).done(function(json){
        }).fail(function(){
            me.removeClass("active");
        });
    });

    //设置标签切换样式
    var pageInt = {
        init : function(){
            var me = this;
        },
        changeTagActive : function(){
            var tagClassNode = $(".tagClass");
            var search = location.search;
            var reg = /tag_id=(\d+).*/;
            var matchArr = search.match(reg);
            if( matchArr && matchArr[1] ){
                tagClassNode.find('[data-tagid="' + matchArr[1] + '"]').addClass("active").siblings().removeClass("active");
            }
        }
    };

    pageInt.init();
});
XXX;
?>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
