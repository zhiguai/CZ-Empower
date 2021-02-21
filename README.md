# CZ-Empower
一个PHP+MYsql构建的域名授权系统
 * 作者: 吃纸怪
 * QQ: 2903074366
 * 邮箱: 2903074366@qq.com
 * QQ交流群: 801235342
 * 开发环境
    * Windows
    * Apache2.4.39
    * MySQL5.7.26
    * PHP7.3.4nts
---

## 目录结构
* admin
   * api.php //后台逻辑交互
   * footer.php //页面构成底部
   * header.php //页面构成头部
   * index.php //页面构成身体 后台首页
   * login.php //登陆页面
   * public.php //公共引入
   * site.php //页面构成身体 授权应用管理页面
   * system.php //页面构成身体 系统配置管理页面
   * url.php //页面构成身体 授权站点管理页面
   * user.php //页面构成身体 用户管理页面
* assets
   * ~ //略
* config
   * sqlConfig.php //数据库配置文件
   * systemCofig.php //系统配置文件
* public
   * geetes //略
      * config
         * config.php //极验sdk配置文件
   * lib
      * ~ //略
   * rsa
      * rsa_private_key.pem //加密证书私钥
      * rsa_public_key.pem //加密证书公钥
      * rsa.php
* api.php //授权接口
* code.php //授权代码