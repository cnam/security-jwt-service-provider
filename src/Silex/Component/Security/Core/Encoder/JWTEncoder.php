<?php

namespace Silex\Component\Security\Core\Encoder;

use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class JWTEncoder implements TokenEncoderInterface
{

    /**
     * Secret key for tokens encode and decode
     *
     * @var string
     */
    private $secretKey;

    /**
     * Life time tokens
     *
     * @var int
     */
    private $lifeTime;

    /**
     * Allowed algorithms array
     *
     * @link https://github.com/firebase/php-jwt#200--2015-04-01
     * @link http://jwt.io
     *
     * @var string
     */
    private $allowed_algs;

    public function __construct($secretKey, $lifeTime, $allowed_algs)
    {
        $this->secretKey = $secretKey;
        $this->lifeTime = $lifeTime;
        $this->allowed_algs = $allowed_algs;
    }

    /**
     * Encoded data
     *
     * @param mixed $data
     *
     * @return string
     */
    public function encode($data)
    {
        $data['exp'] = time() + $this->lifeTime;

       return \JWT::encode($data, $this->secretKey);
    }

    /**
     * Token for decoding
     *
     * @param string $token
     * @return array
     *
     * @throws AccessDeniedException
     */
    public function decode($token)
    {
        try {
            $data = \JWT::decode($token, $this->secretKey, $this->allowed_algs);
        } catch (\UnexpectedValueException $e) {
            throw new \UnexpectedValueException($e->getMessage());
        } catch (\DomainException $e) {
            throw new \UnexpectedValueException($e->getMessage());
        }

        if ($data->exp < time()) {
            throw new \UnexpectedValueException('token not allowed');
        }

        return $data;
    }
}