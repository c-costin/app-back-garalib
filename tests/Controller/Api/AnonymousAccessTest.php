<?php

namespace App\Tests\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AnonymousAccessTest extends WebTestCase
{
    public function getPublicUrls()
    {
        yield ['http://localhost:8000/api/garage/'];
        yield ['http://localhost:8000/api/garage/1'];
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
     * @dataProvider getPublicUrls
     */
    public function testGetPublicUrlsAnonymousAccess($url): void
    {
        $client = static::createClient();
        $client->request('GET', $url);

        $this->assertResponseStatusCodeSame('200');
    }

    /**
     * @dataProvider getPrivateUrls
     */
    public function testGetPrivateUrlsAnonymousAccess($url): void
    {
        $client = static::createClient();
        $client->request('GET', $url);

        $this->assertResponseStatusCodeSame('401');
    }

    /**
     * @dataProvider postPrivateUrls
     */
    public function testPostPrivateUrlsAnonymousAccess($url): void
    {
        $client = static::createClient();
        $client->request('POST', $url);

        $this->assertResponseStatusCodeSame('401');
    }

    /**
     * @dataProvider patchPrivateUrls
     */
    public function testPatchPrivateUrlsAnonymousAccess($url): void
    {
        $client = static::createClient();
        $client->request('PATCH', $url);

        $this->assertResponseStatusCodeSame('401');
    }

    /**
     * @dataProvider deletePrivateUrls
     */
    public function testDeletePrivateUrlsAnonymousAccess($url): void
    {
        $client = static::createClient();
        $client->request('DELETE', $url);

        $this->assertResponseStatusCodeSame('401');
    }
}
