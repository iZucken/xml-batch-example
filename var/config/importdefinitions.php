<?php

return [
    1 => [
        "id" => 1,
        "name" => "productSampleImport",
        "provider" => "streaming_xml_provider",
        "class" => "Product",
        "configuration" => [
            "xPath" => "/products/product",
        ],
        "creationDate" => 1671479353,
        "modificationDate" => 1671488729,
        "runner" => NULL,
        "stopOnException" => FALSE,
        "failureNotificationDocument" => NULL,
        "successNotificationDocument" => NULL,
        "loader" => "primary_key",
        "objectPath" => "@'/imported/products/' ~ category",
        "cleaner" => "none",
        "key" => "name",
        "filter" => NULL,
        "renameExistingObjects" => FALSE,
        "relocateExistingObjects" => FALSE,
        "skipNewObjects" => FALSE,
        "skipExistingObjects" => FALSE,
        "createVersion" => FALSE,
        "omitMandatoryCheck" => FALSE,
        "forceLoadObject" => FALSE,
        "mapping" => [
            [
                "primaryIdentifier" => TRUE,
                "fromColumn" => "name",
                "toColumn" => "o_key"
            ],
            [
                "fromColumn" => "name",
                "toColumn" => "name"
            ],
            [
                "fromColumn" => "description",
                "toColumn" => "description"
            ],
            [
                "fromColumn" => "weight",
                "toColumn" => "weight",
                "interpreter" => "quantity_unit_interpreter"
            ],
            [
                "fromColumn" => "category",
                "toColumn" => "category",
                "interpreter" => "definition",
                "interpreterConfig" => [
                    "definition" => 2
                ]
            ],
            [
                "fromColumn" => "custom",
                "toColumn" => "o_published",
                "interpreter" => "default_value",
                "interpreterConfig" => [
                    "value" => 1
                ],
                "primaryIdentifier" => FALSE,
                "setter" => NULL,
                "setterConfig" => NULL
            ],
        ]
    ],
    2 => [
        "id" => 2,
        "name" => "productCategorySampleImport",
        "provider" => "raw",
        "class" => "ProductCategory",
        "configuration" => [
            "headers" => "name,category"
        ],
        "creationDate" => 1671479353,
        "modificationDate" => 1671501647,
        "mapping" => [
            [
                "fromColumn" => "category",
                "toColumn" => "name",
                "interpreter" => NULL,
                "interpreterConfig" => NULL,
                "primaryIdentifier" => FALSE,
                "setter" => NULL,
                "setterConfig" => NULL
            ],
            [
                "fromColumn" => "custom",
                "toColumn" => "o_published",
                "interpreter" => "default_value",
                "interpreterConfig" => [
                    "value" => 1
                ],
                "primaryIdentifier" => FALSE,
                "setter" => NULL,
                "setterConfig" => NULL
            ],
            [
                "fromColumn" => "category",
                "toColumn" => "o_key",
                "interpreter" => NULL,
                "interpreterConfig" => NULL,
                "primaryIdentifier" => TRUE,
                "setter" => NULL,
                "setterConfig" => NULL
            ]
        ],
        "runner" => NULL,
        "stopOnException" => FALSE,
        "failureNotificationDocument" => NULL,
        "successNotificationDocument" => NULL,
        "loader" => "primary_key",
        "objectPath" => "/imported/categories",
        "cleaner" => "none",
        "key" => "name",
        "filter" => NULL,
        "renameExistingObjects" => FALSE,
        "relocateExistingObjects" => FALSE,
        "skipNewObjects" => FALSE,
        "skipExistingObjects" => FALSE,
        "createVersion" => FALSE,
        "omitMandatoryCheck" => FALSE,
        "forceLoadObject" => FALSE
    ]
];
