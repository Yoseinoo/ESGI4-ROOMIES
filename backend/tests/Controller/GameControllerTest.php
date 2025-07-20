<?php
namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class GameControllerTest extends WebTestCase
{
    /*
    public function testPlayMove(): void
    {
        $client = static::createClient();

        // You can prepare database fixtures here or mock them

        $client->request('POST', '/rooms/1/play', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode([
            'row' => 0,
            'col' => 1,
            'user' => 'player1@example.com',
        ]));

        $this->assertResponseIsSuccessful();
        $this->assertJson($client->getResponse()->getContent());

        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals('ok', $data['status']);
    }
    */
}
