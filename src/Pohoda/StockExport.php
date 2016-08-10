<?php

namespace Mikaelz\Pohoda;

class StockExport
{
    const STORE_ID = 'STORE_ID';
    const STORE_NAME = 'STORE_NAME';
    const STOCK_XML_PATH = '/tmp/pohoda_stock_import.xml';

    private $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function createStockXml()
    {
        $xml = '<?xml version="1.0" encoding="Windows-1250"?>
        <dat:dataPack xmlns:dat="http://www.stormware.cz/schema/version_2/data.xsd"
        xmlns:stk="http://www.stormware.cz/schema/version_2/stock.xsd"
        xmlns:typ="http://www.stormware.cz/schema/version_2/type.xsd"
        id="'.self::STORE_ID.'" ico="'.POHODA_ICO.'"
        application="Eshop" version="2.0" note="XML import zasob.">';
        $xml .= $this->getStockXmlItems();
        $xml .= '</dat:dataPack>';

        if (!file_put_contents(self::STOCK_XML_PATH, $xml)) {
            throw new Exception("Couldn't write file ".self::STOCK_XML_PATH);
        }

        return true;
    }

    private function getStockXmlItems()
    {
        $counter = 1;
        $items = array();
        foreach ($this->data as $item) {
            $name = substr($item['name'], 0, 90);
            $price = number_format((float) $item['price'], 2, '.', '');

            $items[] = '
            <dat:dataPackItem id="ZAS'.date('Ymd').$counter++.'" version="2.0">
                <stk:stock version="2.0">
                    <stk:stockHeader>
                        <stk:stockType>card</stk:stockType>
                        <stk:code><![CDATA['.htmlspecialchars($item['code']).']]></stk:code>
                        <stk:name>'.$name.'</stk:name>
                        <stk:isSales>true</stk:isSales>
                        <stk:isInternet>true</stk:isInternet>
                        <stk:purchasingRateVAT>high</stk:purchasingRateVAT>
                        <stk:sellingRateVAT>high</stk:sellingRateVAT>
                        <stk:unit>ks</stk:unit>
                        <stk:count>'.$item['quantity'].'</stk:count>
                        <stk:storage>
                            <typ:id>1</typ:id>
                            <typ:ids>'.self::STORE_NAME.'</typ:ids>
                        </stk:storage>
                        <stk:sellingPrice>'.$price.'</stk:sellingPrice>
                        <stk:mass>'.$item['weight'].'</stk:mass>
                        <stk:volume></stk:volume>
                        <stk:pictures>
                            <stk:picture default="true">
                                <stk:filepath>'.$item['picture'].'</stk:filepath>
                            </stk:picture>
                        </stk:pictures>
                        <stk:producer>MIKAELZ</stk:producer>
                        <stk:note>XML for import into Pohoda.</stk:note>
                    </stk:stockHeader>
                    <stk:stockPriceItem>
                        <stk:stockPrice>
                            <typ:id>1</typ:id>
                            <typ:ids>VOC</typ:ids>
                            <typ:price>'.$price.'</typ:price>
                        </stk:stockPrice>
                    </stk:stockPriceItem>
                </stk:stock>
            </dat:dataPackItem>';
        }

        return implode('', $items);
    }
}
