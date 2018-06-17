<style>

</style>
<!-- 模态框 -->
<div class="modal" id="picture-ocr" tabindex="-1" role="dialog" aria-hidden="true" style="">
    <div class="progress progress-striped active none">
        <div class="progress-bar progress-bar-success" id="progress-bar" role="progressbar"
             aria-valuenow="60" aria-valuemin="0" aria-valuemax="100">
            <span class="sr-only"></span>
        </div>
    </div>

    <div class="modal-dialog width-95">
        <div class="modal-content">
            <div class="block block-themed block-transparent remove-margin-b">
                <div class="block-header bg-primary">
                    <ul class="block-options">
                        <li>
                            <button data-dismiss="modal" type="button"><i class="si si-close"></i></button>
                        </li>
                    </ul>
                    <h3 class="block-title">图片ocr识别</h3>
                </div>

                <div class="block-content clear">
                    <section class="col-md-5 float-left min-height-800">
                        <div id="picture-box" style="background:#ccc;width:100%;height:100%;min-height: 900px;">
                            <img class="img-big none checkPic" id="" src="" data-url="">
                        </div>
                    </section>

                    <section id="table-section" class="col-md-5 float-right min-height-800">
                        <div class="margin-b-15">
                            <input type="hidden" value="">
                            <button class="btn btn-default btn-sm push-20-t" name="get-ocr-html" data-category="1">OCR(检查报告)</button>
                            <button class="btn btn-default btn-sm push-20-t" name="get-ocr-html" data-category="2">OCR(药盒识别)</button>
                            <button class="btn btn-default btn-sm push-20-t" name="get-ocr-html" data-category="3">OCR(处方单识)</button>
                        </div>
                        <div id="table-box" class="margin-b-15">

                        </div>
                    </section>
                </div>
            </div>
        </div>
    </div>
</div>