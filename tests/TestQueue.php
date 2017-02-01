<?php

namespace yiiunit\extensions\queuemailer;

use yii\base\Component;
use yii\queue\QueueInterface;

/**
*
*/
class TestQueue extends Component implements QueueInterface
{
    
    private $_jobs;

    /**
     * @inheritdoc
     */
    public function push($payload, $queue, $delay = 0)
    {
        if (!$this->_jobs[$queue]) {
            $this->_jobs[$queue] = [];
        }
        return array_push($this->_jobs[$queue], ['body' => $payload]);
    }

    /**
     * @inheritdoc
     */
    public function pop($queue)
    {
        if (!$this->_jobs[$queue]) {
            return null;
        }
        return array_shift($this->_jobs[$queue]);
    }

    /**
     * @inheritdoc
     */
    public function purge($queue)
    {
        # code...
    }

    /**
     * @inheritdoc
     */
    public function delete(array $message)
    {
        # code...
    }

    /**
     * @inheritdoc
     */
    public function release(array $message, $delay = 0)
    {
        # code...
    }
}
