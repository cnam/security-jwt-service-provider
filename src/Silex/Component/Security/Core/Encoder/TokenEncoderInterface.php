<?php

namespace Silex\Component\Security\Core\Encoder;

interface TokenEncoderInterface
{
    /**
     * Encoded data
     *
     * @param mixed $data
     *
     * @return string
     */
    public function encode($data);

    /**
     * Token for decoding
     *
     * @param string $token
     *
     * @return array|mixed
     */
    public function decode($token);
}