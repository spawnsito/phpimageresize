<?php

require 'Resizer.php';

class ResizerTest extends PHPUnit_Framework_TestCase {

    /**
     * @expectedException InvalidArgumentException
     */
    public function testNecessaryCollaboration() {
        $resizer = new Resizer('anyNonPathObject');
    }

    public function testOptionalCollaboration() {
        $resizer = new Resizer('anyNonPathObject');
    }


}
