<?php

namespace LPC\TranslationCsvBundle\Tests;

use LPC\TranslationCsvBundle\Finder\TranslationFinder;
use LPC\TranslationCsvBundle\Finder\Driver\Driver;
use org\bovigo\vfs\vfsStream;
use LPC\TranslationCsvBundle\Translation;

/**
 * Test for TranslationFinder
 *
 * @author Kreemer <kreemer@me.com>
 * @package LPCTranslationCsvBundle
 */
class TranslationFinderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var TranslationFinder
     */
    protected $object;

    /**
     * @var Driver
     */
    protected $driver;

    public function setUp()
    {
        parent::setUp();
        vfsStream::setup();
        $driverClass = 'LPC\TranslationCsvBundle\Finder\Driver\Driver';
        $this->driver = $this->getMock($driverClass);
        $this->object = new TranslationFinder($this->driver);
    }

    /**
     * @test
     */
    public function setDriver()
    {
        $this->assertAttributeSame($this->driver, 'driver', $this->object);
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     */
    public function getTranslatableFilesWithInvalidPath()
    {
        $this->object->getTranslateFiles(1);
    }

    /**
     * @test
     */
    public function getNoTranslatableFilesWhenNothingExists()
    {
        $array = $this->object->getTranslateFiles(vfsStream::url('root') . '/');

        $this->assertInstanceOf('\Iterator', $array);
        $this->assertCount(0, $array);
    }

    /**
     * @test
     */
    public function getNoTranslatableFilesWhenNotInSubdirOfResources()
    {
        vfsStream::create(array('file.txt' => 'test'));
        $array = $this->object->getTranslateFiles(vfsStream::url('root') . '/');

        $this->assertInstanceOf('\Iterator', $array);
        $this->assertCount(0, $array);
    }

    /**
     * @test
     */
    public function getNoTranslatableFilesWhenNotRightDriverExtension()
    {
        $this->driver->expects($this->once())
            ->method('getFileExtension')
            ->will($this->returnValue('yml'));

        vfsStream::create(array('test' => array('Resources' => array('translations' => array('file.txt' => 'test')))));
        $array = $this->object->getTranslateFiles(vfsStream::url('root') . '/');

        $this->assertInstanceOf('\Iterator', $array);
        $this->assertCount(0, $array);
    }

    /**
     * @test
     */
    public function getTranslatableFiles()
    {
        $this->driver->expects($this->once())
            ->method('getFileExtension')
            ->will($this->returnValue('yml'));

        vfsStream::create(array('test' => array('Resources' => array('translations' => array('file.yml' => 'test')))));
        $array = $this->object->getTranslateFiles(vfsStream::url('root') . '/');
        $this->assertInstanceOf('\Iterator', $array);
        $this->assertCount(1, $array);
        foreach ($array as $file) {
            $this->assertEquals('file.yml', $file->getFilename());
        }
    }

    /**
     * @test
     */
    public function getTwoTranslatableFiles()
    {
        $this->driver->expects($this->once())
            ->method('getFileExtension')
            ->will($this->returnValue('yml'));

        vfsStream::create(array('test' => array('Resources' => array('translations' => array('file.yml' => 'test', 'file2.yml' => 'test')))));
        $array = $this->object->getTranslateFiles(vfsStream::url('root') . '/');
        $this->assertInstanceOf('\Iterator', $array);
        $this->assertCount(2, $array);
    }

    /**
     * @test
     */
    public function getTranslatablePopo()
    {
        $this->driver->expects($this->once())
            ->method('parse')

            ->will($this->returnValue(array('test.test' => 'blapp')));

        $file = $this->getMock('Symfony\Component\Finder\SplFileInfo', array(), array(), '', false);
        $file->expects($this->once())
            ->method('getBasename')
            ->will($this->returnValue('test.en.yml'));

        $file->expects($this->exactly(3))
            ->method('getPath')
            ->will($this->returnValue('/var/test/'));

        $file->expects($this->once())
            ->method('getExtension')
            ->will($this->returnValue('yml'));

        $transObjects = $this->object->getTranslations($file);

        $this->assertCount(1, $transObjects);
        $this->assertCount(1, current($transObjects));
        $this->assertCount(1, current(current($transObjects)));
        $transObject = current(current(current($transObjects)));
        $this->assertInstanceOf(
            'LPC\TranslationCsvBundle\Translation',
            $transObject
        );
        $this->assertEquals('yml', $transObject->getFormat());
        $this->assertEquals('/var/test/', $transObject->getPath());
        $this->assertEquals('test', $transObject->getDomain());
        $this->assertEquals('test.test', $transObject->getKey());
        $this->assertCount(1, $transObject->getTranslations());
        $this->assertSame(array('en' => 'blapp'), $transObject->getTranslations());

        return $transObjects;
    }

    /**
     * @test
     * @depends getTranslatablePopo
     */
    public function getTranslatablePopoWithTwoFiles($existingTranslations)
    {
        $this->driver->expects($this->once())
            ->method('parse')
            ->will($this->returnValue(array('test.test' => 'blupp')));

        $file = $this->getMock('Symfony\Component\Finder\SplFileInfo', array(), array(), '', false);
        $file->expects($this->once())
            ->method('getBasename')
            ->will($this->returnValue('test.fr.yml'));

        $file->expects($this->exactly(2))
            ->method('getPath')
            ->will($this->returnValue('/var/test/'));

        $transObjects = $this->object->getTranslations($file, $existingTranslations);

        $this->assertCount(1, $transObjects);
        $this->assertCount(1, current($transObjects));
        $this->assertCount(1, current(current($transObjects)));
        $transObject = current(current(current($transObjects)));
        $this->assertInstanceOf(
            'LPC\TranslationCsvBundle\Translation',
            $transObject
        );
        $this->assertEquals('yml', $transObject->getFormat());
        $this->assertEquals('/var/test/', $transObject->getPath());
        $this->assertEquals('test', $transObject->getDomain());
        $this->assertEquals('test.test', $transObject->getKey());
        $this->assertCount(2, $transObject->getTranslations());
        $this->assertSame(array('en' => 'blapp', 'fr' => 'blupp'), $transObject->getTranslations());

        return $transObjects;
    }

    /**
     * @test
     * @depends getTranslatablePopoWithTwoFiles
     */
    public function getTranslatablePopoWithTwoFilesDifferentDomain($existingTranslations)
    {
        $this->driver->expects($this->once())
            ->method('parse')
            ->will($this->returnValue(array('test.test' => 'blupp')));

        $file = $this->getMock('Symfony\Component\Finder\SplFileInfo', array(), array(), '', false);
        $file->expects($this->once())
            ->method('getBasename')
            ->will($this->returnValue('blah.en.yml'));

        $file->expects($this->exactly(3))
            ->method('getPath')
            ->will($this->returnValue('/var/test/'));

        $file->expects($this->once())
            ->method('getExtension')
            ->will($this->returnValue('yml'));

        $transObjects = $this->object->getTranslations($file, $existingTranslations);

        $this->assertCount(1, $transObjects);
        $this->assertCount(2, current($transObjects));
    }

    /**
     * @test
     * @depends getTranslatablePopoWithTwoFiles
     */
    public function getTranslatablePopoWithTwoFilesDifferentPath($existingTranslations)
    {
        $this->driver->expects($this->once())
            ->method('parse')
            ->will($this->returnValue(array('test.test' => 'blupp')));

        $file = $this->getMock('Symfony\Component\Finder\SplFileInfo', array(), array(), '', false);
        $file->expects($this->once())
            ->method('getBasename')
            ->will($this->returnValue('blah.en.yml'));

        $file->expects($this->exactly(3))
            ->method('getPath')
            ->will($this->returnValue('/var/test1/'));

        $file->expects($this->once())
            ->method('getExtension')
            ->will($this->returnValue('yml'));

        $transObjects = $this->object->getTranslations($file, $existingTranslations);

        $this->assertCount(2, $transObjects);
    }

    /**
     * @test
     * @depends getTranslatablePopo
     */
    public function substitutePath($existingTranslations)
    {
        /*$this->driver->expects($this->once())
            ->method('parse')
            ->will($this->returnValue(array('test.test' => 'blupp')));

        $file = $this->getMock('Symfony\Component\Finder\SplFileInfo', array(), array(), '', false);
        $file->expects($this->once())
            ->method('getBasename')
            ->will($this->returnValue('blah.en.yml'));

        $file->expects($this->exactly(3))
            ->method('getPath')
            ->will($this->returnValue('/var/test1/'));

        $file->expects($this->once())
            ->method('getExtension')
            ->will($this->returnValue('yml'));

        $transObjects = $this->object->getTranslations($file, array(), '/var/');

        $this->assertCount(1, $transObjects);
        $transObject = current($transObjects);

        $this->assertEquals('test1/', $transObject->getPath());*/
    }
}