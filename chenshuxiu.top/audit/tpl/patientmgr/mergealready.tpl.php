<?php
$pagetitle = "{$name} 患者对比列表 Patients";
$cssFiles = []; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = []; //填写完整地址
$pageStyle = <<<STYLE
STYLE;
$pageScript = <<<SCRIPT
SCRIPT;
$sideBarMini = false;
?>
<?php include_once $tpl . '/_header.new.tpl.php'; ?>
    <div class="col-md-12">
        <section class="col-md-12">
            <div class="searchBar">
                <input type="text" id="patientids" style="width:600px" placeholder="例如 10,11,13" value="<?=$patientids ?>">
                <input class="specialpatient btn btn-success" value="查询患者">

                <input type="hidden" id="diff_patientids" value="<?=$diff_patientids?>">
            </div>
            <div class="table-responsive">
                <table class="table table-bordered">
                <thead>
                    <tr>
                        <td>对比</td>
                        <td>createuserid</td>
                        <td>与患者关系</td>
                        <td>关联的所有user</td>
                        <td>报到时间</td>
                        <td>patientid</td>
                        <td>姓名</td>
                        <td>birthday</td>
                        <td>医生</td>
                        <td>疾病</td>
                        <td>患者状态</td>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (empty($patients)) {
                        echo "没有重复的患者";
                    }else{
                        foreach ($patients as $a) {
                            ?>
                            <tr>
                                <td>
                                    <label>
                                        <input class="mergebox" name="merge" type="checkbox" value="<?=$a->id ?>" />
                                    </label>
                                </td>
                                <td><?= $a->createuser->id ?></td>
                                <td><?= $a->createuser->shipstr ?></td>
                                <td style="text-align:right">
                                    <?php
                                        $users = $a->getUsers();
                                        foreach ($users as $u) {
                                            echo $u->id . " " . $u->shipstr . "<br/>";
                                        }
                                    ?>
                                </td>
                                <td><?= $a->createtime ?></td>
                                <td>
                                    <?php
                                        $color = '';
                                        if ($a->createuser->patientid == $a->id) {
                                            $color = 'red';
                                        }
                                    ?>
                                    <a style="color:<?=$color?>" href="/patientmgr/list4bind?patientid=<?=$a->id?>"><?= $a->id ?></a>
                                </td>
                                <td><?= $a->name ?></td>
                                <td><?= $a->birthday ?></td>
                                <td><?= $a->doctor->name ?></td>
                                <td><?= $a->disease->name ?></td>
                                <td><?=XConst::auditStatus($a->auditstatus)?></td>
                            </tr>
                        <?php }
                    } ?>
                </tbody>
            </table>
            </div>
            <div class="searchBar">
                <input class="mergepatient btn btn-success" value="选中患者信息对比" >
            </div>
            <div id="diffpatientHtml">
            </div>
        </section>
    </div>
    <div class="clear"></div>
<?php
$footerScript = <<<XXX
$(function(){
    init();

    function init(){
        var diff_patientids = $("#diff_patientids").val();
        if(diff_patientids != ''){
            var idarr = diff_patientids.split(",");
            $("[name='merge']").each(function(){
                if($.inArray($(this).val(), idarr) != -1){
                    $(this).prop("checked",true);
                }
            });

            $.ajax({
                type: "post",
                url: "/patientmgr/diffpatientHtml",
                data:{
                    "patientids" : diff_patientids
                },
                dataType: "text",
                success : function(data){
                    $("#diffpatientHtml").html(data);
                }
            });
        }
    }

    function checkboxCnt(){
    	var i = 0;
		$("[name='merge']:checked").each(function(){
		    i++;
		});

		return i;
    }

	$(".mergebox").on("change",function(){
		var i = checkboxCnt();

		if(i > 2){
			alert("不能同时对比2个以上的患者");
			$(this).removeAttr("checked");
		}
	});

    $(".specialpatient").on("click",function(){
        var patientids = $("#patientids").val();
        var idarr = patientids.split(",");

        if(idarr.length < 2){
            alert("输入patientid不正确!");
            return;
        }

        var url = location.href;
        var urls = url.split("?");

        window.location.href = urls[0] + "?patientids=" + patientids;
    });

	$(".mergepatient").on("click",function(){

	    var i = checkboxCnt();
	    if(i != 2){
	        alert("对比的患者少于两个");
	        return;
	    }

        var diff_patientids = "";
		$("[name='merge']:checked").each(function(){
            diff_patientids += ($(this).val() + ",");
		});
        diff_patientids = diff_patientids.substring(0,diff_patientids.length - 1);

        $("#diff_patientids").val(diff_patientids);
        var patientids = $("#patientids").val();

        var url = location.href;
        var urls = url.split("?");

        window.location.href = urls[0] + "?patientids=" + patientids + "&diff_patientids=" + diff_patientids;
	});

    $(document).on('click', '.modifypatientA', function() {
        var keys = [];
        var values = [];
        var i = 0;
        $(".patientA").each(function() {
            keys[i] = $(this).data("key");
            values[i++] = $(this).val();
        });

        var patientid = $("#patientAid").val();
        var diff_patientids = $("#diff_patientids").val();
        var patientids = $("#patientids").val();

        mergepatientpostajax(keys, values, patientid, diff_patientids, patientids);
    });

    $(document).on('click', '.modifypatientB', function() {
        var keys = [];
        var values = [];
        var i = 0;
        $(".patientB").each(function() {
            keys[i] = $(this).data("key");
            values[i++] = $(this).val();
        });

        var patientid = $("#patientBid").val();
        var diff_patientids = $("#diff_patientids").val();
        var patientids = $("#patientids").val();

        mergepatientpostajax(keys, values, patientid, diff_patientids, patientids);
    });

    function mergepatientpostajax(keys, values, patientid, diff_patientids, patientids){
        $.ajax({
            url: '/patientmgr/mergepost',
            type: 'post',
            dataType: 'text',
            data: {
                "keys" : keys,
                "values" : values,
                "patientid" : patientid,
                "diff_patientids" : diff_patientids,
                "patientids" : patientids
            },
            success:function(data){
                if (data == 'success') {
                    var url = location.href;
                    var urls = url.split("?");

                    window.location.href = urls[0] + "?patientids=" + patientids + "&diff_patientids=" + diff_patientids;
                }
            }
        })
    };

    $(document).on('click', '.modifypcardA', function() {
        var keys = [];
        var values = [];
        var i = 0;
        $(".pcardA").each(function() {
            keys[i] = $(this).data("key");
            values[i++] = $(this).val();
        });

        var pcardid = $("#pcardAid").val();
        var diff_patientids = $("#diff_patientids").val();
        var patientids = $("#patientids").val();

        mergepcardpostajax(keys, values, pcardid, diff_patientids, patientids);
    });

    $(document).on('click', '.modifypcardB', function() {
        var keys = [];
        var values = [];
        var i = 0;
        $(".pcardB").each(function() {
            keys[i] = $(this).data("key");
            values[i++] = $(this).val();
        });

        var pcardid = $("#pcardBid").val();
        var diff_patientids = $("#diff_patientids").val();
        var patientids = $("#patientids").val();

        mergepcardpostajax(keys, values, pcardid, diff_patientids, patientids);
    });

    function mergepcardpostajax(keys, values, pcardid, diff_patientids, patientids){
        $.ajax({
            url: '/pcardmgr/mergepost',
            type: 'post',
            dataType: 'text',
            data: {
                "keys" : keys,
                "values" : values,
                "pcardid" : pcardid,
                "diff_patientids" : diff_patientids,
                "patientids" : patientids
            },
            success:function(data){
                if (data == 'success') {
                    var url = location.href;
                    var urls = url.split("?");

                    var type = $("#type").val();
                    var typestr = '';
                    if (type == 'query') {
                        typestr = "&type=" + type;
                    }

                    window.location.href = urls[0] + "?patientids=" + patientids + "&diff_patientids=" + diff_patientids;
                }
            }
        })
    }
});
XXX;
?>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
