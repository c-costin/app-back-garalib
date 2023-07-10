<?php

namespace App\Tests\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AuthentificationTest extends WebTestCase
{
    public function testAuthentificationApi()
    {
        $client = static::createClient();
        $client->request('POST', 'http://localhost:8000/api/login_check', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode(['email' => 'admin@admin.com', 'password' => 'admin']));

        $this->assertResponseStatusCodeSame('200');
    }

    public function testInvalidCredentialsAuthentificationApi()
    {
        $client = static::createClient();
        $client->request('POST', 'http://localhost:8000/api/login_check', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode(['email' => 'admin@admin.com', 'password' => '']));

        $this->assertResponseStatusCodeSame('401');
    }

    public function testNoAccountAuthentificationApi()
    {
        $client = static::createClient();
        $client->request('POST', 'http://localhost:8000/api/login_check', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode(['email' => 'root@admin.com', 'password' => 'root']));

        $this->assertResponseStatusCodeSame('401');
    }
}

