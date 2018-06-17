<?php

// $domain = "fangcunyisheng.com";
// $domain = "fangcunyisheng.cn";
// $domain = "fangcunhulian.com";
// $domain = "fangcunhulian.cn";
$ip002 = "10.170.176.163"; // fangcun002, 生产环境主库
$ip001 = "10.172.220.86"; // fangcun001, 生产环境从库, 暂时没用
$ipdev = "10.25.179.16"; // fangcundev, 开发测试库

$http = 'http';

if ('fangcunhulian.cn' == $domain) {
    // 开发环境
    $env = 'development';
    $http = 'http';

    $fcqxdb_ip = $ipdev;
    $statdb_ip = $ipdev;
    $xworkdb_ip = $ipdev;
    $nsqd_ip = $ipdev;
    $redis_ip = $ipdev;
} elseif ('fangcunyisheng.com' == $domain) {
    // 生产环境
    $env = 'production';
    $http = 'https';

    $fcqxdb_ip = $ip003;
    $statdb_ip = $ip003;
    $xworkdb_ip = $ip002;
    $nsqd_ip = $ip002;
    $redis_ip = $ip002;
} elseif (in_array($domain, array(
    'fangcunhulian.com',
    'fangcunyisheng.cn'))) {
    // 也是生产环境,用于审核证书用途
    $env = 'production';
    $http = 'http';

    $fcqxdb_ip = $ip003;
    $statdb_ip = $ip003;
    $xworkdb_ip = $ip002;
    $nsqd_ip = $ip002;
    $redis_ip = $ip002;
} else {
    echo $domain;
    exit();
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
    'db_host' => $fcqxdb_ip,
    "db_database" => "fcqxdb",
    "db_username" => "fcdev",
    "db_password" => "fcdev",
    "db_port" => "3306");

// fcqxdb slaves
$config["database"]["fcqxdb"]["slaves"] = array();
$config["database"]["fcqxdb"]["slaves"][] = array(
    'db_host' => $fcqxdb_ip,
    "db_database" => "fcqxdb",
    "db_username" => "fcdev",
    "db_password" => "fcdev",
    "db_hitratio" => 1,
    "db_port" => "3306");

// xworkdb init 框架日志库
$config["database"]["xworkdb"] = array();
// xworkdb master
$config["database"]["xworkdb"]["master"] = array(
    'db_host' => $statdb_ip,
    "db_database" => "xworkdb",
    "db_username" => "fcdev",
    "db_password" => "fcdev",
    "db_port" => "3306");

// xworkdb slaves
$config["database"]["xworkdb"]["slaves"] = array();
$config["database"]["xworkdb"]["slaves"][] = array(
    'db_host' => $statdb_ip,
    "db_database" => "xworkdb",
    "db_username" => "fcdev",
    "db_password" => "fcdev",
    "db_hitratio" => 1,
    "db_port" => "3306");

// statdb init 统计库
$config["database"]["statdb"] = array();
// statdb master
$config["database"]["statdb"]["master"] = array(
    'db_host' => $xworkdb_ip,
    "db_database" => "statdb",
    "db_username" => "fcdev",
    "db_password" => "fcdev",
    "db_port" => "3306");

// statdb slaves
$config["database"]["statdb"]["slaves"] = array();
$config["database"]["statdb"]["slaves"][] = array(
    'db_host' => $xworkdb_ip,
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
$config['xworkdbOpen'] = true;

// redis
$config['redis']['host'] = $redis_ip;
$config['redis']['port'] = '6379';
$config['redis']['timeout'] = 0;
$config['redis']['pconnect'] = false;
$config['redis']['auth'] = 'fangcundev';

// gearmand job server
$config['gearman']['host'] = $nsqd_ip;
$config['gearman']['port'] = '4730';

// nsq 相关配置
$config['nsqd']['host'] = $nsqd_ip;
$config['nsqd']['port'] = '4151';

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

// 更新语句版本号检查
$config["update_need_check_version"] = true;

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
// $config['photo_uri'] = 'https://photo.fangcunyisheng.com';
$config['photo_uri'] = "https://photo.{$domain}";

// 图片存储本地路径
$config['xphoto_path'] = '/home/xdata/xphoto';

// 客户端下载接口
$config['ios_app_url'] = 'https://itunes.apple.com/cn/app/id955693193';
$config['and_app_url'] = 'https://img.fangcunyisheng.com/apk/fcqx-doctor-and.apk';

//礼来sunflower项目定义的密匙
$config['lilly_app_secret'] = '76ae6d63b449f379';

// 电话会议
$config['meeting_telephone'] = '18600871932'; // 主叫号码
$config['customer_ser_num'] = '01082038177'; // 被叫侧显示号码,备用电话 01066168966

$config['tel_adhd'] = '010-60643332';

// 云通讯回调
$config['hangup_cdr_host'] = "http://api.{$domain}";

// 根据ip查询地址
$config['ip2region_host'] = "http://fangcun002:9091";

// 下载服务地址
$config['dl_uri'] = "http://tool.{$domain}/download";

// websocket
$config['websocket_host'] = "123.57.23.44";
$config['websocket_port'] = "9502";

// 电话录音数据存放路径,天润融通帐号
$config['meeting_airvoice_path'] = ROOT_TOP_PATH . '/wwwroot/voice/meeting';
$config['cdr_userame'] = 'admin';
$config['cdr_pwd'] = 'fcQx1q2w3e4r';
$config['cdr_cno_pwd'] = 'fcqx1q2w3e';
$config['cdr_enterpriseid'] = '3004870';
$config['cdr_queue'] = array(
    '0000');

//天润融通短信平台帐号、密码
$config['cdr_vlink_account'] = '5100116';
$config['cdr_vlink_pswd'] = 'Aa123456';
$config['cdr_vlink_product'] = '44663480';

//漫道短信平台帐号、密码
$config['mandao_sn'] = 'SDK-BBX-010-27964';
$config['mandao_pwd'] = '1410BB-6e03';

// 多个微信号统一token
$config['weixin_token'] = 'qwer0325';

// icp
$config['icp'] = '京ICP备15024348号';

// company
$config['company'] = '方寸泉香(北京)科技有限公司';
