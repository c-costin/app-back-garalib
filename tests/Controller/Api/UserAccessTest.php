<?php

namespace App\Tests\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserAccessTest extends WebTestCase
{
    public function getPublicUrls()
    {
        yield ['http://localhost:8000/api/garage/'];
        yield ['http://localhost:8000/api/garage/1'];
    }

    public function postPublicUrls()
    {
        yield ['http://localhost:8000/api/user/add'];
    }

    public function getPrivateUrls()
    {
        yield ['http://localhost:8000/api/address/'];
        yield ['http://localhost:8000/api/address/1'];
        yield ['http://localhost:8000/api/appointment/'];
        yield ['http://localhost:8000/api/appointment/1'];
        yield ['http://localhost:8000/api/review/'];
        yield ['http://localhost:8000/api/review/1'];
        yield ['http://localhost:8000/api/schedule/'];
        yield ['http://localhost:8000/api/schedule/1'];
        yield ['http://localhost:8000/api/type/'];
        yield ['http://localhost:8000/api/type/1'];
        yield ['http://localhost:8000/api/user/'];
        yield ['http://localhost:8000/api/user/1'];
        yield ['http://localhost:8000/api/vehicle/'];
        yield ['http://localhost:8000/api/vehicle/1'];
    }

    public function postPrivateUrls()
    {
        yield ['http://localhost:8000/api/address/add'];
        yield ['http://localhost:8000/api/appointment/add'];
        yield ['http://localhost:8000/api/garage/add'];
        yield ['http://localhost:8000/api/review/add'];
        yield ['http://localhost:8000/api/schedule/add'];
        yield ['http://localhost:8000/api/type/add'];
        yield ['http://localhost:8000/api/vehicle/add'];
    }

    public function patchPrivateUrls()
    {
        yield ['http://localhost:8000/api/address/edit/1'];
        yield ['http://localhost:8000/api/appointment/edit/1'];
        yield ['http://localhost:8000/api/garage/edit/1'];
        yield ['http://localhost:8000/api/review/edit/1'];
        yield ['http://localhost:8000/api/schedule/edit/1'];
        yield ['http://localhost:8000/api/type/edit/1'];
        yield ['http://localhost:8000/api/user/edit/1'];
        yield ['http://localhost:8000/api/vehicle/edit/1'];
    }

    public function deletePrivateUrls()
    {
        yield ['http://localhost:8000/api/address/delete/1'];
        yield ['http://localhost:8000/api/appointment/delete/1'];
        yield ['http://localhost:8000/api/garage/delete/1'];
        yield ['http://localhost:8000/api/review/delete/1'];
        yield ['http://localhost:8000/api/schedule/delete/1'];
        yield ['http://localhost:8000/api/type/delete/1'];
        yield ['http://localhost:8000/api/user/delete/1'];
        yield ['http://localhost:8000/api/vehicle/delete/1'];
    }

    /**
     * @dataProvider postPublicUrls
     */
    public function testInvalidCredentialsForCreateUserAccess($url): void
    {
        $client = static::createClient();
        $client->request('POST', $url);

        $this->assertResponseStatusCodeSame('400');
    }

    /**
     * @dataProvider postPublicUrls
     */
    public function testCreateUserAccess($url): void
    {
        $client = static::createClient();
        $client->request('POST', $url, [], [], ['CONTENT_TYPE' => 'application/json'], json_encode(['email' => 'john.doe@mail.com', 'password' => 'password', 'roles' => ['ROLE_USER'], 'lastname' => 'doe', 'firstname' => 'john', 'phone' => '0600000000', 'dateOfBirth' => '1997-07-12']));

        $data = json_decode($client->getResponse()->getContent(), true);

        if ($data['code'] === 201) {
            $this->assertResponseStatusCodeSame('201');
        } else {
            $this->assertResponseStatusCodeSame('422');
        }
    }

    public function testAuthentificationUserAccess()
    {
        $client = static::createClient();
        $client->request('POST', 'http://localhost:8000/api/login_check', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode(['email' => 'john.doe@mail.com', 'password' => 'password']));

        $this->assertResponseStatusCodeSame('200');
    }

    public function createAuthenticatedClient()
    {
        $client = static::createClient();
        $client->request('POST', 'http://localhost:8000/api/login_check', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode(['email' => 'john.doe@mail.com', 'password' => 'password']));

        $data = json_decode($client->getResponse()->getContent(), true);

        $client->setServerParameter('HTTP_Authorization', sprintf('Bearer %s', $data['token']));

        return $client;
    }

    public function testGetInfoUserAccess(): void
    {
        $client = $this->createAuthenticatedClient();
        $client->request('GET', 'http://localhost:8000/api/user/info');

        $this->assertResponseStatusCodeSame('200');
    }

    // public function testEditUserAccess(): void
    // {
    //     $client = $this->createAuthenticatedClient();
    //     $client->request('PATCH', 'http://localhost:8000/api/user/edit/320', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode(['fisrtname' => 'john1']));

    //     $this->assertResponseStatusCodeSame('202');
    // }

    /**
     * @dataProvider getPublicUrls
     */
    public function testGetPrivateUrlsAnonymousAccess($url): void
    {
        $client = $this->createAuthenticatedClient();
        $client->request('GET', $url);

        $this->assertResponseStatusCodeSame('200');
    }


    // public function testDeleteUserAccess(): void
    // {
    //     $client = $this->createAuthenticatedClient();
    //     $client->request('DELETE', 'http://localhost:8000/api/user/delete/321');

    //     $this->assertResponseStatusCodeSame('204');
    // }
}
