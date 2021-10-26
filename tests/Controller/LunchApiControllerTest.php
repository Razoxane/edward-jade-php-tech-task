<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class LunchApiControllerTest extends WebTestCase
{
    /**
     * @dataProvider provideUrls
     */
    public function testEndpointIsSuccessful($url)
    {
        $client = static::createClient();

        $client->request('GET', $url);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function provideUrls()
    {
        return [
            ['/lunch'],
            ['/lunch?date=2019-03-07'],
            ['/lunch?date=2019-03-25'],
            ['/lunch?date=2019-03-29'],
        ];
    }

    public function testEndpointFailsOnInvalidDateFormat()
    {
        $client = static::createClient();

        $client->request('GET', '/lunch?date=abcd');

        $this->assertEquals(400, $client->getResponse()->getStatusCode());
        $this->assertEquals(
            '400',
            json_decode($client->getResponse()->getContent())->status
        );
        $this->assertEquals(
            'Value provided for date is an invalid format. YYYY-MM-DD required.',
            json_decode($client->getResponse()->getContent())->message
        );
    }

    public function testEndpointFailsOnInvalidDateValue()
    {
        $client = static::createClient();

        $client->request('GET', '/lunch?date=2021-10-99');

        $this->assertEquals(400, $client->getResponse()->getStatusCode());
        $this->assertEquals(
            '400',
            json_decode($client->getResponse()->getContent())->status
        );
        $this->assertEquals(
            'Value provided for date is an invalid value. Valid date in the format of YYYY-MM-DD required.',
            json_decode($client->getResponse()->getContent())->message
        );
    }

    public function testRecipesDoNotSortWhenBestBeforeNotExceeded()
    {
        $client = static::createClient();

        $client->request('GET', '/lunch', ['date' => '2019-03-13']);
        $earlier_recipes = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals('Hotdog', $earlier_recipes[0]['title']);
        $this->assertEquals('Fry-up', $earlier_recipes[1]['title']);
    }

    public function testRecipesWithOldestBestBeforeIngredientsSortLast()
    {
        $client = static::createClient();

        $client->request('GET', '/lunch', ['date' => '2019-03-14']);
        $later_recipes = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals('Fry-up', $later_recipes[0]['title']);
        $this->assertEquals('Hotdog', $later_recipes[1]['title']);
    }
}
