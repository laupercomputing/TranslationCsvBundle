<?php

namespace LPC\TranslationCsvBundle\Tests;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use org\bovigo\vfs\vfsStream;
use LPC\TranslationCsvBundle\Command\TranslationExportCommand;
use Symfony\Component\Console\Command\Command;
/**
 * Test for TranslationExportCommand
 * 
 * @author Kreemer <kreemer@me.com>
 * @package LPCTranslationCsvBundle
 */
class TranslationExportCommandTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var TranslationExportCommand
     */
    protected $object;
    
    /**
     * @var Application
     */
    protected $app;
    
    /**
     * @var CommandTester
     */
    protected $commandTester;
    
    /**
     * @var Command
     */
    protected $command;
    
    public function setUp()
    {
        parent::setUp();
        vfsStream::setup();
        $this->object = new TranslationExportCommand();
        $this->app = new Application();
        $this->app->add($this->object);
        $this->command = $this->app->find('translation:export');
        $this->commandTester = new CommandTester($this->command); 
    }
    
    /**
     * @test
     */
    public function execute()
    {
        $this->commandTester->execute(
            array(
                'command'   => $this->command->getName(),
                'languages' => 'de',
                'directory' => __DIR__ . '/../Root',
                'format'    => 'yml'
            )
        );

        $display = $this->commandTester->getDisplay();

        $this->assertRegExp('/^path,domain,format,key,de$/im', $display);
        $this->assertRegExp('/Resources\/translations,messages,yml,test.test.test,string$/im', $display);

        // ...
    }
    
}