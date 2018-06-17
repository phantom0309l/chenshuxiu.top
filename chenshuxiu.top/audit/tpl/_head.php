<meta name="viewport" content="initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=0">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<!-- 新 Bootstrap 核心 CSS 文件 -->
<link rel="stylesheet" href="//cdn.bootcss.com/bootstrap/3.3.5/css/bootstrap.min.css">
<link rel="stylesheet" href="<?= $img_uri ?>/static/css/blueimp-gallery.min.css">
<?php if(0){ ?>
<!-- 可选的Bootstrap主题文件（一般不用引入） -->
<link rel="stylesheet" href="//cdn.bootcss.com/bootstrap/3.3.5/css/bootstrap-theme.min.css">
<?php } ?>
<!-- jQuery文件。务必在bootstrap.min.js 之前引入 -->
<script src="//cdn.bootcss.com/jquery/1.11.3/jquery.min.js"></script>
<!-- 最新的 Bootstrap 核心 JavaScript 文件 -->
<script src="//cdn.bootcss.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
<script src="<?= $img_uri ?>/static/js/okzoom.js"></script>
<script src="<?= $img_uri ?>/static/js/vendor/jquery-ui.draggable.min.js"></script>
<script type="text/javascript">
$(".navbar-toggle").on("click", function () {
    var navNode = $("#navbar");
    if (navNode.is(":visible")) {
        navNode.slideUp(200);
    } else {
        navNode.slideDown(200);
    }
});
</script>
<script src="<?=$img_uri ?>/static/js/laydate5/laydate.min.js?ver=20180103"></script>
<script src="<?=$img_uri ?>/v3/cym.js"></script>
<script src="<?=$img_uri ?>/v3/js/xwenda.js"></script>
<script>
	$(function(){
        $(document).on(
            "click",
            ".calendar",
            function () {
                if ($(this).data('laydate') != 'init') {
                    $(this).data('laydate', 'init');
                    var value = $(this).val();
                    if (value == '0000-00-00' || value == '0000-00-00 00:00:00') {
                        value = new Date();
                        $(this).val(value.Format('YYYY-MM-DD'));
                    }
                    laydate.render({
                        elem: this,
                        value: value,
                        show: true
                    });
                }
            });

		$(document).on(
				"change",
				"#selectDoctor",
				function(){
                    var val = parseInt( $(this).val() );
                    //var url = location.pathname + '?doctorid=' + val ;
                    var url = val==0 ? location.pathname : location.pathname + '?doctorid=' + val ;
                    window.location.href = url ;
                });
                $('.modal-dialog').draggable({ cursor: "move"});
    });
</script>
<link rel="stylesheet" href="<?=$img_uri ?>/v3/audit_base.css?v=20160823">
<link rel="stylesheet" href="<?=$img_uri ?>/v3/audit_xsheet.css">
