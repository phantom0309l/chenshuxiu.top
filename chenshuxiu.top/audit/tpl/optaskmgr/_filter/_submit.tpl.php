<div class="" style="border-top:1px dashed #ccc; margin-bottom: 20px;"></div>

<div class="form-group">
    <label class="col-xs-3 control-label" for="title">过滤器名称</label>
    <div class="col-xs-9">
        <input class="form-control" type="text" id="title" name="title" value="<?=$optaskfilter->title?>" placeholder="过滤器名称">
    </div>
</div>
<div class="form-group">
    <label class="col-xs-3 control-label" for="is_public">开放程度</label>
    <div class="col-xs-9">
        <input class="form-control" type="hidden" id="is_public" name="is_public" value="<?=$optaskfilter->is_public?>">
        <?php
            if ($optaskfilter->is_public == 1) {
                $checkedstr = 'checked';
                $text = '开放';
            } else {
                $checkedstr = '';
                $text = '私有';
            }
        ?>
        <label class="css-input switch switch-success">
            <input type="checkbox" id="modify_is_public" <?=$checkedstr?>><span></span> <span id="text_is_public"><?=$text?></span>
        </label>
    </div>
</div>
<div class="form-group">
    <label class="col-xs-3 control-label" for="baodaodate">操作</label>
    <div class="col-xs-9">
        <?php
            if ($myauditor->id == $optaskfilter->create_auditorid && $optaskfilter->title != '') {
                // 过滤器作者可以修改
                ?> <button class="btn btn-sm btn-primary optaskfilter-addormodify" data-type="modify">保存</button> <?php
            }
        ?>
        <button class="btn btn-sm btn-primary optaskfilter-addormodify" data-type="add">另存</button>
        <?php
            // 任何过滤器都可以保存到个人的临时过滤器上
            ?> <button class="btn btn-sm btn-primary" id="optaskfilter-temp">临时</button> <?php

            // 临时过滤器不能删除，只能修改
            if ($myauditor->id == $optaskfilter->create_auditorid && $optaskfilter->title) {
                ?> <button class="btn btn-sm btn-primary" id="optaskfilter-delete" data-optaskfilterid="<?=$optaskfilter->id?>">删除</button> <?php
            }
        ?>
    </div>
</div>
<script>
    $(function () {
        function getOpTaskFilters () {
            var optaskfilters = [];

            $(".optaskfilter-list").each(function () {
                var me = $(this);
                var key = me.data('key');
                var value = $('#' + key).val();
                var key_str = key + "str";
                var value_str = $('#' + key_str).val();

                if (key === 'optasktpl') {
                    var el = $('#optasktplid');
                    var arr = el.val();
                    if (arr !== null && arr !== undefined && arr !== '') { // 任务类型什么都没选
                        var value_str_arr = [];
                        $(el[0].selectedOptions).each(function(index, option) {
                            if (index > 3) {
                                return false;
                            }
                            value_str_arr.push($(option).text());
                        });

                        value = arr.join(',');
                        value_str = value_str_arr.join(',');
                        if (arr.length > 3) {
                            value_str += ' ...';
                        }
                    }
                }

                if (key === 'opnode') {
                    var el = $('#opnodeid');
                    var arr = el.val();
                    if (arr !== null && arr !== undefined && arr !== '') { // 任务节点什么都没选
                        var value_str_arr = [];
                        $(el[0].selectedOptions).each(function(index, option) {
                            value_str_arr.push($(option).text());
                        });

                        value = arr.join(',');
                        value_str = value_str_arr.join(',');
                    }
                }

                var filter = {
                    filter_key : key,
                    filter_value : value,
                    filter_key_str : key_str,
                    filter_value_str : value_str
                };

                optaskfilters.push(filter);
            });

            return optaskfilters;
        }

        $('#modify_is_public').on('click', function () {
            console.log(this);
            console.log($(this).context.checked);
            if ($(this).context.checked == true) {
                $('#is_public').val(1);
                $('#text_is_public').text('开放');
            } else {
                $('#is_public').val(0);
                $('#text_is_public').text('私有');
            }
        });

        // 保存到临时过滤器
        $('#optaskfilter-temp').on('click', function () {
            var optaskfilters = getOpTaskFilters();

            $.ajax({
                url : '/optaskfiltermgr/modifytempjson',
                type : 'get',
                dataType : 'json',
                data : {
                    optaskfilters : optaskfilters
                },
                success : function (result) {
                    if (result.errno == -1) {
                        alert(result.errmsg);
                    } else {
                        alert("保存成功!");
                        console.log(result.data, "====================");
                        window.location.href = '/optaskmgr/listnew?optaskfilterid=' + result.data.optaskfilterid;
                    }
                }
            });
        });

        // 添加过滤器
        $('.optaskfilter-addormodify').on('click', function () {
            var me = $(this);

            var title = $('#title').val();
            var is_public = $('#is_public').val();
            var type = me.data('type');
            var optaskfilterid = $('#optaskfilterid').val();
            var optaskfilters = getOpTaskFilters();

            if (title == '') {
                alert('过滤器名称不能为空!');
                return false;
            }

            if (type == '') {
                alert('操作类型不能为空!');
                return false;
            }

            console.log(optaskfilters);

            $.ajax({
                url : '/optaskfiltermgr/addormodifyjson',
                type : 'get',
                dataType : 'json',
                data : {
                    type : type,
                    optaskfilterid : optaskfilterid,
                    title : title,
                    optaskfilters : optaskfilters,
                    is_public : is_public
                },
                success : function (result) {
                    if (result.errno == -1) {
                        alert(result.errmsg);
                    } else {
                        if (type == 'add') {
                            alert('添加成功');
                        } else if (type == 'modify') {
                            alert('修改成功');
                        }
                        console.log(result.data, "====================");
                        window.location.href = '/optaskmgr/listnew?optaskfilterid=' + result.data.optaskfilterid;
                    }
                }
            });
        });

        $('#optaskfilter-delete').on('click', function () {
            if (!confirm('确定删除吗?')) {
                return false;
            }

            var me = $(this);
            var optaskfilterid = me.data('optaskfilterid');

            $.ajax({
                url : '/optaskfiltermgr/deletejson',
                type : 'get',
                dataType : 'text',
                data : {
                    optaskfilterid : optaskfilterid
                },
                success : function (data) {
                    if (data == 'success') {
                        alert('删除成功!');

                        window.location.href = "/optaskmgr/listnew";
                    } else {
                        alert('删除失败!');
                    }
                }
            });
        });
    });
</script>
