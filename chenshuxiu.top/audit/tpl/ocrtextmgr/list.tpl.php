<?php
$pagetitle = "腾讯OCR接口测试 OCRText";
$cssFiles = [
    $img_uri . "/vendor/oneui/js/plugins/select2/select2.min.css",
    $img_uri . "/vendor/oneui/js/plugins/select2/select2-bootstrap.css",
    $img_uri . '/v5/plugin/viewer/viewer.fangcun.min.css?v=20170829',
]; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = [
    $img_uri . "/vendor/oneui/js/plugins/select2/select2.full.min.js",
    $img_uri . '/v5/plugin/viewer/viewer.fangcun.min.js?v=20170829'
]; //填写完整地址

$pageStyle = <<<STYLE
    .checkPic {
        width: 600px;
    }
    .col-md-6 {
        width: 100%;
    }
STYLE;
$pageScript = <<<SCRIPT

SCRIPT;
$sideBarMini = false;

include_once $tpl . '/_header.new.tpl.php'; ?>
<div class="col-md-12">
    <section class="col-md-12">
        <div style="display: inline-block;">
            <label for="">标记类型：</label>
            <?php echo HtmlCtr::getSelectCtrImp(PatientPicture::getInspectionReportTypes(), 'type', $type, 'f18'); ?>
        </div>

        <div style="display: inline-block;">
            <button id="click_all">全部点击(检查报告)</button>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                <tr>
                    <td width="50">id</td>
                    <td width="100">图片</td>
                    <td width="500">ocr</td>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($patientPictures as $picture) { ?>
                    <tr>
                        <td><?= $picture->id ?></td>
                        <td>
                            <section class="col-md-6 content-left">
                                <div id="picture-box" style="background:#ccc;width:100%;max-height:800px;">
                                    <img class="img-big checkPic"
                                         src="https://photo.fangcunyisheng.com/<?= $picture->obj->picture->picname ?>.<?= $picture->obj->picture->picext ?>"
                                         data-url="https://photo.fangcunyisheng.com/<?= $picture->obj->picture->picname ?>.<?= $picture->obj->picture->picext ?>">
                                </div>
                            </section>

                        </td>
                        <td>
                            <input type="hidden" name="patient-picture-id" value="<?=$picture->id?>">
                            <button class="getTxtBtn" but_name="report" category="1">OCR(检查报告)</button>
                            <button class="getTxtBtn" category="2">OCR(药盒识别)</button>
                            <button class="getTxtBtn" category="3">OCR(处方单识)</button>
                            <button class="remove_dom" category="4"> X</button>
                            <div class="result_table"></div>
                        </td>
                    </tr>
                <?php } ?>
                <tr>
                    <td colspan="9" class="pagelink"><?php include $dtpl . "/pagelink.ctr.php"; ?></td>
                </tr>
                </tbody>
            </table>
        </div>
    </section>
</div>


<div class="clear"></div>
<?php
$footerScript = <<<STYLE
        $(function(){
            $('.img-big').viewer({
                inline: true,
                url: 'data-url',
                navbar: false,
                scalable: false,
                fullscreen: false,
                shown: function (e) {
                },
            });
        });
        
        $(function () {
           	$(document).on("click", ".getTxtBtn", function() {
                var url = $(this).parent('td').prev().children('section').children('div').children('img').attr('src')
                var category = $(this).attr('category')
				var picId = $('input[name="patient-picture-id"]').val()
				var that = $(this)
				that.parent('td').children('div').html(' ')
				
				$.ajax({
				    type    :  'post',
				    url     :  '/ocrtextmgr/OcrDataForOneHtml',
				    data    :  {
				            'url' : url,
				            'type': category,
				            'picId': picId,
				            'isTest': 1
				    },
				    dataType:  'html',
				    success : function (data) {
				        that.parent('td').children('div').append(data)
				    }
				})
			})
			
			$(document).on("change","#type",function() {
			    var type = $(this).val()
                window.location.href = "http://audit.fangcunhulian.cn/ocrtextmgr/list?type="+type
			})
            
                       
     $(document).on("click",'.remove_dom',function() {
                $(this).parent('td').parent('tr').remove()
            })
      
            $(document).on("click",'#click_all',function() {
                $('button[but_name="report"]').click()
            })
        })
STYLE;
?>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
