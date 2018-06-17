<?php
$pagetitle = "新建医生检查报告模板";
$pageStyle = <<<STYLE

STYLE;
$pageScript = <<<SCRIPT

SCRIPT;
$sideBarMini = false;

include_once $tpl . '/_header.new.tpl.php'; ?>

    <div class="col-md-12">
        <section class="col-md-12">
            <form action="/checkuptplmgr/addpost" method="post">
                <div class="table-responsive">
                    <table class="table table-bordered">
                    <tr>
                        <th>绑定所属疾病:</th>
                        <td id="td-disease">
                            <?php echo HtmlCtr::getSelectCtrImp(CtrHelper::getDiseaseCtrArray(false), "diseaseid", $mydisease->id); ?>
                        </td>
                    </tr>
                    <tr>
                        <th>默认模板:</th>
                        <td>
                            <select id="checkuptplid" name="checkuptplids[]" multiple="multiple" size=10>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th width=140>医生:</th>
                        <td>
                            <select id="td-doctor" name="doctorid">
                            </select>
<?php if(0) {?>
                            <?php echo HtmlCtr::getSelectCtrImp(CtrHelper::toDoctorCtrArray(),"doctorid",$doctorid,"doctorselect"); ?>
                        </td>
                    </tr>
                    <tr>
                        <th>是否显示 在约复诊中:</th>
                        <td>
                            <?php echo HtmlCtr::getRadioCtrImp(array(0=>'不显示',1=>'显示'), 'is_in_tkt', 0,'')?>
                        </td>
                    </tr>
                    <tr>
                        <th>是否有问卷:</th>
                        <td>
                            <?php echo HtmlCtr::getRadioCtrImp(array(0=>'没有',1=>'有'), 'is_in_admin', 0,'')?>
                        </td>
                    </tr>
                    <tr>
                        <th>是否在约复诊中默认被选中:</th>
                        <td>
                            <?php echo HtmlCtr::getRadioCtrImp(array(0=>'不是',1=>'是'), 'is_selected', 0,'')?>
                        </td>
                    </tr>
                    <tr>
                        <th>摘要:</th>
                        <td>
                            <textarea name="brief" rows="4" cols="40"></textarea>
                        </td>
                    </tr>
                    <tr>
                        <th>内容:</th>
                        <td>
                            <textarea name="content" rows="4" cols="40"></textarea>
<?php } ?>
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
		/* $(".doctorselect").on("change",function(){
			var doctorid = $(this).val();

			var url = location.pathname + '?doctorid=' + doctorid;
            window.location.href = url ;
		}); */
		/* $(".add").on("click",function(){
			var dose = $(".dose").val();
			var drug = $("#dose").val();

			var str = "<span class='drug-dose' style='border:1px solid #0000ff; margin-left:10px;'>"+drug+""+dose+"</span>";
			$(".doseshowlist").append(str);
			return false;
		}); */

            //初始化
            var diseaseid = $('#td-disease select option:selected').val();
            $.ajax({
                type: "post",
                    url: "/checkuptplmgr/defaultcheckuptplanddoctorofdiseaseJson",
                    data:{"diseaseid" : diseaseid},
                    dataType: "json",
                    success : function(data){
                        renderSelect(data);
                    }
            });
            function renderSelect(data) {
                var checkupTplHtml = '';
                console.log(data.checkuptpls);
                if (data.checkuptpls.length > 0) {
                    for (i in data.checkuptpls) {
                        var d = data.checkuptpls[i];
                        checkupTplHtml += '<option value="' + d.id + '">'+ d.title +'</option>';
                    }
                } else {
                    checkupTplHtml = '<option value="">请选择模板</option>';
                }
                $('#checkuptplid').html(checkupTplHtml);
                var doctorHtml = '';
                if (data.doctors.length > 0) {
                    for (i in data.doctors) {
                        var k = data.doctors[i];
                        doctorHtml += '<option value="' + k.id + '">'+ k.name +'</option>';
                    }
                } else {
                    doctorHtml = '<option value="">请选择医生</option>';
                }
                $('#td-doctor').html(doctorHtml);
            }
            $(document).on('change', '#td-disease select', function(e) {
                var diseaseid = $(this).val();
                $.ajax({
                    type: "post",
                    url: "/checkuptplmgr/defaultcheckuptplanddoctorofdiseaseJson",
                    data:{"diseaseid" : diseaseid},
                    dataType: "json",
                    success : function(data){
                        renderSelect(data);
                    }
                });
            });//---endof document click
            $(document).on('click', '#input-submit', function(e) {
                e.preventDefault();
                var val = $('#checkuptplid').val();
                if (val == null || val[0] == "") {
                    alert('请选择默认的检查报告模板，如没有，请前去添加之，谢谢！');
                    return false;
                }
                if ($('select[name=doctorid]').val() == "") {
                    alert('请选择医生');
                    return false;
                }
                $('form').submit();
            });
	});
STYLE;
?>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
