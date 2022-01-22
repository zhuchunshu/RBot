<?php

declare(strict_types=1);

namespace App\Command;

use App\RBot\Start;
use Hyperf\Command\Command as HyperfCommand;
use Hyperf\Command\Annotation\Command;
use Psr\Container\ContainerInterface;

/**
 * @Command
 */
#[Command]
class ApiServer extends HyperfCommand
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;

        parent::__construct('RBot:ApiServer');
    }

    public function configure()
    {
        parent::configure();
        $this->setDescription('Running Api Server');
    }

    public function handle()
    {
        $watcher = make(\App\RBot\ApiServer::class, [
            'output' => $this->output,
            "command" => $this
        ]);

        $watcher->run();
    }
}
