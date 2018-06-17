<div class="wxvoicemsg-block">
<?php $_url = $a->obj->voice->getUrl()?>
<div class="audio-container">
    <audio controls="controls">
        <source src="<?= $a->obj->voice->getMp3Url()?>" type="audio/mpeg">
        您的浏览器不支持audio标签
    </audio>
</div>
<span data-url="<?= $_url ?>" class="amrbtn btn btn-default collapse"><i class="si si-volume-2"></i> 播放</span>
<?php if($a->obj->content != ''){ ?>
	<a href="#" class="btn btn-default wxvoicemsg-btn" data-wxvoicemsgid="<?= $a->objid ?>"><i class="fa fa-save"></i> 保存 </a>
	<span class="text-danger wxvoicemsg-notice push-10-l"></span>

	<div class="form-group" style="margin-top : 20px; background-color : #555;">
		<div class="col-xs-12">
			<div class="form-material form-material-primary">
				<textarea class="form-control wxvoicemsg-content" id="faq-contact-msg" name="faq-contact-msg"><?= $a->obj->content ?></textarea>
				<label for="faq-contact-msg">语音识别结果：</label>
			</div>
		</div>
	</div>
<?php } ?>
</div>
