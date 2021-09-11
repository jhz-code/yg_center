# yg

#配置数据库

判断用户是否存在
checkUserExist($account);
//获取用户资料  
//$from 数据来源,为空则调用此账户下所有用户数据
getUserInfo($account,$from);
//获取用户数据 默认拉取眼罩系统数据
getUserInfoByAccount($account, $from = 'YG_YGXSj');
//获取用户最高等级
getUserLevel($account);



