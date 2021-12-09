<?php
namespace packages\infrastructure\database;
use packages\domain\model\authentication\authorization\AuthenticationRefreshToken;
interface RefreshTokenRepository {
	public function save(AuthenticationRefreshToken  $refreshToken): void;
}
