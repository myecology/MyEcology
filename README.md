# 必要库
php-bcmath php-zip php-xml php-gmp

# 注意事项

git拉取项目后，分别执行以下操作
* composer install
* ./init
* 选择生产/开发环境
* 更改 `common/config/main-local.php` 和 `common/config/params-local.php`文件中为正确参数
* 重要！更改`vendor/web3p/rlp/src/RLP.php` 文件中的L260行，删除var_dump()方法，确认运行过程不输出过程变量

# 启动的守护进程