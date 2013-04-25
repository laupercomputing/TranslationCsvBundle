<?php
/**
 * This command transform a csv file to a yml or php file, which can be directly used by Symfony2 for translations
 *
 * User: kreemer <kreemer@me.com>
 * File: TranslationImportCommand.php
 * Date: 16.04.13
 * Time: 12:19
 */

namespace LPC\TranslationCsvBundle\Command;

use LPC\TranslationCsvBundle\Import\CsvImporter;
use LPC\TranslationCsvBundle\Reader\CsvReader;
use LPC\TranslationCsvBundle\Writer\YamlWriter;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use LPC\TranslationCsvBundle\Finder\Driver\YamlDriver;
use LPC\TranslationCsvBundle\Finder\TranslationFinder;

/**
 * Class TranslationImportCommand
 * @package LPC\TranslationCsvBundle\Command
 */
class TranslationImportCommand extends ContainerAwareCommand
{
    /**
     * configure this command
     *
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('translation:import')
            ->setDescription('Import a csv file and transform it to a translation file')
            ->addArgument('file', InputArgument::REQUIRED, 'path to the csv file containing the translations')
            ->addArgument('languages', InputArgument::REQUIRED, 'languages which should be imported (comma seperated 2 char identifies like en,fr)')
            ->addArgument('directory', InputArgument::OPTIONAL, 'path to root of the symfony2 application')
            ->addArgument('force-format', InputArgument::OPTIONAL, 'forces the format of the destination translation files (valid entries are: yml)')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $directory = $input->getArgument('directory');
        if ($directory != null && trim($directory) !== '' && file_exists($directory)) {
            $path = realpath($directory);
        } else {
            $path = realpath($this->getContainer()->get('kernel')->getRootDir() . '/../');
        }

        $languages = explode(',', $input->getArgument('languages'));

        $csvReader = new CsvReader($input->getArgument('file'));
        $ymlWriter = new YamlWriter();

        $translations = $csvReader->getTranslations();
        $processedTranslations = $ymlWriter->processTranslations($translations, $languages);

        $ymlWriter->writeToFile($processedTranslations, $path);



    }

}