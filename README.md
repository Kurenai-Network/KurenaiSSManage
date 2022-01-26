# KurenaiSSManage
A Shadowsocks manage with WHMCS
## 前言
使用我们的程序之前，请先想好，是不是要入这一行。
节点通过Mysql数据库手写命令导入，数据库文件附在了根目录
瞎写的代码，凑合着看吧。
## CloudFlare转换规则CloudFlare转换规则
### 1. user
```
(http.request.uri.path contains "/api/v1/server/ShadowsocksTidalab/user" and not http.request.uri.path contains "/modules/servers/KurenaiSSManage/")
```
重写到（Static）
`modules/servers/KurenaiSSManage/api/v1/server/ShadowsocksTidalab/user`
### 2. submit
```
(http.request.uri.path contains "/api/v1/server/ShadowsocksTidalab/submit" and not http.request.uri.path contains "/modules/servers/KurenaiSSManage/")
```
重写到（Static）
`modules/servers/KurenaiSSManage/api/v1/server/ShadowsocksTidalab/submit/`
## 说明
后端对接参照V2board的方式
