<?php
namespace App\Service;

use Firebase\JWT\JWT;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\SignatureInvalidException;

class JwtService {

    private $error;

    private $privateKey;

    private $publicKey;

    private $passPharse;

    private $algorithm;

    public function __construct($privateKey, $publicKey, $passPharse, $algorithm) {
        $this->privateKey = $privateKey;
        $this->publicKey = $publicKey;
        $this->passPharse = $passPharse;
        $this->algorithm = $algorithm;
    }

    public function createJwtCookie($payload = []) {
        $expireTime = isset($payload['exp']) ? $payload['exp'] : time() + 3600;
        $payload['exp'] = $expireTime;
       
        $privateKey = openssl_pkey_get_private('file:///' . $this->privateKey, $this->passPharse);
        
        $jwt = JWT::encode($payload, $privateKey, 'HS256');

        // If you are developing on a non-https server, you will need to set
        // the $useHttps variable to false

        $useHttps = false;
        setcookie("jwt", $jwt, $expireTime, "", "", $useHttps, true);
    }

    public function verifyToken($token) {
        // Default error message
        $this->error = "Impossible de valider la session.";

        try
        {
            $decodedJwt = JWT::decode($token, getenv("JWT_SECRET"), ['HS256']);

            $this->error = "";

            // Refresh token if it's expiring in 10 minutes
            if (time() - $decodedJwt->exp < 600) {
                $this->createJwtCookie([
                    'user_id'               => $decodedJwt->user_id,
                    'identifiantPartenaire' => $decodedJwt->identifiantPartenaire,
                ]);
            }

            return [
                'user_id'               => $decodedJwt->user_id,
                'identifiantPartenaire' => $decodedJwt->identifiantPartenaire,
            ];
        } catch (ExpiredException $e) {
            $this->error = "La session est expirée.";
        } catch (SignatureInvalidException $e) {
            // In this case, you may also want to send an email to yourself with the JWT
            // If someone uses a JWT with an invalid signature, it could
            // be a hacking attempt.
            $this->error = "Tentative d'accès à une session non valide.";
        } catch (\Exception$e) {
            // Use the default error message
        }

        return false;
    }

    public function getError() {
        return $this->error;
    }
}
