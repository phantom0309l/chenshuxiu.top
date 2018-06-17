<?php
$pagetitle = "新建疾病菜单";
$cssFiles = []; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = []; //填写完整地址
$pageStyle = <<<STYLE

STYLE;
$pageScript = <<<SCRIPT

SCRIPT;
$sideBarMini = false;

include_once $tpl . '/_header.new.tpl.php'; ?>
    <div class="col-md-12">
        <section class="col-md-12">
            <form action="/checkuptplmenumgr/adddiseasepost" method="post">
                <div class="table-responsive">
                    <table class="table table-bordered">
                    <tr>
                        <th width=140>绑定所属疾病:</th>
                        <td id="td-disease">
                            <?php echo HtmlCtr::getSelectCtrImp(CtrHelper::getDiseaseCtrArray(false), "diseaseid", $mydisease->id); ?>
                        </td>
                    </tr>
                    <tr>
                        <th>检查报告模板</th>
                        <td id="td-checkuptpl"></td>
                    </tr>
                    <tr>
                        <th>菜单</th>
                        <td id="td-menu">
                        <textarea cols=120 rows=12 name="content"></textarea>
                        </td>
                    </tr>
                    <tr>
                        <th></th>
                        <td>
                            <input id='input-submit' type="submit" class="submit" value="提交" />
                        </td>
                    </tr>
                </table>
                </div>
            </form>
        </section>
    </div>
    <div class="clear"></div>
<?php
$footerScript = <<<STYLE
    $(function(){
        function render(data) {
            var tdHtml = '';
            for (i in data) {
                tdHtml += '<span style="margin-right:10px;margin-bottom:10px;line-height:2;padding:5px;background-color:#eee">'+data[i].title + '&nbsp;&nbsp;' + data[i].ename + '</span>';
            }
            $('#td-checkuptpl').html(tdHtml);
        }
        var diseaseid = $('#td-disease select option:selected').val();
        $.ajax({
            type: "post",
                url: "/checkuptplmenumgr/checkuptplofdiseasejson",
                data:{"diseaseid" : diseaseid},
                dataType: "json",
                success : function(data){
                    console.log(data);
                    render(data);
                }
        });
        $(document).on('change', '#td-disease select', function(e){
            e.preventDefault();
            var diseaseid = $('#td-disease select option:selected').val();
            $.ajax({
                type: "post",
                    url: "/checkuptplmenumgr/checkuptplofdiseasejson",
                    data:{"diseaseid" : diseaseid},
                    dataType: "json",
                    success : function(data){
                        console.log(data);
                        render(data);
                    }
            });
        });
        $(document).on('click', '#add-menu', function(e) {
            e.preventDefault();
            var div = $('#div-menu').clone().attr('id', '');
            $('#td-div').append(div);
        })
        .on('click', '#remove-menu', function(e) {
            e.preventDefault();
            $('#td-div div:last').remove();
        })
    });
STYLE;
?>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
