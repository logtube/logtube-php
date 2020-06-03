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
            ]
        ]
    ]);
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
    ```

6. 在合适的地方使用如下代码，将 `CRID` 发送给调用方

    ```php
    header("X-Correlation-ID:". Logtube::crid());
    ```

## Credits

Guo Y.K., MIT License
