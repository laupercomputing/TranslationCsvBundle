<?php
/**
 * Abstract class for writers
 *
 * User: kreemer <kreemer@me.com>
 * File: Writer.php
 * Date: 25.04.13
 * Time: 11:47
 */
namespace LPC\TranslationCsvBundle\Writer;

use LPC\TranslationCsvBundle\Translation;
use Symfony\Component\Yaml\Yaml;

/**
 * Class Writer
 *
 * @package LPC\TranslationCsvBundle\Writer
 */
abstract class Writer
{
    /**
     * process the translations and return array with key => filename and content is an array
     *
     * @param array   $translations
     * @param string  $languages
     *
     * @return array
     */
    public function processTranslations($translations, $languages)
    {
        $files = array();
        foreach ($languages as $language) {
            foreach ($translations as $translation)
            {
                /** @var Translation $translation */
                $filename = $this->getFilename($translation, $language);
                $files[$filename][] = array(
                    'language' => $language,
                    'translation' => $translation,
                );
            }
        }

        $contents = array();
        foreach ($files as $filename => $infos) {
            foreach ($infos as $info) {
                $trans = $info['translation']->getTranslations();
                if (array_key_exists($info['language'], $trans)) {
                    $contents[$filename][$info['translation']->getKey()] = $trans[$info['language']];
                }
            }
        }


        $fileArray = array();
        foreach ($contents as $filename => $infos) {
            foreach ($infos as $key => $value) {
                $fileArray[$filename] = array_merge_recursive(
                    isset($fileArray[$filename]) ? $fileArray[$filename] : array(),
                    $this->transformToArray($key, $value)
                );
            }
        }

        return $fileArray;
    }

    /**
     * transform a key like test.test.test into an array
     * array(test => array(test => array(test => value)))
     *
     * @param $key
     * @param $value
     *
     * @return array
     */
    protected function transformToArray($key, $value)
    {
        $keys = explode('.', $key);
        $tmpValue = $value;
        for ($i = count($keys); $i > 0; $i--) {
            $returnArray = array();
            $returnArray[$keys[$i - 1]] = $tmpValue;
            $tmpValue = $returnArray;
        }

        return $returnArray;
    }

    /**
     * Process the filename
     *
     * @param Translation $translation
     * @param string      $language
     *
     * @return string
     */
    public function getFilename(Translation $translation, $language)
    {
        return $translation->getPath() . DIRECTORY_SEPARATOR .
            $translation->getDomain() . '.' .
            $language . '.' .
            $translation->getFormat();
    }

    /**
     * write array of translations to files
     *
     * @param array  $array
     * @param string $root
     *
     * @return boolean
     */
    abstract public function writeToFile($array, $root);
}