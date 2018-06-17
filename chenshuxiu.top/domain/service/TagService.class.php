<?php

class TagService
{

    // 根据 typestr 获取tag数组
    // 数组的 key 为tag.id
    // 数组的 value 为 tags.name
    public static function getTagArrByTypestr($typestr) {
        $arr = array();
        $tagList = TagDao::getListByTypestr($typestr);

        foreach ($tagList as $tag) {
            $arr[$tag->id] = $tag->name;
        }
        return $arr;
    }

    // 根据 实体和tags.typestr 获取tagids
    public static function getTagidsByObj(Entity $obj, $typestr = '') {
        $tagRefObjs = TagRefDao::getListByObj($obj, $typestr);
        $arr = array();
        foreach ($tagRefObjs as $tagRefObj) {
            $arr[] = $tagRefObj->tagid;
        }
        return $arr;
    }
}