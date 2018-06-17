<?php
ini_set("arg_seperator.output", "&amp;");
ini_set("magic_quotes_gpc", 0);
ini_set("magic_quotes_sybase", 0);
ini_set("magic_quotes_runtime", 0);
ini_set('display_errors', 1);
ini_set("memory_limit", "2048M");
include_once (dirname(__FILE__) . "/../../sys/PathDefine.php");
include_once (ROOT_TOP_PATH . "/cron/Assembly.php");
mb_internal_encoding("UTF-8");

TheSystem::init(__FILE__);

// Debug::$debug = 'Dev';

//更新运营资源和菜单表中的action名字

class Update_resource_menu
{
    public function dopush () {

        $unitofwork = BeanFinder::get("UnitOfWork");

        $old_action_name = "rpt";
        $new_action_name = "rptmgr";

        $sql = " select id from auditresources where action='{$old_action_name}' ";
        $ids = Dao::queryValues($sql);

        foreach ($ids as $id) {
            Debug::trace("---------auditresourceid:{$id}");
            $auditresource = AuditResource::getById($id);
            $auditresource->action = $new_action_name;
        }

        $sql = " select id from auditmenus where url like '%/{$old_action_name}/%' ";
        $ids = Dao::queryValues($sql);

        foreach ($ids as $id) {
            Debug::trace("----------auditmenuid:{$id}");
            $auditmenu = AuditMenu::getById($id);
            $old_url = $auditmenu->url;
            $new_url = preg_replace("/{$old_action_name}/", $new_action_name, $old_url, 1);
            $auditmenu->url = $new_url;
        }

        $unitofwork->commitAndInit();
    }

}

// //////////////////////////////////////////////////////

$process = new Update_resource_menu(__FILE__);
$process->dopush();
Debug::flushXworklog();
