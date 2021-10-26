<?php

namespace App\Tests\Entity;

use App\Entity\Lunch\Lunch;
use App\Entity\Recipe\Recipe;
use PHPUnit\Framework\TestCase;
use App\Entity\Ingredient\Ingredient;
use Doctrine\Common\Collections\ArrayCollection;

class LunchTest extends TestCase
{
    public function testConstructor()
    {
        $recipe = new Recipe('recipeTitle', ['ingredient1', 'ingredient2']);

        $ingredient1 = new Ingredient('ingredient1', '2021-10-01', '2021-10-11');
        $ingredient2 = new Ingredient('ingredient2', '2021-10-02', '2021-10-12');

        $ingredients = new ArrayCollection([
            $ingredient1,
            $ingredient2,
        ]);

        $lunch = new Lunch($recipe, $ingredients);

        $this->assertInstanceOf(Lunch::class, $lunch);
        $this->assertEquals('recipeTitle', $lunch->recipe->title);
        $this->assertEquals($ingredient2, $lunch->ingredients->last());
    }

    public function testContainsIngredients()
    {
        $recipe = new Recipe('recipeTitle', ['ingredient1', 'ingredient2']);

        $ingredient1 = new Ingredient('ingredient1', '2021-10-01', '2021-10-11');
        $ingredient2 = new Ingredient('ingredient2', '2021-10-02', '2021-10-12');

        $ingredients = new ArrayCollection([
            $ingredient1,
            $ingredient2,
        ]);

        $lunch = new Lunch($recipe, $ingredients);

        $ingredient_names = ['ingredient1', 'ingredient2'];

        $this->assertTrue($lunch->containsIngredients($ingredient_names));
    }

    public function testDoesntContainsIngredients()
    {
        $recipe = new Recipe('recipeTitle', ['ingredient1', 'ingredient2']);

        $ingredient1 = new Ingredient('ingredient1', '2021-10-01', '2021-10-11');
        $ingredient2 = new Ingredient('ingredient2', '2021-10-02', '2021-10-12');

        $ingredients = new ArrayCollection([
            $ingredient1,
            $ingredient2,
        ]);

        $lunch = new Lunch($recipe, $ingredients);

        $ingredient_names = ['ingredient3', 'ingredient4'];

        $this->assertFalse($lunch->containsIngredients($ingredient_names));
    }

    public function testGetLeastFreshIngredient()
    {
        $recipe = new Recipe('recipeTitle', ['ingredient1', 'ingredient2']);

        $ingredient1 = new Ingredient('ingredient1', '2021-10-01', '2021-10-11');
        $ingredient2 = new Ingredient('ingredient2', '2021-10-02', '2021-10-12');

        $ingredients = new ArrayCollection([
            $ingredient1,
            $ingredient2,
        ]);

        $lunch = new Lunch($recipe, $ingredients);

        $least_fresh_ingredient = $lunch->getLeastFreshIngredient();

        $this->assertEquals($ingredient1, $least_fresh_ingredient);
    }

    public function testGetLeastFreshIngredientReturnsNullIfNoIngredients()
    {
        $recipe = new Recipe('recipeTitle', ['ingredient1', 'ingredient2']);

        $ingredient1 = new Ingredient('ingredient1', '2021-10-01', '2021-10-11');
        $ingredient2 = new Ingredient('ingredient2', '2021-10-02', '2021-10-12');

        $ingredients = new ArrayCollection([
            $ingredient1,
            $ingredient2,
        ]);

        $lunch = new Lunch($recipe, new ArrayCollection([]));

        $least_fresh_ingredient = $lunch->getLeastFreshIngredient();

        $this->assertEquals(null, $least_fresh_ingredient);
    }
}
