<?php

namespace trorg\tokenlink\tests;

use PHPUnit\Framework\TestCase;
use trorg\tokenlink\{Identificator, IdentificatorInterface, Token};

class TokenTest extends TestCase
{
    private $tokenOptions = [
        'secret' => '123123',
        'ttl' => 1000,
        'idFields' => [
            'id1', 'id2', 'id3', 'timestamp',
        ],
    ];

    private $idAttributes = [
        'id1' => 1,
        'id2' => 2,
        'id3' => 5,
    ];

    public function testTokenCreation()
    {
        $this->idAttributes['timestamp'] = time();
        $id = new Identificator($this->idAttributes);
        $this->assertTrue($id instanceof IdentificatorInterface);
        $token = new Token($id, $this->tokenOptions);

        $this->assertEquals($token->getSecret(), $this->tokenOptions['secret']);
        $this->assertEquals($token->getTtl(), $this->tokenOptions['ttl']);
        $this->assertEquals($token->isValid(), true);

        return (string)$token;
    }

    /**
     * @depends testTokenCreation
     */
    public function testTokenLoading($uriToken)
    {

        $token = Token::load($uriToken, $this->tokenOptions);
        $token->validate();
        $this->assertEquals($token->isValid(), true);
        $this->assertEquals((string)$token, $uriToken);
    }
}


