<?php
$pagetitle = "修改住院预约";
$cssFiles = []; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = []; //填写完整地址
$pageStyle = <<<STYLE
    .imgDiv {
        border:#ccc 1px solid;
        width:154px;
        height:158px;
        float:left;
        margin:0 2px 5px 0;
        padding:1px;
        text-align:center;
        line-height:150px;
    }
STYLE;
$pageScript = <<<SCRIPT
SCRIPT;
$sideBarMini = false;
?>
<?php include_once $tpl . '/_header.new.tpl.php'; ?>

<script text="javascript" src="<?= $img_uri ?>/jquery/jquery-file-upload.js"></script>
<script text="javascript">
    function uploadimg(obj) {
        if (obj.value.length > 0) {
            var pic_type = $(obj).data('pic_type');
            $.ajaxFileUpload({
                url: '/picture/uploadimagepost/?w=150&h=150&isCut=&type=LessonMaterial', //需要链接到服务器地址,w=缩略图宽,h=缩略图高
                secureuri: false,
                fileElementId: 'input-uploadimg_' + pic_type, //文件选择框的id属性
                dataType: 'json', //服务器返回的格式，可以是json
                success: function (data, status) {            //相当于java中try语句块的用法
                    console.log(data);
                    var reg = /\d+_\d+\./;
                    var image_url = data.thumb.replace(reg, "");
                    var newimgDiv = "<div class=\"img-container fx-opt-zoom-out imgDiv\">\n" +
                        "                <input type=\"hidden\" class=\"pictureid\" name=\"" + pic_type + "[]\" value=\"" + data.pictureid + "\">\n" +
                        "                <img class=\"img-responsive\" src=\"" + data.thumb + "\" alt=\"\">\n" +
                        "                <div class=\"img-options\">\n" +
                        "                    <div class=\"img-options-content\" style=\"margin-top: 60px;\">\n" +
                        "                        <a target='_blank' class=\"btn btn-sm btn-default\" href=\"" + image_url + "\"><i class=\"fa fa-pencil\"></i>原图</a>\n" +
                        "                        <a class=\"btn btn-sm btn-default delete-pic\" href=\"javascript:void(0)\"><i class=\"fa fa-times\"></i>删除</a>\n" +
                        "                    </div>\n" +
                        "                </div>\n" +
                        "            </div>";
                    $("#showimg_" + pic_type).append(newimgDiv);
                },
                error: function (data, status, e) {            //相当于java中catch语句块的用法
                    $('#upload_status').html('上传失败');
                    alert(data);
                    alert(e);
                }
            });
        }
    }
</script>

<div class="col-md-12">
    <section class="col-md-12">
        <form action="/bedtktmgr/modifypost" method="post">
            <input type="hidden" name="bedtktid" value="<?= $bedtkt->id ?>" />
            <div class="table-responsive">
                <table class="table table-bordered">
                <tr>
                    <th width=140>检查id</th>
                    <td><?= $bedtkt->id?></td>
                </tr>
                <tr>
                    <th>患者</th>
                    <td><?= $bedtkt->patient->name?></td>
                </tr>
                <tr>
                    <th>所属医生</th>
                    <td><?= $bedtkt->doctor->name?></td>
                </tr>
                <?php
                    if ($content['is_feetype_show'] == 1) {
                        ?>
                            <tr>
                                <th>费用类型</th>
                                <td>
                                    <div class="col-md-2">
                                        <?php
                                            $arr = [
                                                'default' => '未选择',
                                                'beijing' => '北京',
                                                'notbeijing' => '非北京'
                                            ];
                                            echo HtmlCtr::getSelectCtrImp($arr, 'fee_type', $bedtkt->fee_type, 'form-control');
                                        ?>
                                    </div>
                                </td>
                            </tr>
                    <?php } ?>
                    <?php if ($content['is_plandate_show'] == 1) { ?>
                            <tr>
                                <th>入住日期</th>
                                <td><input type="text" class="calendar" name="want_date" value="<?=$bedtkt->want_date?>"></td>
                            </tr>
                    <?php } ?>
                    <?php if ($content['is_idcard_show'] == 1) { ?>
                            <tr>
                                <th>身份证号</th>
                                <td><input type="text" class="" name="idcard" value="<?=$extra_info['idcard']?>"></td>
                            </tr>
                    <?php } ?>
                    <?php if ($content['is_zhuyuanhao_show'] == 1) { ?>
                            <tr>
                                <th>住院号</th>
                                <td><input type="text" class="" name="zhuyuanhao" value="<?=$extra_info['zhuyuanhao']?>"></td>
                            </tr>
                    <?php } ?>
                    <?php if ($content['is_bingshi_show'] == 1) { ?>
                            <tr>
                                <th>病史</th>
                                <td><input type="text" class="" name="bingshi" value="<?=$extra_info['bingshi']?>"></td>
                            </tr>
                    <?php } ?>
                    <?php if ($content['is_linchuangbiaoxian_show'] == 1) { ?>
                            <tr>
                                <th>临床表现</th>
                                <td><input type="text" class="" name="linchuangbiaoxian" value="<?=$extra_info['linchuangbiaoxian']?>"></td>
                            </tr>
                    <?php } ?>
                    <?php if ($content['is_otherdisease_show'] == 1) { ?>
                            <tr>
                                <th>其他疾病</th>
                                <td><input type="text" class="" name="otherdisease" value="<?=$extra_info['otherdisease']?>"></td>
                            </tr>
                    <?php } ?>
                    <?php if ($content['is_shoushuriqi_show'] == 1) { ?>
                            <tr>
                                <th>手术日期</th>
                                <td><input type="text" class="calendar" name="shoushuriqi" value="<?=$extra_info['shoushuriqi']?>"></td>
                            </tr>
                    <?php } ?>
                    <?php if ($content['is_xingongnengfenji_show'] == 1) { ?>
                            <tr>
                                <th>心功能分级</th>
                                <td>
                                    <div class="col-md-2">
                                        <?php
                                            $arr = [
                                                '' => '未选择',
                                                'I' => 'I',
                                                'II' => 'II',
                                                'III' => 'III',
                                                'IV' => 'IV',
                                            ];
                                            echo HtmlCtr::getSelectCtrImp($arr, 'xingongnengfenji', $extra_info['xingongnengfenji'], 'form-control');
                                        ?>
                                    </div>
                                </td>
                            </tr>
                    <?php } ?>
                    <?php if ($content['is_zhuyuan_photo_show'] == 1) { ?>
                            <tr>
                                <th>住院证图片</th>
                                <td>
                                    <div>
                                        <div id="showimg_bedtktpictureids">
                                            <?php
                                                $bedtktpictures = $bedtkt->getBedTktPictures();
                                                foreach ($bedtktpictures as $a) {
                                                    if (false == $a->picture instanceof Picture) {
                                                        continue;
                                                    }
                                                    ?>
                                                    <div class="img-container fx-opt-zoom-out imgDiv">
                                                        <img class="img-responsive" src="<?= $a->picture->getSrc(150, 150) ?>" alt="">
                                                        <div class="img-options imgDiv">
                                                            <div class="img-options-content" style="margin-top: 60px;">
                                                                <a target='_blank' class="btn btn-sm btn-default" href="<?= $a->picture->getSrc(1000, 1000) ?>"><i class="fa fa-pencil"></i>原图</a>
                                                                <a class="btn btn-sm btn-default has_obj_delete-pic" data-objtype="<?=get_class($a)?>" data-objid="<?=$a->id?>" href="javascript:void(0)"><i class="fa fa-times"></i>删除</a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <?php
                                                }
                                            ?>
                                        </div>
                                        <div style="clear: both;"></div>
                                        <div>
                                            <input class="file-input" onchange="uploadimg(this)" data-pic_type="bedtktpictureids" type="file" id="input-uploadimg_bedtktpictureids" name="imgurl"/>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php
                    }
                    if ($content['is_xuechanggui_photo_show'] == 1) {
                        ?>
                            <tr>
                                <th>血常规图片</th>
                                <td>
                                    <div>
                                        <div id="showimg_wxpicmsgids">
                                            <?php
                                            $wxpicmsgs = $bedtkt->getWxPicMsgs();
                                            foreach ($wxpicmsgs as $a) {
                                                if (false == $a->picture instanceof Picture) {
                                                    continue;
                                                }
                                                ?>
                                                <div class="img-container fx-opt-zoom-out imgDiv">
                                                    <img class="img-responsive" src="<?= $a->picture->getSrc(150, 150) ?>" alt="">
                                                    <div class="img-options">
                                                        <div class="img-options-content" style="margin-top: 60px;">
                                                            <a target='_blank' class="btn btn-sm btn-default" href="<?= $a->picture->getSrc(1000, 1000) ?>"><i class="fa fa-pencil"></i>原图</a>
                                                            <a class="btn btn-sm btn-default has_obj_delete-pic" data-objtype="<?=get_class($a)?>" data-objid="<?=$a->id?>" href="javascript:void(0)"><i class="fa fa-times"></i>删除</a>
                                                        </div>
                                                    </div>
                                                </div>
                                                <?php
                                            }
                                            ?>
                                        </div>
                                        <div style="clear: both;"></div>
                                        <div>
                                            <input class="file-input" onchange="uploadimg(this)" data-pic_type="wxpicmsgids" type="file" id="input-uploadimg_wxpicmsgids" name="imgurl"/>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php
                    }
                    if ($content['is_gangongneng_photo_show'] == 1) {
                        ?>
                            <tr>
                                <th>肝肾功图片</th>
                                <td>
                                    <div>
                                        <div id="showimg_liverpictureids">
                                            <?php
                                            $liverpictures = $bedtkt->getLiverPictures();
                                            foreach ($liverpictures as $a) {
                                                if (false == $a->picture instanceof Picture) {
                                                    continue;
                                                }
                                                ?>
                                                <div class="img-container fx-opt-zoom-out imgDiv">
                                                    <img class="img-responsive" src="<?= $a->picture->getSrc(150, 150) ?>" alt="">
                                                    <div class="img-options">
                                                        <div class="img-options-content" style="margin-top: 60px;">
                                                            <a target='_blank' class="btn btn-sm btn-default" href="<?= $a->picture->getSrc(1000, 1000) ?>"><i class="fa fa-pencil"></i>原图</a>
                                                            <a class="btn btn-sm btn-default has_obj_delete-pic" data-objtype="<?=get_class($a)?>" data-objid="<?=$a->id?>" href="javascript:void(0)"><i class="fa fa-times"></i>删除</a>
                                                        </div>
                                                    </div>
                                                </div>
                                                <?php
                                            }
                                            ?>
                                        </div>
                                        <div style="clear: both;"></div>
                                        <div>
                                            <input class="file-input" onchange="uploadimg(this)" data-pic_type="liverpictureids" type="file" id="input-uploadimg_liverpictureids" name="imgurl"/>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php
                    }
                ?>
                <?php if ($content['is_xindiantu_show'] == 1) { ?>
                            <tr>
                                <th>心电图</th>
                                <td>
                                    <div>
                                        <div id="showimg_xindiantuids">
                                            <?php
                                            $liverpictures = $bedtkt->getXindiantuPictures();
                                            foreach ($liverpictures as $a) {
                                                if (false == $a->picture instanceof Picture) {
                                                    continue;
                                                }
                                                ?>
                                                <div class="img-container fx-opt-zoom-out imgDiv">
                                                    <img class="img-responsive" src="<?= $a->picture->getSrc(150, 150) ?>" alt="">
                                                    <div class="img-options">
                                                        <div class="img-options-content" style="margin-top: 60px;">
                                                            <a target='_blank' class="btn btn-sm btn-default" href="<?= $a->picture->getSrc(1000, 1000) ?>"><i class="fa fa-pencil"></i>原图</a>
                                                            <a class="btn btn-sm btn-default has_obj_delete-pic" data-objtype="<?=get_class($a)?>" data-objid="<?=$a->id?>" href="javascript:void(0)"><i class="fa fa-times"></i>删除</a>
                                                        </div>
                                                    </div>
                                                </div>
                                                <?php
                                            }
                                            ?>
                                        </div>
                                        <div style="clear: both;"></div>
                                        <div>
                                            <input class="file-input" onchange="uploadimg(this)" data-pic_type="xindiantuids" type="file" id="input-uploadimg_xindiantuids" name="imgurl"/>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                <?php } ?>
                <?php if ($content['is_xueshuantanlitu_show'] == 1) { ?>
                            <tr>
                                <th>血栓弹力图</th>
                                <td>
                                    <div>
                                        <div id="showimg_xueshuantanlituids">
                                            <?php
                                            $liverpictures = $bedtkt->getXueshuantanlituPictures();
                                            foreach ($liverpictures as $a) {
                                                if (false == $a->picture instanceof Picture) {
                                                    continue;
                                                }
                                                ?>
                                                <div class="img-container fx-opt-zoom-out imgDiv">
                                                    <img class="img-responsive" src="<?= $a->picture->getSrc(150, 150) ?>" alt="">
                                                    <div class="img-options">
                                                        <div class="img-options-content" style="margin-top: 60px;">
                                                            <a target='_blank' class="btn btn-sm btn-default" href="<?= $a->picture->getSrc(1000, 1000) ?>"><i class="fa fa-pencil"></i>原图</a>
                                                            <a class="btn btn-sm btn-default has_obj_delete-pic" data-objtype="<?=get_class($a)?>" data-objid="<?=$a->id?>" href="javascript:void(0)"><i class="fa fa-times"></i>删除</a>
                                                        </div>
                                                    </div>
                                                </div>
                                                <?php
                                            }
                                            ?>
                                        </div>
                                        <div style="clear: both;"></div>
                                        <div>
                                            <input class="file-input" onchange="uploadimg(this)" data-pic_type="xueshuantanlituids" type="file" id="input-uploadimg_xueshuantanlituids" name="imgurl"/>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                <?php } ?>
                <?php if ($content['is_fengshimianyijiancha_show'] == 1) { ?>
                            <tr>
                                <th>风湿免疫检查</th>
                                <td>
                                    <div>
                                        <div id="showimg_fengshimianyijianchaids">
                                            <?php
                                            $liverpictures = $bedtkt->getFengshimianyijianchaPictures();
                                            foreach ($liverpictures as $a) {
                                                if (false == $a->picture instanceof Picture) {
                                                    continue;
                                                }
                                                ?>
                                                <div class="img-container fx-opt-zoom-out imgDiv">
                                                    <img class="img-responsive" src="<?= $a->picture->getSrc(150, 150) ?>" alt="">
                                                    <div class="img-options">
                                                        <div class="img-options-content" style="margin-top: 60px;">
                                                            <a target='_blank' class="btn btn-sm btn-default" href="<?= $a->picture->getSrc(1000, 1000) ?>"><i class="fa fa-pencil"></i>原图</a>
                                                            <a class="btn btn-sm btn-default has_obj_delete-pic" data-objtype="<?=get_class($a)?>" data-objid="<?=$a->id?>" href="javascript:void(0)"><i class="fa fa-times"></i>删除</a>
                                                        </div>
                                                    </div>
                                                </div>
                                                <?php
                                            }
                                            ?>
                                        </div>
                                        <div style="clear: both;"></div>
                                        <div>
                                            <input class="file-input" onchange="uploadimg(this)" data-pic_type="fengshimianyijianchaids" type="file" id="input-uploadimg_fengshimianyijianchaids" name="imgurl"/>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                <?php } ?>
                <?php if ($content['is_shuqianqitajiancha_show'] == 1) { ?>
                            <tr>
                                <th>术前其他检查</th>
                                <td>
                                    <div>
                                        <div id="showimg_shuqianqitajianchaids">
                                            <?php
                                            $liverpictures = $bedtkt->getShuqianqitajianchaPictures();
                                            foreach ($liverpictures as $a) {
                                                if (false == $a->picture instanceof Picture) {
                                                    continue;
                                                }
                                                ?>
                                                <div class="img-container fx-opt-zoom-out imgDiv">
                                                    <img class="img-responsive" src="<?= $a->picture->getSrc(150, 150) ?>" alt="">
                                                    <div class="img-options">
                                                        <div class="img-options-content" style="margin-top: 60px;">
                                                            <a target='_blank' class="btn btn-sm btn-default" href="<?= $a->picture->getSrc(1000, 1000) ?>"><i class="fa fa-pencil"></i>原图</a>
                                                            <a class="btn btn-sm btn-default has_obj_delete-pic" data-objtype="<?=get_class($a)?>" data-objid="<?=$a->id?>" href="javascript:void(0)"><i class="fa fa-times"></i>删除</a>
                                                        </div>
                                                    </div>
                                                </div>
                                                <?php
                                            }
                                            ?>
                                        </div>
                                        <div style="clear: both;"></div>
                                        <div>
                                            <input class="file-input" onchange="uploadimg(this)" data-pic_type="shuqianqitajianchaids" type="file" id="input-uploadimg_shuqianqitajianchaids" name="imgurl"/>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                <?php } ?>
                <tr>
                    <th></th>
                    <td>
                        <input type="submit" class="btn btn-primary" value="提交" />
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
        $(document).off("click", ".delete-pic").on("click", ".delete-pic", function(){
            var imgDiv = $(this).parents('.img-container');
            if (!confirm("确定删除吗?")) {
                return false;
            }
            imgDiv.remove();
        });

        $(document).off("click", ".has_obj_delete-pic").on("click", ".has_obj_delete-pic", function(){
            var imgDiv = $(this).parents('.img-container');

            var me = $(this);
            var objid = me.data('objid');
            var objtype = me.data('objtype');

            if (!confirm("确定删除吗?")) {
                return false;
            }

            $.ajax({
                url: '/bedtktmgr/deletePic',
                type: 'get',
                dataType: 'text',
                data: {
                    objtype : objtype,
                    objid : objid
                },
                "success": function (data) {
                    if (data == 'success') {
                        imgDiv.remove();        
                        alert("删除成功");
                    } else if (data == 'fail') {
                        alert("删除失败")
                    }
                }
            });

        });
	});
XXX;
?>

<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
