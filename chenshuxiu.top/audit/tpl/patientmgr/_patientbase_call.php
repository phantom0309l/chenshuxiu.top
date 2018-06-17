<script>
    $('.btn-check-online-sit').on("click", function () {
        var me = $(this);
        var menu = me.siblings('ul.dropdown-menu');
        if (menu.is(":visible")) {
            menu.hide();
            return;
        }
        $.ajax({
            "type": "get",
            "data": {},
            "data-type": "JSON",
            "url": "/meetingmgr/onlineseatsjson",
            "success": function (res) {
                if (res.errno != 0) {
                    alert(res.errmsg);
                    return;
                }

                var str = '';
                $.each(res.data, function (index, one) {
                    var _class = 'text-success';
                    if (one.statusDesc != '空闲') {
                        _class = 'text-danger';
                    }
                    str += '<li><a href="javascript:">' + one.cname + '<span style="margin-left:20px;" class="' + _class + '">' + one.statusDesc + '</span></a></li>';
                });
                menu.html(str).show();
            }
        });

    })
</script>

<style>
    .popover-title {
        padding: 8px 14px;
        margin: 0;
        font-size: 12px;
        background-color: #f7f7f7;
        border-bottom: 1px solid #ebebeb;
        border-radius: 5px 5px 0 0;
    }

    .popover-content {
        text-align: center;
    }
</style>

<div class="mt10 J_call_box" style="background:#eee;padding:5px;border-radius:2px">
    <?php
    $linkmans = $patient->getLinkmans();

    foreach ($linkmans as $linkman) {
        ?>
        <a class="callpatient btn btn-warning btn-sm push-5-t" data-mobile="<?= $linkman->mobile ?>">
            <i class="si si-call-out"></i> &nbsp;&nbsp;<?= $linkman->shipstr ? $linkman->shipstr . ':' : '' ?> <?= $linkman->getMarkMobile(); ?>
        </a>
        <?php
    }
    ?>
    <div class="btn-group push-20-r">
        <button type="button" class="btn btn-success btn-sm dropdown-toggle btn-check-online-sit push-5-t">
            <i class="si si-earphones-alt"></i> 在线坐席 <span class="caret"></span>
        </button>
        <ul class="dropdown-menu" role="menu">
        </ul>
    </div>
    <a class="btn btn-default btn-sm push-5-t" target="_blank" href="/linkmanmgr/listofpatient?patientid=<?= $patient->id ?>"><i
                class="fa fa-pencil"></i> 备用联系人</a>
    <a class="btn btn-default btn-sm push-5-t" target="_blank" href="http://www.clink.cn/"><i class="si si-login"></i> 坐席登录</a>
</div>

<script>
    var cdr_no1 = '<?= $myauditor->cdr_no1 ?>',
        cdr_no2 = '<?= $myauditor->cdr_no2 ?>';

    $(function () {
        //给Body加一个Click监听事件
        $('body').on('click', function (event) {
            var target = $(event.target);
            if (!target.hasClass('popover') //弹窗内部点击不关闭
                && target.parent('.popover-content').length === 0
                && target.parent('.popover-title').length === 0
                && target.parent('.popover').length === 0
                && !target.hasClass('callpatient')) {
                //弹窗触发列不关闭，否则显示后隐藏
                $('.callpatient').popover('hide');
            }
        });

        var el_callpatient = $('.callpatient');
        el_callpatient.each(function () {
            var me = $(this);
            var mobile = this.dataset.mobile;

            $(this).popover({
                trigger: 'click', //触发方式
                placement: 'bottom', //top, bottom, left or right
                title: "选择拨打方式",//设置 弹出框 的标题
                html: true, // 为true的话，data-content里就能放html代码了
                content: getPopoverContent(mobile)//这里可以直接写字符串，也可以 是一个函数，该函数返回一个字符串；
            });

            me.data('popover', true);
        });

        el_callpatient.on('show.bs.popover', function () {
            console.log('show.bs.popover');
            $(this).siblings('.callpatient').popover('hide');
        });

        function getPopoverContent(mobile) {
            var content = '';
            if (cdr_no1 !== '') {
                content += '<button class="btn btn-info btn-sm push-10-r" type="button" onclick="cdr_callpatient(' + mobile + ', \'cdr_no1\')">IP话机</button>';
            }
            if (cdr_no2 !== '') {
                content += '<button class="btn btn-info btn-sm" type="button" onclick="cdr_callpatient(' + mobile + ', \'cdr_no2\')">手机</button>';
            }
            return content;
        }
    })

    function cdr_callpatient(mobile, type) {
        $.post("/meetingmgr/cdrcalljson", {mobile: mobile, type: type}, function (data, status) {
            alert(data);
        });
    }
</script>