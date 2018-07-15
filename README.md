Yii2 Queue mailer
=================
Queue mailer for Yii 2 framework

[![Build Status](https://travis-ci.org/alexeevdv/yii2-queue-mailer.svg?branch=master)](https://travis-ci.org/alexeevdv/yii2-queue-mailer)
[![codecov](https://codecov.io/gh/alexeevdv/yii2-queue-mailer/branch/master/graph/badge.svg)](https://codecov.io/gh/alexeevdv/yii2-queue-mailer)
![PHP 5.6](https://img.shields.io/badge/PHP-5.6-green.svg) 
![PHP 7.0](https://img.shields.io/badge/PHP-7.0-green.svg) 
![PHP 7.1](https://img.shields.io/badge/PHP-7.1-green.svg) 
![PHP 7.2](https://img.shields.io/badge/PHP-7.2-green.svg)

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
        'class' => \mikk150\queuemailer\Mailer::class,
        'mailer' => [
            'class' => '<underlying mailer config>',
            'messageClass' => '<underlying mailer message class>',
        ],
        'queue' => 'mailer-queue', // in case you need specific queue
    ],
]
```
and use just like you are using underlying mailer.
Emails are being queued and being sent by worker

To set up yii2 queue, please refer to <https://github.com/yiisoft/yii2-queue>

Retryable job
-------------
You can setup retryable job to make several attemts of mail sending.
```php
'components' => [
    'mailer' => [
        'class' => \mikk150\queuemailer\Mailer::class,
        'jobConfig' => [
            'class' => \mikk150\queuemailer\jobs\MailRetryableJob::class,
            'attempts' => 10,
            'timeToRetry' => 600, // ten minutes
        ],
    ],
]
```
