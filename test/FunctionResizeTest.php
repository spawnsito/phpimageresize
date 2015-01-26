<?php

class FunctionResizeTest extends PHPUnit_Framework_TestCase {
    public function testOpts()
    {
        $this->assertInstanceOf('RuntimeException', new Options);
    }
}

?>
