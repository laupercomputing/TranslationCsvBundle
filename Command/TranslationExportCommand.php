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
            ->addArgument('directory', InputArgument::OPTIONAL, 'path to root of the symfony2 application')
            //->addOption('excel', null, null, 'transforms output to excel charset')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $languages = explode(',', $input->getArgument('languages'));
        $directory = $input->getArgument('directory');
        if ($directory != null && trim($directory) !== '' && file_exists($directory)) {
            $path = realpath($directory);
        } else {
            $path = realpath($this->getContainer()->get('kernel')->getRootDir() . '/../');    
        }
        
        $driver = new \LPC\TranslationCsvBundle\Finder\Driver\YamlDriver();
        $translationFinder = new \LPC\TranslationCsvBundle\Finder\TranslationFinder($driver);
        $files = $translationFinder->getTranslateFiles($path);
        $translations = array();
        foreach ($files as $file) {
            $translations = array_merge(
                $translations, 
                $translationFinder->getTranslations($file, $translations)
            );
        }
        $output->writeln('path,domain,format,key,' . implode(',', $languages));
        
        foreach ($translations as $domains) {
            foreach($domains as $formats) {
                foreach ($formats as $translation)
                {
                    $values = $translation->getTranslations();
                    
                    $output->write(
                        $translation->getPath() . ',' . 
                        $translation->getDomain() . ',' . 
                        $translation->getFormat() . ',' . 
                        $translation->getKey() . ','
                    );
                    
                    foreach ($languages as $index => $lang) {
                        if (isset($values[$lang])) {
                            $output->write($values[$lang]);
                        } else {
                            $output->write('');
                        }
                        if ($index + 1 < count($languages)) {
                            $output->write(',');
                        } else {
                            $output->writeln('');
                        }
                    }
                    
                }
            }
        }
    }
}