<?php

namespace App\Service;

use App\DTO\ApiSuperUser;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class AuthenticationService {
    private $security;

    private $client;

    public const baseUrl = 'local';

    public function __construct(HttpClientInterface $client, SessionInterface $session, SerializerInterface $serializer) {
        $this->client = $client;
        $this->session = $session;
        $encoders = [new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];

        $this->serializer = new Serializer($normalizers, $encoders);
    }

    /**
     * Authentication via post request.
     *
     * @param string $username
     * @param string $password
     */
    public function authentication($identifier, $password) {

        // identifiant : arthur@dev.com arthur1234
        // identifiant : yawoagbotsou 123123
        try {
            $response = $this->client->request('POST', self::baseUrl, [
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
                'json'    => [
                    'identifier' => $identifier,
                    'password'   => $password,
                ],
            ]);

            //dd($response->getContent(), $response->toArray());
            //dd($apiUser, $response->toArray(), $apiUser->jwt);
            $statusCode = $response->getStatusCode();
            if (200 === $statusCode) {
                $apiUser = $this->serializer->deserialize($response->getContent(), ApiSuperUser::class, 'json');
                $content = true;
                $this->session->set('users', $apiUser);
                //dd($this->session->get('users'), $apiUser->jwt, $this->session->get('users')->user['id']);
            } else {
                $content = false;
            }
            // if ($response->toArray()['code'] == 0 && $statusCode === 200) {
            //     $content = true;
            //     // get content from response and set idPartenaire
            //     $content = $response->toArray()['utilisateurExterieur'];
            //     $this->session->set('idPartenaire', $content['idPartenaire']);
            //     $this->session->set('nomPartenaire', $content['nom']);
            //     $this->session->set('prenomPartenaire', $content['prenom']);
            //     $this->session->set('libelleCourtPartenaire', $content['libelleCourtPartenaire']);
            // } else {
            //     $content = false;
            // }
            //dd($content);
            return $content;
        } catch (\Exception$e) {
            return false;
        }

    }
}