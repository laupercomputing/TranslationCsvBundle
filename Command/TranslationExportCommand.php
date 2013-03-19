<?php

namespace LPC\TranslationCsvBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class TranslationExportCommand
 * @package LPC\TranslationCsvBundle\Command
 * @author Kreemer <kreemer@me.com>
 */
class TranslationExportCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('translation:export')
            ->setDescription('Export the translation keys to csv')
            ->addArgument('languages', InputArgument::REQUIRED, 'languages which should be exported (comma seperated 2 char identifies like en,fr)')
            ->addOption('excel', 'e', NULL, 'transforms output to excel charset')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

    }
}