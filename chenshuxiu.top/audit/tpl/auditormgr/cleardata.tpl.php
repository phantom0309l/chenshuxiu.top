<?php
$pagetitle = "清除{$user->name}的测试数据";
$cssFiles = []; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = []; //填写完整地址
?>
<?php include_once $tpl . '/_header.new.tpl.php'; ?>

    <div class="col-md-12">
        <section class="col-md-12">
            <input type="hidden" value="<?= $user->id ?>" id="userid"/>
            <button class="btn btn-danger clearBtn">清除分组相关测试数据</button>
        </section>
    </div>
    <div class="clear"></div>
<?php
$footerScript = <<<XXX
        $(function(){
            var app = {
                canClick : true,
                init : function(){
                    var self = this;
                    self.handleClearData();
                },
                handleClearData : function(){
                    var self = this;
                    $(".clearBtn").on("click", function(){
                        var me = $(this);
                        if( !self.canClick ){
                            return;
                        }
                        self.canClick = false;
                        $.ajax({
                            url: '/auditormgr/ClearDataJson',
                            type: 'post',
                            dataType: 'text',
                            data: {userid: $("#userid").val()}
                        })
                        .done(function(str) {
                            self.canClick = true;
                            if(str=="ok"){
                                me.text("数据已清除");
                            }else{
                                alert('不能清除数据');
                            }
                        })
                        .fail(function() {
                        })
                        .always(function() {
                        });

                    })
                }
            };

            app.init();
        })
XXX;
?>

<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
