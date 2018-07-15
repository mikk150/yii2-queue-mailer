<?php

namespace tests\unit;

use Codeception\Stub;
use Codeception\Stub\Expected;
use Exception;
use mikk150\queuemailer\jobs\MailJob;
use mikk150\queuemailer\jobs\MailRetryableJob;
use mikk150\queuemailer\Mailer;
use tests\DummyMailerInterface;
use yii\base\InvalidConfigException;
use yii\mail\MailerInterface;
use yii\mail\MessageInterface;
use yii\queue\file\Queue;

/**
 * Class MailerTest
 * @package tests\unit
 */
class MailerTest extends \Codeception\Test\Unit
{
    /**
     * @test
     * @throws Exception
     */
    public function ensureMailerDuringInit()
    {
        $this->expectException(InvalidConfigException::class);
        new Mailer([
            'mailer' => 'cannotcrossit',
            'queue' => Stub::makeEmpty(Queue::class),
        ]);
    }

    /**
     * @test
     * @throws Exception
     */
    public function ensureQueueDuringInit()
    {
        $this->expectException(InvalidConfigException::class);
        new Mailer([
            'mailer' => Stub::makeEmpty(MailerInterface::class),
            'queue' => 'cannotcrossit',
        ]);
    }

    /**
     * @test
     * @throws Exception
     */
    public function successfulInstantiation()
    {
        $mailer = new Mailer([
            'mailer' => Stub::makeEmpty(MailerInterface::class),
            'queue' => Stub::makeEmpty(Queue::class),
        ]);
        $this->assertInstanceOf(MailerInterface::class, $mailer);
    }

    /**
     * @test
     * @throws Exception
     */
    public function sendMessage()
    {
        /** @var MessageInterface $message */
        $message = Stub::makeEmpty(MessageInterface::class);
        $mailer = Stub::makeEmpty(MailerInterface::class);

        $queueMailer = new Mailer([
            'mailer' => $mailer,
            'queue' => Stub::makeEmpty(Queue::class, [
                'push' => Expected::once(function($job) use ($message, $mailer) {
                    $this->assertInstanceOf(
                        MailJob::class,
                        $job,
                        'Pushed message should me instance of MailJob'
                    );
                    $this->assertEquals(
                        $job->message,
                        $message,
                        'Job `message` should be the equal to sent message'
                    );
                    $this->assertEquals(
                        $job->mailer,
                        $mailer,
                        'Job `mailer` should be the equal to configured mailer'
                    );
                }),
            ], $this),
        ]);
        $result = $queueMailer->send($message);
        $this->assertTrue($result);
    }

    /**
     * @test
     * @throws Exception
     */
    public function compose()
    {
        $mailer = new Mailer([
            'mailer' => Stub::makeEmpty(MailerInterface::class, [
                'compose' => Expected::once(function($view, $params) {
                    $this->assertEquals(
                        'reset-password',
                        $view,
                        'View should be passed to provided mailer'
                    );
                    $this->assertEquals(
                        ['password' => 'pAssWd'],
                        $params,
                        'Pasrams should be passed to provided mailer'
                    );
                    return Stub::makeEmpty(MessageInterface::class);
                }),
            ], $this),
            'queue' => Stub::makeEmpty(Queue::class),
        ]);

        $message = $mailer->compose('reset-password', ['password' => 'pAssWd']);
        $this->assertInstanceOf(MessageInterface::class, $message);
        $this->assertEquals($message->mailer, $mailer);
    }

    /**
     * @test
     * @throws Exception
     */
    public function proxySetters()
    {
        $mailer = new Mailer([
            'mailer' => Stub::makeEmpty(MailerInterface::class, [
                '__set' => Expected::once(function($name, $value) {
                    $this->assertEquals('notexist', $name);
                    $this->assertEquals('123', $value);
                })
            ], $this),
            'queue' => Stub::makeEmpty(Queue::class),
        ]);
        $mailer->notexist = '123';
    }

    /**
     * @test
     * @throws Exception
     */
    public function proxyGetters()
    {
        $mailer = new Mailer([
            'mailer' => Stub::makeEmpty(MailerInterface::class, [
                'notexist' => '123',
            ], $this),
            'queue' => Stub::makeEmpty(Queue::class),
        ]);
        $value = $mailer->notexist;
        $this->assertEquals('123', $value);
    }

    /**
     * @test
     * @throws Exception
     */
    public function proxyMethods()
    {
        $mailer = new Mailer([
            'mailer' => Stub::makeEmpty(DummyMailerInterface::class, [
                'dummy' => function($value) {
                    $this->assertEquals(1, $value);
                    return '123';
                }

            ], $this),
            'queue' => Stub::makeEmpty(Queue::class),
        ]);
        $value = $mailer->dummy(1);
        $this->assertEquals('123', $value);
    }

    /**
     * @test
     * @throws Exception
     */
    public function configureJob()
    {
        /** @var MessageInterface $message */
        $message = Stub::makeEmpty(MessageInterface::class);
        $mailer = Stub::makeEmpty(MailerInterface::class);

        $queueMailer = new Mailer([
            'jobConfig' => [
                'class' => MailRetryableJob::class,
                'attempts' => 123,
                'timeToRetry' => 321,
            ],
            'mailer' => $mailer,
            'queue' => Stub::makeEmpty(Queue::class, [
                'push' => Expected::once(function($job) use ($message, $mailer) {
                    $this->assertInstanceOf(
                        MailRetryableJob::class,
                        $job,
                        'Pushed message should me instance of MailJob'
                    );
                    $this->assertEquals(123, $job->attempts);
                    $this->assertEquals(321, $job->timeToRetry);
                    $this->assertEquals(
                        $job->message,
                        $message,
                        'Job `message` should be the equal to sent message'
                    );
                    $this->assertEquals(
                        $job->mailer,
                        $mailer,
                        'Job `mailer` should be the equal to configured mailer'
                    );
                }),
            ], $this),
        ]);
        $result = $queueMailer->send($message);
        $this->assertTrue($result);
    }
}
