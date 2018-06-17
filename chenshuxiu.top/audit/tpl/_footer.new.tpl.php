</div>
<!-- END Main Container -->
<!-- Footer -->
<footer id="page-footer" class="content-mini content-mini-full font-s12 bg-gray-lighter clearfix">
    <div class="pull-right">
        <?php
        $costTime = 1000 * Debug::getCostTimeFromStart();
        echo "[ " . Debug::getUnitofworkId();
        echo " | sql " . intval(Debug::getSqltimesum()) . " ms";
        echo " | " . intval(Debug::$method_end / 1000) . " ";
        echo " &lt; " . intval(Debug::$commit_end / 1000) . " ";
        echo " &lt; " . intval($costTime / 1000) . " ms ]";
        ?>
        @方寸医生 版权所有
    </div>
</footer>
<!-- END Footer -->
</div>
<!-- END Page Container -->
<!-- Apps Modal -->
<!-- Opens from the button in the header -->
<div class="modal fade" id="apps-modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-sm modal-dialog modal-dialog-top">
        <div class="modal-content">
            <!-- Apps Block -->
            <div class="block block-themed block-transparent">
                <div class="block-header bg-primary-dark">
                    <ul class="block-options">
                        <li>
                            <button data-dismiss="modal" type="button">
                                <i class="si si-close"></i>
                            </button>
                        </li>
                    </ul>
                    <h3 class="block-title">Apps</h3>
                </div>
                <div class="block-content">
                    <div class="row text-center">
                        <div class="col-xs-6">
                            <a class="block block-rounded" href="index.html">
                                <div class="block-content text-white bg-default">
                                    <i class="si si-speedometer fa-2x"></i>
                                    <div class="font-w600 push-15-t push-15">Backend</div>
                                </div>
                            </a>
                        </div>
                        <div class="col-xs-6">
                            <a class="block block-rounded" href="frontend_home.html">
                                <div class="block-content text-white bg-modern">
                                    <i class="si si-rocket fa-2x"></i>
                                    <div class="font-w600 push-15-t push-15">Frontend</div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <!-- END Apps Block -->
        </div>
    </div>
</div>
<!-- END Apps Modal -->
<script>
    <?= $footerScript ?>

    $(function () {
        // TODO 实例，无用，可以删除
        /*
        ws.addEventCode();

        // 监听回调
        ws.watch('wsquickpass:pushMessage:after', function (d) {
            console.log('wsquickpass:pushMessage:after callback', d);
        });
        */

        if (typeof ws === 'object') {
            ws.connect();
        }
    });
</script>
</body>
</html>
