<?php
$pagetitle = "加号单列表 RevisitTkts";
$cssFiles = []; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = []; //填写完整地址
?>
<?php include_once $tpl . '/_header.new.tpl.php'; ?>
<div class="col-md-12">
        <section class="col-md-6 content-left">
                <div class="searchBar">
                    <form action="/revisittktmgr/list" method="get">
                        <div class="mt10">
                            <label for="">模糊搜索 : </label>
                            <input type="text" name="word" value="<?= $word ?>" />
                            (手机号|患者名) (不为空则其他条件失效)
                        </div>
                        <br>
                        <div style="display: inline-block;">
                            <label for="">医生：</label>
                    		<?php echo HtmlCtr::getSelectCtrImp(CtrHelper::getDoctorCtrArray($mydisease->id),"doctorid",$doctorid,'f18'); ?>
            			</div>
                        <div class="mt10">
                            <label for="">状态：</label>
                    		<?php
                    $arr = array(
                        '-1' => '全部',
                        '0' => '无效',
                        '1' => '有效');
                    echo HtmlCtr::getSelectCtrImp($arr, 'status', $status, 'f18');
                    ?>
                            <label for="">关闭：</label>
                            <?php
                            $arr = array(
                                '-1' => '全部',
                                '1' => '关闭',
                                '0' => '未关闭');
                            echo HtmlCtr::getSelectCtrImp($arr, 'isclosed', $isclosed, 'f18');
                            ?>
                            <label for="">审核状态：</label>
                            <?php
                            $arr = array(
                                '-1' => '全部',
                                '0' => '审核中',
                                '1' => '审核通过',
                                '2' => '未通过');
                            echo HtmlCtr::getSelectCtrImp($arr, 'auditstatus', $auditstatus, 'f18');
                            ?>
            			</div>
                        <div class="mt10">
                            <label for="">时间:</label>
                            从
                            <input type="text" class="calendar" style="width: 100px; height: 27px;" name="fromdate" value="<?= $fromdate ?>" />
                            到
                            <input type="text" class="calendar" style="width: 100px" name="todate" value="<?= $todate ?>" />
                            &nbsp;
                            <button class="btn_style4" id="cleardate">清空日期</button>
                        </div>
                        <div class="mt10">
                            <input type="submit" class="btn btn-success" value="组合筛选">
                        </div>
                    </form>
                </div>
            <div class="table-responsive">
                <table class="table table-bordered tdcenter">
                    <thead>
                        <tr>
                            <td>id</td>
                            <td>患者</td>
                            <td>所属医生</td>
                            <td>创建时间</td>
                            <td>预约时间</td>
                            <td>创建人</td>
                            <td>患者确认状态</td>
                            <td>状态</td>
                            <td>关闭</td>
                            <td>审核状态</td>
                            <td>操作</td>
                        </tr>
                    </thead>
                    <tbody>
            		<?php foreach ($revisittkts as $a ){ ?>
                		<tr>
                            <td><?=$a->id ?></td>
                            <td>
                                <?php
                                    if ($a->patient instanceof Patient) {
                                        echo $a->patient->getMaskName();
                                    }
                                ?>
                            </td>
                            <td><?=$a->doctor->name ?></td>
                            <td><?=$a->getCreateDay() ?></td>
                            <td id="show_thedate"><?=$a->thedate ?></td>
                            <td><?=$a->getCreatebyStr() ?></td>
                            <td><?=$a->getPatient_confirm_statusStr();?></td>
                            <td class="status-<?=$a->id ?>"><?=$a->status ? '有效':'无效' ?></td>
                            <td><?=$a->getIsclosedStr() ?></td>
                            <td class="auditstatus-<?=$a->id ?>"><?=$a->getDescStep(); ?></td>
                            <td>
                                <a data-revisittktid="<?= $a->id ?>" class="showRevisitTktOneHtml revisittktid-<?=$a->id ?>">详情</a>
                            </td>
                        </tr>
              		<?php } ?>
    			</tbody>
                </table>
            </div>
            </section>
            <section class="col-md-6 content-right pb10">
                    <div id="RevisitHtmlShell" class="contentBoxBox"></div>
            </section>
        </div>
    <div class="clear"></div>
<?php
$footerScript = <<<XXX
$(function(){
	$(document).on("click", ".showRevisitTktOneHtml", function() {
		$(".content-right").show();
		var me = $(this);
		var revisittktid = me.data("revisittktid");

		var auditstatus = $(".auditstatus-" + revisittktid).text();

		$("#RevisitHtmlShell").html('');

		$.ajax({
			"type" : "get",
			"data" : {
				revisittktid : revisittktid
			},
			"dataType" : "html",
			"url" : "/revisittktmgr/oneHtml",
			"success" : function(data) {
				$("#RevisitHtmlShell").html(data);

				if(auditstatus == '未通过'){
					$("#audit-reason").show();
					$(".audit-btn-refuse-submit").val("修改拒绝原因");
				}
			}
		});
	});

	$(document).on("click", ".audit-btn-pass", function() {
		var me = $(this);
		var revisittktid = me.data("revisittktid");

        $.ajax({
            type: "post",
            url: "/revisittktmgr/passJson",
            data:{"revisittktid" : revisittktid},
            dataType: "text",
            success : function(url){
                $(".auditstatus-" + revisittktid).text("已通过");
                $(".status-" + revisittktid).text("有效");
            	//点击左侧的查看
        		$(".revisittktid-" + revisittktid).click();
            }
        });
    });

	$(document).on("click", ".audit-btn-refuse-submit", function() {
		var me = $(this);
		var revisittktid = me.data("revisittktid");

        var auditremark = $("#auditremark").val();

        var alertstr = $(this).val();

        if (confirm("确定" + alertstr + "?")){
            $.ajax({
                type: "post",
                url: "/revisittktmgr/refuseJson",
                data:{"revisittktid" : revisittktid , "auditremark" : auditremark},
                dataType: "text",
                success : function(){
                    if(alertstr != '修改拒绝原因'){
                    	$(".auditstatus-" + revisittktid).text("未通过");
                    	$(".status-" + revisittktid).text("无效");
                        //点击左侧的查看
                		$(".revisittktid-" + revisittktid).click();
                    }else{
						alert("已成功修改");
                    }
                }
            });
        }
    });

	$("#audit-reason").hide();
	$(document).on("click", ".audit-btn-refuse", function() {
        $("#audit-reason").show();
    });

    $(document).on("click",".modify_thedate",function(){
        var revisittktid = $(this).data("revisittktid");

        var thedate = $("input[name=thedate]").val();
        if(thedate == ''){
            alert("日期不能为空");
            return ;
        }
        if(withToday(thedate) < 0){
            alert("预约日期不能小于今天");
            return ;
        }else{
            if (confirm("确定修改预约时间吗?")){
            $.ajax({
                type: "post",
                url: "/revisittktmgr/modifythedateJson",
                data:{"revisittktid" : revisittktid , "thedate" : thedate},
                dataType: "text",
                success : function(data){
                    if(data == "success"){
                        alert("修改成功");
                        $("#thedate").text(thedate);
                        $("input[name=thedate]").val('');
                        $("#show_thedate").text(thedate);
                    }else if(data == "fail"){
                        alert(thedate + "医生没有门诊");
                    }
                }
            });
        }
        }
    });

    function withToday(thedate){
        var d = new Date();
        var today = d.getFullYear() + "-" + (d.getMonth() + 1) + "-" + d.getDate();

        return compareDate(thedate,today);
    };

    function compareDate(date1,date2){
        var date1Arr = date1.split("-");
        var date2Arr = date2.split("-");

        var date1Num = parseInt(date1Arr[0]) * 10000 + parseInt(date1Arr[1]) * 100 + parseInt(date1Arr[2]);
        var date2Num = parseInt(date2Arr[0]) * 10000 + parseInt(date2Arr[1]) * 100 + parseInt(date2Arr[2]);

        return date1Num - date2Num;
    };
});
XXX;
?>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
