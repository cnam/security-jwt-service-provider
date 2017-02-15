<?php

namespace Silex\Component\Security\Core\Encoder;

use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use \Firebase\JWT\JWT;

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
    private $algorithm;

    public function __construct($secretKey, $lifeTime, $allowed_algs)
    {
        $this->secretKey = $secretKey;
        $this->lifeTime = $lifeTime;
        $this->allowed_alg = $allowed_algs;
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

        if (is_array($this->secretKey) && $this->algorithm == 'RS256') {
            return \JWT::encode($data, $this->secretKey['private'], $this->algorithm);
        }

        return JWT::encode($data, $this->secretKey, $this->algorithm);
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
            if (is_array($this->secretKey) && $this->algorithm == 'RS256') {
                $data = \JWT::decode($token, $this->secretKey['public'], [$this->algorithm]);
            } else {
                $data = \JWT::decode($token, $this->secretKey, [$this->algorithm]);
            }
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
