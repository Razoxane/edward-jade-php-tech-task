<?php

namespace App\Controller;

use \DateTime;
use App\Utils\Validator;
use App\Entity\Fridge\Fridge;
use App\Utils\FileSystemDataLoader;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class LunchApiController extends AbstractController
{
    /**
     * Returns list of optionally filtered recipes.
     *
     * Request object may have optional query string parameter 'date' to filter
     * the recipes by the freshness of the available ingredients.
     *
     * Default is to use today as the date, which may result in no results being
     * returned if there are no fresh ingredients.
     *
     * @param   Request $request
     * @return  JsonResponse
     */
    public function list(Request $request)
    {
        $raw_date = $request->query->filter(
            'date',
            date('Y-m-d'),
            FILTER_SANITIZE_NUMBER_INT
        );

        $date = DateTime::createFromFormat('Y-m-d', Validator::validateDate($raw_date, 'date'));

        $loader = new FileSystemDataLoader();

        $fridge = new Fridge();

        $eligible_recipes = $fridge
            ->loadIngredients($loader)
            ->loadRecipes($loader)
            ->assembleLunches()
            ->getRecipesByDate($date);

        return $this->json($eligible_recipes->getValues());
    }
}
