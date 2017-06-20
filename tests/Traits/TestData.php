<?php

/** Created by PhpStorm,  User: jonphipps,  Date: 2017-06-10,  Time: 10:37 AM */

namespace Tests\Traits;

trait TestData
{
    public static function getTestData()
    {
        $testData                                = [];
        $testData['project']['adminId']          = 422;
        $testData['project']['id']               = 177;
        $testData['project']['title']            = 'ALA Publishing';
        $testData['vocabulary']['id']            = 437;
        $testData['vocabulary']['title']         = 'RDA Material';
        $testData['vocabulary']['importId']      = 866;
        $testData['vocabulary']['exportId']      = 625;
        $testData['vocabulary']['maintainerId']  = 500;
        $testData['concept']['id']               = 8455;
        $testData['concept']['preferredLabelId'] = 33545;
        $testData['concept']['title']            = 'acetate';
        $testData['elementSet']['id']            = 83;
        $testData['elementSet']['title']         = 'RDA Classes';
        $testData['elementSet']['importId']      = 1053;
        $testData['import']['validSpreadsheetUri']    ='https://docs.google.com/spreadsheets/d/1WTxiOvHHUurz76NZ0WU_2GjjY4SG8Gzbg0vH8xwNz_I/edit#gid=0';

        return $testData;
    }
}
