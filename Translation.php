<?php

namespace LPC\TranslationCsvBundle;

use \InvalidArgumentException;

/**
 * Translation POPO
 * 
 * @author Kreemer <kreemer@me.com>
 * @package LPCTranslationCsvBundle
 */
class Translation
{
    /**
     * @var string 
     */
    protected $format; 
    
    /**
     * @var string 
     */
    protected $path;
    
    /**
     * @var string 
     */
    protected $domain;
    
    /**
     * @var string 
     */
    protected $key;
    
    /**
     * @var array 
     */
    protected $translations = array();

    /**
     * @return string
     */
    public function getFormat() {
        return $this->format;
    }

    /**
     * @param string $format
     * @return \LPC\TranslationCsvBundle\Translation
     * @throws InvalidArgumentException
     */
    public function setFormat($format) {
        if (!is_string($format)) {
            throw new InvalidArgumentException('format should be string');
        }
        $this->format = $format;
        return $this;
    }
   
    /**
     * @return string
     */
    public function getPath() {
        return $this->path;
    }

    /**
     * @param string $path
     * @return \LPC\TranslationCsvBundle\Translation
     * @throws InvalidArgumentException
     */
    public function setPath($path) {
        if (!is_string($path)) {
            throw new InvalidArgumentException('path should be string');
        }
        $this->path = $path;
        return $this;
    }

    /**
     * @return string
     */
    public function getDomain() {
        return $this->domain;
    }

    /**
     * @param string $domain
     * @return \LPC\TranslationCsvBundle\Translation
     * @throws InvalidArgumentException
     */
    public function setDomain($domain) {
        if (!is_string($domain)) {
            throw new InvalidArgumentException('domain should be string');
        }
        $this->domain = $domain;
        return $this;
    }

    /**
     * @return string
     */
    public function getKey() {
        return $this->key;
    }

    /**
     * @param string $key
     * @return \LPC\TranslationCsvBundle\Translation
     * @throws InvalidArgumentException
     */
    public function setKey($key) {
        if (!is_string($key)) {
            throw new InvalidArgumentException('key should be string');
        }
        $this->key = $key;
        return $this;
    }

    /**
     * @return string
     */
    public function getTranslations() {
        return $this->translations;
    }

    /**
     * @param string $language
     * @param string $translation
     * @return \LPC\TranslationCsvBundle\Translation
     * @throws InvalidArgumentException
     */
    public function addTranslation($language, $translation) {
        if (!is_string($language) || !is_string($translation)) {
            throw new InvalidArgumentException('language and translation should be strings');
        }
        $this->translations[$language] = $translation;
        return $this;
    }


}