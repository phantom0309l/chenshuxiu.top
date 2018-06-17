<!DOCTYPE html>
<html>
<head>
    <title><?= strip_tags($pagetitle) ?></title>
    <meta name="description" content="方寸医生运营平台">
    <meta name="author" content="方寸研发团队">
    <meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1.0">

    <!-- Stylesheets -->
    <!-- Web fonts -->
    <link rel="stylesheet"
          href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400italic,600,700%7COpen+Sans:300,400,400italic,600,700">

    <!-- Page JS Plugins CSS -->
    <link rel="stylesheet" href="<?= $img_uri ?>/vendor/oneui/js/plugins/slick/slick.min.css">
    <link rel="stylesheet" href="<?= $img_uri ?>/vendor/oneui/js/plugins/slick/slick-theme.min.css">

    <link rel="stylesheet" href="<?= $img_uri ?>/vendor/oneui/js/plugins/select2/select2.min.css">
    <link rel="stylesheet" href="<?= $img_uri ?>/vendor/oneui/js/plugins/select2/select2-bootstrap.css">
    <!--    <link rel="stylesheet" href="-->
    <? //= $img_uri ?><!--/vendor/oneui/js/plugins/select2/select2-bootstrap.css">-->

    <!-- Auto Complete -->
    <link rel="stylesheet" id="css-main" href="<?= $img_uri ?>/static/css/jquery-ui.autocomplete.min.css?v=20180208">

    <!-- OneUI CSS framework -->
    <link rel="stylesheet" id="css-main" href="<?= $img_uri ?>/vendor/oneui/css/oneui.css">
    <link rel="stylesheet" id="css-main" href="<?= $img_uri ?>/v3/audit_base.css?v=2018052501">
    <link rel="stylesheet" href="<?= $img_uri ?>/v3/audit_xsheet.css">
    <!--应该按需加载-->
    <link rel="stylesheet" href="<?= $img_uri ?>/static/css/blueimp-gallery.min.css">
    <?php if (is_array($cssFiles)) {
        foreach ($cssFiles as $cssFile) { ?>
            <link rel="stylesheet" href="<?= $cssFile ?>">
        <?php }
    }

    $theme_color = Config::getConfig('env') === 'production' ? '#438eb9' : '#5c90d2';
    ?>
    <style><?= $pageStyle ?></style>
    <style>
        .ui-autocomplete {
            z-index: 1032;
        }

        #sidebar {
            background-color: #eef1f6;
        }

        .header-navbar-fixed #main-container {
            padding-top: 48px;
            background: #fff;
        }

        .nav-main a, .nav-main ul a {
            color: #48576a;
        }

        .nav-main a.active, .nav-main a.active:hover {
            color: #20a0ff;
        }

        .nav-main li.open {
            background-color: #e4e8f1;
        }

        .nav-main ul {
            background-color: #e4e8f1;
        }

        .nav-main li.open > a.nav-submenu {
            color: #20a0ff;
        }

        .nav-main a:hover, .nav-main a:focus {
            background-color: #d1dbe5;
            color: #48576a;
        }

        .nav-main ul a:hover, .nav-main ul a:focus {
            color: #20a0ff;
        }

        .nav-main li > a.nav-submenu > i, .nav-main li.open > a.nav-submenu > i {
            color: #48576a;
        }

        .bg-white-op {
            background-color: <?= $theme_color ?>;
        }

        .bg-white-op_dev {
            background-color: <?= $theme_color ?>;
        }

        .side-header {
            min-height: 48px;
        }

        .side-header > span, .side-header > a {
            line-height: 22px;
        }

        #header-navbar {
            min-height: 40px;
            /*border-left: 1px solid #ddd;*/
            background-color: <?= $theme_color ?>;
        }

        #header-navbar_dev {
            min-height: 40px;
            /*border-left: 1px solid #ddd;*/
            background-color: <?= $theme_color ?>;
        }

        .content-mini.content-mini-full {
            padding-top: 7px;
            padding-bottom: 7px;
        }

        .side-header .site-logo {
            width: 50px;
            height: 20px;
        }

        .sidebar-mini .side-header .site-logo {
            margin-left: -14px;
        }

        .side-content .menu-search input {
            padding-left: 20px;
        }

        /*修正js-select2样式*/
        .select2-container .select2-selection--single {
            height: 34px;
            line-height: 1.42857143;
            border: 1px solid #e6e6e6;
            color: #646464;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: #646464;
            line-height: 2.328571;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow b {
            margin-top: 0;
        }

        .select2-container--default .select2-search--dropdown .select2-search__field {
            border: 1px solid #e6e6e6;
        }

        .select2-results {
            color: #646464;
        }

        /*select2 readonly，通过样式解决select2不支持readonly的问题*/
        select[readonly].select2-hidden-accessible + .select2-container {
            pointer-events: none;
            touch-action: none;
        }

        select[readonly].select2-hidden-accessible + .select2-container .select2-selection {
            background: #eee;
            box-shadow: none;
        }

        select[readonly].select2-hidden-accessible + .select2-container .select2-selection__arrow,
        select[readonly].select2-hidden-accessible + .select2-container .select2-selection__clear {
            display: none;
        }

        /*global*/
        #main-container > div.col-md-12 {
            padding-left: 0;
            padding-right: 0;
        }

        .btn-trans {
            background: transparent;
            color: #f5f5f5;
            border: 0;
        }

        .el-message {
            box-shadow: 0 2px 4px rgba(0, 0, 0, .12), 0 0 6px rgba(0, 0, 0, .04);
            min-width: 300px;
            padding: 10px 12px;
            box-sizing: border-box;
            border-radius: 2px;
            position: fixed;
            left: 50%;
            top: 20px;
            transform: translate(-50%, -100%);
            background-color: #fff;
            transition: opacity .3s, transform .4s;
            overflow: hidden;
        }

        .el-message__img {
            width: 40px;
            height: 40px;
            position: absolute;
            left: 0;
            top: 0;
        }

        .el-message__group {
            margin-left: 38px;
            position: relative;
            height: 20px;
            line-height: 20px;
            display: -ms-flexbox;
            display: flex;
            -ms-flex-align: center;
            align-items: center;
        }

        .el-message__group p {
            font-size: 14px;
            margin: 0 34px 0 0;
            white-space: nowrap;
            color: #8391a5;
            text-align: justify;
        }

        .el-message-fade-enter-active {
            opacity: 1;
            -ms-transform: translate(-50%, 0%);
            transform: translate(-50%, 0%)
        }

        .el-message-fade-leave-active {
            opacity: 0;
            -ms-transform: translate(-50%, -100%);
            transform: translate(-50%, -100%)
        }

        .vip_quickpass {
            display: inline-block;
            height: 24px;
            width: 24px;
            background: url("<?= $img_uri ?>/static/img/vip_img.png") no-repeat;
            background-size: 20px;
            vertical-align: middle;
        }

        .todaymark {
            display: inline-block;
            vertical-align: middle;
            height: 24px;
            width: 24px;
            background-size: 20px;
            background-repeat: no-repeat;
        }

        .todaymark_primary {
            background-image: url("<?= $img_uri ?>/static/img/star_img.png");
        }

        .todaymark_default {
            background-image: url("<?= $img_uri ?>/static/img/star_default.png");
        }
    </style>
    <!-- OneUI Core JS: jQuery, Bootstrap, slimScroll, scrollLock, Appear, CountTo, Placeholder, Cookie and App.js -->
    <script src="<?= $img_uri ?>/vendor/oneui/js/core/jquery.min.js"></script>
    <script src="<?= $img_uri ?>/vendor/oneui/js/core/bootstrap.min.js"></script>
    <!-- slims scrollLock appear countTo placeholder js.cookie storage  -->
    <script src="<?= $img_uri ?>/vendor/oneui/js/core/jquery.bundle.min.js"></script>
    <script src="<?= $img_uri ?>/vendor/oneui/js/app.js"></script>

    <!-- Page Plugins -->
    <script src="<?= $img_uri ?>/vendor/oneui/js/plugins/slick/slick.min.js"></script>
    <script src="<?= $img_uri ?>/static/js/okzoom.js"></script>
    <script src="<?= $img_uri ?>/static/js/vendor/jquery-ui.custom.min.js"></script>
    <script src="<?= $img_uri ?>/static/js/laydate5/laydate.min.js?ver=20180103"></script>
    <script src="<?= $img_uri ?>/v3/cym.js"></script>
    <script src="<?= $img_uri ?>/vendor/oneui/js/plugins/select2/select2.full.min.js"></script>
    <script src="<?= $img_uri ?>/vendor/oneui/js/plugins/bootstrap-notify/bootstrap-notify.min.js"></script>
    <script src="<?= $img_uri ?>/v5/common/websocket.js?ver=2018031503"></script>

    <!-- Auto Complete -->
    <script src="<?= $img_uri ?>/static/js/vendor/jquery-ui.autocomplete.min.js?v=20180208"></script>
    <script src="<?= $img_uri ?>/v5/common/autoComplete.js"></script>

    <!-- Page JS Code -->
    <?php if (is_array($jsFiles)) {
        foreach ($jsFiles as $jsFile) { ?>
            <script src="<?= $jsFile ?>"></script>
        <?php }
    } ?>
    <script><?= $pageScript ?></script>
    <script>
        Date.prototype.Format = function (fmt) {
            var o = {
                "M+": this.getMonth() + 1,
                "D+": this.getDate(),
                "h+": this.getHours(),
                "m+": this.getMinutes(),
                "s+": this.getSeconds(),
                "q+": Math.floor((this.getMonth() + 3) / 3),
                "S": this.getMilliseconds()
            };
            if (/(Y+)/.test(fmt)) fmt = fmt.replace(RegExp.$1, (this.getFullYear() + "").substr(4 - RegExp.$1.length));
            for (var k in o)
                if (new RegExp("(" + k + ")").test(fmt))
                    fmt = fmt.replace(RegExp.$1, (RegExp.$1.length == 1) ? (o[k]) : (("00" + o[k]).substr(("" + o[k]).length)));
            return fmt;
        }


        //premsg
        var preMsg = '<?= $preMsg ?>';
        $(function () {
            if (preMsg != '') {
                $("#preMsg").prop('class', 'el-message el-message-fade-enter-active');
                setTimeout(hidePreMsg, 3000);
            }

            function hidePreMsg() {
                $("#preMsg").prop('class', 'el-message el-message-fade-leave-active');
            }

            $(".navbar-toggle").on("click", function () {
                var navNode = $("#navbar");
                if (navNode.is(":visible")) {
                    navNode.slideUp(200);
                } else {
                    navNode.slideDown(200);
                }
            });

            $(document).on(
                "click",
                ".calendar",
                function () {
                    if ($(this).data('laydate') != 'init') {
                        $(this).data('laydate', 'init');
                        var value = $(this).val();
                        if (value == '0000-00-00' || value == '0000-00-00 00:00:00') {
                            value = new Date();
                            $(this).val(value.Format('YYYY-MM-DD'));
                        }
                        laydate.render({
                            elem: this,
                            value: value,
                            show: true
                        });
                    }
                });

            $(document).on(
                "change",
                "#selectDoctor",
                function () {
                    var val = parseInt($(this).val());
                    //var url = location.pathname + '?doctorid=' + val ;
                    var url = val == 0 ? location.pathname : location.pathname + '?doctorid=' + val;
                    window.location.href = url;
                });
            $('.modal-dialog').draggable({
                cursor: "move",
                cancel: ".block-content, .modal-body, .modal-footer"
            });
            // Init page helpers (Slick Slider plugin)
            App.initHelpers('slick');
            App.initHelper('notify');
        });

        <?php // 开发环境不连接websocket
        $env = Config::getConfig("env");
        if ('production' == $env) { ?>
        var ws = new FCWebSocket({
            url: '<?= $websocket_host ?>',
            sid: $.cookie('_myuserid_'),
            event_codes: [
                'wsquickpass:pushMessage',
                'wsquickconsult:pushMessage'
            ]
        });
        <?php } ?>

    </script>
</head>
<body>
<!-- Page Container -->
<!--
    Available Classes:

    'sidebar-l'                  Left Sidebar and right Side Overlay
    'sidebar-r'                  Right Sidebar and left Side Overlay
    'sidebar-mini'               Mini hoverable Sidebar (> 991px)
    'sidebar-o'                  Visible Sidebar by default (> 991px)
    'sidebar-o-xs'               Visible Sidebar by default (< 992px)

    'side-overlay-hover'         Hoverable Side Overlay (> 991px)
    'side-overlay-o'             Visible Side Overlay by default (> 991px)

    'side-scroll'                Enables custom scrolling on Sidebar and Side Overlay instead of native scrolling (> 991px)

    'header-navbar-fixed'        Enables fixed header
-->
<div id="page-container"
     class="sidebar-l sidebar-o side-scroll header-navbar-fixed <?php if ($sideBarMini === true) { ?>sidebar-mini<?php } ?>">
    <!-- Side Overlay-->
    <aside id="side-overlay">
        <!-- Side Overlay Scroll Container -->
        <div id="side-overlay-scroll">
            <!-- Side Header -->
            <div class="side-header side-content">
                <!-- Layout API, functionality initialized in App() -> uiLayoutApi() -->
                <button class="btn btn-default pull-right" type="button" data-toggle="layout"
                        data-action="side_overlay_close">
                    <i class="fa fa-times"></i>
                </button>
                <span>
                            <img class="img-avatar img-avatar32"
                                 src="<?= $img_uri ?>/vendor/oneui/img/avatars/avatar10.jpg" alt="">
                            <span class="font-w600 push-10-l">头部区域</span>
                        </span>
            </div>
            <!-- END Side Header -->

            <!-- Side Content -->
            <div class="side-content remove-padding-t">
                内容区域
            </div>
            <!-- END Side Content -->
        </div>
        <!-- END Side Overlay Scroll Container -->
    </aside>
    <!-- END Side Overlay -->
    <?php include_once dirname(__FILE__) . '/_nav.new.tpl.php'; ?>
    <!-- Header -->
    <header id="header-navbar" class="content-mini content-mini-full">
        <!-- Header Navigation Right -->
        <ul class="nav-header pull-right">
            <li class="hidden-xs">
                <div class="optask-patientmsg-cnt" style="display:inline-block">
                </div>
            </li>
            <li class="hidden-xs">
                <?php
                $pendingQuickConsultOrderCount = $myauditor->getPendingQuickConsultOrderCount();
                if ($pendingQuickConsultOrderCount > 0) {
                    ?>
                    <a target="_blank" style="display:inline-block"
                       href="/quickconsultordermgr/list?status=3"
                       class="btn btn-default btn-trans" type="button">
                        待处理快速咨询
                    </a>
                    <span class="badge badge-danger"><?= $pendingQuickConsultOrderCount ?></span>
                    <?php
                }
                ?>
            </li>
            <li class="hidden-xs">
                <?php
                $quickPassWaitAuditorReplyPatientCount = $myauditor->getQuickPassWaitAuditorReplyPatientCount();
                if ($quickPassWaitAuditorReplyPatientCount > 0) {
                    ?>
                    <a target="_blank" style="display:inline-block"
                       href="javascript:void(0);"
                       class="btn btn-default btn-trans" type="button">
                        快速通行证
                    </a>
                    <span class="badge badge-danger"><?= $quickPassWaitAuditorReplyPatientCount ?></span>
                    <?php
                }
                ?>
            </li>
            <li class="hidden-xs">
                <?php
                //$needAuditPatientCnt = XCache::getValue('need_audit_patient_cnt', 600, function() use($myauditor){
                //return $myauditor->needAuditPatientCnt();
                //});
                $needAuditPatientCnt = $myauditor->needAuditPatientCnt();
                if ($needAuditPatientCnt > 0) {
                    ?>
                    <a target="_blank" style="display:inline-block"
                       href="/patientmgr/needauditlist?auditorid=<?= $myauditor->id ?>"
                       class="btn btn-default btn-trans" type="button">
                        待审核患者
                    </a>
                    <span class="badge badge-danger"><?= $needAuditPatientCnt ?></span>
                    <?php
                }
                ?>
            </li>
            <li>
                <?php
                if ($myauditor->isDoctorHasNewPipe()) {
                    ?>
                    <a href="/dwx_pipemgr/list?auditorid=<?= $myauditor->id ?>" class="btn btn-default btn-trans"
                       type="button">
                        医生有新消息<span style="color: red">*</span>
                    </a>
                    <?php
                }
                ?>
            </li>
            <li>
                <div class="btn-group">
                    <button class="btn btn-default dropdown-toggle btn-trans" data-toggle="dropdown" type="button">
                        <?= $myuser->getShowName() ?>
                        <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-right">
                        <li>
                            <a tabindex="-1" href="<?= UrlFor::wwwLogout() ?>">
                                <i class="si si-logout pull-right"></i>退出登录
                            </a>
                        </li>
                    </ul>
                </div>
            </li>
            <li class="hidden-xs hidden-sm">
                <!-- Layout API, functionality initialized in App() -> uiLayoutApi() -->
                <button class="btn btn-default btn-trans" data-toggle="layout" data-action="side_overlay_toggle"
                        type="button">
                    <i class="fa fa-tasks"></i>
                </button>
            </li>
        </ul>
        <!-- END Header Navigation Right -->

        <!-- Header Navigation Left -->
        <ul class="nav-header pull-left">
            <li class="hidden-md hidden-lg">
                <!-- Layout API, functionality initialized in App() -> uiLayoutApi() -->
                <button class="btn btn-default btn" data-toggle="layout" data-action="sidebar_toggle" type="button">
                    <i class="fa fa-navicon"></i>
                </button>
            </li>
            <li class="hidden-xs hidden-sm">
                <!-- Layout API, functionality initialized in App() -> uiLayoutApi() -->
                <button class="btn btn-default btn-trans" data-toggle="layout" data-action="sidebar_mini_toggle"
                        type="button">
                    <i class="fa fa-ellipsis-v"></i>
                </button>
            </li>
            <!--                    <li>
                                     Opens the Apps modal found at the bottom of the page, before including JS code
                                    <button class="btn btn-default pull-right" data-toggle="modal" data-target="#apps-modal" type="button">
                                        <i class="si si-grid"></i>
                                    </button>
                                </li>-->
            <?php if (0) { ?>
                <li class="visible-xs">
                    <!-- Toggle class helper (for .js-header-search below), functionality initialized in App() -> uiToggleClass() -->
                    <button class="btn btn-default" data-toggle="class-toggle" data-target=".js-header-search"
                            data-class="header-search-xs-visible" type="button">
                        <i class="fa fa-search"></i>
                    </button>
                </li>
            <?php } ?>
            <li class="">
                <?php include $tpl . "/_disease_select.new.tpl.php"; ?>
            </li>
            <!--            <li class="js-header-search header-search collapse">-->
            <!--                <form class="form-horizontal" action="base_pages_search.html" method="post">-->
            <!--                    <div class="form-material form-material-primary input-group remove-margin-t remove-margin-b">-->
            <!--                        <input class="form-control" type="text" id="base-material-text" name="base-material-text"-->
            <!--                               placeholder="Search..">-->
            <!--                        <span class="input-group-addon" style="color:#fff;"><i class="si si-magnifier"></i></span>-->
            <!--                    </div>-->
            <!--                </form>-->
            <!--            </li>-->
        </ul>
        <!-- END Header Navigation Left -->
    </header>
    <!-- END Header -->
    <!-- Main Container -->
    <main id="main-container">
        <div id="preMsg" class="el-message <?= empty($preMsg) ? 'hide' : 'show' ?>" style="z-index: 2000;">
            <img src="data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiIHN0YW5kYWxvbmU9Im5vIj8+Cjxzdmcgd2lkdGg9IjQwcHgiIGhlaWdodD0iNDBweCIgdmlld0JveD0iMCAwIDQwIDQwIiB2ZXJzaW9uPSIxLjEiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgeG1sbnM6eGxpbms9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkveGxpbmsiPgogICAgPCEtLSBHZW5lcmF0b3I6IFNrZXRjaCAzOS4xICgzMTcyMCkgLSBodHRwOi8vd3d3LmJvaGVtaWFuY29kaW5nLmNvbS9za2V0Y2ggLS0+CiAgICA8dGl0bGU+aWNvbl9pbmZvPC90aXRsZT4KICAgIDxkZXNjPkNyZWF0ZWQgd2l0aCBTa2V0Y2guPC9kZXNjPgogICAgPGRlZnM+PC9kZWZzPgogICAgPGcgaWQ9IkVsZW1lbnQtZ3VpZGVsaW5lLXYwLjIuNCIgc3Ryb2tlPSJub25lIiBzdHJva2Utd2lkdGg9IjEiIGZpbGw9Im5vbmUiIGZpbGwtcnVsZT0iZXZlbm9kZCI+CiAgICAgICAgPGcgaWQ9Ik1lc3NhZ2UiIHRyYW5zZm9ybT0idHJhbnNsYXRlKC02MC4wMDAwMDAsIC0xNTIuMDAwMDAwKSI+CiAgICAgICAgICAgIDxnIGlkPSLluKblgL7lkJFf5L+h5oGvIiB0cmFuc2Zvcm09InRyYW5zbGF0ZSg2MC4wMDAwMDAsIDE1Mi4wMDAwMDApIj4KICAgICAgICAgICAgICAgIDxnIGlkPSJSZWN0YW5nbGUtMiI+CiAgICAgICAgICAgICAgICAgICAgPGcgaWQ9Imljb25faW5mbyI+CiAgICAgICAgICAgICAgICAgICAgICAgIDxyZWN0IGlkPSJSZWN0YW5nbGUtMiIgZmlsbD0iIzUwQkZGRiIgeD0iMCIgeT0iMCIgd2lkdGg9IjQwIiBoZWlnaHQ9IjQwIj48L3JlY3Q+CiAgICAgICAgICAgICAgICAgICAgICAgIDxwYXRoIGQ9Ik0yMS42MTUzODQ2LDI2LjU0MzIwOTkgQzIxLjYxNTM4NDYsMjYuOTQ3ODc1MSAyMS40NTgzMzQ4LDI3LjI5MTgzNjggMjEuMTQ0MjMwOCwyNy41NzUxMDI5IEMyMC44MzAxMjY4LDI3Ljg1ODM2ODkgMjAuNDQ4NzE5NCwyOCAyMCwyOCBDMTkuNTUxMjgwNiwyOCAxOS4xNjk4NzMyLDI3Ljg1ODM2ODkgMTguODU1NzY5MiwyNy41NzUxMDI5IEMxOC41NDE2NjUyLDI3LjI5MTgzNjggMTguMzg0NjE1NCwyNi45NDc4NzUxIDE4LjM4NDYxNTQsMjYuNTQzMjA5OSBMMTguMzg0NjE1NCwxOS43NDQ4NTYgQzE4LjM4NDYxNTQsMTkuMzQwMTkwNyAxOC41NDE2NjUyLDE4Ljk5NjIyOSAxOC44NTU3NjkyLDE4LjcxMjk2MyBDMTkuMTY5ODczMiwxOC40Mjk2OTY5IDE5LjU1MTI4MDYsMTguMjg4MDY1OCAyMCwxOC4yODgwNjU4IEMyMC40NDg3MTk0LDE4LjI4ODA2NTggMjAuODMwMTI2OCwxOC40Mjk2OTY5IDIxLjE0NDIzMDgsMTguNzEyOTYzIEMyMS40NTgzMzQ4LDE4Ljk5NjIyOSAyMS42MTUzODQ2LDE5LjM0MDE5MDcgMjEuNjE1Mzg0NiwxOS43NDQ4NTYgTDIxLjYxNTM4NDYsMjYuNTQzMjA5OSBaIE0yMCwxNS44MDQyOTgxIEMxOS40NDQ0NDI3LDE1LjgwNDI5ODEgMTguOTcyMjI0LDE1LjYxOTM2ODcgMTguNTgzMzMzMywxNS4yNDk1MDQ2IEMxOC4xOTQ0NDI3LDE0Ljg3OTY0MDYgMTgsMTQuNDMwNTI1NSAxOCwxMy45MDIxNDkxIEMxOCwxMy4zNzM3NzI2IDE4LjE5NDQ0MjcsMTIuOTI0NjU3NSAxOC41ODMzMzMzLDEyLjU1NDc5MzUgQzE4Ljk3MjIyNCwxMi4xODQ5Mjk1IDE5LjQ0NDQ0MjcsMTIgMjAsMTIgQzIwLjU1NTU1NzMsMTIgMjEuMDI3Nzc2LDEyLjE4NDkyOTUgMjEuNDE2NjY2NywxMi41NTQ3OTM1IEMyMS44MDU1NTczLDEyLjkyNDY1NzUgMjIsMTMuMzczNzcyNiAyMiwxMy45MDIxNDkxIEMyMiwxNC40MzA1MjU1IDIxLjgwNTU1NzMsMTQuODc5NjQwNiAyMS40MTY2NjY3LDE1LjI0OTUwNDYgQzIxLjAyNzc3NiwxNS42MTkzNjg3IDIwLjU1NTU1NzMsMTUuODA0Mjk4MSAyMCwxNS44MDQyOTgxIFoiIGlkPSJDb21iaW5lZC1TaGFwZSIgZmlsbD0iI0ZGRkZGRiI+PC9wYXRoPgogICAgICAgICAgICAgICAgICAgIDwvZz4KICAgICAgICAgICAgICAgIDwvZz4KICAgICAgICAgICAgPC9nPgogICAgICAgIDwvZz4KICAgIDwvZz4KPC9zdmc+"
                 alt="" class="el-message__img">
            <div class="el-message__group"><p><?= $preMsg ?></p></div>
        </div>
        <?php include_once $tpl . "/_pagetitle.new.tpl.php"; ?>
