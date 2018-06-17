<?php $serviceorder = $a->obj;
if ($serviceorder instanceof ServiceOrder) { ?>
    <div class="optaskContent">
        <h5>
            服务类订单
        </h5>
        <p class="push-10-t pb10 border-b">
            <span class='label label-primary'>
                <?php
                switch ($serviceorder->serviceproduct_type) {
                    case 'quickpass':
                        echo '快速通行证服务';
                        break;
                    default:
                        break;
                }
                ?>
            </span>
        </p>
    </div>
<?php } ?>