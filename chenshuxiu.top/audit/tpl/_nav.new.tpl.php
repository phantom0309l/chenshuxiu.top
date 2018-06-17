<?php
$auditresource_current = AuditResourceDao::getByActionMethod($action, $method);
//$parentMenuArr = XCache::getValue('parentMenuArr', 3600, function() use($myauditor){
//return AuditMenuDao::getParentMenuListByAuditor($myauditor);
//}, 'php');
$parentMenuArr = AuditMenuDao::getParentMenuListByAuditor($myauditor);
$menuIconMap = [
    '运营首页' => 'fa fa-home',
    '运营任务' => 'fa fa-tasks',
    '运营任务(新)' => 'fa fa-tasks',
    '患者' => 'fa fa-wheelchair',
    '医生' => 'fa fa-user-md',
    '患者列表' => 'fa fa-wheelchair',
    '查询' => 'si si-magnifier',
    '微信号' => 'fa fa-wechat',
    '药品' => 'fa fa-medkit',
    '开药门诊' => 'fa fa-shopping-cart',
    '培训课' => 'fa fa-book',
    '课程量表' => 'fa fa-newspaper-o',
    '报表统计' => 'fa fa-pie-chart',
    '医生数据库' => 'fa fa-database',
    '管理' => 'fa fa-cogs',
    '个人中心' => 'fa fa-user',
    '研发' => 'fa fa-coffee',
    '隐藏菜单' => 'fa fa-eye-slash',
];
?>
<!-- Sidebar -->
<nav id="sidebar">
    <!-- Sidebar Scroll Container -->
    <div id="sidebar-scroll">
        <!-- Sidebar Content -->
        <!-- Adding .sidebar-mini-hide to an element will hide it when the sidebar is in mini mode -->
        <div class="sidebar-content">
            <!-- Side Header -->
            <div class="side-header side-content bg-white-op">
                <!-- Layout API, functionality initialized in App() -> uiLayoutApi() -->
                <button class="btn btn-link text-gray pull-right hidden-md hidden-lg" type="button" data-toggle="layout"
                        data-action="sidebar_close">
                    <i class="fa fa-times"></i>
                </button>
                <!-- Themes functionality initialized in App() -> uiHandleTheme() -->
                <img class="site-logo" style="" src="<?= $img_uri ?>/static/img/logo_text.png">
                <a class="h5 text-white" href="/" style="padding-left:10px;">
                    <span class="h5 font-w600 sidebar-mini-hide">运营平台</span>
                </a>
            </div>
            <!-- END Side Header -->

            <!-- Side Content -->
            <div class="side-content">
                <ul class="nav-main">
                    <li class="menu-search sidebar-mini-hide">
                        <div class="form-material form-material-primary input-group remove-margin-t remove-margin-b">
                            <input class="form-control" type="text" id="base-material-text" name="base-material-text"
                                   autocomplete="off" placeholder="搜索菜单">
                            <span class="input-group-addon" style="color:#48576a;"><i
                                        class="si si-magnifier"></i></span>
                        </div>
                    </li>
                    <?php
                    //$submenuArrList = XCache::getValue('submenuArrList', 3600, function() use($myauditor, $parentMenuArr){
                    //$list = [];
                    //foreach ($parentMenuArr as $a) {
                    //$list[] = $a->getSubMenuListByAuditor($myauditor);
                    //}
                    //return $list;
                    //}, 'php');

                    $submenuArrList = [];
                    foreach ($parentMenuArr as $a) {
                        $submenuArrList[] = $a->getSubMenuListByAuditor($myauditor);
                    }
                    //$submenuPyTitles = XCache::getValue('submenu_py_titles', 3600, function() use($parentMenuArr, $submenuArrList){
                    //$pys = [];
                    //foreach ($parentMenuArr as $a) {
                    //$pys[$a->id] = PinyinUtilNew::Word2PY($a->title) . ',' . strtoupper(PinyinUtilNew::Word2PY($a->title, false));
                    //}
                    //foreach ($submenuArrList as $a) {
                    //foreach ($a as $b) {
                    //$pys[$b->id] = PinyinUtilNew::Word2PY($b->title) . ',' . strtoupper(PinyinUtilNew::Word2PY($b->title, false));
                    //}
                    //}
                    //return $pys;
                    //}, 'json');
                    $pys = [];
                    foreach ($parentMenuArr as $a) {
                        $pys[$a->id] = PinyinUtilNew::Word2PY($a->title) . ',' . strtoupper(PinyinUtilNew::Word2PY($a->title, false));
                    }
                    foreach ($submenuArrList as $a) {
                        foreach ($a as $b) {
                            $pys[$b->id] = PinyinUtilNew::Word2PY($b->title) . ',' . strtoupper(PinyinUtilNew::Word2PY($b->title, false));
                        }
                    }
                    $submenuPyTitles = $pys;
                    ?>
                    <?php foreach ($parentMenuArr as $key => $a) { ?>
                        <?php $submenuArr = $submenuArrList[$key]; ?>
                        <li <?php if ($a->id == $auditresource_current->auditmenu->parentmenuid || $a->id == $auditresource_current->auditmenuid) { ?> class="open" <?php } ?>
                            <?php if (count($submenuArr) == 0){ ?>nosubmenu="1"<?php } ?>>
                            <a class="nav-submenu" data-toggle="nav-submenu" data-pinyin="<?= $submenuPyTitles[$a->id] ?>"
                               href="<?php if (count($submenuArr) == 0) {
                                   echo $a->url;
                               } else { ?>#<?php } ?>"><i
                                        class="<?php echo $menuIconMap[$a->title]; ?>"></i><span
                                        class="sidebar-mini-hide"><?= $a->title ?></span></a>
                            <ul>
                                <?php foreach ($submenuArr as $b) { ?>
                                    <li>
                                        <?php if ($auditresource_current->auditmenuid == $b->id) { ?>
                                            <a class="active" href="<?= $b->url ?>" data-pinyin="<?= $submenuPyTitles[$b->id] ?>"><?= $b->title ?></a>
                                        <?php } else if ($b->url) { ?>
                                            <a href="<?= $b->url ?>" data-pinyin="<?= $submenuPyTitles[$b->id] ?>"><?= $b->title ?></a>
                                        <?php } else { ?>
                                            <a href="javascript:" data-pinyin="<?= $submenuPyTitles[$b->id] ?>"><?= $b->title ?>(未完成/已废弃)</a>
                                        <?php } ?>
                                    </li>
                                <?php } ?>
                            </ul>
                        </li>
                    <?php } ?>
                </ul>
            </div>
            <!-- END Side Content -->
        </div>
        <!-- Sidebar Content -->
    </div>
    <!-- END Sidebar Scroll Container -->
</nav>
<script>
    $(function () {
        (function (f) {
            var d = {}
            $('.side-content ul.nav-main li a').each(function () {
                d[$.trim($(this).text())] = $(this).data('pinyin').split(',')
            })
            f(d)
        }(function (pinyinjson) {
            var hascode = function (a, k) {
                if (!$.isArray(a)) {
                    return false
                }
                var r = false
                $.each(a, function (i, one) {
                    if (typeof(one) != 'string') {
                        //continue
                        return true
                    }
                    k = k.toUpperCase()
                    one = one.toUpperCase()
                    if (one.indexOf(k) != -1) {
                        //break
                        r = true
                        return false
                    }
                })
                return r
            }

            <?php if ($myauditor->id != 10130) { // 排除掉安生?>

            $('#base-material-text').autoComplete({
                type: 'all',
                select: function (event, ui) {
                    if (ui.item.type === 'doctor') {
                        window.location.href = "/doctorconfigmgr/overview?doctorid=" + ui.item.id;
                    } else if (ui.item.type === 'patient') {
                        window.location.href = "/optaskmgr/listnew?patientid=" + ui.item.id + "&patient_name=" + ui.item.value;
                    }
                }
            });
            <?php } ?>

            $('.side-content .menu-search input').keyup(function () {
                var keyword = $.trim($(this).val())
                var first_char = keyword.substr(0, 1);
                if (first_char !== '@' && first_char !== '$') { // 患者+医生
                    $('.nav-main>li:not([class="menu-search sidebar-mini-hide"])').each(function () {
                        var p = $(this)
                        var ptext = p.children('a').text()
                        var ptextpy = pinyinjson[ptext]
                        var searchInSubmenu = function () {
                            //遍历查找子菜单
                            var flag = false
                            p.find('ul li a').each(function () {
                                //console.log($(this), $(this).text(), keyword, $(this).text().indexOf(keyword))
                                var t = $.trim($(this).text())
                                var pinyin = []
                                if (pinyinjson.hasOwnProperty(t)) {
                                    pinyin = pinyinjson[t]
                                }
                                if (t.indexOf(keyword) == -1 && hascode(pinyin, keyword) === false) {
                                    $(this).hide()
                                    //console.log($(this).text(), keyword, 'hide')
                                } else {
                                    flag = true
                                    $(this).show()
                                    if (keyword != "") {
                                        $(this).html(t.replace(keyword, "<span class='text-danger'>" + keyword + "</span>"))
                                    }
                                }
                            })
                            return flag
                        }
                        //父菜单没有找到，再尝试找子菜单
                        var k = keyword.toUpperCase()
                        if (ptext.indexOf(k) != -1 || hascode(ptextpy, k) === true) {
                            p.show()
                            p.find('ul li a').show()
                            if (keyword == "") {
                                p.removeClass('open')
                            } else {
                                p.addClass('open')
                            }
                        } else {
                            //找一下子菜单
                            var flag = searchInSubmenu()
                            if (flag === false) {
                                p.hide()
                                p.removeClass('open')
                            } else {
                                p.show()
                                if (keyword == "") {
                                    p.removeClass('open')
                                } else {
                                    p.addClass('open')
                                }
                            }
                        }
                    })
                }
                if (keyword == "") {
                    $('li[nosubmenu="1"]').show();
                }
            }) //end of keyup
        }));
    })
</script>
<!-- END Sidebar -->
