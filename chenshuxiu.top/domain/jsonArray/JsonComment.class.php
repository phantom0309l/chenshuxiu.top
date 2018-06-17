<?php

class JsonComment
{
    // jsonArray
    public static function jsonArray (Comment $comment) {
        $arr = array(
            'commentid' => $comment->id,
            'typestr' => $comment->typestr,
            'title' => $comment->title,
            'content' => $comment->content,
            'replycontent' => $comment->replycontent,
            "userid" => $comment->userid,
            "user" => JsonUser::jsonArray($comment->user));

        return $arr;
    }
}