<?php

namespace App\Entity\Lunch;

use App\Entity\Recipe\Recipe;
use App\Entity\Ingredient\Ingredient;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\ArrayCollection;

class Lunch
{
    /**
    * @var object The recipe
    */
    public $recipe;

    /**
     * @var ArrayCollection An array of ingredient objects
     */
    public $ingredients;

    /**
     * @var string A "Y-m-d" formatted value
     */
    public $use_by;

    /**
     * Constructs a new instance.
     *
     * @param      \App\Entity\Recipe\Recipe                     $recipe       The recipe
     * @param      \Doctrine\Common\Collections\ArrayCollection  $ingredients  The ingredients
     */
    public function __construct(Recipe $recipe, ArrayCollection $ingredients)
    {
        $this->recipe = $recipe;
        $this->ingredients = $ingredients;
        $this->use_by = $this->getLeastFreshIngredient()->use_by ?? null;
    }

    /**
     * Check if the lunch contains the named ingredients
     *
     * @param      array  $available_ingredient_names  The available ingredient names
     *
     * @return     bool   True if the ingredients exist, false if not.
     */
    public function containsIngredients(array $available_ingredient_names): bool
    {
        $pack_ingredient_names = $this->getIngredientNames($this->ingredients);

        $matching_ingredients = array_intersect($pack_ingredient_names, $available_ingredient_names);

        if (count($pack_ingredient_names) === count($matching_ingredients)) {
            return true;
        }

        return false;
    }

    /**
     * Gets the ingredient names from the ArrayCollection
     *
     * @param      \Doctrine\Common\Collections\ArrayCollection  $ingredients  The ingredients
     *
     * @return     array  The ingredient names in a simple array
     */
    private function getIngredientNames(ArrayCollection $ingredients = null): array
    {
        $ingredients = $ingredients ?? $this->ingredients;

        $ingredient_names = $ingredients->map(function (Ingredient $ingredient) {
            return $ingredient->title;
        })->toArray();

        return $ingredient_names;
    }

    /**
     * Gets the least fresh ingredient, ordering by best-before and use-by dates.
     *
     * @return     App\Entity\Ingredient\Ingredient|null  The least fresh ingredient.
     */
    public function getLeastFreshIngredient(): ?object
    {
        $criteria = new Criteria();
        $criteria->orderBy(['best_before' => Criteria::ASC]);
        $criteria->orderBy(['use_by' => Criteria::ASC]);

        $least_fresh_ingredient = $this->ingredients->matching($criteria)->first();

        if (false === $least_fresh_ingredient) {
            return null;
        }

        return $least_fresh_ingredient;
    }
}
