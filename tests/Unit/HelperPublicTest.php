<?php

namespace Tests\Unit;

use App\Helpers\HelperPublic;
use PHPUnit\Framework\TestCase;

class HelperPublicTest extends TestCase
{
    public function testHelpNumeric()
    {
        HelperPublic::helpNumeric(random_int(1, 10));
        HelperPublic::helpNumeric('a');
        $this->assertTrue(true);
    }
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function testHelpRoman()
    {
        HelperPublic::helpRoman(random_int(1, 10));
        HelperPublic::helpRoman(random_int(40, 100));
        HelperPublic::helpRoman(random_int(400, 1000));
        $this->assertTrue(true);
    }

    public function testHelpSecSql()
    {
        HelperPublic::helpSecSql(" or 1=1 or 1=''");
        $this->assertTrue(true);
    }

    public function testHelpTerbilang()
    {
        HelperPublic::helpTerbilang(random_int(1, 1000000000000000));
        HelperPublic::helpTerbilang(19);
        HelperPublic::helpTerbilang(199);
        HelperPublic::helpTerbilang(1999);
        $this->assertTrue(true);
    }

    public function testHelpResponse()
    {
        $responseCode = [
            200,201,202,204,304,400,401,403,404,405,414,500,502,503,504,505
        ];

        for ($i=0; $i<count($responseCode); $i++) {
            HelperPublic::helpResponse($responseCode[$i]);
        }

        $this->assertTrue(true);
    }

    public function testHelpRandomString()
    {
        HelperPublic::helpRandomString();
        HelperPublic::helpRandomString(10, true);

        $this->assertTrue(true);
    }

    public function testHelpCurrency()
    {
        HelperPublic::helpCurrency(1000000000);
        HelperPublic::helpCurrency('');
        HelperPublic::helpCurrency(1000.67, '', '.', false);

        $this->assertTrue(true);
    }

    public function testHelpToNum()
    {
        HelperPublic::helpToNum('seratus');

        $this->assertTrue(true);
    }

    public function testToAlpha()
    {
        HelperPublic::toAlpha(random_int(1, 25));
        HelperPublic::toAlpha(random_int(26, 100));

        $this->assertTrue(true);
    }

    public function testHelpRename()
    {
        HelperPublic::helpRename('jean doe', 'john doe');

        $this->assertTrue(true);
    }

    public function testHelpText()
    {
        HelperPublic::helpText('jean doe');
        HelperPublic::helpText('jean doe', true, true);

        $this->assertTrue(true);
    }
}
