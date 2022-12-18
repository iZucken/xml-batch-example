<?php

namespace App\Controller;

use Pimcore\Controller\FrontendController;
use Pimcore\Model\DataObject\Product;
use Pimcore\Model\DataObject\ProductCategory;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductsListController extends FrontendController
{
    #[Route(path: "/products", methods: Request::METHOD_GET)]
    public function productsPage(): Response
    {
        return $this->render('products/list.html.twig', [
            'productCategories' => (new ProductCategory\Listing)->getObjects()
        ]);
    }

    #[Route(path: "/products/datatable", methods: Request::METHOD_GET)]
    public function productsListData(Request $request): Response
    {
        $list = (new Product\Listing);
        $query = $request->query->all();
        foreach ($query['columns']??[] as $column) {
            switch ($column['data']) {
                case "category":
                    if (!empty($column['search']['value'])) {
                        $list->filterByCategory([
                            'id' => $column['search']['value'],
                            'type' => 'object',
                        ]);
                    }
                    break;
            }
        }
        if (!empty($query['order'])) {
            $column = $query['columns'][$query['order'][0]['column']]['data'] ?? null;
            $dir = $query['order'][0]['dir'] ?? null;
            switch ($column) {
                case 'id':
                    $list->setOrderKey('object_ProductClass.oo_id', false)
                        ->setOrder($dir);
                    break;
                case 'name':
                    $list->setOrderKey('object_ProductClass.name', false)
                        ->setOrder($dir);
                    break;
                case 'weight':
                    $list->setOrderKey('CAST(object_ProductClass.absolute_weight AS UNSIGNED)', false)
                        ->setOrder($dir);
                    break;
                case 'category':
                    $list->setOrderKey('object_ProductClass.category__id', false)
                        ->setOrder($dir);
                    break;
            }
        }
        if (!empty($query['search']['value'])) {
            $list->onCreateQueryBuilder(
                function (\Doctrine\DBAL\Query\QueryBuilder $queryBuilder) use ($query) {
                    $search = $queryBuilder->getConnection()->quote("%{$query['search']['value']}%");
                    $queryBuilder
                        ->leftJoin('object_ProductClass', 'object_ProductCategoryClass', 'object_ProductCategoryClass',
                            'object_ProductCategoryClass.o_id = object_ProductClass.category__id')
                        ->andWhere("object_ProductClass.name LIKE $search OR description LIKE $search OR object_ProductCategoryClass.name LIKE $search");
                }
            );
        }
        $list->setLimit($request->get('length', 10));
        $list->setOffset($request->get('start', 10));
        return new JsonResponse([
            "draw" => $request->query->get('draw'),
            "recordsTotal" => (new Product\Listing)->getTotalCount(),
            "recordsFiltered" => $list->getTotalCount(),
            'data' => array_map(fn(Product $product) => [
                'id' => $product->getId(),
                'name' => $product->getName(),
                'description' => $product->getDescription(),
                'weight' => (string)$product->getWeight(),
                'category' => $product->getCategory()->getName(),
            ], $list->getObjects()),
        ]);
    }
}
