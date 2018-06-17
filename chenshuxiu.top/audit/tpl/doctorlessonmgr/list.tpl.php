<?php
$pagetitle = "医生专栏";
$cssFiles = []; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = []; //填写完整地址
$pageStyle = <<<STYLE
.lessontype {
	height: 800px;
}

.item-box{
	height: 700px;
	OVERFLOW-Y: auto;
}

.item {
	border-bottom: 1px solid #CCCCCC;
    margin: 10px 0px;
    padding: 5px 0px 15px 0px;
    height: 70px;
    display: block;
    width: 100%;
	text-align: left;
    font-size: 14px;
    font-weight: normal;
    line-height: 20px;
}

.item-title {
	font-size: 20px;
	margin: 10px 0px;
	padding: 5px 0px;
	border-bottom: 1px solid #CCCCCC;
}

.item-r {
	// float: right;
	margin: 5px 2px;
}

.addnew {
	position: absolute;
	margin: 40px auto 20px auto;
	bottom: 10px;
	clear: both;
}
STYLE;
$pageScript = <<<SCRIPT

SCRIPT;
$sideBarMini = true;

include_once $tpl . '/_header.new.tpl.php'; ?>
<div class="col-md-12">
        <section class="col-md-12">
        <div class="p10 border1">
            医生列表：
            <?php
            foreach (CtrHelper::getDoctorHaveLessonCtrArray() as $k => $v) {
                ?>
                <a class="btn <?= $k == $doctorid ? 'btn-primary' : 'btn-default' ?>" href="/doctorlessonmgr/list?doctorid=<?= $k ?>"><?= $v ?></a>
            <?php } ?>
        </div>
            <div class="col-md-4 border1 text-align-center lessontype">
                <div class="text-align-center item-title">文章课文列表</div>
				<div class="item-box">
	            <?php
				if (count($lessons_content) > 0)
	                foreach ($lessons_content as $lesson) {
	                    ?>
	                    <div class="am-padding-top item clear">
		                    <span class="">
		                        <?= $lesson->title?>
		                    </span>
		                    <div class="item-r">
		                        <a class="btn btn-default item-button" href="/lessonmgr/modify?lessonid=<?= $lesson->id ?>">编辑</a>
		                        <button class="btn btn-default item-button lessonDelete" data-lessonid="<?= $lesson->id?>">删除</button>
		                    </div>
		                </div>
	                <?php } ?>
				</div>
	            <a class="btn btn-primary addnew" href="/lessonmgr/add?doctorid=<?= $doctorid ?>&courseid=<?=$course->id?>">新增文章课文</a>
            </div>
            <div class="col-md-4 border1 text-align-center lessontype">
                <div class="text-align-center item-title">音频课文列表</div>
				<div class="item-box">
	            <?php
				if (count($lessons_voice) > 0)
	                foreach ($lessons_voice as $lesson) {
	                    ?>
	                    <div class="item clear">
		                    <div class="">
		                        <?= $lesson->title?>
	                        </div>
		                    <div class="item-r">
		                        <a class="btn btn-default item-button" href="/voicemgr/modify?voiceid=<?= $lesson->voice->id ?>">编辑音频资源</a>
		                        <a class="btn btn-default item-button" href="/lessonmgr/modify?lessonid=<?= $lesson->id ?>">编辑</a>
		                        <button class="btn btn-default item-button lessonDelete" data-lessonid="<?= $lesson->id?>">删除</button>
		                    </div>
		                </div>
	                <?php } ?>
				</div>
	            <a class="btn btn-primary addnew" href="/lessonmgr/addvoice?doctorid=<?= $doctorid ?>&courseid=<?=$course->id?>">添加音频课文</a>
			</div>
            <div class="col-md-4 border1 text-align-center lessontype">
                <div class="text-align-center item-title">视频课文列表</div>
				<div class="item-box">
	            <?php
				if (count($lessons_video) > 0)
	                foreach ($lessons_video as $lesson) {
	                    ?>
	                    <div class="am-padding-top item clear">
		                    <span class="">
		                        <?= $lesson->title?>
	                        </span>
		                    <div class="item-r">
		                        <a class="btn btn-default item-button" href="/lessonmgr/modify?lessonid=<?= $lesson->id ?>">编辑</a>
		                        <button class="btn btn-default item-button lessonDelete" data-lessonid="<?= $lesson->id?>">删除</button>
		                    </div>
		                </div>
	                <?php } ?>
				</div>
                <a class="btn btn-primary addnew">添加视频课文</a>
			</div>
        </section>
	</div>
    <div class="clear"></div>
<?php
$footerScript = <<<STYLE
    $(document).on(
        "click",
        ".lessonDelete",
        function() {
            var me = $(this);
            var lessonid = me.data("lessonid");
            $.ajax({
                "type" : "post",
                "data" : {lessonid : lessonid},
                "dataType" : "text",
                "url" : "/lessonmgr/setuselessJson",
                "success" : function(data){
                    if(data == "ok"){
                        alert("成功哦");
                    }
                }
            });
        });
STYLE;
?>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
