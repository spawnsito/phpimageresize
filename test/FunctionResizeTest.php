<?php

include 'Options.php';

class FunctionResizeTest extends PHPUnit_Framework_TestCase {

    private $defaults = array(
        'crop' => false,
        'scale' => 'false',
        'thumbnail' => false,
        'maxOnly' => false,
        'canvas-color' => 'transparent',
        'output-filename' => false,
        'cacheFolder' => './cache/',
        'remoteFolder' => './cache/remote/',
        'quality' => 90,
        'cache_http_minutes' => 20
    );

    public function testOpts()
    {
        $this->assertInstanceOf('Options', new Options);
    }

    public function testDefaults() {
        $options = new Options();
        $defaults = array(
            'crop' => false,
            'scale' => 'false',
            'thumbnail' => false,
            'maxOnly' => false,
            'canvas-color' => 'transparent',
            'output-filename' => false,
            'cacheFolder' => './cache/',
            'remoteFolder' => './cache/remote/',
            'quality' => 90,
            'cache_http_minutes' => 20
        );

        $asHash = $options->asHash();
        $nullOptions = new Options(null);

        $configuration = array(
            'thumbnail' => true,
            'maxOnly' => true
        );

        $notNullOptions = new Options($configuration);
        $configured = $notNullOptions->asHash();

        $this->assertEquals($defaults, $asHash);
        $this->assertEquals($defaults, $nullOptions->asHash());
        $this->assertTrue($configured['thumbnail']);
        $this->assertTrue($configured['maxOnly']);
    }

    public function testDefaults() {
        $options = new Options();
        $asHash = $options->asHash();

        $this->assertEquals($this->defaults, $asHash);
    }

    public function testDefaultsNotOverwriteConfiguration() {
        $options = new Options();

        $configuration = array(
            'thumbnail' => true,
            'maxOnly' => true
        );

        $notNullOptions = new Options($configuration);
        $configured = $notNullOptions->asHash();

        $this->assertTrue($configured['thumbnail']);
        $this->assertTrue($configured['maxOnly']);
    }
}

?>
