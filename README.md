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

* ./yii bitcoin/index       #比特币公链交易记录查询
* ./yii currency/index      #用户钱包地址分配专用地址任务
* ./yii eth/transaction     #公链交易导入
* ./yii eth/import          #根据公链交易记录创建充值记

# 计划任务 crontab

* 0 \* \* \* \* /usr/bin/php yii gift-money/expire    # 过期红包退回
* 0 5 \* \* \* /usr/bin/php yii currency/price    # USDT交易对行情更新（wanshare）
\* \*/5 \* \* \* \* /usr/bin/php yii withdraw/expired    # 过期未审核单据退回、释放锁定金额
0 0 \* \* \* /root/mysql_backup.sh     #   备份数据库
0 0 \* \* \* /usr/bin/php yii lockbank/index           #锁仓银行
\* \* \* \* \* /usr/bin/php yii signup/index             #用户注册后续操作
\*/30 \* \* \* \* /usr/bin/php yii eth/balance-hourly             #每半个小时重置拉取过交易的地址
\* \* \* \* \* /usr/bin/php yii withdraw/index      #BTC提现脚本
\* \* \* \* \* /usr/bin/php yii withdraw/usdt      #USDT（BTC）提现脚本

# supervisor 管理
* /usr/bin/php yii eth/transaction      \# ETH交易拉取
* /usr/bin/php yii eth/transaction-token      \# TOKEN交易拉取
* /usr/bin/php yii eth/import      \# 公链交易导入充值记录
* /usr/bin/php yii eth/balance      \# 余额刷新，
* /usr/bin/php yii currency/index      \# 新注册用户预分配钱包
* /usr/bin/php yii withdraw/send      \# 提现交易发送
* /usr/bin/php yii eth/collect      \# ETH/ERC20 Token 归集任务
    * currency.params.gas 转入待归集账户ETH数量，也是归集任务时预留用于归集任务的gas ETH，建议0.00035
    * collect_threshold 归集触发阀指，大于此余额才归集
    * collect_switch 归集开关, 等于1才执行
    * address 归集主账户
    * 用于交易类的 contract_address/abi/bytecode/decimal


2018-12-08:
    添加    iec_user                                            pool_id 字段    （糖果ID）
    添加    iec_invite_pool                                     type    字段    （糖果类型  /   0:项目方糖果，1:官方糖果
    添加    iec_invite_pool                                     name    字段    （糖果名称）
    添加    iec_invite_pool                                     icon    字段    （糖果图标）
    添加    iec_invite_pool                                     background  字段    （糖果海报背景图）
    添加    iec_invite_pool                                     uid     字段    （用户ID)
    添加    iec_invite_pool                                     url     字段    （白皮书链接）
    添加    iec_invite_pool                                     description    字段    （描述）

2018-1218:
    添加    iec_bank_order                                      symbol              字段    （币种）
    添加    iec_bank_order                                      supernode_uid       字段    （收益超级节点）

2018-12-21:
    添加    iec_bank_profit                                     symbol              字段    （币种）
    添加    iec_bank_profit                                     type                字段    （类型）

    添加    iec_deposit                                         address_id          字段    （地址ID）
    添加    iec_deposit                                         address             字段    （转入地址）
    添加    iec_deposit                                         fee                 字段    （手续费）
    添加    iec_deposit                                         fee_symbol          字段    （手续费币种）
    添加    iec_deposit                                         remark              字段    （备注）            text
    添加    iec_deposit                                         transaction_hash    字段    （交易哈希）




BTC/USDT   回调地址：
    /api/controllers/CallbackController - btc

发送 BTC  
    BTC         console = withdraw/index
    USDT        console = withdraw/usdt