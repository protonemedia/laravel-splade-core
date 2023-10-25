<?php

namespace Tests\Unit\Data;

use App\Models\User;
use ProtoneMedia\SpladeCore\Data\ParseDataAttribute;
use ProtoneMedia\SpladeCore\Facades\Transformer;
use Tests\TestCase;

class ParseDataAttributeTest extends TestCase
{
    /** @test */
    public function it_handles_null_data()
    {
        $result = ParseDataAttribute::handle(null);

        $this->assertNull($result);
    }

    /** @test */
    public function it_handles_array_data_without_a_transformer()
    {
        $data = ['key' => 'value'];
        $result = ParseDataAttribute::handle($data);

        $this->assertEquals($data, $result);
    }

    /** @test */
    public function it_handles_object_data_without_a_transformer()
    {
        $data = new \stdClass();
        $data->key = 'value';
        $result = ParseDataAttribute::handle($data);

        $this->assertEquals(['key' => 'value'], $result);
    }

    /** @test */
    public function it_handles_array_data_with_a_transformer()
    {
        $data = [
            new User(['id' => 1, 'name' => 'Pascal']),
        ];

        Transformer::register(User::class, function (User $user) {
            return [
                'name' => $user->name,
            ];
        });

        $result = ParseDataAttribute::handle($data);

        $this->assertEquals($result, [['name' => $data[0]->name]]);
    }

    /** @test */
    public function it_handles_object_data_with_a_transformer()
    {
        $data = new User(['id' => 1, 'name' => 'Pascal']);

        Transformer::register(User::class, function (User $user) {
            return [
                'name' => $user->name,
            ];
        });

        $result = ParseDataAttribute::handle($data);

        $this->assertEquals($result, ['name' => $data->name]);
    }

    /** @test */
    public function it_handles_jsonable_data()
    {
        $data = new JsonableTestObject();
        $result = ParseDataAttribute::handle($data);

        $this->assertEquals(['key' => 'value'], $result);
    }

    /** @test */
    public function it_handles_json_serializable_data()
    {
        $data = new JsonSerializableTestObject();
        $result = ParseDataAttribute::handle($data);

        $this->assertEquals(['key' => 'value'], $result);
    }

    /** @test */
    public function it_handles_arrayable_data()
    {
        $data = new ArrayableTestObject();
        $result = ParseDataAttribute::handle($data);

        $this->assertEquals(['key' => 'value'], $result);
    }

    /** @test */
    public function it_handles_json_string_data()
    {
        $jsonString = '{"key": "value"}';
        $result = ParseDataAttribute::handle($jsonString);

        $this->assertEquals(['key' => 'value'], $result);
    }
}

class JsonableTestObject implements \Illuminate\Contracts\Support\Jsonable
{
    public function toJson($options = 0)
    {
        return '{"key": "value"}';
    }
}

class JsonSerializableTestObject implements \JsonSerializable
{
    public function jsonSerialize()
    {
        return ['key' => 'value'];
    }
}

class ArrayableTestObject implements \Illuminate\Contracts\Support\Arrayable
{
    public function toArray()
    {
        return ['key' => 'value'];
    }
}
