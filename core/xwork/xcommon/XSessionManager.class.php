<?php

/**
 * XSessionManager
 * @desc 		Session管理类 , [不建议使用session,建议用可靠的cookie+db来代替session]
 * @remark		依赖类: 无
 * @copyright 	(c)2012 xwork.
 * @file		XSessionManager.class.php
 * @author		shijianping <shijpcn@qq.com>
 * @date		2012-02-26
 */
class XSessionManager
{
    // session manager是否已经初始化的标志
    private static $__sessionManagerInitialized = false;

    /**
     * static method that takes care of initializing a session if there is none
     * yet.
     * It will also call
     * setSessionSavePath() in order to tell PHP where sessions should be read
     * and stored.
     *
     * @return nothing
     * @static
     *
     * @see SessionInfo
     * @see SessionManager::setSessionSavePath()
     */
    // 初始化Session管理器
    public static function init ($session_domain = "", $session_name = "x_session", $sessionId = "") {
        if (self::$__sessionManagerInitialized == false) {
            session_name($session_name);
            session_set_cookie_params(0, "/", $session_domain);

            if ((string) $sessionId != "") {
                session_id($sessionId);
            }

            ini_set("session.save_handler", "files");
            session_start();

            // inform the other methods that the session manager has already
            // been intialized
            self::$__sessionManagerInitialized = true;
        }
    }
}
