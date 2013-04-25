<?php
/**
 * CSV Importer
 *
 * User: kreemer <kreemer@me.com>
 * File: CsvImporter.php
 * Date: 16.04.13
 * Time: 13:19
 */

namespace LPC\TranslationCsvBundle\Reader;

use \InvalidArgumentException;
use LPC\TranslationCsvBundle\Translation;

class CsvReader extends Reader
{
    protected $file;

    /**
     * Construct this object with a path to the specific file
     *
     * @param string $file
     * @throws InvalidArgumentException
     */
    public function __construct($file)
    {
        if (!file_exists($file)) {
            throw new InvalidArgumentException();
        }
        $this->file = $file;
    }

    /**
     * return all translation keys
     *
     * @return array
     */
    public function getTranslations()
    {
        $handle = fopen($this->file, 'r');

        //Read first line to determinate the languages
        $line = fgetcsv($handle);
        if (!$line) {
            // empty file
            return array();
        }

        $languages = array();
        for ($i = 4; $i < count($line); $i++) {
            $languages[] = $line[$i];
        }

        $return = array();
        while ($line = fgetcsv($handle)) {
            $translation = new Translation();
            $translation->setPath($line[0]);
            $translation->setDomain($line[1]);
            $translation->setFormat($line[2]);
            $translation->setKey($line[3]);
            $i = 4;
            foreach ($languages as $language) {
                $translation->addTranslation($language, $line[$i]);
                $i++;
            }
            $return[] = $translation;
        }

        return $return;
    }
}