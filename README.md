# logtube-php

Logtube PHP SDK v1.2.0

## 设计

每一条日志都包含如下几个要素

1. 时间
2. 项目名
3. 环境名
4. 主题，传统主题为 info, debug, warn, err，也可以按需要自定义主题
5. CRID，一个随机字符串，微服务互相调用的时候，会通过 X-Correlation-ID 头传递这个字符串，并在日志中输出，用来追踪整个调用链
6. 关键字，为了节约日志系统资源，只有关键字字段内的文本会被索引，并可以查询
7. 日志内容，纯文本内容
8. 额外字段，可以将多个JSON字段附加到这条日志上，用来进行更精确的索引

## 使用方法

1. `composer require logtube/logtube`

2. 在项目尽可能早的位置初始化 Logtube

    ```php
    Logtube::setup([
        "project" => "testcase", // 项目名
        "env" => "test",         // 环境名
        "file" => [
            "dir" => "logs",     // 日志目录
            "subdirs" => [               // 指定某些主题日志输出到 xlog 子目录，便于 Filebeat 收集
                "err" => "xlog",
                "warn" => "xlog",
                "info" => "xlog",
                "x-access" => "xlog"
            ],
            "balance" => 5, // 随机均衡写入 xx.1.log, xx.2.log, xx.3.log, xx.4.log, xx.5.log，最小可配置为1。
        ]
    ]);
    ```
   
   如果有需要，可以移除 `file` 字段，使用 `single` 字段启动单文件输出

   ```php
   Logtube::setup([
        "project" => "testcase", // 项目名
        "env" => "test",         // 环境名
        "single" => "/tmp/php.log.fifo"
    ]);
   ```
   
   或者使用 `fifodir` 使用一个目录内的任意数量的 FIFO 文件

   ```php
   Logtube::setup([
        "project" => "testcase", // 项目名
        "env" => "test",         // 环境名
        "fifodir" => "/tmp/php-log",
        "fifodir_nb" => true     // 使用 NON_BLOCKING 打开 FIFO
   ])
   ```
   
3. 在主要代码前使用 `Logtube::beginAccessLog()` 开始 访问日志记录

4. 在主要代码后使用 `Logtube::endAccessLog()` 结束 访问日志记录

5. 在业务代码中使用

    ```php
    Logtube::addDefaultKeyword("keyword1", "keyword2"); // 提前为所有日志添加默认的关键字

    ILog("hello,world", "hello %s", "world"); // info 主题输出，第一个参数为关键词，第二个为格式，第三个为参数
    ELog("hello,world", "hello %s", "world"); // err
    WLog("hello,world", "hello %s", "world"); // warn
    DLog("hello,world", "hello %s", "world"); // debug
    FLog("hello,world", "hello %s", "world"); // fatal
    ```

6. 在合适的地方使用如下代码，将 `CRID` 发送给调用方

    ```php
    header("X-Correlation-ID:". Logtube::crid());
    ```
   
7. 发起 HTTP 调用的时候，通过 `X-Correlation-Src` 头表明自己身份，便于日志追踪

    ```php
   $curl = curl_init();
   curl_setopt($curl, CURLOPT_HTTPHEADER, array([
       'Content-Type:'.'application/json',
       'X-Correlation-Src:'.Logtube::project(),
   ]));
    ```
   
## 预置模块

### 审计

```
$committer = Logtube::audit();
$committer->setUserCode("202002020")->setUserName("xxxx")->commit();
```

### 性能统计

```
$committer = Logtube::perf()->setAction("submit-order");
// 完成一些耗时的操作
$committer.commit();
```

### 定时任务

```php
$committer = Logtube::job();
$committer->setJobName("sleep_1s_job")  // 设置任务名
   ->setJobId("jobxxxxxx-xxxxx-xxxxx")  // 设置任务ID
   ->addKeyword("sleep")                // 添加关键字
   ->markStart();                       // 输出任务开始日志

// 执行任务

$committer->setResult(true, "sleep result is good")   // 标记任务执行是否成功 并 记录任意结果字符串，true 代表成功
   ->markEnd();                                       // 输出任务结束日志
```

## Credits

Guo Y.K., MIT License
