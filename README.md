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
 * 版本
    * 1.0.0
       * 功能
       * 站点基本设置
       * 站点管理员设置
       * 授权应用设置
       * 授权站点设置
       * 远程应用授权
    * 1.0.1 / 2021/2/23
       * 修复相同站点信息重复添加
       * 修复部分权限问题
       * 新增RSA公司钥修改
    * 1.0.2 / 2021/2/24
       * 新增身份验证功能
          * 包括邮件发送
          * 后台邮件配置
          * 前台申请授权功能
          * 注:自助授权配置在index/api.php
    * 1.0.3 / 2021/2/25
       * 新增打卡功能
          * 打卡后自动为授权延时
          * 添加验证后显示的留言条
          * 注:打卡配置在index/api.php
    * 1.0.4 / 2021/2/26
       * 更改验证api消息通知方式
---

## 目录结构
* admin
   * api.php //后台逻辑交互库
   * footer.php //页面构成底部
   * header.php //页面构成头部
   * index.php //页面构成身体 后台首页
   * login.php //登陆页面
   * public.php //公共引入
   * site.php //页面构成身体 授权应用管理页面
   * system.php //页面构成身体 系统配置管理页面
   * url.php //页面构成身体 授权站点管理页面
   * user.php //页面构成身体 用户管理页面
   * rsa.php //页面构成身体 密钥管理页面
   * email.php //页面构成身体 邮箱配置管理页面
* index
   * api.php //前台逻辑交互库
   * footer.php //页面构成底部
   * header.php //页面构成头部
   * index.php //页面构成身体 前台首页
   * finde.php //页面构成身体 查询页
   * login.php //页面构成身体 身份呢验证
   * shop.php //页面构成身体 获取授权
* assets
   * ~ //略
* config
   * sqlConfig.php //数据库配置文件
   * systemCofig.php //系统配置文件
* public
   * geetes //略
      * config
         * config.php //极验sdk配置文件
   * email //略
      * config.php //邮件发送配置文件
   * lib
      * ~ //略
   * rsa
      * rsa_private_key.pem //加密证书私钥
      * rsa_public_key.pem //加密证书公钥
      * rsa.php
* api.php //授权接口
* code.php //授权代码


---
## 授权原理
![1](https://github.com/zhiguai/CZ-Empower/blob/master/%E9%AA%8C%E8%AF%81%E6%B5%81%E7%A8%8B.png "图")
