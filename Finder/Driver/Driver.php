<?php

namespace LPC\TranslationCsvBundle\Finder\Driver;

use Symfony\Component\HttpFoundation\File\File;

interface Driver
{
    /**
     * get the file extension, which are handled through this driver
     */
    public function getFileExtension();
    
    /**
     * return all translations (key => value paired)
     * 
     * @param \Symfony\Component\HttpFoundation\File\File $file
     * @return array
     */
    public function parse(File $file);
}