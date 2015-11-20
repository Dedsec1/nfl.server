<?php
/**
 * Created by PhpStorm.
 * User: sbabych
 * Date: 20.11.2015
 * Time: 16:50
 */

namespace NflBundle\Lib\EventListener;

use Symfony\Component\Console\Event\ConsoleExceptionEvent;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

class ConsoleExceptionListener
{
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function onConsoleException(ConsoleExceptionEvent $event)
    {
        $command = $event->getCommand();
        $exception = $event->getException();

        $message = sprintf(
            '%s: %s (uncaught exception) at %s line %s while running console command `%s`',
            get_class($exception),
            $exception->getMessage(),
            $exception->getFile(),
            $exception->getLine(),
            $command->getName()
        );

        $this->logger->error($message, array('exception' => $exception));
        $this->logger->error('Exception trace:');

        // exception related properties
        $trace = $exception->getTrace();
        array_unshift($trace, array(
            'function' => '',
            'file' => $exception->getFile() !== null ? $exception->getFile() : 'n/a',
            'line' => $exception->getLine() !== null ? $exception->getLine() : 'n/a',
            'args' => array(),
        ));

        for ($i = 0, $count = count($trace); $i < $count; ++$i) {
            $class      = isset($trace[$i]['class'])    ? $trace[$i]['class']   : '';
            $type       = isset($trace[$i]['type'])     ? $trace[$i]['type']    : '';
            $function   = $trace[$i]['function'];
            $file       = isset($trace[$i]['file'])     ? $trace[$i]['file']    : 'n/a';
            $line       = isset($trace[$i]['line'])     ? $trace[$i]['line']    : 'n/a';

            $this->logger->error(sprintf(' %s%s%s() at %s:%s', $class, $type, $function, $file, $line));
        }

        $this->logger->error('');
        $this->logger->error('');

    }
}