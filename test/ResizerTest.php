<?php

require 'Resizer.php';

class ResizerTest extends PHPUnit_Framework_TestCase {

    /**
     * @expectedException InvalidArgumentException
     */
    public function testInstantiation() {
        $resizer = new Resizer('anyNonPathObject');


    }
}
