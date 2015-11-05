<?php
/**
 * Created by PhpStorm.
 * User: sbabych
 * Date: 05.11.2015
 * Time: 11:55
 */

namespace NflBundle\Command;

use NflBundle\Lib\NflHandler;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CronCommand extends NflCommand
{
    protected function configure()
    {
        $this
            ->setName('nfl:cron')
            ->setDescription('Cron command for NFL Games streaming')
        ;

        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $streamCmd = $this->getContainer()->get('nfl.command.stream');
        $urlsCmd = $this->getContainer()->get('nfl.command.urls');

        $streamCmd->run($input, $output);

        if (!$streamCmd->isStreaming) {
            if ($urlsCmd->run($input, $output) != 0) {
                $streamCmd->run($input, $output);
            }
        }
    }
}