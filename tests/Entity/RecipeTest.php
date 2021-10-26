<?php

namespace App\Tests\Entity;

use App\Entity\Recipe\Recipe;
use PHPUnit\Framework\TestCase;

class RecipeTest extends TestCase
{
    public function testConstructor()
    {
        $recipe = new Recipe('title', ['ingredient1', 'ingredient2']);

        $this->assertInstanceOf(Recipe::class, $recipe);
    }

    public function testContainsIngredients()
    {
        $recipe = new Recipe('title', ['ingredient1', 'ingredient2']);

        $this->assertTrue($recipe->containsIngredients(['ingredient1', 'ingredient2']));
    }

    public function testDoesntContainsIngredients()
    {
        $recipe = new Recipe('title', ['ingredient1', 'ingredient2']);

        $this->assertFalse($recipe->containsIngredients(['ingredient3', 'ingredient4']));
    }

    public function testFromObject()
    {
        $plain_object = (object) [
            'title' => 'recipeTitle',
            'ingredients' => [
                'ingredient1',
                'ingredient2',
            ],
        ];

        $recipe = Recipe::fromObject($plain_object);

        $this->assertInstanceOf(Recipe::class, $recipe);
        $this->assertEquals('recipeTitle', $recipe->title);
        $this->assertEquals($plain_object->ingredients, $recipe->ingredients);
    }

    public function testFromObjectFailsWhenTitleWrong()
    {
        $this->expectException('InvalidArgumentException');
        $this->expectExceptionMessage('title missing');

        $plain_object = (object) [
            'xtitle' => 'recipeTitle',
            'ingredients' => [
                'ingredient1',
                'ingredient2',
            ],
        ];

        $recipe = Recipe::fromObject($plain_object);
    }

    public function testFromObjectFailsWhenIngredientsKeyWrong()
    {
        $this->expectException('InvalidArgumentException');
        $this->expectExceptionMessage('ingredients missing');

        $plain_object = (object) [
            'title' => 'recipeTitle',
            'xingredients' => [
                'ingredient1',
                'ingredient2',
            ],
        ];

        $recipe = Recipe::fromObject($plain_object);
    }

    public function testFromObjectFailsWhenIngredientsValueWrong()
    {
        $this->expectException('TypeError');
        $this->expectExceptionMessage('ingredients not an array');

        $plain_object = (object) [
            'title' => 'recipeTitle',
            'ingredients' => 'notAnArray',
        ];

        $recipe = Recipe::fromObject($plain_object);
    }
}
