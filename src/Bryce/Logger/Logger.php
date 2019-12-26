<?php
/**
 * @author Bryce<lushaoming6@gmail.com>
 * @date   2019/12/26
 */
namespace Bryce\Logger;

class Logger
{
    public static $_instance = null;
    private $config;

    private function __construct($config)
    {
        $this->config = $config;
    }

    public static function init($config = [])
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self($config);
        }
        return self::$_instance;
    }

    public function write($data, $level = 1)
    {
        static $pid = null;
        if (!$pid) {
            $pid = substr(uniqid(), 6, -1);
        }

        $file = $this->getLogFile();
        file_put_contents($file, <<<LOG
[{$this->currentTime()}:{$pid}][{$this->getLevel($level)}]{$data}
LOG
            .PHP_EOL, FILE_APPEND);
    }
    
    public function getLogFile()
    {
        if (!isset($this->config['file'])) {
            $this->config['file'] = $this->systemOS() == 1 ? 'C:/log.txt' : '/www/logs/log.txt';
        }
        return $this->config['file'];
    }

    public function systemOS()
    {
        if (strtoupper(substr(PHP_OS,0,3))==='WIN') {
            return 1;
        } else {
            return 2;
        }
    }
    public function currentTime()
    {
        $microtime = explode('.', microtime(true) . '')[1] ?? '000000';
        $date = date('Y-m-d H:i:s');
        return "{$date}.{$microtime}";
    }

    public function getLevel($level)
    {
        switch ($level) {
            case 1:
                return 'debug';
            case 2:
                return 'warning';
            case 3:
                return 'error';
            default:
                return 'debug';
        }
    }
}