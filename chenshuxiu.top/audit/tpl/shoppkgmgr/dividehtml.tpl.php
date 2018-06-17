<div class="">
    <form id="divideForm" class="divideForm">
        <div class="table-responsive">
            <input type="hidden" id="shoppkgid" value="<?= $shopPkg->id ?>" name="shoppkgid"/>
            <input type="hidden" id="shoppkgnum" value="<?= $shopPkgNum ?>" name="shoppkgnum"/>
            <table class="table table-bordered">
                <tr>
                    <th>

                    </th>
                    <?php for ($i = 0; $i < $shopPkgNum; $i++) { ?>
                        <th>配送单<?= $i + 1 ?></th>
                    <?php } ?>
                </tr>
                <?php foreach ($shopPkgItems as $shopPkgItem) {
                    $shopProduct = $shopPkgItem->shopproduct;
                    ?>
                    <tr>
                        <td><?= $shopProduct->title ?></td>
                        <?php for ($i = 0; $i < $shopPkgNum; $i++) { ?>
                            <td>
                                <?php if (0 == $i) { ?>
                                    <input class="form-control divide-input divide-read" readonly type="text" id="val-digits"
                                           name="shopproduct[<?= $shopProduct->id ?>][<?= $i ?>]" placeholder="输入自然数"
                                           value="<?= $shopPkgItem->cnt ?>">
                                <?php } else { ?>
                                    <input class="form-control divide-input" type="text" id="val-digits"
                                           name="shopproduct[<?= $shopProduct->id ?>][<?= $i ?>]"
                                           data-prevalue="0"
                                           placeholder="输入自然数" value="0">
                                <?php } ?>
                            </td>
                        <?php } ?>
                    </tr>
                <?php } ?>
            </table>
        </div>
        <!--        <p class="optaskBox-notice text-success none text-right"></p>-->
    </form>
</div>
<script>
    $(function () {
        $(".divide-input").bind("input propertychange", function () {
            var me = $(this);
            var input_read = me.parents("tr").find(".divide-read");
            var temp = input_read.val();
            var preValue = me.data("prevalue");
            var newValue = me.val();

            //输入为空时
            if ('' == newValue) {
                input_read.val(parseInt(temp) + parseInt(preValue));
                me.data("prevalue", 0);
                return;
            }

            //正则判断输入是否是个自然数
            if ((/^[1-9]*[0-9]{1}$/.test(newValue))) {
                if (input_read.val() >= newValue - preValue) {
                    input_read.val(temp - (newValue - preValue));
                    me.data("prevalue", newValue);
                } else {
                    alert("要拆分的数量超出了配送单1的量，先退回去些。");
                    me.val(preValue);
                }
            } else {
                alert("请输入自然数！");
                me.val(preValue);
            }
        });
    });
</script>

