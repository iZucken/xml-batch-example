<?php

namespace App\Features\Products\Controllers;

use App\Features\Products\DTO\ProductsIndexQuery;
use App\Features\Products\Services\ProductsRepository;
use Pimcore\Controller\FrontendController;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Autoconfigure(tags: ['controller.service_arguments'])]
class ProductsListController extends FrontendController
{
    private ProductsRepository $repository;
    private ValidatorInterface $validator;

    public function __construct(ProductsRepository $repository, ValidatorInterface $validator)
    {
        $this->repository = $repository;
        $this->validator = $validator;
    }

    #[Route(path: "/products", methods: Request::METHOD_GET, name: 'products.page.list')]
    public function productsPage(): Response
    {
        return $this->render('products/list.html.twig', [
            'productCategories' => $this->repository->getAllCategoryNames()
        ]);
    }

    #[Route(path: "/products/datatable", methods: Request::METHOD_GET)]
    public function productsListData(Request $request): Response
    {
        $query = ProductsIndexQuery::fromDatatableRequest($request);
        foreach ($this->validator->validate($query) as $violation) {
            throw new BadRequestHttpException($violation->getMessage());
        }
        $index = $this->repository->indexByQuery($query);
        return new JsonResponse([
            "draw" => $request->query->get('draw'),
            "recordsTotal" => $this->repository->getTotalProducts(),
            "recordsFiltered" => $index['filtered'],
            "data" => $index['list'],
        ]);
    }
}
