<?php

/** Created by PhpStorm,  User: jonphipps,  Date: 2017-05-05,  Time: 6:59 PM */

namespace App\Services\Import;


use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class GoogleSpreadsheetTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * @test
     */
    public function it_retrieves_a_set_of_worksheet_titles_from_a_google_spreadsheet()
    {
        //given a new google sheet class
        $sheetUrl = 'https://docs.google.com/spreadsheets/d/1fM26a68SScrDvIJfgtT4QS-Vmhxw_deNhBrPMmBWQkI/edit?usp=sharing';
        $sheet    = new GoogleSpreadsheet($sheetUrl);

        //when I get the worksheets
        $worksheets = $sheet->getWorksheets()->toArray();

        //then worksheets contains the following array
        $expected = [ 'RDACarrierType_en-fr_20170503T162715_483_0', 'RDAMaterial_en-fr_20170503T230849_484_0', 'rdai_en-fr_20170504T170250_542_0' ];
        $this->assertArraySubset($expected, $worksheets);
    }

    /**
     * @test
     */
    public function it_retrieves_an_associative_array_of_data_for_a_worksheet()
    {
        //given a spreadsheet and worksheet title string
        $sheetUrl = 'https://docs.google.com/spreadsheets/d/1WTxiOvHHUurz76NZ0WU_2GjjY4SG8Gzbg0vH8xwNz_I/edit#gid=0';
        $sheet    = new GoogleSpreadsheet($sheetUrl);
        //when I request the data for a worksheet
        $data = $sheet->getWorksheetDataForImport('worksheet data test')->toArray();
        //then i get the data back
        $expected = [ '1' => ['reg_id' => 'data A2', 'header B1' => 'data B2' ]];
        //ddd($data, $expected);
        $this->assertSame($expected, $data);
    }

    /**
     * @test
     */
    public function it_retrieves_an_associative_array_of_data_for_a_worksheet_that_has_no_regsitry_ids()
    {
        //given a spreadsheet and worksheet title string
        $sheetUrl = 'https://docs.google.com/spreadsheets/d/1WTxiOvHHUurz76NZ0WU_2GjjY4SG8Gzbg0vH8xwNz_I/edit#gid=0';
        $sheet    = new GoogleSpreadsheet($sheetUrl);
        //when I request the data for a worksheet
        $data = $sheet->getWorksheetDataForImport('worksheet no id test')->toArray();
        //then i get the data back
        $expected = [ '1' => [ 'reg_id' => '', 'header B1' => 'data B2' ], '2' => [ 'reg_id' => '', 'header B1' => 'data B3' ] ];
        // ddd($data, $expected);
        $this->assertSame($expected, $data);
    }

    /**
     * @test
     */
    public function it_retrieves_the_data_for_a_worksheet()
    {
        //given a spreadsheet and worksheet title string
        $sheetUrl = 'https://docs.google.com/spreadsheets/d/1WTxiOvHHUurz76NZ0WU_2GjjY4SG8Gzbg0vH8xwNz_I/edit#gid=0';
        $sheet    = new GoogleSpreadsheet($sheetUrl);
        //when I request the data for a worksheet
        $data = $sheet->getWorksheetData('worksheet data test')->toArray();
        //then i get the data back
        //dd($data);
        $expected = [ [ 'reg_id', 'header B1' ], [ "data A2", "data B2" ] ];
        $this->assertSame($expected, $data);
    }

    public function setUp()
    {
        $this->dontSetupDatabase();
        parent::setUp();
    }
}
