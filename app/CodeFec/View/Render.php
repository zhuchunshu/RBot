<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */
namespace App\CodeFec\View;


use Hyperf\View\Mode;
use Hyperf\Utils\Context;
use Hyperf\Task\TaskExecutor;
use Hyperf\View\RenderInterface;
use Hyperf\View\Engine\NoneEngine;
use Hyperf\Contract\ConfigInterface;
use Psr\Container\ContainerInterface;
use Hyperf\View\Engine\EngineInterface;
use Psr\Http\Message\ResponseInterface;
use Hyperf\View\Exception\RenderException;
use Hyperf\HttpMessage\Stream\SwooleStream;
use Hyperf\View\Exception\EngineNotFindException;

class Render implements RenderInterface
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var string
     */
    protected $engine;

    /**
     * @var string
     */
    protected $mode;

    /**
     * @var array
     */
    protected $config;

    public function __construct(ContainerInterface $container, ConfigInterface $config)
    {
        $engine = $config->get('view.engine', NoneEngine::class);
        if (! $container->has($engine)) {
            throw new EngineNotFindException("{$engine} engine is not found.");
        }

        $this->engine = $engine;
        $this->mode = $config->get('view.mode', Mode::TASK);
        $this->config = $config->get('view.config', []);
        $this->container = $container;
    }

    public function render(string $template, array $data = [],int $code=200): ResponseInterface
    {
        return $this->response()
            ->withStatus($code)
            ->withAddedHeader('content-type', $this->getContentType())
            ->withBody(new SwooleStream($this->getContents($template, $data)));
    }

    public function getContents(string $template, array $data = []): string
    {
        try {
            switch ($this->mode) {
                case Mode::SYNC:
                    /** @var EngineInterface $engine */
                    $engine = $this->container->get($this->engine);
                    $result = $engine->render($template, $data, $this->config);
                    break;
                case Mode::TASK:
                default:
                    $executor = $this->container->get(TaskExecutor::class);
                    $result = $executor->execute(new Task([$this->engine, 'render'], [$template, $data, $this->config]));
                    break;
            }

            return $result;
        } catch (\Throwable $throwable) {
            throw new RenderException($throwable->getMessage(), $throwable->getCode(), $throwable);
        }
    }

    public function getContentType(): string
    {
        $charset = ! empty($this->config['charset']) ? '; charset=' . $this->config['charset'] : '';

        return 'text/html' . $charset;
    }

    protected function response(): ResponseInterface
    {
        return Context::get(ResponseInterface::class);
    }
}
