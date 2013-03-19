<?php

namespace LPC\TranslationCsvBundle\Finder;

use LPC\TranslationCsvBundle\Finder\Driver\Driver;
use Symfony\Component\Finder\Finder;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\File\File;
use LPC\TranslationCsvBundle\Translation;

/**
 * Translation Finder
 * 
 * @author Kreemer <kreemer@me.com>
 * @package LPCTranslationCsvBundle
 */
class TranslationFinder
{
    /**
     * @var \LPC\TranslationCsvBundle\Finder\Driver\Driver 
     */
    protected $driver;

    /**
     * @param \LPC\TranslationCsvBundle\Finder\Driver\Driver $driver
     */
    public function __construct(Driver $driver)
    {
        $this->driver = $driver;
    }
    
    /**
     * returns all files which can be handled with the driver
     * 
     * @param string $path
     * @return array
     */
    public function getTranslateFiles($path)
    {
        if (!is_string($path)) {
            throw new InvalidArgumentException();
        }
        $finder = new Finder();
        return $finder->files()
                ->in($path)
                ->path('Resources/translations')
                ->name('*.' . $this->driver->getFileExtension())
                ->getIterator();
    }
    
    /**
     * returns Translation POPO Objects for Translations
     * 
     * @param File $file
     * @param translationArray $array
     * @return array
     */
    public function getTranslations(File $file, $translationArray = array())
    {
        $fileInfos = explode('.', $file->getBasename());
        $domain = $fileInfos[0];
        $language = $fileInfos[1];
        
        $translations = $this->driver->parse($file);
        
        foreach ($translations as $translationKey => $translation) {
            $checked = false;
            
            // check if translation already exists
            if (isset($translationArray[$file->getPath()][$domain])) {
                foreach ($translationArray[$file->getPath()][$domain] as $trans) {
                    if ($trans->getKey() == $translationKey) {
                        $checked = true;
                        $trans->addTranslation($language, $translation);
                    }
                    continue;
                }
            } 
            
            // doesnt exist --> create one
            if (!$checked) {
                $trans = new Translation();
                $trans->setDomain($domain)
                    ->setFormat($file->getExtension())
                    ->setKey($translationKey)
                    ->setPath($file->getPath())
                    ->addTranslation($language, $translation);

                $translationArray[$file->getPath()][$domain][] = $trans;
                
            }
        }
        
        return $translationArray;
    }
            
}