<?php

namespace App\Factory;

use Monolog\Formatter\HtmlFormatter;
use Monolog\Formatter\JsonFormatter;
use Monolog\Formatter\LineFormatter;
use Monolog\Formatter\NormalizerFormatter;
use Monolog\Formatter\SyslogFormatter;
use Monolog\Handler\ErrorLogHandler;
use Monolog\Handler\HandlerInterface;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\SyslogHandler;
use Monolog\Logger;
use Psr\Log\LoggerInterface;
use Symfony\Component\Uid\Uuid;

/**
 * Factory.
 */
final class LoggerFactory
{
    private string $path;

    private int $level;

    private array $handler = [];

    private ?LoggerInterface $testLogger;

    /**
     * The constructor.
     *
     * @param array $settings The settings
     */
    public function __construct(array $settings = [])
    {
        $this->path = (string)($settings['path'] ?? '');
        $this->level = (int)($settings['level'] ?? Logger::DEBUG);

        // This can be used for testing to make the Factory testable
        if (isset($settings['test'])) {
            $this->testLogger = $settings['test'];
        }
    }

    /**
     * Build the logger.
     *
     * @param string|null $name The logging channel
     *
     * @return LoggerInterface The logger
     */
    public function createLogger(string $name = null): LoggerInterface
    {
        if (isset($this->testLogger)) {
            return $this->testLogger;
        }

        $logger = new Logger($name ?: Uuid::v4());
        foreach ($this->handler as $handler) {
            $logger->pushHandler($handler);
        }

        $this->handler = [];

        return $logger;
    }

    /**
     * Add a handler.
     *
     * @param HandlerInterface $handler The handler
     *
     * @return self The logger factory
     */
    public function addHandler(HandlerInterface $handler): static
    {
        $this->handler[] = $handler;

        return $this;
    }

    /**
     * Add rotating file logger handler.
     *
     * @param string $filename The filename
     * @param int|null $level The level (optional)
     *
     * @return self The logger factory
     */
    public function addFileHandler(string $filename, int $level = null): static
    {
        $filename = sprintf('%s/%s', $this->path, $filename);
        $fileHandler = new StreamHandler($filename);

        // The last "true" here tells monolog to remove empty []'s
        $formatter = new JsonFormatter(appendNewline: true);
        // $formatter->setJsonPrettyPrint(true);
        $fileHandler->setFormatter($formatter);

        $this->addHandler($fileHandler);

        return $this;
    }

    /**
     * Add a console logger.
     *
     * @param int|null $level The level (optional)
     *
     * @return self The logger factory
     */
    public function addConsoleHandler(int $level = null): static
    {
        /** @phpstan-ignore-next-line */
        $streamHandler = new StreamHandler('php://output', $level ?? $this->level);
        $streamHandler->setFormatter(new LineFormatter(null, null, false, true));

        $this->addHandler($streamHandler);

        return $this;
    }
}
