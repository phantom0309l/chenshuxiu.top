<?php
$pagetitle = "合作医生修改 Doctor_hezuo";
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
        <form action="/doctor_hezuomgr/modifypost" method="post">
            <input type="hidden" name="doctor_hezuoid" value="<?= $doctor_hezuo->id ?>"/>
            <div class="table-responsive">
                <table class="table table-bordered">
                <tr>
                    <th width='140'>id</th>
                    <td><?= $doctor_hezuo->id ?></td>
                </tr>
                <tr>
                    <th>创建时间</th>
                    <td><?= $doctor_hezuo->createtime ?></td>
                </tr>
                <tr>
                    <th>公司</th>
                    <td><?= $doctor_hezuo->company ?></td>
                </tr>
				<tr>
                    <th>医生名字</th>
                    <td><?= $doctor_hezuo->name ?></td>
                </tr>
				<tr>
                    <th>doctor_code</th>
                    <td><?= $doctor_hezuo->doctor_code ?></td>
                </tr>
				<tr>
                    <th>入第一个患者时间</th>
                    <td><?= $doctor_hezuo->first_patient_date ?></td>
                </tr>
				<tr>
                    <th>是否能够推送首次入患者消息（一血）</th>
                    <td><?= $doctor_hezuo->canSendFirstPatientMsg() ? "是" : "否" ?></td>
                </tr>
				<tr>
                    <th>是否能够发送调研问卷</th>
                    <td><?= $doctor_hezuo->canPushSurveyMsg() ? "是" : "否" ?></td>
                </tr>
				<tr>
                    <th>是否能够每两周推送患者报告</th>
                    <td><?= $doctor_hezuo->canSendNoticeMsg() ? "是" : "否" ?></td>
                </tr>
				<tr>
                    <th>医生点击了sunflower知情同意</th>
                    <td><?= 1 == $doctor_hezuo->is_clicked_agree ? "是" : "否" ?></td>
                </tr>
				<tr>
                    <th>合作医生开通时间</th>
                    <td><?= $doctor_hezuo->starttime ?></td>
                </tr>
				<tr>
                    <th>方寸医生doctorid</th>
                    <td><?= $doctor_hezuo->doctorid ?></td>
                </tr>
				<tr>
                    <th>是否开通合作</th>
                    <td><?= 1 == $doctor_hezuo->status ? "已开通" : "未开通" ?></td>
                </tr>
                <tr>
                    <th>性别</th>
                    <td>
						<?= 0 == $doctor_hezuo->sex ? "未知" : (1 == $doctor_hezuo->sex ? "男" : "女") ?>
                    </td>
                </tr>
                <tr>
                    <th>技术职称</th>
                    <td>
						<input type="text" name="title1" value="<?= $doctor_hezuo->title1 ?>"/>
					</td>
                </tr>
                <tr>
                    <th>行政职称</th>
                    <td>
						<input type="text" name="title2" value="<?= $doctor_hezuo->title2 ?>"/>
					</td>
                </tr>
				<tr>
                    <th>医院名称</th>
                    <td>
                        <input type="text" name="hospital_name" value="<?= $doctor_hezuo->hospital_name ?>"/>
                    </td>
                </tr>
				<tr>
                    <th>部门科室</th>
                    <td>
                        <input type="text" name="department" value="<?= $doctor_hezuo->department ?>"/>
                    </td>
                </tr>
                <tr>
                    <th>市场人员名</th>
                    <td>
                        <input type="text" name="marketer_name" value="<?= $doctor_hezuo->marketer_name ?>"/>
                    </td>
                </tr>
                <tr>
                    <th>城市名</th>
                    <td>
                        <input type="text" name="city_name_bymarketer" value="<?= $doctor_hezuo->city_name_bymarketer ?>"/>
                    </td>
                </tr>
                <tr>
                    <th>大区</th>
                    <td>
                        <input type="text" name="area_bymarketer" value="<?= $doctor_hezuo->area_bymarketer ?>"/>
                    </td>
                </tr>
			<?php
                if($doctor_hezuo->json != ""){
                $json_arr = json_decode($doctor_hezuo->json);
                    foreach ($json_arr as $k => $v) { ?>
                    <tr>
                        <th><?= $k ?></th>
                        <td>
                            <input type="text" name="json[<?= $k ?>]" value="<?= $v ?>"/>
                        </td>
                    </tr>
			<?php
                    }
                }?>
                <tr>
                    <th style="padding-top: 12px;">
						<div class="btn btn-primary addField">新增属性</div>
					</th>
                    <td>
                        <div class="btn btn-primary submit">提交</div>
                    </td>
                </tr>
            </table>
            </div>
        </form>
    </section>
</div>
<div class="clear"></div>
<?php
$footerScript = <<<XXX
    $(function(){
        var app = {
            init : function(){
                var self = this;
                self.handleAddField();
                self.handleSubmit();
            },
            handleAddField : function(){
                $(document).on("click", ".addField", function(){
                    var me = $(this);
					var htmlstr = "<tr><th><input class='fieldkey' type='text'/></th><td><input class='fieldvalue' type='text' name='' value=''/></td></tr>";
					me.parents("tr").before(htmlstr);
                })
            },
            handleSubmit : function(){
                $(document).on("click", ".submit", function(){
                    var me = $(this);
					var fieldkeyNodes = $(".fieldkey");
					fieldkeyNodes.each(function(){
				        var item = $(this);
				        if('' != item.val()){
				            var fieldvalue = item.parents("tr").find(".fieldvalue");
							var fieldname = "json[" + item.val() + "]";
				            fieldvalue.attr("name", fieldname);
				        }
				    });
					$("form").submit();

                })
            }
        }
        app.init();
    })
XXX;
?>

<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
