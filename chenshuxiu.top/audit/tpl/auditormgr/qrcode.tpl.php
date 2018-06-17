<?php
$pagetitle = "服务号列表 WxShops";
$cssFiles = []; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = []; //填写完整地址
?>
<?php include_once $tpl . '/_header.new.tpl.php'; ?>
    <div class="col-md-12">
        <section class="col-md-12">
            <div>
                <div class="border1">
                    <a href="https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=<?= $auditor->qr_ticket ?>" target="_blank">
                        <img src="https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=<?= $auditor->qr_ticket ?>" />
                    </a>
                </div>
            </div>
        </section>
    </div>
    <div class="clear"></div>

    <?php include_once $tpl . '/_footer.new.tpl.php'; ?>
