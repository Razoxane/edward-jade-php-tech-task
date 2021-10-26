<?php
namespace App\Tests\Utils;

use App\Utils\Validator;
use PHPUnit\Framework\TestCase;

class ValidatorTest extends TestCase
{
    public function testValidateDate()
    {
        $date = '2021-10-10';

        $validDate = Validator::validateDate($date, 'name');

        $this->assertEquals($date, $validDate);
    }

    public function testValidateDateThrowsExceptionWhenDateFormatWrong()
    {
        $this->expectException('InvalidArgumentException');
        $this->expectExceptionMessage('Value provided for invalidFormat is an invalid format. YYYY-MM-DD required.');

        $date = '20211010';

        Validator::validateDate($date, 'invalidFormat');
    }

    public function testValidateDateThrowsExceptionWhenDateValueWrong()
    {
        $this->expectException('InvalidArgumentException');
        $this->expectExceptionMessage('Value provided for invalidValue is an invalid value. Valid date in the format of YYYY-MM-DD required.');

        $date = '2021-10-99';

        Validator::validateDate($date, 'invalidValue');
    }
}
