<!DOCTYPE html>
<html lang="zh-cn">
<head>
<title>方寸运营后台管理系统</title>
<meta name="viewport" content="initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=0">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" type="text/css" href="http://www.jq22.com/jquery/bootstrap-3.3.4.css">
<link rel="stylesheet" type="text/css" href="http://www.jq22.com/jquery/font-awesome.4.6.0.css">
<link rel="stylesheet" href="<?= $img_uri ?>/static/css/build.css">
<?php include $tpl."/_head.php"; ?>
</head>
<script>
    $(function(){
        $("#inlineRadio1-op_hz").prop("checked",true);
    });
</script>
<body>
<?php include $tpl . "/_nav.php"; ?>
    <div class="col-md-12">
        <form action="/fhw/radiotestpost" method="post">
            <input type="text" name="test[]" value="11111111">
            <input type="text" name="test[]" value="22222222">
            <input type="text" name="test[]" value="33333333">
            <input type="text" name="test[]" value="44444444">
            <div class="table-responsive">
                <table class="table table-bordered ">
                <tbody>
                    <tr>
                        <td>
                            <div>
                                <div class="radio radio-info radio-inline">
                                    <input type="radio" id="inlineRadio1-op_hz" value="1" class="must" name="is_show_p_wx[op_hz]">
                                    <label for="inlineRadio1-op_hz" style="padding-left: 5px;">显示</label>
                                </div>
                                <div class="radio radio-inline">
                                    <input type="radio" id="inlineRadio2-op_hz" value="0" class="notmust" name="is_show_p_wx[op_hz]">
                                    <label for="inlineRadio2-op_hz" style="padding-left: 5px;">不显示</label>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <?php echo HtmlCtr::getNewRadioCtrImp() ?>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="10">
                            <input class="btn btn-primary" type="submit" value="保存" />
                        </td>
                    </tr>
                </tbody>
            </table>
            </div>
        </form>
    </div>
</body>
</html>
