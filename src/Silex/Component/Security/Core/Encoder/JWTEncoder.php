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

    public function __construct($secretKey, $lifeTime)
    {
        $this->secretKey = $secretKey;
        $this->lifeTime = $lifeTime;
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
            $data = \JWT::decode($token, $this->secretKey, true);
        } catch (\UnexpectedValueException $e) {
            throw new AccessDeniedException();
        } catch (\DomainException $e) {
            throw new AccessDeniedException();
        }

        if ($data->exp < time()) {
            throw new AccessDeniedException('token not allowed');
        }

        return $data;
    }
}