# yg

#配置数据库

#  判断用户是否存在
YgUser::checkUserExist($account);
//获取用户资料  
# $from 数据来源,为空则调用此账户下所有用户数据
YgUser::getUserInfo($account,$from);
# 获取用户数据 默认拉取眼罩系统数据
YgUser::getUserInfoByAccount($account, $from = 'YG_YGXSj');
# 获取用户最高等级
YgUser::getUserLevel($account);


# 增加数据库配置中心

### /config/database.php 文件目录


 'YG_CENTER' => [
            // 数据库类型
            'type'            => env('database.type', 'mysql'),
            // 服务器地址
            'hostname'        => env('database.hostname', '127.0.0.1'),
            // 数据库名
            'database'        => env('database.database', ''),
            // 用户名
            'username'        => env('database.username', 'root'),
            // 密码
            'password'        => env('database.password', ''),
            // 端口
            'hostport'        => env('database.hostport', '3306'),
            // 数据库连接参数
            'params'          => [],
            // 数据库编码默认采用utf8
            'charset'         => env('database.charset', 'utf8'),
            // 数据库表前缀
            'prefix'          => env('database.prefix', ''),

            // 数据库部署方式:0 集中式(单一服务器),1 分布式(主从服务器)
            'deploy'          => 0,
            // 数据库读写是否分离 主从式有效
            'rw_separate'     => false,
            // 读写分离后 主服务器数量
            'master_num'      => 1,
            // 指定从服务器序号
            'slave_no'        => '',
            // 是否严格检查字段是否存在
            'fields_strict'   => true,
            // 是否需要断线重连
            'break_reconnect' => false,
            // 监听SQL
            'trigger_sql'     => env('app_debug', true),
            // 开启字段缓存
            'fields_cache'    => false,
        ],



#env 数据文件增加配置

[DATABASE]
### TYPE = mysql
### HOSTNAME = xxxx
### DATABASE = yg_center
### USERNAME = xxxx
### PASSWORD = xxxx
### HOSTPORT = 3306
### PREFIX = yg_
### CHARSET = utf8
### DEBUG = true



