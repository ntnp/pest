<?php

it('allows properties to be accessed from the value', function () {
    expect(['foo' => 1])->foo->toBeInt()->toEqual(1);
});

it('can access multiple properties from the value', function () {
    expect(['foo' => 'bar', 'hello' => 'world'])
        ->foo->toBeString()->toEqual('bar')
        ->hello->toBeString()->toEqual('world');
});

it('works with not', function () {
    expect(['foo' => 'bar', 'hello' => 'world'])
        ->foo->not->not->toEqual('bar')
        ->foo->not->toEqual('world')->toEqual('bar')
        ->hello->toEqual('world')->not()->toEqual('bar')->not->toBeNull;
});

it('works with each', function () {
    expect(['numbers' => [1, 2, 3, 4], 'words' => ['hey', 'there']])
        ->numbers->toEqual([1, 2, 3, 4])->each->toBeInt->toBeLessThan(5)
        ->words->each(function ($word) {
            $word->toBeString()->not->toBeInt();
        });
});

it('works inside of each', function () {
    expect(['books' => [['title' => 'Foo', 'cost' => 20], ['title' => 'Bar', 'cost' => 30]]])
        ->books->each(function ($book) {
            $book->title->not->toBeNull->cost->toBeGreaterThan(19);
        });
});

it('works with sequence', function () {
    expect(['books' => [['title' => 'Foo', 'cost' => 20], ['title' => 'Bar', 'cost' => 30]]])
        ->books->sequence(
            function ($book) {
                $book->title->toEqual('Foo')->cost->toEqual(20);
            },
            function ($book) {
                $book->title->toEqual('Bar')->cost->toEqual(30);
            },
        );
});

it('can compose complex expectations', function () {
    expect(['foo' => 'bar', 'numbers' => [1, 2, 3, 4]])
        ->toContain('bar')->toBeArray()
        ->numbers->toEqual([1, 2, 3, 4])->not()->toEqual('bar')->each->toBeInt
        ->foo->not->toEqual('world')->toEqual('bar')
        ->numbers->toBeArray();
});

it('works with objects', function () {
    expect(new HasProperties())
        ->name->toEqual('foo')->not->toEqual('world')
        ->posts->toHaveCount(2)->each(function ($post) {
            $post->is_published->toBeTrue();
        })
        ->posts->sequence(
            function ($post) {
                $post->title->toEqual('Foo');
            },
            function ($post) {
                $post->title->toEqual('Bar');
            },
        );
});

it('works with nested properties', function () {
    expect(new HasProperties())
        ->nested->foo->bar->toBeString()->toEqual('baz')
        ->posts->toBeArray()->toHaveCount(2);
});

it('works with nested properties via dot notation', function () {
    expect(new HasProperties())
        ->nested->toBeArray()
        ->{'nested.foo'}->toBeArray()
        ->{'nested.foo.bar'}->toBeString()->toEqual('baz')
        ->posts->toBeArray()
        ->{'posts.0.title'}->toBeString()->toEqual('Foo')
        ->{'posts.1.is_published'}->toBeTrue();
});

it('works with magic properties via dot notation', function () {
    expect(new HasMagicProperties())
        ->unknown->toBeNull()
        ->nested->toBeArray()
        ->{'nested.foo'}->toBeArray()
        ->{'nested.unknown'}->toBeNull()
        ->{'nested.foo.bar'}->toBeString()->toEqual('baz');
});

it('works with higher order tests')
    ->expect(new HasProperties())
    ->nested->foo->bar->toBeString()->toEqual('baz')
    ->posts->toBeArray()->toHaveCount(2);

class HasProperties
{
    public $name = 'foo';

    public $posts = [
        [
            'is_published' => true,
            'title' => 'Foo',
        ],
        [
            'is_published' => true,
            'title' => 'Bar',
        ],
    ];

    public $nested = [
        'foo' => ['bar' => 'baz'],
    ];
}

class HasMagicProperties
{
    public $data = [
        'one' => 1,
        'two' => 2,
        'nested' => [
            'foo' => ['bar' => 'baz'],
        ],
    ];

    public function __get(string $name)
    {
        if (array_key_exists($name, $this->data)) {
            return $this->data[$name];
        }

        return null;
    }
}
