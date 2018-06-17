<?php
/**
 * Created by PhpStorm.
 * User: nigestream
 * Date: 2018/4/28
 * Time: 14:49
 */

class TestAction extends AuditBaseAction
{
    //voice
    public function doVoiceCreateByFetch() {
        $media_id = "otywF0ddOjyovE7XiorpoAYSx4N6LvGVCiLcVmCLkLc5Hzj8IkudffT5vJnyMmpm";
        $wxuserid = "490665326";
        $wxuser = WxUser::getById($wxuserid);
        $wxshop = $wxuser->wxshop;
        $access_token = $wxshop->getAccessToken();

        $url = "https://api.weixin.qq.com/cgi-bin/media/get?access_token=" . $access_token . "&media_id=" . $media_id;

        $voice = Voice::createByFetch($url, $wxuser->id);
        var_dump($voice);die;
    }

    //voice
    public function doVoiceUpload() {
        $upload_uri = "http://audit.fangcunhulian.cn/voice/upload?dev_user=chenshigang";
        $tmpname = "/tmp/media_test/97_20180130183853.amr";
        $data = [];
        $data['voicefile'] = new CURLFile($tmpname);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $upload_uri);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        $content = curl_exec($ch);

        $errno = curl_errno($ch);
        if ($errno != 0) {
            print_r(curl_error($ch));
        }
        curl_close($ch);
        print_r($content);die;
    }

    /********************************
     ***********@photo 开始**********
     *******************************/
    //直接走页面测试
    public function doUploadOneOrMulImagePost() {
        //http://wx.fangcunhulian.cn/MulPictures/list?openid=oiOZEw8AgKg7MrabLF091MpqmaGg
        echo "由于该页面需要登录状态，通过访问页面测试";
        exit();
    }

    public function doUploadImagePost() {
        $upload_uri = "http://audit.fangcunhulian.cn/picture/UploadImagePost?dev_user=chenshigang";
        $tmpname = "/tmp/media_test/cropped.jpg";
        $data = [];
        $data['imgurl'] = new CURLFile($tmpname);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $upload_uri);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        $content = curl_exec($ch);

        $errno = curl_errno($ch);
        if ($errno != 0) {
            print_r(curl_error($ch));
        }
        curl_close($ch);
        print_r($content);die;
    }

    public function doUploadWordImagePost() {
        $upload_uri = "http://audit.fangcunhulian.cn/picture/UploadWordImagePost?dev_user=chenshigang";
        $tmpname = "/tmp/media_test/cropped.jpg";
        $data = [];
        $data['upfile'] = new CURLFile($tmpname);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $upload_uri);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        $content = curl_exec($ch);

        $errno = curl_errno($ch);
        if ($errno != 0) {
            print_r(curl_error($ch));
        }
        curl_close($ch);
        print_r($content);die;
    }

    public function doUploadFaceJson() {
        $upload_uri = "http://wx.fangcunhulian.cn/zhuanti/UploadFaceJson?openid=oiOZEw8AgKg7MrabLF091MpqmaGg";
        $tmpname = "/tmp/media_test/inch1.jpg";
        $data = [];
        $data['imgurl'] = new CURLFile($tmpname);
        $cookie = "dev_user=chenshigang";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $upload_uri);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_COOKIE, $cookie);
        $content = curl_exec($ch);

        $errno = curl_errno($ch);
        if ($errno != 0) {
            print_r(curl_error($ch));
        }
        curl_close($ch);
        print_r($content);die;
    }

    //Picture Entity
    public function doCreateByFetch() {
        $picurl = "http://a0.att.hudong.com/31/35/300533991095135084358827466_950.jpg";
        $picture = Picture::CreateByFetch($picurl);
        var_dump($picture);die;
    }

    public function doCreateByFetchWXOfMediaid() {
        $media_id = "FuPvI2NhR8CwX5toX_FU3ddQ4aaewr6O_1JTR0WOeGO5EcH9Ez8upjqm4iM2kw0O";
        $picture = Picture::createByFetchWXOfMediaid($media_id);
        var_dump($picture);die;
    }

    public function doAddJson() {
        $upload_uri = "http://audit.fangcunhulian.cn/picture/addjson?dev_user=chenshigang";
        $tmpname = "/tmp/media_test/cropped.jpg";
        $data = [];
        $data['imgfile'] = new CURLFile($tmpname);
        $cookie = "dev_user=chenshigang";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $upload_uri);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_COOKIE, $cookie);
        $content = curl_exec($ch);

        $errno = curl_errno($ch);
        if ($errno != 0) {
            print_r(curl_error($ch));
        }
        curl_close($ch);
        print_r($content);die;
    }
    /********************************
     ***********@photo 结束**********
     *******************************/

    //video
    public function doVideoCreateByFetch() {
        $media_id = "w3P-sQLusjYPKQNSAa0kWT3Z2D5_lFbY6bfeqHwM1617PaLgBoQ7tCJrCotDKnz";
        $wxuserid = "103282613";
        $wxuser = WxUser::getById($wxuserid);
        $wxshop = $wxuser->wxshop;
        $access_token = $wxshop->getAccessToken();

        $url = "https://api.weixin.qq.com/cgi-bin/media/get?access_token=" . $access_token . "&media_id=" . $media_id;

        $video = Video::createByFetch($url, $wxuser->id);
        var_dump($video);die;
    }

    //meeting
    public function doMeeting() {
        $id = "682578196";
        $userName = Config::getConfig('cdr_userame');
        $pwd = Config::getConfig('cdr_pwd');
        $seed = time();
        $pwdonemd5 = md5($pwd);
        $pwdtwomd5 = md5($pwdonemd5 . $seed);

        $date = Date("Ymd");
        $cdrmeeting = CdrMeeting::getById($id);
        $recordurl = '';
        if ($cdrmeeting->cdr_record_file) {
            $recordurl = "http://api.clink.cn/{$date}/{$cdrmeeting->cdr_record_file}?enterpriseId={$cdrmeeting->cdr_enterprise_id}&userName={$userName}&pwd={$pwdtwomd5}&seed={$seed}";
        }
        $paramArr = [
            'cdr_main_unique_id' => $cdrmeeting->cdr_main_unique_id,
            'recordurl' => $recordurl
        ];
        $params = json_encode($paramArr, JSON_UNESCAPED_UNICODE);
        print_r($params);

        $job = Job::getInstance();
        $job->doBackground('download_cdrmeeting_airvoice', $params);
        die;
    }

    public function doIndex() {
        Debug::trace("hello", "fangcun", "你
            好", 12345, true);
        Debug::trace("hello" . " fangcun" . "你好 " . 123456);
        Config::setConfig('open_sql_log', false);
        $user = User::getById(1);
        $obj = new stdClass();
        $obj->name = "老王";
        $obj->data = ['body' => 'young', 'face' => 'beatiful'];
        Debug::trace();
        Debug::info(__METHOD__, "xxx", ["你是一个good man"]);
        //Config::setConfig('open_sys_log', false);
        Debug::sys("lksjdfkl0---sys");
        Debug::warn($user, ['good', 'man', '好人'], $obj, true, 123, "123", 98.5);
        $foo = new Foo();
        $foo->setA('你是个好人');
        Debug::error($foo, 'bar', $obj);
        DBC::requireTrue(false, "hello dbc");

        return self::TEXTJSON;
    }

}

class Foo Implements \JsonSerializable{
    private $a;
    public function __construct() {
        $this->a = 11;
    }

    public function getA() {
        return $this->a;
    }

    public function setA($a) {
        $this->a = $a;
    }

    public function jsonSerialize() {
        $vars = get_object_vars($this);
        return $vars;
    }
}
