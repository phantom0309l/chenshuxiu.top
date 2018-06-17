<?php

class ArticleCommentMgrAction extends AuditBaseAction
{

    public function doList () {
        $courses = CourseDao::getListByGroupstr("article");
        $course = $courses[0];

        $lessons = $course->getLessons();
        $comments = array();

        foreach ($lessons as $i => $a) {
            $comments["$i"] = CommentDao::getListByObjtypeObjid('Lesson', $a->id, ' order by status ');
        }

        XContext::setValue("lessons", $lessons);
        XContext::setValue("comments", $comments);

        return self::SUCCESS;
    }

    public function doAuditPassPost () {
        $commentid = XRequest::getValue("commentid", 0);
        $comment = Comment::getById($commentid);
        $comment->status = 1;

        XContext::setJumpPath("/articlecommentmgr/list");

        return self::SUCCESS;
    }

    public function doAuditRefusePost () {
        $commentid = XRequest::getValue("commentid", 0);
        $comment = Comment::getById($commentid);
        $comment->status = 2;

        XContext::setJumpPath("/articlecommentmgr/list");

        return self::SUCCESS;
    }

    public function doAuditReplyPost () {
        $commentid = XRequest::getValue("commentid", 0);
        $replycontent = XRequest::getValue("replycontent", "");

        $comment = Comment::getById($commentid);
        $comment->replycontent = $replycontent;

        XContext::setJumpPath("/articlecommentmgr/list");
        return self::SUCCESS;
    }

}
