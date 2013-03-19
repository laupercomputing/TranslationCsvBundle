<?php

namespace LPC\TranslationCsvBundle\Finder\Driver;

use Symfony\Component\Yaml\Parser;
use Symfony\Component\Finder\SplFileInfo;

/**
 * YamlDriver
 * 
 * @author Kreemer <kreemer@me.com>
 * @package TranslationCsvBundle
 */
class YamlDriver implements Driver
{
    /**
     * @return string
     */
    public function getFileExtension() 
    {
        return 'yml';
    }

    /**
     * @param SplFileInfo $file
     */
    public function parse(SplFileInfo $file) 
    {
        $parser = new Parser();
        $array = $parser->parse(file_get_contents($file->getRealPath()));
        return $this->flatten($array);
    }
    
    protected function flatten($array, $prefix = '')
    {
        $return = array();
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $return = array_merge($return, $this->flatten($value, $prefix . $key . '.'));
            } else {
                $return[$prefix . $key] = $value;
            }
        }
        return $return;
    }
}