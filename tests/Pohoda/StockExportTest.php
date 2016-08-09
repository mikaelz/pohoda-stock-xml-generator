<?php

namespace MikaelzTests\Pohoda;

use Mikaelz\Pohoda\StockExport;

class StockExportTest extends \PHPUnit_Framework_TestCase
{
    protected $stockExport;

    public function setUp()
    {
        $data = array(array(
            'name' => 'Test item',
            'code' => 'Item123',
            'picture' => 'http://lorempixel.com/400/400/animals/',
            'quantity' => 2,
            'price' => 123.99,
            'weight' => 123.50,
        ));
        $this->stockExport = new StockExport($data);
    }

    public function tearDown()
    {
        unset($this->stockExport);
    }

    /** @test */
    public function validInstance()
    {
        $this->assertInstanceOf(StockExport::class, $this->stockExport);
    }

    /** @test */
    public function validateStockXml()
    {
        $this->stockExport->createStockXml();
        $xml = new \DOMDocument();
        $xml->load(stockExport::STOCK_XML_PATH);

        $isValid = $xml->schemaValidate('http://www.stormware.cz/schema/version_2/data.xsd');
        $this->assertTrue($isValid);
    }
}
