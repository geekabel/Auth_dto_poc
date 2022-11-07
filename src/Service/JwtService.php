<?php
namespace App\Service;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\SignatureInvalidException;

class JwtService {

    private $error;

    private $privateKey;

    private $publicKey;

    private $passPhrase;

    private $algorithm;

    public function __construct($privateKey, $publicKey, $passPhrase, $algorithm) {
        $this->privateKey = $privateKey;
        $this->publicKey = $publicKey;
        $this->passPhrase = $passPhrase;
        $this->algorithm = $algorithm;
    }

    public function createJwtCookie($payload = []) {
        $expireTime = isset($payload['exp']) ? $payload['exp'] : time() + 3600;
        $payload['exp'] = $expireTime;

        // dd($this->privateKey, $this->passPharse);
        $privateKey = openssl_pkey_get_private(
            file_get_contents($this->privateKey),
            $this->passPhrase
        );
        $jwt = JWT::encode($payload, $privateKey, 'RS256');
        // dd($jwt);
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
            $privateKey = openssl_pkey_get_private(
                file_get_contents($this->privateKey),
                $this->passPhrase
            );
            $publicKey = openssl_pkey_get_details($privateKey)['key'];
            $decodedJwt = JWT::decode($token, new Key($publicKey, 'RS256'));
            $decodedJwt_array = (array) $decodedJwt;
            // dd($decodedJwt_array, $decodedJwt_array['identifiantPartenaire']);

            $this->error = "";

            // Refresh token if it's expiring in 10 minutes
            if (time() - $decodedJwt->exp < 600) {
                $this->createJwtCookie([
                    'user_id'               => $decodedJwt_array['user_id'],
                    'identifiantPartenaire' => $decodedJwt_array['identifiantPartenaire'],
                ]);
            }

            return $decodedJwt_array;
        } catch (ExpiredException $e) {
            $this->error = "La session est expirée.";
        } catch (SignatureInvalidException $e) {
            // In this case, you may also want to send an email to yourself with the JWT
            // If someone uses a JWT with an invalid signature, it could
            // be a hacking attempt.
            $this->error = "Tentative d'accès à une session non valide.";
        } catch (\Exception$e) {
            // Use the default error message
            $e->getMessage();
        }

        return false;
    }

    public function getError() {
        return $this->error;
    }
}
