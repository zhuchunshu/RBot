<?php

declare(strict_types=1);

namespace App\Command;

use Hyperf\Command\Command as HyperfCommand;
use Hyperf\Command\Annotation\Command;
use Hyperf\Di\Annotation\Inject;
use Hyperf\WebSocketClient\ClientFactory;
use Psr\Container\ContainerInterface;
use Swoole\Coroutine;
use Swoole\Coroutine\Channel;
use Swoole\Timer;


/**
 * @Command
 */
#[Command]
class RBot extends HyperfCommand
{
    /**
     * @var ContainerInterface
     */
    protected ContainerInterface $container;

    /**
     * @Inject
     * @var ClientFactory
     */
    protected ClientFactory $clientFactory;



    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;

        parent::__construct('RBot');
    }

    public function configure()
    {
        parent::configure();
        $this->setDescription('Run RBot');
    }

    public function handle()
    {
        $host = get_options("websocket","ws://127.0.0.1:6700");
        $client = $this->clientFactory->create($host,false);
        $this->running($client);

    }

    public function running($client): void
    {
        while (true){
            $msg = $client->recv(2);
            $this->info($msg."\n");
            (new \App\RBot\RBot())->handle($msg);
        }
    }
}
