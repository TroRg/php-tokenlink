<?php

namespace trorg\tokenlink\tests;

use PHPUnit\Framework\TestCase;
use trorg\tokenlink\{Identificator, Token};

class IdentificatorTest extends TestCase
{
    public function testIdentificatorLoading()
    {
        $id1 = 1;
        $id2 = 1;
        $id3 = 5;
        $timestamp = time();

        // id1, id2, id3, timestamp
        $attributes = sprintf("%d;%d;%d;%d", $id1, $id2, $id3, $timestamp);
        $id = Identificator::load($attributes, [
            'id1', 'id2', 'id3', 'timestamp'
        ]);

        $this->assertEquals($id->id1, $id1);
        $this->assertEquals($id->id2, $id2);
        $this->assertEquals($id->id3, $id3);
        $this->assertEquals($id->timestamp, $timestamp);
        $this->assertEquals(count($id->getAttributes()), 4);
        $this->assertEquals((string)$id, $attributes);
    }

    public function testIdentificatorInstantiating()
    {
        $id1 = 1;
        $id2 = 1;
        $id3 = 5;
        $timestamp = time();

        $id = new Identificator([
            'id1' => $id1,
            'id2' => $id2,
            'id3' => $id3,
            'timestamp' => $timestamp,
        ]);
        $this->assertEquals($id->id1, $id1);
        $this->assertEquals($id->id2, $id2);
        $this->assertEquals($id->id3, $id3);
        $this->assertEquals($id->timestamp, $timestamp);
        $this->assertEquals(count($id->getAttributes()), 4);
        $this->assertEquals((string)$id, sprintf('%d;%d;%d;%d', $id1, $id2, $id3, $timestamp));
    }
}

