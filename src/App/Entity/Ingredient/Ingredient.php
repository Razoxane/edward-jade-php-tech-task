<?php

namespace App\Entity\Ingredient;

use \DateTime;
use App\Utils\Validator;
use \InvalidArgumentException;

class Ingredient
{
    /**
     * @var string
     */
    public $title;

    /**
     * @var string A "Y-m-d" formatted value
     */
    public $best_before;

    /**
     * @var string A "Y-m-d" formatted value
     */
    public $use_by;

    /**
     * Constructs a new instance.
     *
     * @param      string  $title        The title
     * @param      string  $best_before  The best before date in Y-m-d format
     * @param      string  $use_by       The use by date in Y-m-d format
     */
    public function __construct(string $title, string $best_before, string $use_by)
    {
        $this->title = $title;
        $this->best_before = Validator::validateDate($best_before, 'best_before');
        $this->use_by = Validator::validateDate($use_by, 'use_by');
    }

    /**
     * Instantiate an instance of the class from a plain object, in the same format
     * as the Ingredient/data.json values.
     *
     * @param      object                     $object
     *
     * @throws     InvalidArgumentException
     *
     * @return     self                       An instance of the Ingredient
     */
    public static function fromObject(object $object)
    {
        if (false === property_exists($object, 'title')) {
            throw new InvalidArgumentException('title missing');
        }

        if (false === property_exists($object, 'best-before')) {
            throw new InvalidArgumentException('best-before missing');
        }

        if (false === property_exists($object, 'use-by')) {
            throw new InvalidArgumentException('use-by missing');
        }

        return new self($object->title, $object->{'best-before'}, $object->{'use-by'});
    }

    /**
     * Determines whether the specified date is within use by date of the Ingredient.
     *
     * @param      DateTime|int  $date   The date
     *
     * @return     bool          True if the specified date is within use by date, False otherwise.
     */
    public function isWithinUseByDate(DateTime $date)
    {
        $use_by = DateTime::createFromFormat('Y-m-d', $this->use_by);

        if ($date > $use_by) {
            return false;
        }

        return true;
    }

    /**
     * Determines whether the specified date is within best before date.
     *
     * @param      DateTime|int  $date   The date
     *
     * @return     bool          True if the specified date is within best before date, False otherwise.
     */
    public function isWithinBestBeforeDate(DateTime $date)
    {
        $best_before = DateTime::createFromFormat('Y-m-d', $this->best_before);

        if ($date > $best_before) {
            return false;
        }

        return true;
    }
}
