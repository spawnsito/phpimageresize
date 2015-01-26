<?php

include 'Configuration.php';

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
        $this->assertInstanceOf('Configuration', new Configuration);
    }

    public function testNullOptsDefaults() {
        $configuration = new Configuration(null);

        $this->assertEquals($this->defaults, $configuration->asHash());
    }

    public function testDefaults() {
        $configuration = new Configuration();
        $asHash = $configuration->asHash();

        $this->assertEquals($this->defaults, $asHash);
    }

    public function testDefaultsNotOverwriteConfiguration() {

        $configuration = array(
            'thumbnail' => true,
            'maxOnly' => true
        );

        $options = new Configuration($configuration);
        $configured = $options->asHash();

        $this->assertTrue($configured['thumbnail']);
        $this->assertTrue($configured['maxOnly']);
    }

    public function testObtainCache() {
        $options = new Configuration();

        $this->assertEquals('./cache/', $options->obtainCache());
    }

    public function testObtainRemote() {
        $options = new Configuration();

        $this->assertEquals('./cache/remote/', $options->obtainRemote());
    }

    public function testObtainConvertPath() {
        $options = new Configuration();

        $this->assertEquals('convert', $options->obtainConvertPath());
    }
}

?>
