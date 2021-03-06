<?php

namespace App\RBot;

use Psr\Container\ContainerInterface;
use Swoole\Coroutine\System;
use Symfony\Component\Console\Output\OutputInterface;

class ApiServer
{
    /**
     * @var ContainerInterface
     */
    protected ContainerInterface $container;

    /**
     * @var OutputInterface
     */
    protected OutputInterface $output;

    /**
     * @var \App\Command\ApiServer
     */
    protected \App\Command\ApiServer $command;


    public function __construct(ContainerInterface $container, OutputInterface $output,\App\Command\ApiServer $command)
    {
        $this->container = $container;
        $this->output = $output;
        $this->command = $command;
    }

    public function run(){
        $descriptorspec = [
            0 => STDIN,
            1 => STDOUT,
            2 => STDERR,
        ];
        // 二进制文件不存在
        if(!file_exists(BASE_PATH."/app/RBot/Core/BotServer")){
            $this->build();
            return;
        }
        // 二进制文件存在

        proc_open("cd ".BASE_PATH . '/app/RBot/Core && ./BotServer', $descriptorspec, $pipes);
    }

    /**
     * 编译CQHTTP
     */
    private function build(): void
    {
        $this->command->line("未找到 ".BASE_PATH."app/RBot/Core/BotServer 文件,请编译后重新执行此命令");
    }


}