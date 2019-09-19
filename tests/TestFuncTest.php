<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TestFuncTest extends WebTestCase
{
    /**
     * @dataProvider provideUrls
     */
    public function testMenu($url)
    {
        $client = static::createClient();
        //Permet de naviguer grace au crawler
        $crawler = $client->request('GET', $url);

        $this->assertResponseIsSuccessful();
        $this->assertCount(1, $crawler->filter('nav.main_menu'));
    }

    public function provideUrls()
    {
        return [
            ['/'],
            ['/annonce/'],
            ['/reservation/']
        ];
    }
}
