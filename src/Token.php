<?php

namespace trorg\tokenlink;

class Token
{
    /* @var string secret */
    private $_secret = '';

    /* @var int token time to live */
    private $_ttl = 43200;

    /* @var string token identificator */
    private $_identificator;

    /* @var string[] names of Identificator attributes */
    private $_identificatorFields = [];

    /* @var string identificator class */
    private $_identificatorClass = '\trorg\tokenlink\Identificator';

    /* @var string token verification hash */
    private $_hash;

    /* @var int expiration time */
    private $_expiration;

    /**
     * constructor
     */
    public function __construct(IdentificatorInterface $identificator, array $options = [])
    {
        if (array_key_exists('secret', $options)) {
            $this->_secret = $options['secret'];
        }

        if (array_key_exists('ttl', $options)) {
            $this->_ttl = (int)$options['ttl'];
        }

        if (array_key_exists('idFields', $options)) {
            $this->_identificatorFields = (array)$options['idFields'];
        }
        $this->setIdentificator($identificator);
        $this->setExpiration(time() + $this->getTtl());
    }

    /**
     * Create new token from string representation
     * @param string $token raw base64 encoded token
     * @param array $config configuration options like in constructor
     * return Token
     */
    public static function load(string $token, array $config): Token
    {
        $fields = ['id', 'h', 'e'];
        $params = [];

        parse_str(self::base64url_decode($token), $params);
        foreach ($fields as $f) {
            if (!array_key_exists($f, $params)) {
                throw new \UnexpectedValueException('"'. $f .'" should exists in token attributes.');
            }
        }

        $id = Identificator::load($params['id'], $config['idFields']);
        $token = new Token($id, $config);
        $token->setHash($params['h']);
        $token->setExpiration($params['e']);

        return $token;
    }

    /**
     * Secret getter
     *
     * @return string
     */
    public function getSecret(): string
    {
        return $this->_secret;
    }

    /**
     * TTL getter
     *
     * @return int
     */
    public function getTtl(): int
    {
        return $this->_ttl;
    }

    /**
     * Get identificator field names
     *
     * @return string[]
     */
    public function getIdentificatorFields(): array
    {
        return $this->_identificatorFields;
    }

    /**
     * Identificator setter
     *
     * @param Identificator $identificator
     * @return self
     */
    public function setIdentificator(Identificator $identificator): self
    {
        $this->_identificator = $identificator;
        return $this;
    }

    /**
     * Identificator getter
     *
     * @return Identificator|null
     */
    public function getIdentificator(): ?Identificator
    {
        return $this->_identificator;
    }

    /**
     * Hash setter
     * @param string $hash
     * @return self
     */
    public function setHash(string $hash): self
    {
        $this->_hash = $hash;
        return $this;
    }

    /**
     * Hash getter
     *
     * @return string
     */
    public function getHash(): ?string
    {
        if (!$this->_hash) {
            $this->_hash = $this->generateHash();
        }

        return $this->_hash;
    }

    /**
     * Generate new hash for identificator
     *
     * @return string
     */
    public function generateHash(): string
    {
        $h = sprintf('%s%s%s', (string)$this->getIdentificator(), $this->getExpiration(), $this->getSecret());
        $h = md5($h, true);
        $h = self::base64url_encode($h);
        return $h;
    }

    /**
     * Expiration setter
     */
    public function setExpiration(int $ts): self
    {
        $this->_expiration = $ts;
        return $this;
    }

    /**
     * Expiration getter
     *
     * @return int
     */
    public function getExpiration(): int
    {
        return $this->_expiration;
    }

    /**
     * Validate token, usually used only when token is loaded from
     * string representation.
     */
    public function validate()
    {
        if (time() > $this->getExpiration()) {
            throw new exceptions\TokenExpiredException('Token is expired.');
        }
        if ($this->getHash() != $this->generateHash()) {
            throw new exceptions\InvalidHashException('Invalid token hash.');
        }
    }

    /**
     * Check if token is valid
     *
     * @return bool
     */
    public function isValid(): bool
    {
        try {
            $this->validate();
            return true;
        } catch(\Exception $e) {
            return false;
        }
    }

    /**
     * Returns string representation of token, as it used in URLs
     */
    public function __toString()
    {
        $t = sprintf('id=%s&h=%s&e=%d', $this->getIdentificator(), $this->getHash(), $this->getExpiration());
        return self::base64url_encode($t);
    }

    private static function base64url_encode($data) { 
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '='); 
    } 

    private static function base64url_decode($data) { 
        return base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT)); 
    }
}

