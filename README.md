Yii2 Queue mailer
=================
Queue mailer for Yii 2 framework

[![Build Status](https://travis-ci.org/mikk150/yii2-queue-mailer.svg?branch=master)](https://travis-ci.org/mikk150/yii2-queue-mailer)

Installation
------------
Either run
```
php composer.phar require --prefer-dist mikk150/yii2-queuemailer "*"
```
or add
```
"mikk150/yii2-queuemailer": "*"
```
to the require section of your `composer.json` file

Usage
-----
configure Yii2 config
```php
'components' => [
    'mailer' => [
        'class' => 'mikk150\queuemailer\Mailer',
        'mailer' => [
            'class' => '<underlying mailer config>',
        ],
        'messageClass' => '<underlying mailer message class>',
    ],
]
```
and use just like you are using underlying mailer.
Emails are being queued and being sent by worker

To set up yii2 queue, please refer to <https://github.com/yiisoft/yii2-queue>