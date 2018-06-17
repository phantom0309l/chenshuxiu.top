<?php
$pagetitle = "缓存管理";
$cssFiles = []; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = []; //填写完整地址
$sideBarMini = false;
$pageStyle = <<<STYLE
td {
    word-break:break-all; 
}
td:nth-child(1) {
    width:10%;
}
td:nth-child(2) {
    width:70%;
}
td:nth-child(3) {
    width:10%;
}
td:nth-child(4) {
    width:10%;
}
STYLE;
?>
<?php include_once $tpl . '/_header.new.tpl.php'; ?>
    <div class="col-md-12">
        <section class="col-md-12">
        <div class="row">
        <div class="col-sm-6">
            <div class="form-group">
            <label class="font-w400">选择db</label>
            <select class="form-control" id="search-db">
                <?php for($i=0;$i<16;$i++) { ?>
                <option value="<?=$i?>" <?php if($i==0){ ?>selected="selected"<?php }?>><?=$i?></option>
                <?php } ?>
            </select>
            </div>
            <div class="form-group">
            <label class="font-w400">查询key</label>
            <input class="form-control" type="text" id="key" name="" placeholder="请输入要查询的key">
            </div>
            <p class="push-10-t">结果(<span class="text-danger">*最多只显示100条</span>)<button class="btn btn-info btn-xs pull-right" data-toggle="modal" data-target="#modal-cleardb" type="button">清空全部</button></p>
            <div class="clear"></div>
            <button id="search-btn" class="btn btn-minw btn-primary push-10-t">查询</button>
        </div>
        </div>
        <div class="row push-10-t">
        <div class="col-sm-12">
        <div class="table-responsive">
            <table class="table table-bordered">
            <tr>
                <td>key</td>
                <td>value</td>
                <td>ttl</td>
                <td>操作</td>
            </tr>
            <tbody id="table-body">
                <tr>
                    <td colspan="4" class="text-center">无结果</td>
                </tr>
            </tbody>
            </table>
        </div>
        </div>
        </div>
        <!--清空db modal-->
        <div class="modal in" id="modal-cleardb" tabindex="-1" role="dialog" aria-hidden="false" style="display: none; padding-right: 17px;">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="block block-themed block-transparent remove-margin-b">
                        <div class="block-header bg-primary">
                            <ul class="block-options">
                                <li>
                                    <button data-dismiss="modal" type="button"><i class="si si-close"></i></button>
                                </li>
                            </ul>
                            <h3 class="block-title">清空db</h3>
                        </div>
                        <div class="block-content">
                            <p>选择database</p>
                            <?php for($i=0;$i<16;$i++) { ?>
                            <label class="css-input css-checkbox css-checkbox-warning">
                                <input type="checkbox" <?php if($i==0){?>checked="checked"<?php }?>name="dbs" value="<?=$i?>"><span></span> db<?=$i?>
                            </label>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-sm btn-default" type="button" data-dismiss="modal">关闭</button>
                        <button id="btn-submit-cleardb" class="btn btn-sm btn-primary" type="button" data-dismiss="modal"><i class="fa fa-check"></i> 提交</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal in" id="modal-fullval" tabindex="-1" role="dialog" aria-hidden="false" style="display: none; padding-right: 17px;">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="block block-themed block-transparent remove-margin-b">
                        <div class="block-header bg-primary">
                            <ul class="block-options">
                                <li>
                                    <button data-dismiss="modal" type="button"><i class="si si-close"></i></button>
                                </li>
                            </ul>
                            <h3 class="block-title">Value</h3>
                        </div>
                        <div class="block-content">
			    <textarea class="form-control" rows=20 id="fullval-content-modal">
			    </textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-sm btn-default" type="button" data-dismiss="modal">关闭</button>
                    </div>
                </div>
            </div>
        </div>
        </section>
    </div>
    <div class="clear"></div>
<?php
$footerScript = <<<SCRIPT
$(function(){
    $('#modal-fullval').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        console.log('button...', button);
        var fullval = button.next('span').text();
        $('#fullval-content-modal').val(fullval);
    })
    $(document).on('click', '.delete', function(){
        if (!confirm('确定要删除吗？')) {
            return false;
        }
        var td = $($(this).parent().siblings()[0]);
        var key = td.text();
        var tr = td.parent();
        $.ajax({
            url: '/cachemgr/deletevaluejson',
            type: 'post',
            dataType: 'json',
            data: {
                key: key,
                db: $('#search-db').val()
            },  
            "success": function (data) {
                if (data.errno != 0) {
                    alert(data.errmsg)
                    return
                }
                if (data.data.ret == 0) {
                    alert('删除失败');
                } else {
                    alert('删除成功');
                    tr.remove();
                }
            }
        });
    });
    //清空db 
    $(document).on('click', '#btn-submit-cleardb', function(){
        if (!confirm('再问一次，确定要清空吗？')) {
            return false;
        }
        var dbs = [];
        $('input[name="dbs"]:checked').each(function(){
            dbs.push($(this).val());
        });
        $.ajax({
            url: '/cachemgr/cleardbjson',
            type: 'post',
            dataType: 'json',
            data: {
                dbs: dbs
            },  
            "success": function (data) {
                if (data.errno != 0) {
                    alert(data.errmsg)
                    return
                }
                alert(data.data.ret);
                location.reload();
            }
        });
    });
    $('#search-btn').bind('click', function(){
       $.ajax({
            url: '/cachemgr/getvaluejson',
            type: 'post',
            dataType: 'json',
            data: {
                key: $('#key').val(),
                db: $('#search-db').val()
            },  
            "success": function (data) {
                if (data.errno != 0) {
                    alert(data.errmsg)
                    return
                }
                var s = ''
                if (data.data == '') {
                    $('#table-body').html('<tr><td class="text-center" colspan="4">没有查到对应的key</td></tr>'); 
                    return 
                }
                $.each(data.data, function(i, one){
                    var val = '';
                    if (one.val.length > 200) {
                        val = one.val.substring(0, 200) + '...';
                        val += ' <button class="btn btn-info btn-xs" data-toggle="modal" data-target="#modal-fullval" type="button">展开</button> <span style="display:none">'+one.val+'</span>'
                    } else {
                        val = one.val
                    }
                    s += '<tr>';
                    s += '<td>' + one.key +'</td>'; 
                    s += '<td>' + val +'</td>'; 
                    s += '<td>' + one.ttl +'</td>'; 
                    s += '<td><a class="delete" href="javascript:">删除</a></td>'; 
                    s += '</tr>'; 
                })
                $('#table-body').html(s); 
            }   
         }); 
    });
});
SCRIPT;
?>
<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
