<?php

declare(strict_types=1);

namespace App\RBot;

use Hyperf\Contract\ConfigInterface;
use Hyperf\Di\Annotation\AnnotationReader;
use Hyperf\Di\ClassLoader;
use Hyperf\Utils\Codec\Json;
use Hyperf\Utils\Coroutine;
use Hyperf\Utils\Exception\InvalidArgumentException;
use Hyperf\Utils\Filesystem\FileNotFoundException;
use Hyperf\Utils\Filesystem\Filesystem;
use Hyperf\Watcher\Driver\DriverInterface;
use Hyperf\Watcher\Option;
use PhpParser\PrettyPrinter\Standard;
use Psr\Container\ContainerInterface;
use Swoole\Coroutine\Channel;
use Swoole\Coroutine\System;
use Swoole\Process;
use Symfony\Component\Console\Output\OutputInterface;

class Start
{
    /**
     * @var ContainerInterface
     */
    protected ContainerInterface $container;

    /**
     * @var Option
     */
    protected Option $option;

    /**
     * @var DriverInterface
     */
    protected mixed $driver;

    /**
     * @var Filesystem
     */
    protected Filesystem $filesystem;

    /**
     * @var OutputInterface
     */
    protected OutputInterface $output;

    /**
     * @var ClassLoader
     */
    protected ClassLoader $loader;

    /**
     * @var array
     */
    protected array $autoload;

    /**
     * @var AnnotationReader
     */
    protected AnnotationReader $reader;

    /**
     * @var ConfigInterface
     */
    protected mixed $config;

    /**
     * @var Standard
     */
    protected Standard $printer;

    /**
     * @var Channel
     */
    protected Channel $channel;

    /**
     * @var string
     */
    protected string $path = BASE_PATH . '/runtime/container/rbot.cache';

    public function __construct(ContainerInterface $container, Option $option, OutputInterface $output)
    {
        $this->container = $container;
        $this->option = $option;
        $this->driver = $this->getDriver();
        $this->filesystem = new Filesystem();
        $this->output = $output;
        $json = Json::decode($this->filesystem->get(BASE_PATH . '/composer.json'));
        $this->autoload = array_flip($json['autoload']['psr-4'] ?? []);
        $this->reader = new AnnotationReader();
        $this->config = $container->get(ConfigInterface::class);
        $this->printer = new Standard();
        $this->channel = new Channel(1);
        $this->channel->push(true);
    }

    public function run(): void
    {
        $this->dumpautoload();
        $this->restart(true);

        $channel = new Channel(999);
        Coroutine::create(function () use ($channel) {
            $this->driver->watch($channel);
        });

        $result = [];
        while (true) {
            $file = $channel->pop(0.001);
            if ($file === false) {
                if (count($result) > 0) {
                    $result = [];
                    $this->restart(false);
                }
            } else {
                $ret = System::exec(sprintf('%s %s/vendor/hyperf/watcher/collector-reload.php %s', $this->option->getBin(), BASE_PATH, $file));
                if ($ret['code'] === 0) {
                    $this->output->writeln('Class reload success.');
                } else {
                    $this->output->writeln('Class reload failed.');
                    $this->output->writeln($ret['output'] ?? '');
                }
                $result[] = $file;
            }
        }
    }

    public function dumpautoload()
    {
        $ret = System::exec('composer dump-autoload -o --no-scripts -d ' . BASE_PATH);
        $this->output->writeln($ret['output'] ?? '');
    }

    public function restart($isStart = true)
    {
        if (! $this->option->isRestart()) {
            return;
        }
        $file = $this->config->get('codefec.RBot_pid_file');
        if (empty($file)) {
            throw new FileNotFoundException('The config of RBot_pid_file is not found.');
        }
        if (! $isStart && $this->filesystem->exists($file)) {
            $pid = $this->filesystem->get($file);
            try {
                $this->output->writeln('Stop server...');
                if (Process::kill((int) $pid, 0)) {
                    Process::kill((int) $pid, SIGTERM);
                }
            } catch (\Throwable $exception) {
                $this->output->writeln('Stop server failed. Please execute `composer dump-autoload -o`');
            }
        }

        Coroutine::create(function () {
            $this->channel->pop();
            $this->output->writeln('Start server ...');

            $descriptorspec = [
                0 => STDIN,
                1 => STDOUT,
                2 => STDERR,
            ];

            proc_open($this->option->getBin() . ' ' . BASE_PATH . '/CodeFec RBot', $descriptorspec, $pipes);

            $this->output->writeln('Stop server success.');
            $this->channel->push(1);
        });
    }

    protected function getDriver()
    {
        $driver = $this->option->getDriver();
        if (! class_exists($driver)) {
            throw new \InvalidArgumentException('Driver not support.');
        }
        return make($driver, ['option' => $this->option]);
    }
}
