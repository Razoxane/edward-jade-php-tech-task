<?php

namespace App\Tests\Entity;

use \DateTime;
use App\Entity\Lunch\Lunch;
use App\Entity\Recipe\Recipe;
use org\bovigo\vfs\vfsStream;
use App\Entity\Fridge\Fridge;
use PHPUnit\Framework\TestCase;
use App\Utils\FileSystemDataLoader;
use App\Entity\Ingredient\Ingredient;
use Doctrine\Common\Collections\ArrayCollection;

class FridgeTest extends TestCase
{
    private $file_system;

    protected function setUp(): void
    {
        parent::setUp();

        $directory = [
            'json' => [
                'recipes.json' => '{
                    "recipes": [{
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
                    }, {
                        "title": "Salad",
                        "ingredients": [
                            "Lettuce",
                            "Tomato",
                            "Cucumber",
                            "Beetroot",
                            "Salad Dressing"
                        ]
                    }, {
                        "title": "Hotdog",
                        "ingredients": [
                            "Hotdog Bun",
                            "Sausage",
                            "Ketchup",
                            "Mustard"
                        ]
                    }]
                }',
                'recipes2.json' => '{
                    "recipes": [{
                        "title": "Vegemite and Cheese Toastie",
                        "ingredients": [
                            "Vegemite",
                            "Cheese",
                            "Bread",
                            "Butter"
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
                    }, {
                        "title": "Butter",
                        "best-before": "2019-03-25",
                        "use-by": "2019-03-27"
                    }, {
                        "title": "Bacon",
                        "best-before": "2019-03-25",
                        "use-by": "2019-03-27"
                    }, {
                        "title": "Eggs",
                        "best-before": "2019-03-25",
                        "use-by": "2019-03-27"
                    }, {
                        "title": "Mushrooms",
                        "best-before": "2019-03-25",
                        "use-by": "2019-03-27"
                    }, {
                        "title": "Sausage",
                        "best-before": "2019-03-25",
                        "use-by": "2019-03-27"
                    }, {
                        "title": "Hotdog Bun",
                        "best-before": "2019-03-25",
                        "use-by": "2019-03-27"
                    }, {
                        "title": "Ketchup",
                        "best-before": "2019-03-25",
                        "use-by": "2019-03-27"
                    }, {
                        "title": "Mustard",
                        "best-before": "2019-03-25",
                        "use-by": "2019-03-27"
                    }, {
                        "title": "Lettuce",
                        "best-before": "2019-03-25",
                        "use-by": "2019-03-27"
                    }, {
                        "title": "Tomato",
                        "best-before": "2019-03-25",
                        "use-by": "2019-03-27"
                    }, {
                        "title": "Cucumber",
                        "best-before": "2019-03-25",
                        "use-by": "2019-03-27"
                    }, {
                        "title": "Beetroot",
                        "best-before": "2019-03-25",
                        "use-by": "2019-03-27"
                    }, {
                        "title": "Salad Dressing",
                        "best-before": "2019-03-06",
                        "use-by": "2019-03-07"
                    }]
                }',
                'ingredients2.json' => '{
                    "ingredients": [{
                        "title": "Vegemite",
                        "best-before": "2019-03-25",
                        "use-by": "2019-03-27"
                    }]
                }',
            ],
        ];

        // setup and cache the virtual file system
        $this->file_system = vfsStream::setup('root', 444, $directory);

        $this->loader = new FileSystemDataLoader;
    }


    public function testConstructor()
    {
        $fridge = new Fridge();

        $this->assertInstanceOf(Fridge::class, $fridge);
        $this->assertEquals(null, $fridge->recipes);
        $this->assertEquals(null, $fridge->ingredients);
    }

    public function testConstructorWithProvidedArguments()
    {
        $recipes = new ArrayCollection([
            new Recipe('Recipe Title', ['ingredient1',]),
            new Recipe(
                'title',
                [
                    'Ingredient 1',
                    'Ingredient 2',
                ]
            ),
        ]);

        $ingredients = new ArrayCollection([
            new Ingredient('Ingredient 1', '2021-10-01', '2021-10-10'),
            new Ingredient('Ingredient 2', '2021-10-02', '2021-10-11'),
        ]);

        $fridge = new Fridge($ingredients, $recipes);

        $this->assertInstanceOf(Fridge::class, $fridge);
        $this->assertEquals('Recipe Title', $fridge->recipes->first()->title);
        $this->assertEquals('Ingredient 1', $fridge->ingredients->first()->title);
    }

    public function testLoadIngredients()
    {
        $fridge = new Fridge();

        $fridge->loadIngredients($this->loader);

        $this->assertInstanceOf(Fridge::class, $fridge);
        $this->assertInstanceOf(ArrayCollection::class, $fridge->ingredients);
        $this->assertInstanceOf(Ingredient::class, $fridge->ingredients->first());
        $this->assertEquals('Ham', $fridge->ingredients->first()->title);
    }

    public function testLoadIngredientsWithASpecificPath()
    {
        $fridge = new Fridge();

        $fridge->loadIngredients($this->loader, $this->file_system->url() . '/json/ingredients2.json');

        $this->assertInstanceOf(Fridge::class, $fridge);
        $this->assertInstanceOf(ArrayCollection::class, $fridge->ingredients);
        $this->assertInstanceOf(Ingredient::class, $fridge->ingredients->first());
        $this->assertEquals('Vegemite', $fridge->ingredients->first()->title);
    }

    public function testLoadRecipes()
    {
        $fridge = new Fridge();

        $fridge->loadRecipes($this->loader);

        $this->assertInstanceOf(Fridge::class, $fridge);
        $this->assertInstanceOf(ArrayCollection::class, $fridge->recipes);
        $this->assertInstanceOf(Recipe::class, $fridge->recipes->first());
        $this->assertEquals('Ham and Cheese Toastie', $fridge->recipes->first()->title);
    }

    public function testLoadRecipesWithASpecificPath()
    {
        $fridge = new Fridge();

        $fridge->loadRecipes($this->loader, $this->file_system->url() . '/json/recipes2.json');

        $this->assertInstanceOf(Fridge::class, $fridge);
        $this->assertInstanceOf(ArrayCollection::class, $fridge->recipes);
        $this->assertInstanceOf(Recipe::class, $fridge->recipes->first());
        $this->assertEquals('Vegemite and Cheese Toastie', $fridge->recipes->first()->title);
    }

    public function testAssembleLunchesWithOwnProperties()
    {
        $fridge = new Fridge();

        $fridge->loadIngredients($this->loader)->loadRecipes($this->loader)->assembleLunches();

        $this->assertInstanceOf(Lunch::class, $fridge->lunches->first());
    }

    public function testAssembleLunchesWithProvidedProperties()
    {
        $fridge = new Fridge();

        $recipes = new ArrayCollection([
            new Recipe('title', ['ingredient1',]),
            new Recipe(
                'title',
                [
                    'ingredient1',
                    'ingredient2',
                    'ingredient2',
                ]
            ),
        ]);

        $ingredients = new ArrayCollection([
            new Ingredient('title', '2021-10-01', '2021-10-10'),
            new Ingredient('title', '2021-10-02', '2021-10-11'),
        ]);

        $packed_fridge = $fridge->assembleLunches($recipes, $ingredients);

        $this->assertInstanceOf(Lunch::class, $packed_fridge->lunches->first());
    }

    public function testAssembleLunchesFailsWithNoIngredients()
    {
        $this->expectException('InvalidArgumentException');
        $this->expectExceptionMessage('Ingredients required to assemble lunches');

        $fridge = new Fridge();

        $packed_fridge = $fridge->loadRecipes($this->loader)->assembleLunches();

        $this->assertInstanceOf(Lunch::class, $packed_fridge->lunches->first());
    }

    public function testAssembleLunchesFailsWithNoRecipes()
    {
        $this->expectException('InvalidArgumentException');
        $this->expectExceptionMessage('Recipes required to assemble lunches');

        $fridge = new Fridge();

        $packed_fridge = $fridge->loadIngredients($this->loader)->assembleLunches();

        $this->assertInstanceOf(Lunch::class, $packed_fridge->lunches->first());
    }

    public function testFilterIngredientsByDate()
    {
        $fridge = new Fridge();

        $fridge->loadIngredients($this->loader);

        $all_ingredients_date = DateTime::createFromFormat('Y-m-d', '2019-03-01');
        $all_ingredients = $fridge->filterIngredientsByDate($all_ingredients_date);
        $this->assertEquals($fridge->ingredients->count(), $all_ingredients->count());

        $filtered_ingredients_date = DateTime::createFromFormat('Y-m-d', '2019-03-27');
        $filtered_ingredients = $fridge->filterIngredientsByDate($filtered_ingredients_date);
        $this->assertEquals(14, $filtered_ingredients->count());

        $no_ingredients_date = DateTime::createFromFormat('Y-m-d', '2019-03-30');
        $no_ingredients = $fridge->filterIngredientsByDate($no_ingredients_date);
        $this->assertEquals(0, $no_ingredients->count());
    }

    public function testFilterRecipesByIngredients()
    {
        $fridge = new Fridge();

        $fridge->loadIngredients($this->loader)->loadRecipes($this->loader);

        $all_ingredients_date = DateTime::createFromFormat('Y-m-d', '2019-03-01');
        $all_ingredients = $fridge->filterIngredientsByDate($all_ingredients_date);
        $all_recipes = $fridge->filterRecipesByIngredients($all_ingredients);
        $this->assertEquals(3, $all_recipes->count());

        $filtered_ingredients_date = DateTime::createFromFormat('Y-m-d', '2019-03-27');
        $filtered_ingredients = $fridge->filterIngredientsByDate($filtered_ingredients_date);
        $filtered_recipes = $fridge->filterRecipesByIngredients($filtered_ingredients);
        $this->assertEquals(1, $filtered_recipes->count());

        $no_ingredients_date = DateTime::createFromFormat('Y-m-d', '2019-03-30');
        $no_ingredients = $fridge->filterIngredientsByDate($no_ingredients_date);
        $no_recipes = $fridge->filterRecipesByIngredients($no_ingredients);
        $this->assertEquals(0, $no_recipes->count());
    }

    public function testSortLunchesByFreshness()
    {
        $fridge = new Fridge();

        $fridge->loadIngredients($this->loader)->loadRecipes($this->loader)->assembleLunches();

        $lunches = $fridge->lunches;

        $original_names = $lunches->map(function ($lunch) {
            return $lunch->recipe->title;
        })->getValues();

        $this->assertEquals(
            [
                'Ham and Cheese Toastie',
                'Fry-up',
                'Salad',
                'Hotdog',
            ],
            $original_names
        );

        $sorted_lunches = $fridge->sortLunchesByFreshness();

        $sorted_names = $sorted_lunches->map(function ($lunch) {
            return $lunch->recipe->title;
        })->getValues();

        $this->assertEquals(
            [
                'Fry-up',
                'Hotdog',
                'Ham and Cheese Toastie',
                'Salad',
            ],
            $sorted_names
        );
    }
}
