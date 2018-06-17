<?php
/**
 * XWork框架初始化
 * 其实就是为了加载框架基本文件
 * 框架文件有相互依赖情况
 *
 * @copyright (c) 2012 xwork.
 * @file      xwork.init.php
 * @author    shijianping <shijpcn@qq.com>
 * @date      2012-02-23
 */

// xcommon
include_once (dirname(__FILE__) . '/xcommon/Config.class.php');
include_once (dirname(__FILE__) . '/xcommon/DBC.class.php');
include_once (dirname(__FILE__) . '/xcommon/Debug.class.php');
include_once (dirname(__FILE__) . '/xcommon/LogLevel.class.php');
include_once (dirname(__FILE__) . '/xcommon/Noticer.class.php');
include_once (dirname(__FILE__) . '/xcommon/XDateTime.class.php');
include_once (dirname(__FILE__) . '/xcommon/XUtility.class.php');
include_once (dirname(__FILE__) . '/xcommon/XSessionManager.class.php');

// xmap/db
include_once (dirname(__FILE__) . '/xmap/db/DbExecuter.class.php');
include_once (dirname(__FILE__) . '/xmap/db/MysqlDataSource.class.php');
include_once (dirname(__FILE__) . '/xmap/db/PDODataSource.class.php');
include_once (dirname(__FILE__) . '/xmap/db/PDODataSourceConfig.class.php');
include_once (dirname(__FILE__) . '/xmap/db/RwMysqlDataSource.class.php');

// xmap
include_once (dirname(__FILE__) . '/xmap/BeanFinder.class.php');
include_once (dirname(__FILE__) . '/xmap/Dao.class.php');
include_once (dirname(__FILE__) . '/xmap/Entity.class.php');
include_once (dirname(__FILE__) . '/xmap/IDGenerator.class.php');
include_once (dirname(__FILE__) . '/xmap/NotEntityObj.class.php');
include_once (dirname(__FILE__) . '/xmap/Status.class.php');
include_once (dirname(__FILE__) . '/xmap/TableNameCreator.class.php');
include_once (dirname(__FILE__) . '/xmap/UnitOfWork.class.php');
include_once (dirname(__FILE__) . '/xmap/XMemCached.class.php');

// xmvc
include_once (dirname(__FILE__) . '/xmvc/ApplicationSessionModifyInterceptor.class.php');
include_once (dirname(__FILE__) . '/xmvc/ApplicationSessionReadOnlyInterceptor.class.php');
include_once (dirname(__FILE__) . '/xmvc/Interceptor.class.php');
include_once (dirname(__FILE__) . '/xmvc/WidgetController.class.php');
include_once (dirname(__FILE__) . '/xmvc/XAction.class.php');
include_once (dirname(__FILE__) . '/xmvc/XContext.class.php');
include_once (dirname(__FILE__) . '/xmvc/XController.class.php');
include_once (dirname(__FILE__) . '/xmvc/XRequest.class.php');
include_once (dirname(__FILE__) . '/xmvc/XView.class.php');
