<?php

namespace AI\Tester\Util;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response
    ;
class HttpServer
{
    /**
     * @var
     */
    protected $requestHandler;

    /**
     * @param $port
     * @param string $host
     */
    public function listen($port, $host = '0.0.0.0')
    {
        if ($this->isCli()) {
            $this->startServer($port, $host);
        } else {
            $this->handle();
        }
    }

    /**
     * @param callable $requestHandler
     */
    public function registerHandler(callable $requestHandler)
    {
        $this->requestHandler = $requestHandler;
    }

    /**
     * @return bool
     */
    protected function isCli()
    {
        return php_sapi_name() == 'cli';
    }

    /**
     * @param $port
     * @param $host
     */
    protected function startServer($port, $host)
    {
        $command = escapeshellcmd(
            sprintf(
                '%s -S %s -t %s',
                PHP_BINARY,
                "{$host}:{$port}",
                $this->getDocumentRoot()
//                $this->getRouter()
            )
        );
        proc_open($command, [0 => STDIN, 1 => STDOUT, 2 => STDERR], $pipes);
    }

    protected function handle()
    {
        $output = call_user_func_array($this->requestHandler, [Request::createFromGlobals()]);
        if ($output instanceof Response) {
            $response = $output;
        } else {
            $response = new Response($output);
        }
        $response->send();
    }

    /**
     * @return string
     */
    protected function getDocumentRoot()
    {
        return "web";
    }

    /**
     * @return string
     */
    protected function getRouter()
    {
        return realpath($_SERVER['argv'][0]);
    }
}