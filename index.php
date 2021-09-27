<?php
/**
 * @Descripttion:
 * @Author: ovim <ovimcloud@gmail.com>
 * @Date: 2021/9/24 3:10 下午
 */

error_reporting(E_ALL);

require_once 'vendor/autoload.php';

function d($data) {
    echo "<pre>";
    var_dump($data);
}

function echoDie($data) {
    print_r($data);
    die;
}

class Test {

    /**
     * @var string
     */
    protected $cliendId;

    /**
     * @var null
     */
    protected $broker;

    public function __construct($cliendId)
    {
        $this->cliendId = $cliendId;

        $this->connect();
    }

    private function connect() {
        list($address, $port, $clientId, $username, $password) = [
            'ip for server',
            'port',
            $this->cliendId,
            'username',
            'password'
        ];

        $broker = new Bluerhinos\phpMQTT($address, $port, $clientId);
        $connectResult = $broker->connect(true, null, $username, $password);
        if (!$connectResult) echoDie('EMQ 服务端连接失败');
        $this->broker = $broker;
    }

    /**
     * publish message
     *
     * @param $topic
     * @param $content
     * @param int $qos
     * @param bool $retain
     */
    public function pub($topic, $content, $qos = 0, $retain = true)
    {
        $result = $this->broker->publish($topic, $content, $qos, $retain);
        d($result);
    }

    /**
     * subscribe message
     *
     * @param $topic
     * @param int $qos
     */
    public function sub($topic, $qos = 0)
    {
        $result = $this->broker->subscribeAndWaitForMessage($topic, $qos);
        // $result = $this->broker->subscribe($topic, $qos);
        // 这个地方如果获取到脏数据 如何处理【 订阅之前，向此主题发送一个空的消息，清空保留消息 】
        d($result);
    }

}

$clientId = mt_rand();
$topic = 'test';
(new Test($clientId))->pub($topic, 'ovimTest1');
(new Test($clientId))->sub($topic);
