<?php
namespace App\Utils;

use App\Entity\Recipe\Recipe;
use \InvalidArgumentException;
use App\Entity\Ingredient\Ingredient;
use Doctrine\Common\Collections\ArrayCollection;

class FileSystemDataLoader implements DataLoaderInterface
{
    /**
     * Gets the ingredients.
     *
     * @param      string  $source  The source of the ingredient data
     *
     * @return     object  An object containing zero or more ingredients
     */
    public function loadIngredients(string $source): object
    {
        $raw_ingredients = $this->loadJson($source);

        if (false === property_exists($raw_ingredients, 'ingredients')) {
            throw new InvalidArgumentException('ingredients property missing');
        }

        $ingredients = [];

        foreach ($raw_ingredients->ingredients as $key => $properties) {
            $ingredient = Ingredient::fromObject($properties);

            $ingredients[] = $ingredient;
        }

        return new ArrayCollection($ingredients);
    }

    /**
     * Gets the recipes.
     *
     * @param      string  $source  The source of the recipe data
     *
     * @return     object  An object containing zero or more recipes
     */
    public function loadRecipes(string $source) : object
    {
        $raw_recipes = $this->loadJson($source);

        if (false === property_exists($raw_recipes, 'recipes')) {
            throw new InvalidArgumentException('recipes property missing');
        }

        $recipes = [];

        foreach ($raw_recipes->recipes as $key => $properties) {
            $recipe = Recipe::fromObject($properties);

            $recipes[] = $recipe;
        }

        return new ArrayCollection($recipes);
    }

    /**
     * Gets the contents of a file from the passed in path.
     *
     * @param      string       $file_path  The filepath
     *
     * @return     exception|string
     */
    public function loadFile(string $file_path)
    {
        $this->checkFileExists($file_path);
        $data = file_get_contents($file_path);

        return $data;
    }

    /**
     * Loads json from a file path
     *
     * @param      string       $source  The filepath
     *
     * @return     exception|object
     */
    public function loadJson(string $source): object
    {
        $data = $this->loadFile($source);
        $json = $this->parseJson($data);

        return $json;
    }

    /**
     * Parses json from a string.
     *
     * @param      string       $data  The data
     *
     * @return     exception|object
     */
    public function parseJson(string $data): object
    {
        $json = json_decode($data, false, 512, JSON_THROW_ON_ERROR);

        return $json;
    }

    /**
     * Determines if a file exists.
     *
     * @param      string      $file_path  The file path
     *
     * @throws     \Exception  (description)
     */
    private function checkFileExists(string $file_path)
    {
        if (false === file_exists($file_path)) {
            throw new \Exception('File not found');
        }
    }
}
