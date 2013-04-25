<?php
/**
 * Interface for the importer
 *
 * User: kreemer <kreemer@me.com>
 * File: Importer.php
 * Date: 16.04.13
 * Time: 13:17
 */

namespace LPC\TranslationCsvBundle\Reader;

use LPC\TranslationCsvBundle\Translation;

/**
 * Class Reader
 *
 * @package LPC\TranslationCsvBundle\Reader
 */
abstract class Reader
{
    /**
     * Construct this object with a path to the specific file
     *
     * @param string $file
     */
    abstract public function __construct($file);

    /**
     * return all translation keys
     *
     * @return array
     */
    abstract public function getTranslations();
}