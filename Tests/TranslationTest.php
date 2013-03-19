<?php

namespace LPC\TranslationCsvBundle\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use LPC\TranslationCsvBundle\Translation;

/**
 * Test for Translation POPO
 * 
 * @author Kreemer <kreemer@me.com>
 * @package LPCTranslationCsvBundle
 */
class TranslationTest extends WebTestCase
{
    /**
     * @var Translation 
     */
    protected $object;
    
    public function setUp()
    {
        parent::setUp();
        $this->object = new Translation();
    }
    
    /**
     * @test
     */
    public function setPath()
    {
        $this->object->setPath('/var/test/');
        $this->assertAttributeEquals('/var/test/', 'path', $this->object);
    }
    
    /**
     * @test
     * @expectedException \InvalidArgumentException
     */
    public function setInvalidPath()
    {
        $this->object->setPath(1);
    }
    
    /**
     * @test
     */
    public function setDomain()
    {
        $this->object->setDomain('messages');
        $this->assertAttributeEquals('messages', 'domain', $this->object);
    }
    
    /**
     * @test
     * @expectedException \InvalidArgumentException
     */
    public function setInvalidDomain()
    {
        $this->object->setDomain(1);
    }
    
    /**
     * @test
     */
    public function setKey()
    {
        $this->object->setKey('test');
        $this->assertAttributeEquals('test', 'key', $this->object);
    }
    
    /**
     * @test
     * @expectedException \InvalidArgumentException
     */
    public function setInvalidKey()
    {
        $this->object->setKey(1);
    }
    
    /**
     * @test
     */
    public function addTranslation()
    {
        $this->object->addTranslation('en', 'blupp');
        $this->assertAttributeEquals(array('en' => 'blupp'), 'translations', $this->object);
    }
    
    /**
     * @test
     * @depends addTranslation
     */
    public function addTranslationDoesNotOverwrite()
    {
        $this->object->addTranslation('en', 'blupp');
        $this->object->addTranslation('fr', 'blûpp');
        $this->assertAttributeEquals(array('en' => 'blupp', 'fr' => 'blûpp'), 'translations', $this->object);
    }
    
    /**
     * @test
     * @depends addTranslation
     */
    public function addTranslationDoesOverwriteSameLanguage()
    {
        $this->object->addTranslation('en', 'blupp');
        $this->object->addTranslation('en', 'blapp');
        $this->assertAttributeEquals(array('en' => 'blapp'), 'translations', $this->object);
    }
    
    /**
     * @test
     * @expectedException \InvalidArgumentException
     */
    public function addInvalidTranslation()
    {
        $this->object->addTranslation('en', 1);
    }
}