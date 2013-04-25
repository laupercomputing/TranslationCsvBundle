<?php
/**
 * Test for the YamlWriter
 *
 * User: kreemer <kreemer@me.com>
 * File: YamlWriterTest.php
 * Date: 25.04.13
 * Time: 11:50
 */
namespace LPC\TranslationCsvBundle\Tests\Writer;

use LPC\TranslationCsvBundle\Writer\YamlWriter;
use Symfony\Component\Yaml\Yaml;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;

/**
 * Class YamlWriterTest
 *
 * @package LPC\TranslationCsvBundle\Tests\Writer
 */
class YamlWriterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var YamlWriter
     */
    protected $object;

    /**
     * Set up this test
     *
     * @return void
     */
    public function setUp()
    {
        $this->object = new YamlWriter();
    }

    /**
     * @test
     *
     * @return void
     */
    public function processAnTranslation()
    {
        $translation = $this->mockTranslation('testA.testB.testC', array('de' => 'blupp'));

        $return = $this->object->processTranslations(array($translation), array('de'));
        $expected = array('/tmp/test.de.yml' => array('testA' => array('testB' => array('testC' => 'blupp'))));

        $this->assertEquals($expected, $return);
    }

    /**
     * @test
     * @depends processAnTranslation
     *
     * @return void
     */
    public function processTwoTranslations()
    {
        $translation1 = $this->mockTranslation('testA.testB.testC', array('de' => 'blupp'));
        $translation2 = $this->mockTranslation('testA.testB.testD', array('de' => 'blah'));

        $return = $this->object->processTranslations(array($translation1, $translation2), array('de'));
        $expected = array(
            '/tmp/test.de.yml' => array(
                'testA' => array('testB' => array('testC' => 'blupp', 'testD' => 'blah'))
            )
        );

        $this->assertEquals($expected, $return);
    }

    /**
     * @test
     * @depends processAnTranslation
     *
     * @return void
     */
    public function processTwoTranslationsWithDifferentParent()
    {
        $translation1 = $this->mockTranslation('testA.testB.test', array('de' => 'blupp'));
        $translation2 = $this->mockTranslation('testA.testC.test', array('de' => 'blah'));

        $return = $this->object->processTranslations(array($translation1, $translation2), array('de'));
        $expected = array(
            '/tmp/test.de.yml' => array(
                'testA' => array(
                    'testB' => array('test' => 'blupp'),
                    'testC' => array('test' => 'blah')
                )
            )
        );

        $this->assertEquals($expected, $return);
    }

    /**
     * @test
     * @depends processAnTranslation
     *
     * @return void
     */
    public function processTwoTranslationsWithDifferentRoot()
    {
        $translation1 = $this->mockTranslation('testA.test.test', array('de' => 'blupp'));
        $translation2 = $this->mockTranslation('testB.test.test', array('de' => 'blah'));

        $return = $this->object->processTranslations(array($translation1, $translation2), array('de'));
        $expected = array(
            '/tmp/test.de.yml' => array(
                'testA' => array('test' => array('test' => 'blupp')),
                'testB' => array('test' => array('test' => 'blah'))
            )
        );

        $this->assertEquals($expected, $return);
    }

    /**
     * @test
     * @depends processAnTranslation
     *
     * @return void
     */
    public function processTwoTranslationsWithDifferentPath()
    {
        $translation1 = $this->mockTranslation('test.test', array('de' => 'blupp'), '/tmpA');
        $translation2 = $this->mockTranslation('test.test', array('de' => 'blah'), '/tmpB');

        $return = $this->object->processTranslations(array($translation1, $translation2), array('de'));
        $expected = array(
            '/tmpA/test.de.yml' => array('test' => array('test' => 'blupp')),
            '/tmpB/test.de.yml' => array('test' => array('test' => 'blah'))
        );

        $this->assertEquals($expected, $return);
    }

    /**
     * @test
     *
     * @return void
     */
    public function writeToYamlFile()
    {
        $expected = array(
            '/tmpA/test.de.yml' => array(
                'testA' => array('test' => 'blupp'),
                'testB' => array('test' => 'blah'),
            ),
        );
        vfsStream::setup('root');
        $url = vfsStream::url('root');
        $this->object->writeToFile($expected, $url);
        $this->assertTrue(file_exists($url . '/tmpA/test.de.yml'));
        $content = file_get_contents($url . '/tmpA/test.de.yml');
        $this->assertEquals(Yaml::dump(current($expected)), $content);
    }

    /**
     * @test
     *
     * @return void
     */
    public function getFilename()
    {
        $translation = $this->getMock('LPC\TranslationCsvBundle\Translation');
        $translation->expects($this->once())
            ->method('getPath')
            ->will($this->returnValue('/tmp'));
        $translation->expects($this->once())
            ->method('getFormat')
            ->will($this->returnValue('ccc'));
        $translation->expects($this->once())
            ->method('getDomain')
            ->will($this->returnValue('test'));

        $filename = $this->object->getFilename($translation, 'de');

        $this->assertEquals('/tmp/test.de.ccc', $filename);
    }

    /**
     * mock a translation
     *
     * @param string $key
     * @param array $value
     * @param string $path
     * @param string $domain
     * @param string $format
     *
     * @return mixed
     */
    protected function mockTranslation($key, $value, $path = '/tmp', $domain = 'test', $format = 'yml')
    {
        $translation = $this->getMock('LPC\TranslationCsvBundle\Translation');
        $translation->expects($this->once())
            ->method('getPath')
            ->will($this->returnValue($path));
        $translation->expects($this->once())
            ->method('getFormat')
            ->will($this->returnValue($format));
        $translation->expects($this->once())
            ->method('getDomain')
            ->will($this->returnValue($domain));
        $translation->expects($this->any())
            ->method('getKey')
            ->will($this->returnValue($key));
        $translation->expects($this->any())
            ->method('getTranslations')
            ->will($this->returnValue($value));

        return $translation;
    }
}
