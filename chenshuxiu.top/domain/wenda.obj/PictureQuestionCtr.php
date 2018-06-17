<?php

class PictureQuestionCtr extends QuestionCtr
{

    public function getHtml () {
        return $this->getHtmlImp('picture.ctr.php');
    }

    public function getWenzhenHtml () {
        return $this->getHtmlImp('picture.ctr.php');
    }

    public function getHtml4hwk () {
        return $this->getHtmlImp('picture.ctr.liu.php');
    }

    //直接定制了
    public function getHtmlOfCheckupTpl4Admin() {
        $ctrFileName = 'picture.ctr.php';
        $inputClassDiv = $this->xquestion->ename;
        if ($this->xquestion->isDefaultHide()) {
            $inputClassDiv .= ' collapse';
        }
        $tmp = <<< INPUTHTML
            <div class="form-group {$inputClassDiv}">
            <label class="control-label col-md-2 col-sm-2">{$this->getQuestionContent()}</label>
            <div class="col-md-10 col-sm-10">
            {$this->getStartHtml()}
            <div style="margin-top:5px;">
INPUTHTML;
        echo $tmp;
        $pictureid = $this->getAnswerContent();
        if (empty($pictureid)) {
            $pictureid = 0;
        }
        $dtpl = XContext::getValue("dtpl");
        $img_uri = XContext::getValue("img_uri");
        $picWidth = 150;
        $picHeight = 150;
        $pictureInputName = "{$this->getContentInputName()}";
        $isCut = false;
        $picture = Picture::getById($pictureid);
        include ("{$dtpl}/{$ctrFileName}");
        echo "</div></div></div>";
        return "";
    }

    private function getHtmlImp ($ctrFileName = 'picture.ctr.php') {
        $tmp = <<< INPUTHTML
            <span class="mb5">{$this->getQuestionContent()}</span>
            {$this->getStartHtml()}
            <div style="margin-top:5px;">
INPUTHTML;
        echo $tmp;
        $pictureid = $this->getAnswerContent();
        if (empty($pictureid)) {
            $pictureid = 0;
        }
        $dtpl = XContext::getValue("dtpl");
        $img_uri = XContext::getValue("img_uri");
        $picWidth = 150;
        $picHeight = 150;
        $pictureInputName = "{$this->getContentInputName()}";
        $isCut = false;
        $picture = Picture::getById($pictureid);
        include ("{$dtpl}/{$ctrFileName}");
        echo "</div>";
        return "";
    }

    // 重载
    public function getQaHtmlAnswerContent () {
        $pictureid = $this->getAnswerContent();

        $picture = null;
        if ($pictureid > 0) {
            $picture = Picture::getById($pictureid);
        }

        $str = '';
        if ($picture instanceof Picture) {
            $src = $picture->getSrc();
            $thumb = $picture->getSrc(200, 200);
            $str .= "<a target='_blank' href='{$src}'><img src='{$thumb}' /></a>";
        } else {
            $str .= '无图片';
        }
        return $str;
    }

}
