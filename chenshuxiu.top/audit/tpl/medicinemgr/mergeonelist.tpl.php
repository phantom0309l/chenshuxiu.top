<?php
$pagetitle = "单个重复药品列表 Medicines";
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
                <span>药品id在403～426（包含边界）之间，有特殊用途，不能合并</span>
            </div>
            <div class="searchBar">
                <input type="text" id="ids" placeholder="例如 10,11,13" value="<?=$ids ?>">
                <input class="specialmedicine btn btn-success" value="查询药物">
            </div>
            <input type="hidden" id="medicineidsstr" value="<?=$ids ?>">
            <div class="table-responsive">
                <table class="table table-bordered">
                <thead>
                    <tr>
                        <td>合并</td>
                        <td>id</td>
                        <td>商品名</td>
                        <td>药名</td>
                        <td>分组</td>
                        <td>关联的疾病</td>
                        <td>相关的记录</td>
                        <td>图片</td>
                        <td>合并</td>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (empty($medicines)) {
                        echo "没有重复的药品";
                    }else{
                        foreach ($medicines as $a) {
                            ?>
                            <tr>
                                <td>
                                    <label>
                                        <?php
                                            if(!($a->id >= 403 && $a->id <= 426)){
                                            ?>
                                                <input class="mergebox" name="merge" type="checkbox" value="<?=$a->id ?>" />
                                            <?php
                                            }
                                        ?>
                                    </label>
                                </td>
                                <td><?= $a->id ?></td>
                                <td><?= $a->name ?></td>
                                <td><?= $a->scientificname ?></td>
                                <td><?= $a->groupstr ?></td>
                                <td>
                                    <?php
                                        if($a instanceof Medicine){
                                            $refs = $a->getDiseaseMedicineRefs();
                                            foreach ($refs as $ref){
                                            ?>
                                                <span style="color: green">[<?php echo $ref->disease->name; ?>]</span>
                                            <?php
                                            }
                                        }
                                    ?>
                                </td>
                                <td><?= $cnts["{$a->id}"]?></td>
                                <td><?php if($a->picture){ ?><img src="<?=$a->picture->getSrc(40,40)?>"><?php } ?></td>
                                <td>
                                    <?php
                                        if(!($a->id >= 403 && $a->id <= 426)){
                                        ?>
                                            <input data-medicineid="<?=$a->id ?>" class="mergemedicine btn btn-success" value="保留" >
                                        <?php
                                        }
                                    ?>
                                </td>
                            </tr>
                        <?php }
                    } ?>
                </tbody>
            </table>
            </div>
        </section>
    </div>
    <div class="clear"></div>
<?php
$footerScript = <<<XXX
$(function(){
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
			alert("不能同时合并2个以上的药品");
			$(this).removeAttr("checked");
		}
	});

    $(".specialmedicine").on("click",function(){
        var ids = $("#ids").val();
        var idarr = ids.split(",");

        if(idarr.length < 2){
            alert("输入medicineid不正确!");
            return;
        }

        var url = location.href;
        var urls = url.split("?");

        window.location.href = urls[0] + "?ids=" + ids;
    });

	$(".mergemedicine").on("click",function(){

	    var i = checkboxCnt();
	    if(i != 2){
	        alert("合并的药品少于两个");
	        return;
	    }

		var me = $(this).data();
		var keepmedicineid = me.medicineid;

		var deletemedicineid = 0;
		$("[name='merge']:checked").each(function(){
		    var seletemedicineid = $(this).val();
		    if(keepmedicineid != seletemedicineid){
			    deletemedicineid = seletemedicineid;
		        return false;
		    }
		});

		if(!confirm("确认 保留[" + keepmedicineid + "] 删除[" + deletemedicineid + "] 吗？")){
			return;
	    }

		$.ajax({
            type: "post",
            url: "/medicinemgr/mergepost",
            data:{
                "keepmedicineid" : keepmedicineid ,
                "deletemedicineid" : deletemedicineid
            },
            dataType: "text",
            success : function(data){
                if(data == 'success'){
                    alert("合并成功");
                }
//                 window.opener.location.reload();

        		var url = location.href;
                var urls = url.split("?");

                var idarr = $("#medicineidsstr").val().split(",");
                var ids = '';
                for(var i = 0; i < idarr.length; i++){
                    if(idarr[i] != deletemedicineid){
                        ids += (idarr[i] + ",");
                    }
                }

                ids = ids.substring(0,ids.length - 1);

                window.location.href = urls[0] + "?ids=" + ids;
            }
        });
	});
});
XXX;
?>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
