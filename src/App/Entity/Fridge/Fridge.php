<?php
namespace App\Entity\Fridge;

use \DateTime;
use \InvalidArgumentException;
use App\Entity\Lunch\Lunch;
use App\Utils\DataLoaderInterface;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\ArrayCollection;

class Fridge
{
    /**
     * @var object An ArrayCollection of Ingredient objects
     */
    public $ingredients;

    /**
     * @var object An ArrayCollection of Lunch objects
     */
    public $lunches;

    /**
     * @var object An ArrayCollection of Recipe objects
     */
    public $recipes;

    /**
     * Constructs a new instance.
     */
    public function __construct(ArrayCollection $ingredients = null, ArrayCollection $recipes = null)
    {
        $this->ingredients = $ingredients;
        $this->recipes = $recipes;
    }

    /**
     * Assembles a collection of Lunches from either the provided collections of
     * recipes and ingredients, or from the recipes and ingredients the Fridge
     * already has initialised.
     *
     * @param      \Doctrine\Common\Collections\ArrayCollection         $recipes      The recipes
     * @param      \Doctrine\Common\Collections\ArrayCollection         $ingredients  The ingredients
     *
     * @return     \Doctrine\Common\Collections\ArrayCollection|object  Collection of Lunches
     */
    public function assembleLunches(ArrayCollection $recipes = null, ArrayCollection $ingredients = null): object
    {
        $ingredients = $ingredients ?? $this->ingredients;

        $recipes = $recipes ?? $this->recipes;

        if (true === is_null($recipes)) {
            throw new InvalidArgumentException("Recipes required to assemble lunches");
        }

        if (true === is_null($ingredients)) {
            throw new InvalidArgumentException("Ingredients required to assemble lunches");
        }

        $this->lunches = $recipes->map(function ($recipe) use ($ingredients) {
            $expression_builder = Criteria::expr();
            $expression = $expression_builder->in('title', $recipe->ingredients);
            $eligible_ingredients = $ingredients->matching(new Criteria($expression));

            return new Lunch($recipe, $eligible_ingredients);
        });

        return $this;
    }

    /**
     * Loads the ingredients from the JSON file
     *
     * @param      string  $file_path  The file path
     *
     * @return     object  A Fridge with ingredients populated.
     */
    public function loadIngredients(DataLoaderInterface $loader, string $file_path = null): object
    {
        $file_path = $file_path ??  __DIR__ . '/../Ingredient/data.json';

        $this->ingredients = $loader->loadIngredients($file_path);

        return $this;
    }

    /**
     * Gets the ingredient names from a collection of Ingredient objects
     *
     * @param      \Doctrine\Common\Collections\ArrayCollection        $ingredients  The ingredients
     *
     * @return     \Doctrine\Common\Collections\ArrayCollection|array  The ingredient names.
     */
    private function getIngredientNames(ArrayCollection $ingredients = null): array
    {
        $ingredients = $ingredients ?? $this->ingredients;

        $ingredient_names = $ingredients->map(function ($ingredient) {
            return $ingredient->title;
        })->toArray();

        return $ingredient_names;
    }

    /**
     * Loads the recipes.from the JSON file
     *
     * @param      string  $file_path  The file path
     *
     * @return     object  A Fridge with recipes populated.
     */
    public function loadRecipes(DataLoaderInterface $loader, string $file_path = null): object
    {
        $file_path = $file_path = $file_path ?? __DIR__ . '/../Recipe/data.json';

        $this->recipes = $loader->loadRecipes($file_path);

        return $this;
    }

    /**
     * Returns only those Ingredients in the Fridge which are within their use by date.
     *
     * @param      DateTime  $date   The date
     *
     * @return     object    ( description_of_the_return_value )
     */
    public function filterIngredientsByDate(DateTime $date = null): object
    {
        $eligible_ingredients = $this->ingredients->filter(function ($ingredient) use ($date) {
            return $ingredient->isWithinUseByDate($date);
        });

        return $eligible_ingredients;
    }

    /**
     * Returns only those Recipes which contain the specified Ingredients.
     * Accepts a collection of ingredients, or falls back to the ingredients already in the Fridge.
     *
     * @param      \Doctrine\Common\Collections\ArrayCollection  $ingredients  The ingredients
     *
     * @return     \Doctrine\Common\Collections\ArrayCollection The recipes that can be made
     */
    public function filterRecipesByIngredients(ArrayCollection $ingredients = null): object
    {
        $ingredients = $ingredients ?? $this->ingredients;

        $ingredient_names = $this->getIngredientNames($ingredients);

        $eligible_recipes = $this->recipes->filter(function ($recipe) use ($ingredient_names) {
            return $recipe->containsIngredients($ingredient_names);
        });

        return $eligible_recipes;
    }

    /**
     * Returns only those Lunches which contain specified Ingredients.
     * Accepts a collection of ingredients, or falls back to the ingredients already in the Fridge.
     *
     * @param      \Doctrine\Common\Collections\ArrayCollection  $ingredients  The ingredients
     *
     * @return     \Doctrine\Common\Collections\ArrayCollection The in date lunches
     */
    public function filterLunchesByIngredients(ArrayCollection $ingredients = null): object
    {
        $ingredients = $ingredients ?? $this->ingredients;

        $ingredient_names = $this->getIngredientNames($ingredients);

        $eligible_lunches = $this->lunches->filter(function ($lunch) use ($ingredient_names) {
            return $lunch->containsIngredients($ingredient_names);
        });

        return $eligible_lunches;
    }

    /**
     * Returns a Collection of ingredients.
     * Sorted by best before and use by dates, least fresh at the top, freshest at the bottom.
     *
     * @param      \Doctrine\Common\Collections\ArrayCollection         $ingredients  The ingredients
     *
     * @return     \Doctrine\Common\Collections\ArrayCollection|object  The sorted ingredients
     */
    public function sortIngredientsByFreshness(ArrayCollection $ingredients = null): object
    {
        $ingredients = $ingredients ?? $this->ingredients;

        $criteria = new Criteria();
        $criteria->orderBy(['best_before' => Criteria::DESC]);
        $criteria->orderBy(['use_by' => Criteria::DESC]);

        $sorted_ingredients = $ingredients->matching($criteria);

        return $sorted_ingredients;
    }

    /**
     * Returns a Collection of Lunches.
     * Sorted by best before and use by dates, freshest at the top, least fresh at the bottom.
     *
     * @param      \Doctrine\Common\Collections\ArrayCollection         $lunches  The lunches
     *
     * @return     \Doctrine\Common\Collections\ArrayCollection|object  The sorted lunches
     */
    public function sortLunchesByFreshness(ArrayCollection $lunches = null): object
    {
        $lunches = $lunches ?? $this->lunches;

        $criteria = new Criteria();
        $criteria->orderBy(['use_by' => Criteria::DESC]);

        $sorted_lunches = $lunches->matching($criteria);

        return $sorted_lunches;
    }

    /**
     * Gets the Lunches which are in date, sorted by freshness descending.
     *
     * @param      DateTime  $date   The date
     *
     * @return     Doctrine\Common\Collections\ArrayCollection|object    The lunches ordered by freshness descending.
     */
    public function getLunchesByDate(DateTime $date = null): object
    {
        $eligible_ingredients = $this->filterIngredientsByDate($date);

        $eligible_lunches = $this->filterLunchesByIngredients($eligible_ingredients);

        $sorted_lunches = $this->sortLunchesByFreshness($eligible_lunches);

        return $sorted_lunches;
    }

    /**
     * Gets the recipes which use ingredients that are in date.
     *
     * @param      DateTime  $date   The date
     *
     * @return     Doctrine\Common\Collections\ArrayCollection|object    The recipes sorted by freshness descending.
     */
    public function getRecipesByDate(DateTime $date = null): object
    {
        $eligible_lunches = $this->getLunchesByDate($date);

        $eligible_recipes = $eligible_lunches->map(function ($lunch) {
            return $lunch->recipe;
        });

        return $eligible_recipes;
    }
}
