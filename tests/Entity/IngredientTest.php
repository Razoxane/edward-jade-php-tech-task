<?php

namespace App\Tests\Entity;

use \DateTime;
use PHPUnit\Framework\TestCase;
use App\Entity\Ingredient\Ingredient;

class IngredientTest extends TestCase
{
    public function testConstructor()
    {
        $ingredient = new Ingredient('ingredientTitle', '2021-10-01', '2021-10-05');

        $this->assertInstanceOf(Ingredient::class, $ingredient);
        $this->assertEquals('ingredientTitle', $ingredient->title);
        $this->assertEquals('2021-10-01', $ingredient->best_before);
        $this->assertEquals('2021-10-05', $ingredient->use_by);
    }

    public function testConstructorFailsWhenBestBeforeValueNotADate()
    {
        $this->expectException('InvalidArgumentException');
        $this->expectExceptionMessage('Value provided for best_before is an invalid format. YYYY-MM-DD required.');

        $ingredient = new Ingredient('ingredientTitle', 'notADate', '2021-10-05');
    }

    public function testConstructorFailsWhenBestBeforeValueNotValidDate()
    {
        $this->expectException('InvalidArgumentException');
        $this->expectExceptionMessage('Value provided for best_before is an invalid value. Valid date in the format of YYYY-MM-DD required.');

        $ingredient = new Ingredient('ingredientTitle', '2021-23-99', '2021-10-05');
    }

    public function testConstructorFailsWhenUseByValueNotADate()
    {
        $this->expectException('InvalidArgumentException');
        $this->expectExceptionMessage('Value provided for use_by is an invalid format. YYYY-MM-DD required.');

        $ingredient = new Ingredient('ingredientTitle', '2021-10-05', 'notADate');
    }

    public function testConstructorFailsWhenUseByValueNotValidDate()
    {
        $this->expectException('InvalidArgumentException');
        $this->expectExceptionMessage('Value provided for use_by is an invalid value. Valid date in the format of YYYY-MM-DD required.');

        $ingredient = new Ingredient('ingredientTitle', '2021-10-05', '2021-23-99');
    }

    public function testFromObject()
    {
        $plain_object = (object) [
            'title' => 'ingredientTitle',
            'best-before' => '2021-10-01',
            'use-by' => '2021-10-05',
        ];

        $ingredient = Ingredient::fromObject($plain_object);

        $this->assertInstanceOf(Ingredient::class, $ingredient);
        $this->assertEquals('ingredientTitle', $ingredient->title);
        $this->assertEquals('2021-10-01', $ingredient->best_before);
        $this->assertEquals('2021-10-05', $ingredient->use_by);
    }

    public function testFromObjectFailsWhenTitleWrong()
    {
        $this->expectException('InvalidArgumentException');
        $this->expectExceptionMessage('title missing');

        $plain_object = (object) [
            'xtitle' => 'ingredientTitle',
            'best-before' => '2021-10-01',
            'use-by' => '2021-10-05',
        ];

        $ingredient = Ingredient::fromObject($plain_object);
    }

    public function testFromObjectFailsWhenBestBeforeKeyWrong()
    {
        $this->expectException('InvalidArgumentException');
        $this->expectExceptionMessage('best-before missing');

        $plain_object = (object) [
            'title' => 'ingredientTitle',
            'xbest-before' => '2021-10-01',
            'use-by' => '2021-10-05',
        ];

        $ingredient = Ingredient::fromObject($plain_object);
    }

    public function testFromObjectFailsWhenUseByKeyWrong()
    {
        $this->expectException('InvalidArgumentException');
        $this->expectExceptionMessage('use-by missing');

        $plain_object = (object) [
            'title' => 'ingredientTitle',
            'best-before' => '2021-10-01',
            'xuse-by' => '2021-10-05',
        ];

        $ingredient = Ingredient::fromObject($plain_object);
    }

    public function testBeforeBestBeforeDate()
    {
        $ingredient = new Ingredient('title', '2021-10-10', '2021-10-15');
        $date = DateTime::createFromFormat('Y-m-d', '2021-10-05');

        $this->assertTrue($ingredient->isWithinBestBeforeDate($date));
    }

    public function testOnBestBeforeDate()
    {
        $ingredient = new Ingredient('title', '2021-10-10', '2021-10-15');
        $date = DateTime::createFromFormat('Y-m-d', '2021-10-10');

        $this->assertTrue($ingredient->isWithinBestBeforeDate($date));
    }

    public function testAfterBestBeforeDate()
    {
        $ingredient = new Ingredient('title', '2021-10-10', '2021-10-15');
        $date = DateTime::createFromFormat('Y-m-d', '2021-10-20');

        $this->assertFalse($ingredient->isWithinBestBeforeDate($date));
    }

    public function testBeforeUseByDate()
    {
        $ingredient = new Ingredient('title', '2021-10-10', '2021-10-15');
        $date = DateTime::createFromFormat('Y-m-d', '2021-10-11');

        $this->assertTrue($ingredient->isWithinUseByDate($date));
    }

    public function testOnUseByDate()
    {
        $ingredient = new Ingredient('title', '2021-10-10', '2021-10-15');
        $date = DateTime::createFromFormat('Y-m-d', '2021-10-15');

        $this->assertTrue($ingredient->isWithinUseByDate($date));
    }

    public function testAfterUseByDate()
    {
        $ingredient = new Ingredient('title', '2021-10-10', '2021-10-15');
        $date = DateTime::createFromFormat('Y-m-d', '2021-10-20');

        $this->assertFalse($ingredient->isWithinUseByDate($date));
    }
}
