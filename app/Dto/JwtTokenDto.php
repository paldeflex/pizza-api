<?php

namespace App\Dto;

readonly class JwtTokenDto
{
    public function __construct(
        public string $accessToken,
        public string $tokenType,
        public int $expiresIn
    ) {}

    public function toArray(): array
    {
        return [
            'access_token' => $this->accessToken,
            'token_type' => $this->tokenType,
            'expires_in' => $this->expiresIn,
        ];
    }
}
