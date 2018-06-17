<?php
$pagetitle = $doctor->name . "医生头像、职称、科室、简介、擅长信息抓取页";
$cssFiles = []; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = []; //填写完整地址
?>
<?php include_once $tpl . '/_header.new.tpl.php'; ?>
<div class="col-md-12">
    <section class="col-md-12">
        <div style="margin: 20px 0px 20px 10px;">
            <i class="fa fa-search"></i> <a href="http://so.haodf.com/index/search?kw=<?= $doctor->name ?>" target="_blank">帮助在好大夫搜索此医生</a>
        </div>
        <input type="hidden" name="doctorid" value="<?= $doctor->id ?>" />
        <input class="form-control" type="text" id="fetch_url" name="fetch_url" placeholder="请输入此医生在好大夫的信息中心页" style="width: 80%" value="">
        <p style="padding-left: 10px;">  要输入链接的例子（王迁医生的信息中心页）：<a href="http://www.haodf.com/doctor/DE4r0BCkuHzduTWZaHGUIHuKomU0X.htm" target="_blank">http://www.haodf.com/doctor/DE4r0BCkuHzduTWZaHGUIHuKomU0X.htm</a></p>
        </br>
        <button class="btn btn-sm btn-primary btn-minw btn-fetch" type="submit" data-doctorid=<?= $doctor->id ?>><i class="si si-cloud-download"></i> 抓取</button>
    <section>
</div>
<?php
$footerScript = <<<XXX
    $(function () {
        $(".btn-fetch").on("click", function () {
            var me = $(this);
            var doctorid = me.data("doctorid");
            var node = $("#fetch_url");
            var fetch_url = node.val();
            if(fetch_url == ""){
                alert("请填写要抓取信息的目标页面地址链接！");
                return;
            }

            var data = {};
            data.doctorid = doctorid;
            data.fetch_url = fetch_url;

            $.ajax({
                "type" : "get",
                "data" : data,
                "dataType" : "text",
                "url" : "/doctormgr/fetchInfoJson",
                "success" : function(data){
                    if(data == "ok"){
                        window.location.href = '/doctormgr/modify?doctorid=' + doctorid;
                    }else {
                        alert(data);
                    }
                }
            });
        });
    });
XXX;
?>

<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
