<?php

namespace LPC\TranslationCsvBundle\Finder\Driver;

use Symfony\Component\Finder\SplFileInfo;

interface Driver
{
    /**
     * get the file extension, which are handled through this driver
     */
    public function getFileExtension();
    
    /**
     * return all translations (key => value paired)
     * 
     * @param SplFileInfo $file
     * @return array
     */
    public function parse(SplFileInfo $file);
}