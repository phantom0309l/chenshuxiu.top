<?php
/**
 *
 */
include_once (ROOT_TOP_PATH . "/../core/tools/AESCrypt.php");

class LillyService
{
	public static $aeskey = 'd4OuwB8yJ41wkssXPrieLvWY/ADK5E2K';   // 礼来方接口加解密的密钥
	public static $url_online = 'https://wechat-qa.lillyadmin.cn';   // 礼来方生产环境域名
	public static $url_test = 'https://idoctorchinawechat-qa.xh3.lilly.com';   // 礼来方测试环境域名

	private $online = true;
	private $appId = "fangcunyisheng";
	//旧的密匙，idoctor调试成功后换新的
	private $appSignKey = "cfa5360820de1f5c382940bc3022c3bff96bc36e";
	//新的密匙
	// private $appSignKey = "dOuiNGR1ZutkygYlp6kC1rLaorbW4hGZmSsZfOfrBWVDoxeaNcy60qxI9xw4qaVc";
	private $nonce = "";
	private $timestamp = "";
	private $signature  = "";

	// 构造函数，初始化了很多数据
	public function __construct () {
		$this->nonce = $this->getRandomStr();
		$this->timestamp = time();

		if('development' == Config::getConfig('env')){
			$this->online = false;
		}

		$target = "appId={$this->appId}&appSignKey={$this->appSignKey}&nonce={$this->nonce}&timestamp={$this->timestamp}";
		$this->signature = sha1($target);
	}

	private function getRandomStr () {
		$metas = range(0, 9);
		$metas = array_merge($metas, range('A', 'Z'));
		$metas = array_merge($metas, range('a', 'z'));

		$str = '';

		for ($i=0; $i < 16; $i++) {
		   $str .= $metas[rand(0, count($metas)-1)];
		}
		return $str;
	}

	public function sendTemplate($messagetype, $doctor_code, $content, $msgurl = "")
	{
		$aes = new AESCrypt(LillyService::$aeskey);

		// $doctor_code = "CN-3002441HCP";
		// $content = '{first: "您好，您有一位患者",keywords: ["张三", "2017年4月27日"],remark: "点此查看"}';
		$doctor_code = $aes->encrypt($doctor_code);
		$content = $aes->encrypt($content);
		// 接口需要urlencode，礼来那边相应的会进行urldecode
		$doctor_code = urlencode($doctor_code);
		$content = urlencode($content);

		$data = array(
			"appId" => $this->appId,
			"timestamp" => $this->timestamp,
			"nonce" => $this->nonce,
			"doctorid" => $doctor_code,
			"signature" => $this->signature,
			"messagetype" => $messagetype,
			"url" => $msgurl,
			"content" => $content
		);
		$data = json_encode($data);
		$data = str_replace("\\/", "/", $data);
		Debug::trace($data);

		$url = $this->getTemplateUrl();
		$result = $this->postByCurl($url, $data);

		Debug::trace($result);
		$data = json_decode($result, true);
		return $data["Status"];
	}

	public function sendDoctorList($doctorIdList)
	{
		$aes = new AESCrypt(LillyService::$aeskey);

		// $doctorIdList = 'CN-3002441HCP';
		$doctorIdList = $aes->encrypt($doctorIdList);
		$doctorIdList = urlencode($doctorIdList);

		$data = array(
			"appId" => $this->appId,
			"timestamp" => $this->timestamp,
			"nonce" => $this->nonce,
			"signature" => $this->signature,
			"doctorIdList" => $doctorIdList
		);
		$data = json_encode($data);
		$data = str_replace("\\/", "/", $data);
		Debug::trace($data);

		$url = $this->getDoctorListUrl();
		$result = $this->postByCurl($url, $data);

		$data = json_decode($result, true);
		return $data["Status"];
	}

	//获取礼医方接口 url
	private function getTemplateUrl() {
		if($this->online){
			$url = self::$url_online . "/iDoctorWeChat/api/sendtemplatemessage";
		} else {
			$url = self::$url_test . "/iDoctorWeChat/api/sendtemplatemessage";
		}

		return $url;
	}

	//获取礼医方接口 url
	private function getDoctorListUrl() {
		if($this->online){
			$url = self::$url_online . "/iDoctorWeChat/api/syncfangcunhcps";
		} else {
			$url = self::$url_test . "/iDoctorWeChat/api/syncfangcunhcps";
		}

		return $url;
	}

	private function postByCurl($remote_server, $post_string) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $remote_server);
		// curl_setopt($ch, CURLOPT_POSTFIELDS, 'mypost=' . $post_string);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post_string);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$data = curl_exec($ch);
		curl_close($ch);

		return $data;
	}
}
