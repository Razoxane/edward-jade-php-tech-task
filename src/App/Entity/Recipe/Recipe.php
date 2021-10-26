<?php

namespace App\Entity\Recipe;

use \TypeError;
use \InvalidArgumentException;

class Recipe
{
    /**
     * @var string The name of the recipe
     */
    public $title;

    /**
     * @var array An array of ingredient names
     */
    public $ingredients;

    /**
     * Constructs a new instance.
     *
     * @param      string  $title        The title
     * @param      Array   $ingredients  The ingredients
     */
    public function __construct(string $title, array $ingredients = null)
    {
        $this->title = $title;
        $this->ingredients = $ingredients;
    }

    /**
     * Instantiate an instance of the class from a plain object, in the same format
     * as the Recipe/data.json values.
     *
     * @param      object                    $object  The object
     *
     * @throws     InvalidArgumentException  when a value is missing
     * @throws     TypeError                 when the ingredients value is not an array
     *
     * @return     object|self               A Recipe object
     */
    public static function fromObject(object $object): object
    {
        if (false === property_exists($object, 'title')) {
            throw new InvalidArgumentException('title missing');
        }

        if (false === property_exists($object, 'ingredients')) {
            throw new InvalidArgumentException('ingredients missing');
        }

        if (false === is_array($object->ingredients)) {
            throw new TypeError('ingredients not an array');
        }

        return new self($object->title, $object->ingredients);
    }

    /**
     * Check if the recipe contains the named ingredients
     *
     * @param      array  $available_ingredient_names  The available ingredient names
     *
     * @return     bool   True if the ingredients exist, false if not.
     */
    public function containsIngredients(array $ingredient_names): bool
    {
        $matching_ingredients = array_intersect($ingredient_names, $this->ingredients);

        if (count($this->ingredients) === count($matching_ingredients)) {
            return true;
        }

        return false;
    }
}
