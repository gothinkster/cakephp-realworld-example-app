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
        $this->sendRequest("/tags", 'GET', []);
        $this->assertResponseOk();
        $this->assertEquals([
            'tags' => Hash::extract($tags, '{n}.label')
        ], $this->responseJson());
    }

    public function testEmptyTagList()
    {
        $this->sendRequest("/tags", 'GET', []);
        $this->assertResponseOk();
        $this->assertEquals([
            'tags' => []
        ], $this->responseJson());
    }
}
