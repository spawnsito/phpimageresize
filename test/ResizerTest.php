<?php

require 'Resizer.php';

class ResizerTest extends PHPUnit_Framework_TestCase {

    public function testInstantiation() {
        $resizer = new Resizer('anyNonPathObject');
    }
}