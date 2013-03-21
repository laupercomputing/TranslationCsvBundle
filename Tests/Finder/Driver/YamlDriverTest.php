<?php

namespace LPC\TranslationCsvBundle\Tests;

use LPC\TranslationCsvBundle\Finder\Driver\YamlDriver;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\visitor\vfsStreamStructureVisitor;

/**
 * Test for YamlDriver
 * 
 * @author Kreemer <kreemer@me.com>
 * @package LPCTranslationCsvBundle
 */
class YamlDriverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var YamlDriver 
     */
    protected $object;
    
    public function setUp()
    {
        parent::setUp();
        vfsStream::setup('exampleDir');
        $this->object = new YamlDriver();
    }
    
    /**
     * @test
     */
    public function getExtension()
    {
        $this->assertEquals('yml', $this->object->getFileExtension());
    }
    
    /**
     * @test
     */
    public function getYamlArray()
    {
        file_put_contents(vfsStream::url('exampleDir') . '/testfile.yml', $this->getYamlContent());
        $file = $this->getMock('Symfony\Component\Finder\SplFileInfo', array(), array(), '', false);
        $file->expects($this->once())
            ->method('getRealPath')
            ->will($this->returnValue(vfsStream::url('exampleDir') . '/testfile.yml'));
        
        $this->assertSame(
            array(
                'test.test.blah'    => 'Test1',
                'test.test.blupp'   => 'Test2',
                'test.test2.blah'   => 'Test3',
            ),
            $this->object->parse($file)
        );
    }
    
    protected function getYamlContent()
    {
        
        return <<<EOF
test:
    test:
        blah: Test1
        blupp: Test2
    test2:
        blah: Test3
EOF;
    }
}