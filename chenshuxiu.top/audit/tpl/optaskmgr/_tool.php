<!-- 一排按钮 begin-->
<div id="goPatientBase" name="goPatientBase" class="colorBox remove-margin-t" style="line-height: 150%">
    <?php
    $patientid = $patient->id;
    ?>
    <div>
        <a href="/wxpicmsgmgr/list?patientid=<?= $patientid ?>" id="showcase" target="_blank" class="btn btn-default btn-sm collapse">病历图</a>
        <a href="/papermgr/list?patientid=<?= $patientid ?>" id="showAllScales" target="_blank" class="btn btn-default btn-sm">量表列表</a>
        <a href="/checkupmgr/list?patientid=<?= $patientid ?>" id="showAllScales" target="_blank" class="btn btn-default btn-sm collapse">检查报告列表</a>
        <a href="/xanswersheetmgr/list?patientid=<?= $patientid ?>" id="showAllScales" target="_blank" class="btn btn-default btn-sm collapse">答卷列表(没用)</a>
        <a href="/lessonuserrefmgr/list?patientid=<?= $patientid ?>" target="_blank" class="btn btn-default btn-sm">课程/观察</a>
        <a href="/patientmedicinetargetmgr/detailofpatient?patientid=<?= $patientid ?>" target="_blank" class="btn btn-default btn-sm">患者核对用药</a>
        <a href="/revisitrecordmgr/list?patientid=<?= $patientid ?>" target="_blank" class="btn btn-default btn-sm">患者门诊历史</a>
        <a href="/optaskmgr/listnew?patientid=<?= $patientid ?>" target="_blank" class="btn btn-default btn-sm collapse">任务列表</a>
        <a href="/patientmgr/index?patientid=<?= $patientid ?>" target="_blank" class="btn btn-default btn-sm collapse">控制台(beta)</a>
        <a href="/patientremarkmgr/list?patientid=<?=$patientid?>" target="_blank" class="btn btn-default btn-sm collapse">PatientRemark</a>
        <a href="/reportmgr/add?patientid=<?=$patientid?>" target="_blank" class="btn btn-default btn-sm">汇报</a>
        <a href="#" data-toggle="modal" data-target="#cancer-calculate" data-original-title="Edit Client" class="btn btn-default btn-sm">肿瘤营养计算</a>
        <?php
        $mult_diseaseids = Disease::getMultDiseaseIds();
        if (in_array($patient->diseaseid, $mult_diseaseids)) { ?>
            <a href="/padrmonitormgr/list?patientid=<?=$patientid?>" target="_blank" class="btn btn-default btn-sm">不良反应监测</a>
        <?php } ?>
        <?php if (1010 == $patient->doctorid) {  // 只针对秦燕医生开通 ?>
            <a href="/certicanmgr/list4patient?patientid=<?=$patientid?>" target="_blank" class="btn btn-default btn-sm">秦燕项目</a>
        <?php } ?>
        <a href="/dc_patientplanmgr/list?patientid=<?=$patientid?>" target="_blank" class="btn btn-default btn-sm">项目</a>
        <?php if ($patient->diseaseid == 26) { ?>
            <a href="/plan_qdxzmgr/list4patient?patientid=<?=$patientid?>" target="_blank" class="btn btn-default btn-sm">气道狭窄呼吸量表</a>
        <?php } ?>
        <a href="/auditoroplogmgr/list?patientid=<?=$patientid?>" target="_blank" class="btn btn-default btn-sm">运营操作日志</a>
    </div>
    <div class="mt10">
        <button class="patientStatus-btn btn btn-primary btn-sm">状态更变</button>
        <span class="red" style="margin-left: 20px;">[<?=$patient->getStatusStr()?>]</span>
        <div class="patientStatus-box none">
            <p class="bg-warning mt10 p5"><?= $patient->auditremark ?></p>
            <textarea class="form-control patientStatus-auditremark" rows="3" data-patientid="<?=$patient->id?>"></textarea>
            <div>
                标记并添加更变历史：
                <?php if ($patient->is_live == 1) { ?>
                    <button class="patientStatus-setClose btn btn-danger">下线</button>
                <?php } ?>
                <?php if ($patient->is_live == 0) { ?>
                    <button class="patientStatus-setRelive btn btn-danger">复活</button>
                <?php } ?>
<!--                <button class="patientStatus-setDead btn btn-danger">死亡</button>-->
            </div>
        </div>
    </div>
</div>

<!-- 模态框 -->
<div class="modal" id="cancer-calculate" tabindex="-1" role="dialog" aria-hidden="true" style="display: none; padding-right: 17px;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="block block-themed block-transparent remove-margin-b">
                <div class="block-header bg-primary">
                    <ul class="block-options">
                        <li>
                            <button data-dismiss="modal" type="button"><i class="si si-close"></i></button>
                        </li>
                    </ul>
                    <h3 class="block-title">肿瘤营养计算</h3>
                </div>
                <div class="block-content">
                    <div class="form-group">
                        <label class="" for="title">身高(cm)</label>
                        <div class="">
                            <input class="form-control" type="text" id="height" name="height" value="" placeholder="请输入身高">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="" for="content">体重(kg)</label>
                        <div class="">
                            <input class="form-control" type="text" id="weight" name="weight" value="" placeholder="请输入体重">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="" for="content">年龄(岁)</label>
                        <div class="">
                            <input class="form-control" type="text" id="age" name="age" value="" placeholder="请输入年龄">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="" for="content">状态</label>
                        <div class="">
                            <?php
                                $arr = [
                                    'lieUp' => '卧床',
                                    'active' => '活动'
                                ];
                                echo HtmlCtr::getRadioCtrImp4OneUi($arr, 'status', 'lieUp');
                            ?>
                        </div>
                    </div>
                    <div class="form-group" >
                        <label class="" for="content">计算数据:</label>
                        <div class="">
                            <textarea class="form-control" id="caaculate-data" rows="6" placeholder=""></textarea>
                        </div>
                    </div>
                    <div class="form-group" >
                        <label class="" for="content">信息输出:</label>
                        <div class="">
                            <textarea class="form-control" id="show" rows="10" placeholder=""></textarea>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-sm btn-default" type="button" data-dismiss="modal">关闭</button>
                <button class="btn btn-sm btn-primary" type="button" id="cancer-submit" data-dismiss="modal"><i class="fa fa-check"></i>计算</button>
            </div>
        </div>
    </div>
</div>

<script>
    $(function () {
		$("#cancer-submit").on('click', function(){
			var height = $("#height").val();
			var weight = $("#weight").val();
			var age = $("#age").val();
			var status = $("input[name='status']:checked").val();

// 			alert("身高:" + height + "\n" + "体重:" + weight + "\n" + "年龄:" + age + "\n" + "状态:" + status + "\n");

			// 理想体重
			var goodWight = height - 105;

			// BMI
			var BMI = weight / Math.pow(height / 100, 2);
			var BMI = Math.round(BMI * 10) / 10;

			// 单位体重所需热量
			var unit_heats = {
				'thin_lieUp' : 25,
				'thin_active' : 30,
				'normal_lieUp' : 25,
				'normal_active' : 30,
				'fat_lieUp' : 20,
				'fat_active' : 25
			};
			var type = '';
			var typestr = '';
			if (BMI <= 18.5) {
				type = 'thin';
				typestr = '消瘦';
			} else if (BMI < 24) {
				type = 'normal';
				typestr = '正常';
			} else {
				type = 'fat';
				typestr = '肥胖';
			}
			var type = type + '_' + status;
			var unitHeat = unit_heats[type];

			// 全天所需热量（千卡）
			var dayHeat = goodWight * unitHeat;

			// 各类饮食摄入（交换份）
			var unit_heats = {
				'<1400' : {
					'sum' : 14,
					'main' : 5,
					'vegetable' : 1,
					'fruit' : 1,
					'Meat' : 4,
					'bean' : 1,
					'milk' : 1,
					'Grease' : 1
				},
				'<1600' : {
					'sum' : 16,
					'main' : 6,
					'vegetable' : 1,
					'fruit' : 1,
					'Meat' : 5,
					'bean' : 1,
					'milk' : 1,
					'Grease' : 1
				},
				'<1800' : {
					'sum' : 18,
					'main' : 7,
					'vegetable' : 1,
					'fruit' : 1,
					'Meat' : 6,
					'bean' : 1,
					'milk' : 1,
					'Grease' : 1
				},
				'<2000' : {
					'sum' : 20,
					'main' : 8,
					'vegetable' : 1,
					'fruit' : 1,
					'Meat' : 7,
					'bean' : 1,
					'milk' : 1,
					'Grease' : 1
				},
				'<2200' : {
					'sum' : 22,
					'main' : 9,
					'vegetable' : 1,
					'fruit' : 1,
					'Meat' : 8,
					'bean' : 1,
					'milk' : 1,
					'Grease' : 1
				},
				'>=2200' : {
					'sum' : 24,
					'main' : 10,
					'vegetable' : 1,
					'fruit' : 1,
					'Meat' : 9,
					'bean' : 1,
					'milk' : 1,
					'Grease' : 1
				},
			};
			if (dayHeat < 1400) {
				type = '<1400';
			} else if (dayHeat < 1600) {
				type = '<1600';
			} else if (dayHeat < 1800) {
				type = '<1800';
			} else if (dayHeat < 2000) {
				type = '<2000';
			} else if (dayHeat < 2200) {
				type = '<2200';
			} else {
				type = '>=2200';
			}
			var allswaps = unit_heats[type];
			var allswapstr = allswaps['sum'] + " " + allswaps['main'] + " " + allswaps['vegetable'] + " " + allswaps['fruit'] + " " + allswaps['Meat'] + " " + allswaps['bean'] + " " + allswaps['milk'] + " " +allswaps['Grease'] + " ";

			var calculateStr = "理想体重:" + goodWight + "公斤\n";
			calculateStr += "单位体重所需热量:" + unitHeat + "千卡\n";
			calculateStr += "全天所需需热量:" + dayHeat + "千卡\n";
			calculateStr += "全天所需需热量:" + allswaps['sum'] + "交换份\n";
			calculateStr += "各类饮食摄入(交换份):";
			calculateStr += "主食" + allswaps['main'] + "份、";
			calculateStr += "蔬菜" + allswaps['vegetable'] + "份、";
			calculateStr += "水果" + allswaps['fruit'] + "份、";
			calculateStr += "肉蛋类" + allswaps['Meat'] + "份、";
			calculateStr += "豆类" + allswaps['bean'] + "份、";
			calculateStr += "奶类" + allswaps['milk'] + "份、";
			calculateStr += "油脂" + allswaps['Grease'] + "份。\n";

			var showStr = "您的BMI为" + BMI + "属于" + typestr;
			showStr += ",您的理想体重为" + goodWight + "公斤";
			showStr += ",每日所需热量为" + dayHeat + "千卡";
			showStr += ",换算交换份为" + allswaps['sum'] + "。";
			showStr += "具体为";
			showStr += "主食" + allswaps['main'] + "份、";
			showStr += "蔬菜" + allswaps['vegetable'] + "份、";
			showStr += "水果" + allswaps['fruit'] + "份、";
			showStr += "肉蛋类" + allswaps['Meat'] + "份、";
			showStr += "豆类" + allswaps['bean'] + "份、";
			showStr += "奶类" + allswaps['milk'] + "份、";
			showStr += "油脂" + allswaps['Grease'] + "份。\n";
			showStr += "常见食物交换份对照:\n";
			showStr += "1份主食 约合1/3碗米饭（11cm直径碗）、约合2/3碗米粥（11cm直径碗）、约合手工馒头1/3个（7cm直径、6cm高）、约合烙饼1/8张（一牙）、约合半个烧饼（10cm直径）\n";
			showStr += "1份蔬菜约合500g（一斤）\n";
			showStr += "1份水果 约合橙、橘子、苹果、猕猴桃、菠萝、李子、香梨、桃子、樱桃200g 、鲜枣100g、柿子荔枝125g\n";
			showStr += "1份蛋类约合 1个鸡蛋\n";
			showStr += "1份肉类约合 生重1两左右\n";
			showStr += "1份乳类约合160g 牛奶、约合100g酸奶\n";
			showStr += "1份油脂约合一汤匙食用油\n";
			showStr += "1份豆类约合北豆腐一小块（10cm*10cm*2cm）\n";

			$("#caaculate-data").val(calculateStr);

			$("#show").val(showStr);
			return false;
		});

        $("#sendmedicinemsg").on("click", function () {
            var patientid = $(this).data("patientid");

            $.ajax({
                "type": "get",
                "data": {
                    patientid: patientid
                },
                "dataType": "text",
                "url": "/patientmedicinesheetmgr/sendmsgJson",
                "success": function (data) {
                    if (data == 'success') {
                        alert("消息已发送给患者");
                    }
                }
            });
        });
        $(".patientStatus-setClose").on("click", function () {
            if (confirm("确认删除该患者？")) {
                var me = $(this);
                $.ajax({
                    "type" : "get",
                    "data" : {
                        "patientid" : $(".patientStatus-auditremark").data('patientid'),
                        "auditremark" : $(".patientStatus-auditremark").val()
                    },
                    "url" : "/patientmgr/offlineJson",
                    "success" : function(data) {
                        if (data == 'ok') {
                            me.addClass("btn-default").removeClass("btn-danger");
                        }
                    }
                });
            }
        });
        $(".patientStatus-setRelive").on("click", function () {
            if (confirm("确认复活该患者？")) {
                var me = $(this);
                $.ajax({
                    "type" : "get",
                    "data" : {
                        "patientid" : $(".patientStatus-auditremark").data('patientid'),
                        "auditremark" : $(".patientStatus-auditremark").val()
                    },
                    "url" : "/patientmgr/revivejson",
                    "success" : function(data) {
                        if (data == 'ok') {
                            alert("复活成功！");
                            me.hide();
                        }
                    }
                });
            }
        });
        $(".patientStatus-setDead").on("click", function () {
            if (confirm("确认删除该患者？")) {
                var me = $(this);
                $.ajax({
                    "type" : "get",
                    "data" : {
                        "patientid" : $(".patientStatus-auditremark").data('patientid'),
                        "auditremark" : $(".patientStatus-auditremark").val()
                    },
                    "url" : "/patientmgr/deadJson",
                    "success" : function(data) {
                        if (data == 'ok') {
                            me.addClass("btn-default").removeClass("btn-danger");
                        }
                    }
                });
            }
        });
        $(".patientStatus-btn").on("click", function () {
            $(".patientStatus-box").toggle();
        });

    });
</script>
<!-- 一排按钮 end-->
