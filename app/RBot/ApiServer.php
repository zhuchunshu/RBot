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
        $go_bin = env("GO_BIN","go");
        $ret = System::exec('cd ' . BASE_PATH."/app/RBot/Core && ".$go_bin." env -w GOPROXY=https://goproxy.cn,direct && ".$go_bin." build -ldflags \"-s -w -extldflags '-static'\" -o BotServer");
        if($ret['output']){
            $this->command->info($ret['output']);
        }else{
            $this->command->info('编译完成,请重新运行此命令');
        }
    }


}