<?php
/**
 * test case for the csv importer
 *
 * User: kreemer <kreemer@me.com>
 * File: CsvImporterTest.php
 * Date: 16.04.13
 * Time: 13:22
 */

namespace LPC\TranslationCsvBundle\Tests\Reader;

use LPC\TranslationCsvBundle\Reader\CsvReader;
use LPC\TranslationCsvBundle\Translation;
use org\bovigo\vfs\vfsStream;

/**
 * Class CsvImporterTest
 * @package LPC\TranslationCsvBundle\Tests\Import
 */
class CsvReaderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var CsvReader
     */
    protected $object;

    public function setUp()
    {
        parent::setUp();
        vfsStream::setup('exampleDir');
        vfsStream::create(array('test' => 'testfile.csv'));
        $this->object = new CsvReader(vfsStream::url('exampleDir/test'));
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     */
    public function setNotExistingFile()
    {
        new CsvReader(vfsStream::url('/exampleDir') . '/doesNotExist');
    }

    /**
     * @test
     */
    public function noTranslationsIfFileIsEmpty()
    {
        $this->assertCount(0, $this->object->getTranslations());
    }

    /**
     * @test
     */
    public function parseOneLine()
    {
        vfsStream::create(array('test' => 'path,domain,format,key,de,fr' . PHP_EOL . '/test/path,msg,yml,test,hallo,bonjour'));
        $results = $this->object->getTranslations();
        $this->assertCount(1, $results);

        return current($results);
    }

    /**
     * @test
     * @depends parseOneLine
     */
    public function isTranslationObject($result)
    {
        $this->assertTrue($result instanceof Translation);

        return $result;
    }

    /**
     * @test
     * @depends isTranslationObject
     */
    public function hasAllProperties($result)
    {
        $this->assertAttributeEquals('/test/path', 'path', $result);
        $this->assertAttributeEquals('msg', 'domain', $result);
        $this->assertAttributeEquals('yml', 'format', $result);
        $this->assertAttributeEquals('test', 'key', $result);
        $this->assertAttributeCount(2, 'translations', $result);
    }

    /**
     * @test
     */
    public function parseMultipleLines()
    {
        vfsStream::create(
            array(
                'test' => 'path,domain,format,key,de,fr'
                    . PHP_EOL . '/test/path,msg,yml,test,hallo,bonjour'
                    . PHP_EOL . '/test/path,msg,yml,test2,huhu,blupp'
                    . PHP_EOL . '/test/path1,msg,yml,test,hello,hello'
            )
        );
        $results = $this->object->getTranslations();
        $this->assertCount(3, $results);
    }

}
