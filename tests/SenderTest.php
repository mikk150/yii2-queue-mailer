<?php

namespace yiiunit\extensions\queuemailer;

use Yii;
use PHPUnit_Framework_Constraint_IsType as PHPUnit_IsType;

/**
*
*/
class SenderTest extends TestCase
{
    public function testCreateBaseMailer()
    {
        $app = $this->mockApplication([
            'components' => [
                'mailer' => [
                    'class' => '\mikk150\queuemailer\Mailer',
                    'mailer' => [
                        'class' => '\yii\swiftmailer\Mailer'
                    ]
                ],
                'queue' => [
                    'class' => '\yii\queue\Queue',
                    'serializer' => '\yii\queue\serializers\PhpSerializer',
                    'messenger' => '\yii\queue\messengers\instant\InstantMessenger',
                    'executor' => '\yii\queue\executors\instant\Executor',
                ],
            ]
        ]);

        $mailer = $app->mailer->getInstance();

        $this->assertInstanceOf('yii\swiftmailer\Mailer', $mailer);
    }

    public function testCreateMessage()
    {
        $app = $this->mockApplication([
            'components' => [
                'mailer' => [
                    'class' => '\mikk150\queuemailer\Mailer',
                    'mailer' => [
                        'class' => '\yii\swiftmailer\Mailer'
                    ]
                ],
                'queue' => [
                    'class' => '\yii\queue\Queue',
                    'serializer' => '\yii\queue\serializers\PhpSerializer',
                    'messenger' => '\yii\queue\messengers\instant\InstantMessenger',
                    'executor' => '\yii\queue\executors\instant\Executor',
                ],
            ]
        ]);

        $message = $app->mailer->compose('test', []);

        $this->assertInstanceOf('yii\swiftmailer\Message', $message);
    }

    public function testSendingMessage()
    {
        $app = $this->mockApplication([
            'components' => [
                'mailer' => [
                    'class' => '\mikk150\queuemailer\Mailer',
                    'mailer' => [
                        'class' => '\yii\swiftmailer\Mailer'
                    ]
                ],
                'queue' => [
                    'class' => '\yii\queue\Queue',
                    'serializer' => '\yii\queue\serializers\PhpSerializer',
                    'messenger' => '\yii\queue\messengers\instant\InstantMessenger',
                    'executor' => '\yii\queue\executors\instant\Executor',
                ],
            ],
        ]);

        $jobId = $app->mailer->compose('test', [])->setTo('test@mailinator.com')->send();

        $this->assertInternalType(PHPUnit_IsType::TYPE_INT, $jobId);
    }

    public function testRetrievingMessageFromQueue()
    {
        $app = $this->mockApplication([
            'components' => [
                'mailer' => [
                    'class' => '\mikk150\queuemailer\Mailer',
                    'mailer' => [
                        'class' => '\yii\swiftmailer\Mailer'
                    ]
                ],
                'queue' => [
                    'class' => '\yii\queue\Queue',
                    'serializer' => '\yii\queue\serializers\PhpSerializer',
                    'messenger' => '\yii\queue\messengers\arraymessenger\ArrayMessenger',
                    'executor' => '\yii\queue\executors\instant\Executor',
                ]
            ]
        ]);

        $app->mailer->compose('test', [])->setTo(['test@mailinator.com' => 'John Test'])->send();

        /**
         * @var        \yii\queue\messengers\Message
         */
        $message = $app->queue->messenger->reserve();
        $job = $app->queue->serializer->unserialize($message->message);

        $this->assertInstanceOf('yii\swiftmailer\Message', $job->message);
        $this->assertEquals(['test@mailinator.com' => 'John Test'], $job->message->getTo());
    }
}
