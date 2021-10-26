<?php

namespace App\Utils;

interface DataLoaderInterface
{
    /**
     * Gets the ingredients.
     *
     * @param      string  $source  The source of the ingredient data
     *
     * @return     object  An object containing zero or more ingredients
     */
    public function loadIngredients(string $source): object;

    /**
     * Gets the recipes.
     *
     * @param      string  $source  The source of the recipe data
     *
     * @return     object  An object containing zero or more recipes
     */
    public function loadRecipes(string $source): object;
}
