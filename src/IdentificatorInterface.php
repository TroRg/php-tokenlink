<?php

namespace trorg\tokenlink;

interface IdentificatorInterface
{
    public static function load(string $attributes, array $keys = []): IdentificatorInterface;
    public function __toString();
}

