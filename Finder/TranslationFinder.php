<?php

namespace LPC\TranslationCsvBundle\Finder;

use LPC\TranslationCsvBundle\Finder\Driver\Driver;
use Symfony\Component\Finder\Finder;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\File\File;
use LPC\TranslationCsvBundle\Translation;
use Symfony\Component\Finder\SplFileInfo;
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
        if (file_exists($path . 'app') && file_exists($path . 'src')) {
            return $finder->files()
                ->in($path . 'app')
                ->in($path . 'src')
                ->path('Resources/translations')
                ->notPath('cache')
                ->notPath('logs')
                ->notPath('Tests')
                ->name('*.' . $this->driver->getFileExtension())
                ->getIterator();
        }
        return $finder->files()
                ->in($path)
                ->path('Resources/translations')
                ->notPath('cache')
                ->notPath('logs')
                ->notPath('Tests')
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
    public function getTranslations(SplFileInfo $file, $translationArray = array())
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
                        break;
                    }
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