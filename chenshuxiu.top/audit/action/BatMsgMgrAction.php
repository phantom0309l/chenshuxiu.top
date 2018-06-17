<?php

class BatMsgMgrAction extends AuditBaseAction
{

    // 广播消息列表 , order by auditstatus asc , status desc, issend asc
    public function doList () {
        $batmsgs = BatMsgDao::getAllList();

        $mydisease = $this->mydisease;

        $arr = array();
        foreach ($batmsgs as $a) {

            if ($mydisease instanceof Disease && false == $a->user->getDoctor()->isBindDisease($mydisease->id)) {
                continue;
            }

            $arr[] = $a;
        }

        XContext::setValue("batmsgs", $arr);

        return self::SUCCESS;
    }

    // 广播消息Html
    public function doOneHtml () {
        $id = XRequest::getValue("id", 0);
        $batmsg = BatMsg::getById($id);
        XContext::setValue("batmsg", $batmsg);

        return self::SUCCESS;
    }

    // 广播消息修改
    public function doModifyJson () {
        $id = XRequest::getValue("id", 0);
        $content = XRequest::getValue("content", '');
        $a = BatMsg::getById($id);
        $a->content = $content;
        $a->set4lock("auditorid", $this->getAuditorid());
        echo "ok";
        return self::BLANK;
    }

    // 广播消息审核通过
    public function doPassJson () {
        $id = XRequest::getValue("id", 0);
        $content = XRequest::getValue("content", '');
        $a = BatMsg::getById($id);
        $a->content = $content;
        $a->auditstatus = 1;
        $a->status = 1;
        $a->set4lock("auditorid", $this->getAuditorid());
        echo "ok";
        return self::BLANK;
    }

    // 广播消息审核拒绝
    public function doRefuseJson () {
        $id = XRequest::getValue("id", 0);
        $a = BatMsg::getById($id);
        $a->auditstatus = 1;
        $a->status = 0;
        $a->set4lock("auditorid", $this->getAuditorid());
        echo "ok";
        return self::BLANK;
    }

    private function getAuditorid () {
        $myauditor = $this->myauditor;
        return $myauditor->id;
    }
}
