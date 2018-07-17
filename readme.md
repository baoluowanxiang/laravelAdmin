<p align="center"><img src="https://laravel.com/assets/img/components/logo-laravel.svg"></p>

## About 
lv5admin 是使用laravel5.6开发的一个后台管理

初次使用laravel,错误之处还请大师们指点,使用中有不明之处联系QQ:5552123(阿杜) 
## 使用说明

初次使用请将根目录下的 复制.env.example 为.evn,将数据库账号与密码更新. 

数据库:database 目录中

后台账号:  用户名 admin 密码 admin

##nginx配置

```
server {
        listen       80; 
        server_name lv5admin.91shiwan.com;
        root   "/Users/mac/wwwroot/work/lv5admin.91shiwan.com/public";
        location / { 
            index   index.php;
            try_files $uri $uri/ /index.php?$query_string;
        }   
        location ~ \.php(.*)$ {
            fastcgi_pass   127.0.0.1:9000;
            fastcgi_index  index.php;
            fastcgi_split_path_info  ^((?U).+\.php)(/?.+)$;
            fastcgi_param  SCRIPT_FILENAME  $document_root$fastcgi_script_name;
            fastcgi_param  PATH_INFO  $fastcgi_path_info;
            fastcgi_param  PATH_TRANSLATED  $document_root$fastcgi_path_info;
            include        fastcgi_params;
        }
}
```

## 演示网址 [lv5admin.91shiwan.com](http://lv5admin.91shiwan.com)
