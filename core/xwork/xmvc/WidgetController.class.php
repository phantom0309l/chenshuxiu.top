<?php

/**
 * WidgetController
 * @desc		挂件控制器
 * @remark		依赖类: 框架内的多个类
 * @copyright	(c)2012 xwork.
 * @file		WidgetController.class.php
 * @author		shijianping <shijpcn@qq.com>
 * @date		2012-02-26
 *
 * 机制：类似于XController, 调用action->method, 完成一个独立功能;区别于XController的地方是内嵌了 unitOfWork->commitAndInit ; 不需要
 * 用途：在主action的模板上, 可以通过调用 WidgetController::process(...), 来完成组合action的目的
 * 注意：不适用于post类型的action
 * 注意：暂不支持拦截器
 * 警告为了保证 parent_action 和 widget 的 setcookie 不出错，在 XView::render 或 更早的地方 加了一行 ob_start();
 */
class WidgetController
{
    // 静态方法
    public static function process ($action, $method, $params = array(), $template_root_path = "") {
        Debug::sys("[-- widget beg [$action,$method] --]");

        $timeStart = XUtility::getStartTime();

        // 备份 $_REQUEST
        $_REQUEST_bak = $_REQUEST; // 备份 TODO实验需要
        $mainAction = XRequest::getValue("xaction", '');
        $mainMethod = XRequest::getValue("method", '');

        // 清空 $_REQUEST,并赋新值
        $_REQUEST = array();
        $_REQUEST["parent_xaction"] = $mainAction;
        $_REQUEST["parent_method"] = $mainMethod;
        $_REQUEST["xaction"] = $action;
        $_REQUEST["method"] = $method;
        foreach ($params as $k => $v) {
            $_REQUEST[$k] = $v;
        }

        // 初始化XContext的widget数据区 并 启动一个新的 UnitOfWork
        XContext::startWidget();
        BeanFinder::startWidget();
        $unitOfWork = BeanFinder::get("UnitOfWork");

        $template_root_path = $template_root_path ? $template_root_path : WWW_TPL_PATH;
        $template_root_path .= "/";

        $controller = new WidgetController($template_root_path, $action, $method);
        $controller->doProcess();

        // 提交工作单元
        if (XContext::getValue("notcommit") != true) {
            $unitOfWork->commit();
        }

        // 还原XContext 和 UnitOfWork
        XContext::stopWidget();
        BeanFinder::stopWidget();
        $_REQUEST = $_REQUEST_bak;

        // 记录耗时
        $timeSpan = XUtility::getCostTime($timeStart);
        $timeSpan = XUtility::trimTimeSpan($timeSpan);

        Debug::sys("[-- widget end [$action,$method][{$timeSpan}] --]");
    }

    // /////////////////////////////////////////////////////////
    private $template_root_path = "";

    // 保存处理结果消息信息，成功失败均可设置
    private $errorMsg = "";

    private $action = "";

    private $method = "";

    private function __construct ($template_root_path, $action, $method) {
        $this->template_root_path = $template_root_path;
        $this->action = $action;
        $this->method = $method;
    }

    private function doProcess () {
        $actionClass = $this->getActionClassName();
        $actionMethod = $this->getActionMethod();

        global $lowerclasspath;
        if (! empty($lowerclasspath)) {
            $realname = $lowerclasspath[strtolower($actionClass)];

            DBC::requireNotEmpty($realname, "actionClass:$actionClass not found!");
            $actionClass = $realname;
        }

        $actionObject = new $actionClass();

        try {
            $result = $actionObject->$actionMethod();
            $this->errorMsg = XContext::getMessage();
        } catch (BizException $bizEx) {
            XContext::setValue("exception", $bizEx);
            Debug::errlogEx($bizEx, "bizEx");

            $this->errorMsg = $bizEx->getMessage();
            XContext::setValue("notcommit", true);
            $result = XAction::SYSTEM_ERROR;
        } catch (DbException $dbEx) {
            XContext::setValue("exception", $dbEx);
            Debug::errlogEx($dbEx, "dbEx");

            $this->errorMsg = "数据库操作失败，请重试"; // 主要针对的是并发冲突
            XContext::setValue("notcommit", true);
            $result = XAction::SYSTEM_ERROR;
        } catch (Exception $ex) {
            XContext::setValue("exception", $ex);
            Debug::errlogEx($ex, "topEx");

            $this->errorMsg = "操作失败,请联系技术人员";
            if (Config::getConfig('innerSystem')) {
                $this->errorMsg .= ("!内部系统错误提示：<br/>");
                $this->errorMsg .= $ex->getMessage();
                $this->errorMsg .= "<br/>";
                $this->errorMsg .= $ex->getTraceAsString();
            }

            XContext::setValue("notcommit", true);
            $result = XAction::SYSTEM_ERROR;
        }

        // ////////////////////////////
        if (XAction::SUCCESS == $result) {
            $template = $this->geSuccessTemplate();
        } elseif (XAction::SYSTEM_ERROR == $result) {
            $template = $this->getErrorTemplate();
        } elseif (XAction::IMG == $result) {
            header('Content-type: image/jpeg');
            echo XContext::getValue("data");
            return;
        } elseif (XAction::BLANK == $result) {
            return;
        } else {
            $template = $this->getOtherTemplate($result);
        }

        // 创建视图
        $view = new XView(XContext::getModel(), $template);
        $view->setValue("errorMsg", $this->errorMsg);
        // 渲染模板
        $view->render();
        return;
    }

    private function getActionClassName () {
        return $this->action . "Action";
    }

    private function getActionMethod () {
        return "do" . $this->method;
    }

    // 成功模板
    private function geSuccessTemplate () {
        // 允许中断
        if (XContext::getSuccessTemplate() != null && XContext::getSuccessTemplate() != "") {
            $successTemplate = XContext::getSuccessTemplate();
        } else {
            $successTemplate = $this->action . "/" . $this->method . ".tpl.php"; // 方法名称
        }
        return $this->template_root_path . strtolower($successTemplate);
    }

    // 失败模板
    private function getErrorTemplate () {
        // 允许中断
        if (XContext::getErrorTemplate() != null && XContext::getErrorTemplate() != "") {
            $errorTemplate = XContext::getErrorTemplate();
        } else {
            $errorTemplate = $this->action . "/" . $this->method . "." . XAction::SYSTEM_ERROR . ".tpl.php"; // 方法名称
        }
        return $this->template_root_path . strtolower($errorTemplate);
    }

    // 其他结果模板
    private function getOtherTemplate ($result) {
        if (empty($result)) {
            echo "not action return value";
            exit();
        }
        $resultTemplate = $this->action . "/" . $this->method . ".$result.tpl.php";
        return $this->template_root_path . strtolower($resultTemplate);
    }
}
