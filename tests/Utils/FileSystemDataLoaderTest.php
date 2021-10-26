<?php
namespace App\Tests\Utils;

use App\Entity\Recipe\Recipe;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;
use App\Utils\DataLoaderInterface;
use App\Utils\FileSystemDataLoader;
use App\Entity\Ingredient\Ingredient;
use Doctrine\Common\Collections\ArrayCollection;

class FileSystemDataLoaderTest extends TestCase
{
    private $file_system;

    protected function setUp(): void
    {
        parent::setUp();

        $directory = [
            'json' => [
                'valid.json' => '{"VALID_KEY":123}',
                'invalid.json' => '{"test":123',
                'recipes.json' => '{"recipes": [
                    {
                        "title": "Ham and Cheese Toastie",
                        "ingredients": [
                            "Ham",
                            "Cheese",
                            "Bread",
                            "Butter"
                        ]
                    }, {
                        "title": "Fry-up",
                        "ingredients": [
                            "Bacon",
                            "Eggs",
                            "Baked Beans",
                            "Mushrooms",
                            "Sausage",
                            "Bread"
                        ]
                    }]
                }',
                'ingredients.json' => '{
                    "ingredients": [{
                        "title": "Ham",
                        "best-before": "2019-03-25",
                        "use-by": "2019-03-27"
                    }, {
                        "title": "Cheese",
                        "best-before": "2019-03-08",
                        "use-by": "2019-03-13"
                    }, {
                        "title": "Bread",
                        "best-before": "2019-03-25",
                        "use-by": "2019-03-27"
                    }]
                }',
            ],
        ];

        // setup and cache the virtual file system
        $this->file_system = vfsStream::setup('root', 444, $directory);
    }

    protected function tearDown(): void
    {
        unset($this->file_system);
    }

    public function testIsAnInstanceOfADataLoaderInterface()
    {
        $loader = new FileSystemDataLoader();

        $this->assertInstanceOf(DataLoaderInterface::class, $loader);
        $this->assertInstanceOf(FileSystemDataLoader::class, $loader);
    }

    public function testLoadFile()
    {
        $loader = new FileSystemDataLoader();

        $fileContents = $loader->loadFile($this->file_system->url() . '/json/valid.json');

        $this->assertEquals('{"VALID_KEY":123}', $fileContents);
    }

    public function testLoadFileFails()
    {
        $this->expectException('Exception');

        $loader = new FileSystemDataLoader();

        $loader->loadFile($this->file_system->url() . '/no-file.json');
    }

    public function testParseJson()
    {
        $loader = new FileSystemDataLoader();

        $jsonString = json_encode([
            'first' => 'value',
            'second' => [
                'third',
                'fourth',
            ],
        ]);

        $json = $loader->parseJson($jsonString);

        $this->assertEquals('object', gettype($json));
    }

    public function testParseJsonFails()
    {
        $this->expectException('JsonException');

        $loader = new FileSystemDataLoader();

        $invalidString = '';

        $json = $loader->parseJson($invalidString);
    }

    public function testLoadIngredients()
    {
        $loader = new FileSystemDataLoader();

        $ingredients = $loader->loadIngredients($this->file_system->url() . '/json/ingredients.json');

        $this->assertInstanceOf(ArrayCollection::class, $ingredients);
        $this->assertInstanceOf(Ingredient::class, $ingredients->first());
        $this->assertEquals('Ham', $ingredients->first()->title);
    }


    public function testLoadIngredientsFailsWhenIncorrectlyStructuredFileProvided()
    {
        $this->expectException('InvalidArgumentException');
        $this->expectExceptionMessage('ingredients property missing');

        $loader = new FileSystemDataLoader();

        $loader->loadIngredients($this->file_system->url() . '/json/recipes.json');
    }

    public function testLoadRecipes()
    {
        $loader = new FileSystemDataLoader();

        $recipes = $loader->loadRecipes($this->file_system->url() . '/json/recipes.json');

        $this->assertInstanceOf(ArrayCollection::class, $recipes);
        $this->assertInstanceOf(Recipe::class, $recipes->first());
        $this->assertEquals('Ham and Cheese Toastie', $recipes->first()->title);
    }

    public function testLoadRecipesFailsWhenIncorrectlyStructuredFileProvided()
    {
        $this->expectException('InvalidArgumentException');
        $this->expectExceptionMessage('recipes property missing');

        $loader = new FileSystemDataLoader();

        $loader->loadRecipes($this->file_system->url() . '/json/ingredients.json');
    }
}
