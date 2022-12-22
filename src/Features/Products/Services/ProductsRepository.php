<?php

namespace App\Features\Products\Services;

use App\Features\Products\DTO\ProductsIndexQuery;
use Doctrine\DBAL\Connection;

class ProductsRepository
{
    private Connection $db;

    public function __construct(Connection $db)
    {
        $this->db = $db;
    }

    public function getAllCategoryNames(): array
    {
        return $this->db->executeQuery("SELECT DISTINCT(category) FROM products")->fetchFirstColumn();
    }

    public function getTotalProducts(): int
    {
        return $this->db->executeQuery("SELECT COUNT(id) FROM products")->fetchOne();
    }

    /**
     * @param ProductsIndexQuery $query
     * @return array{list: array, filtered: int}
     */
    public function indexByQuery(ProductsIndexQuery $query): array
    {
        $qb = $this->db->createQueryBuilder()
            ->select("*")
            ->from("products", "p");
        if (isset($query->offset)) {
            $qb->setFirstResult($query->offset);
        }
        if (isset($query->limit)) {
            $qb->setMaxResults($query->limit);
        }
        if (!empty($query->category)) {
            $qb->andWhere('category = :category')
                ->setParameter('category', $query->category);
        }
        if (!empty($query->search)) {
            $qb->andWhere('name LIKE :search OR category LIKE :search OR description_common LIKE :search OR description_for_wildberries LIKE :search OR description_for_ozon LIKE :search')
                ->setParameter('search', "%$query->search%");
        }
        if (isset($query->orderColumn)) {
            switch ($query->orderColumn) {
                case 'name':
                    $qb->orderBy('name', $query->orderDirection);
                    break;
                case 'weight':
                    $qb->orderBy('absolute_weight', $query->orderDirection);
                    break;
                case 'category':
                    $qb->orderBy('category', $query->orderDirection);
                    break;
            }
        }
        return [
            'list' => $qb->execute()->fetchAllAssociative(),
            'filtered' => $qb->select("COUNT(id)")
                ->setFirstResult(0)
                ->setMaxResults(null)
                ->execute()->fetchOne(),
        ];
    }
}
