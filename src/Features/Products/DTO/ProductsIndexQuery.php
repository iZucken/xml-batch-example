<?php

namespace App\Features\Products\DTO;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\Range;

class ProductsIndexQuery
{
    public function __construct(
        #[Range(min: 10, max: 100)]
        public readonly int $limit,
        #[Range(min: 0)]
        public readonly int $offset,
        #[Choice(choices: ['name', 'weight', 'category'])]
        public readonly ?string $orderColumn = null,
        #[Choice(choices: ['asc', 'desc'])]
        public readonly ?string $orderDirection = null,
        public readonly ?string $search = null,
        public readonly ?string $category = null,
    ) {
    }

    public static function fromDatatableRequest(Request $request): ProductsIndexQuery
    {
        $filterCategory = null;
        $query = $request->query->all();
        foreach ($query['columns'] ?? [] as $column) {
            switch ($column['data']) {
                case "category":
                    if (!empty($column['search']['value'])) {
                        $filterCategory = $column['search']['value'];
                    }
                    break;
            }
        }
        return new self(
            $query['length'] ?? 10,
            $query['start'] ?? 10,
            $query['columns'][$query['order'][0]['column'] ?? '']['data'] ?? null,
                $query['order'][0]['dir'] ?? null,
                $query['search']['value'] ?? '',
            $filterCategory,
        );
    }
}
