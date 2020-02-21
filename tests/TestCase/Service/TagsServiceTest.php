<?php

namespace App\Test\TestCase\Service;

use App\Test\FixturesTrait;
use App\Test\TestCase\IntegrationTestCase;
use CakephpFactoryMuffin\FactoryLoader;
use Cake\Utility\Hash;

class TagsServiceTest extends IntegrationTestCase
{
    use FixturesTrait;

    public function testTagsArray()
    {
        $tags = FactoryLoader::seed(5, 'Tags');
        $this->sendJsonRequest("/tags", 'GET');
        $this->assertResponseOk();
        $response = $this->getJsonResponse();
        sort($response['tags']);
        $expected = Hash::extract($tags, '{n}.label');
        sort($expected);
        $this->assertEquals([
            'tags' => $expected
        ], $response);
    }

    public function testEmptyTagList()
    {
        $this->sendJsonRequest("/tags", 'GET');
        $this->assertResponseOk();
        $this->assertEquals([
            'tags' => []
        ], $this->getJsonResponse());
    }
}
