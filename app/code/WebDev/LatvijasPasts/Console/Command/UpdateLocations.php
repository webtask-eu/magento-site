<?php

namespace WebDev\LatvijasPasts\Console\Command;

use Magento\Framework\App\ObjectManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use WebDev\LatvijasPasts\Cron\CronUpdateLocations;

class UpdateLocations extends Command
{
    protected function configure()
    {
        $this->setName('webdev:latvijaspasts:updatelocations');
        $this->setDescription('Update Pickup Points.');
        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $objectManager = ObjectManager::getInstance();
            $cron = $objectManager->create(CronUpdateLocations::class);
            $cron->execute();

            $output->writeln('<info>Pickup Points were updated.</info>');
        } catch (\Exception $e) {
            $output->writeln('<error>' . $e->getMessage() . '</error>');
        }
        return 0;
    }
}
