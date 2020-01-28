<?php

namespace trorg\tokenlink;

class Identificator implements IdentificatorInterface
{
    /* @var string array of k = v */
    private $_attributes = [];

    /* @var string delimiter used in load function */
    private $_delimiter = ';';

    public function __construct(array $attributes = [], string $delimiter = ';')
    {
        $this->_attributes = $attributes;
        $this->_delimiter = $delimiter;
    }

    /**
     * Create Identificator instance from string attributes.
     */
    public static function load(string $attributes, array $keys = []): IdentificatorInterface
    {
        $values = explode(";", $attributes);
        $attributes = [];
        foreach ($keys as $k => $key) {
            if (array_key_exists($k, $values)) {
                $attributes[$key] = $values[$k];
            }
        }

        return new Identificator($attributes);
    }

    /**
     * Get all identificator attributes
     *
     * @return array
     */
    public function getAttributes(): array
    {
        return $this->_attributes;
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return join(';', $this->_attributes);
    }

    /**
     * {@inheritdoc}
     */
    public function __get(string $name)
    {
        if (array_key_exists($name, $this->_attributes)) {
            return $this->_attributes[$name];
        }

        return null;
    }
}

