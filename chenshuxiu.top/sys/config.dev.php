<?php

// $domain = "fangcunyisheng.com";
// $domain = "fangcunyisheng.cn";
// $domain = "fangcunhulian.com";
// $domain = "fangcunhulian.cn";

$ip002 = "10.170.176.163"; // fangcun002, 生产环境主库
$ip001 = "10.172.220.86"; // fangcun001, 生产环境从库, 暂时没用
$ipdev = "10.25.179.16"; // fangcundev, 开发测试库

$http = 'http';
$domain = "";

if ('fangcunhulian.cn' == $domain) {
    // 开发环境
    $env = 'development';
    $theip = $ipdev;
    $http = 'http';
} elseif ('fangcunyisheng.com' == $domain) {
    // 生产环境
    $env = 'production';
    $theip = $ip002;
    $http = 'https';
} elseif (in_array($domain, array(
    'fangcunhulian.com',
    'fangcunyisheng.cn'))) {
    // 也是生产环境,用于审核证书用途
    $env = 'production';
    $theip = $ip002;
    $http = 'http';
} else {
    $theip = "192.168.1.24";
    //echo $domain;
    //exit();
}

// config init
$config = array();
$config['env'] = $env;
$config['website_domain'] = $domain;

// $config['xworkdev'] = true;

// ===== database settings begin =====

// 初始化一个静态变量,中心库名称/默认库名称
DaoBase::init_defaultdb_name('fcqxdb');

// database init
$config["database"] = array();

// fcqxdb init 中心库, 默认库
$config["database"]["fcqxdb"] = array();
// fcqxdb master
$config["database"]["fcqxdb"]["master"] = array(
    'db_host' => $theip,
    "db_database" => "fcqxdb",
    "db_username" => "fcdev",
    "db_password" => "fcdev",
    "db_port" => "3306");

// fcqxdb slaves
$config["database"]["fcqxdb"]["slaves"] = array();
$config["database"]["fcqxdb"]["slaves"][] = array(
    'db_host' => $theip,
    "db_database" => "fcqxdb",
    "db_username" => "fcdev",
    "db_password" => "fcdev",
    "db_hitratio" => 1,
    "db_port" => "3306");

// xworkdb init 框架日志库
$config["database"]["xworkdb"] = array();
// xworkdb master
$config["database"]["xworkdb"]["master"] = array(
        'db_host' => $theip,
        "db_database" => "xworkdb",
        "db_username" => "fcdev",
        "db_password" => "fcdev",
        "db_port" => "3306");

// xworkdb slaves
$config["database"]["xworkdb"]["slaves"] = array();
$config["database"]["xworkdb"]["slaves"][] = array(
        'db_host' => $theip,
        "db_database" => "xworkdb",
        "db_username" => "fcdev",
        "db_password" => "fcdev",
        "db_hitratio" => 1,
        "db_port" => "3306");

// statdb init 统计库
$config["database"]["statdb"] = array();
// statdb master
$config["database"]["statdb"]["master"] = array(
    'db_host' => $theip,
    "db_database" => "statdb",
    "db_username" => "fcdev",
    "db_password" => "fcdev",
    "db_port" => "3306");

// statdb slaves
$config["database"]["statdb"]["slaves"] = array();
$config["database"]["statdb"]["slaves"][] = array(
    'db_host' => $theip,
    "db_database" => "statdb",
    "db_username" => "fcdev",
    "db_password" => "fcdev",
    "db_hitratio" => 1,
    "db_port" => "3306");

// ===== database settings middle =====

// 开发环境,清洗开发库用途
if ('fangcunhulian.cn' == $domain) {
    // fcqxdb_tmp init
    $config["database"]["fcqxdb_tmp"] = array();
    // fcqxdb_tmp master
    $config["database"]["fcqxdb_tmp"]["master"] = array(
        'db_host' => $ipdev,
        "db_database" => "fcqxdb_tmp",
        "db_username" => "fcdev",
        "db_password" => "fcdev",
        "db_port" => "3306");

    // fcqxdb_tmp slaves
    $config["database"]["fcqxdb_tmp"]["slaves"] = array();
    $config["database"]["fcqxdb_tmp"]["slaves"][] = array(
        'db_host' => $ipdev,
        "db_database" => "fcqxdb_tmp",
        "db_username" => "fcdev",
        "db_password" => "fcdev",
        "db_hitratio" => 1,
        "db_port" => "3306");
}

// 生产环境,修复redmine用途,用过一次20170104
if ('fangcunyisheng.com' == $domain) {
    // redmine001 init
    $config["database"]["redmine001"] = array();
    // redmine001 master
    $config["database"]["redmine001"]["master"] = array(
        'db_host' => $ip001,
        "db_database" => "redmine",
        "db_username" => "redmine",
        "db_password" => "redmine123456",
        "db_port" => "3306");

    // redmine001 slaves
    $config["database"]["redmine001"]["slaves"] = array();
    $config["database"]["redmine001"]["slaves"][] = array(
        'db_host' => $ip001,
        "db_database" => "redmine",
        "db_username" => "redmine",
        "db_password" => "redmine123456",
        "db_hitratio" => 1,
        "db_port" => "3306");

    // redmine002 init
    $config["database"]["redmine002"] = array();
    // redmine002 master
    $config["database"]["redmine002"]["master"] = array(
        'db_host' => $ip002,
        "db_database" => "redmine",
        "db_username" => "redmine",
        "db_password" => "redmine123456",
        "db_port" => "3306");

    // redmine002 slaves
    $config["database"]["redmine002"]["slaves"] = array();
    $config["database"]["redmine002"]["slaves"][] = array(
        'db_host' => $ip002,
        "db_database" => "redmine",
        "db_username" => "redmine",
        "db_password" => "redmine123456",
        "db_hitratio" => 1,
        "db_port" => "3306");
}

// ===== database settings end =====

// 框架统计服务开关
$config['xworkdbOpen'] = false;

// redis
$config['redis']['host'] = '127.0.0.1';
$config['redis']['port'] = '6379';
$config['redis']['timeout'] = 0;
$config['redis']['pconnect'] = false;

// gearmand job server
$config['gearman']['host'] = $theip;
$config['gearman']['port'] = '4730';

$config['needDBC'] = true;
$config['needUrlRewrite'] = false;

$config['cacheOpen'] = false; // open memcached?
$config["mem_cached_cluster"][] = array(
    "host" => '127.0.0.1',
    "port" => '11211');
$config["key_prefix"] = $domain;
$config["cacheExpireTime"] = 3600;
$config["entityCacheOpen"] = false; // 如果打开各个系统都需要打开,否则会造成数据不一致
$config["entityCacheExpireTime"] = 7200;
$config["idListCacheOpen"] = true;
$config["idListCacheExpireTime"] = 600;

$config['debug'] = 'Dev1';
$config['debugkey'] = 'fcqx20170904';
$config['debug_ouput'] = 'web';
$config['debug_trace'] = false;
$config['debug_errlog'] = true;
$config['debug_logpath'] = ROOT_TOP_PATH . "/../xworklog/" . $domain;
$config['debug_sqllog_close'] = false;

$subsys_arr = array(
    'admin',
    'api',
    'audit',
    'da',
    'dapi',
    'dm',
    'doctor',
    'dwx',
    'ipad',
    'www',
    'wx',
    'wxapp',
    'img',
    'voice');

foreach ($subsys_arr as $subsys) {
    $config["{$subsys}_uri"] = "{$http}://{$subsys}.{$domain}";
}

// 公众号或小应用
$config['dwx_uri'] = "http://dwx.{$domain}";
$config['wx_uri'] = "http://wx.{$domain}";
$config['wxapp_uri'] = "http://wxapp.{$domain}";

// 都用线上的图片库
$config['photo_uri'] = "https://photo.{$domain}";

// 图片存储本地路径
$config['xphoto_path'] = '/home/xdata/xphoto';

// 客户端下载接口
$config['ios_app_url'] = 'https://itunes.apple.com/cn/app/id955693193';
$config['and_app_url'] = 'https://img.fangcunyisheng.com/apk/fcqx-doctor-and.apk';

// 电话会议
$config['meeting_telephone'] = '18600871932'; // 主叫号码
$config['customer_ser_num'] = '01082038177'; // 被叫侧显示号码,备用电话 01066168966

// 云通讯回调
$config['hangup_cdr_host'] = "http://api.{$domain}";

// 电话录音数据存放路径
$config['meeting_airvoice_path'] = ROOT_TOP_PATH . '/wwwroot/voice/meeting';

// 多个微信号统一token
$config['weixin_token'] = 'qwer0325';

// icp
$config['icp'] = '京ICP备15024348号';

// company
$config['company'] = '方寸泉香(北京)科技有限公司';
