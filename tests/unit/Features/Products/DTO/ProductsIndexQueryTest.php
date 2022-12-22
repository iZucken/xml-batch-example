<?php

namespace unit\Features\Products\DTO;

use App\Features\Products\DTO\ProductsIndexQuery;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

class ProductsIndexQueryTest extends TestCase
{
    /**
     * @covers \App\Features\Products\DTO\ProductsIndexQuery::__construct
     * @covers \App\Features\Products\DTO\ProductsIndexQuery::fromDatatableRequest
     */
    public function testFromDatatableRequest()
    {
        $query = ProductsIndexQuery::fromDatatableRequest(new Request([
            "draw" => "19",
            "columns" => [
                [
                    "data" => "name",
                    "name" => "",
                    "searchable" => "true",
                    "orderable" => "true",
                    "search" => [
                        "value" => "",
                        "regex" => "false"
                    ]
                ],
                [
                    "data" => "platform_attributes",
                    "name" => "",
                    "searchable" => "true",
                    "orderable" => "false",
                    "search" => [
                        "value" => "",
                        "regex" => "false"
                    ]
                ],
                [
                    "data" => "weight",
                    "name" => "",
                    "searchable" => "true",
                    "orderable" => "true",
                    "search" => [
                        "value" => "",
                        "regex" => "false"
                    ]
                ],
                [
                    "data" => "category",
                    "name" => "",
                    "searchable" => "true",
                    "orderable" => "true",
                    "search" => [
                        "value" => "category",
                        "regex" => "false"
                    ]
                ]
            ],
            "order" => [
                [
                    "column" => "0",
                    "dir" => "desc"
                ]
            ],
            "start" => "50",
            "length" => "100",
            "search" => [
                "value" => "search text",
                "regex" => "false"
            ],
            "_" => "1671732139310"
        ]));
        $this->assertEquals(100, $query->limit);
        $this->assertEquals(50, $query->offset);
        $this->assertEquals('category', $query->category);
        $this->assertEquals('search text', $query->search);
        $this->assertEquals('name', $query->orderColumn);
        $this->assertEquals('desc', $query->orderDirection);
    }
}
