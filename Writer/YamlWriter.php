<?php
/**
 * YamlWriter
 *
 * User: kreemer <kreemer@me.com>
 * File: YamlWriter.php
 * Date: 25.04.13
 * Time: 11:47
 */
namespace LPC\TranslationCsvBundle\Writer;

use Symfony\Component\Yaml\Yaml;

/**
 * Class YamlWriter
 *
 * @package LPC\TranslationCsvBundle\Writer
 */
class YamlWriter extends Writer
{
    /**
     * write array to file
     *
     * @param array  $array
     * @param string $root
     *
     * @return bool
     */
    public function writeToFile($array, $root)
    {
        foreach ($array as $filename => $content) {
            $dirname = dirname($root . $filename);
            if (!file_exists($dirname)) {
                mkdir($dirname, 0777, true);
            }
            if (!file_exists($dirname)) {
                throw new \RuntimeException('Failure to create directory');
            }
            file_put_contents($root . $filename, Yaml::dump($content));
        }

        return true;
    }
}