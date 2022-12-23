<?php

namespace App\Features\ProductsImport\Controllers;

use App\Features\BatchDataImport\Services\BatchImportProcessManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BatchImportController extends AbstractController
{
    private BatchImportProcessManager $importProcessManager;

    public function __construct(BatchImportProcessManager $importProcessManager)
    {
        $this->importProcessManager = $importProcessManager;
    }

    #[Route(path: "/import", methods: Request::METHOD_GET, name: 'products.page.import')]
    public function importFormView(): Response
    {
        return $this->render('products/import.html.twig');
    }

    #[Route(path: "/import/upload", methods: Request::METHOD_POST)]
    public function importUpload(Request $request): Response
    {
        /** @var UploadedFile $file */
        $file = $request->files->get('importSource');
        if (empty($file)) {
            return new Response("No file", 400);
        }
        $safeFilename = preg_replace("#[^\w\s]#", "",
                pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . ".xml";
        if (is_file('/tmp/' . $safeFilename)) {
            return new Response("File \"$safeFilename\" already exists in the storage", 400);
        }
        $realisticMime = $file->getMimeType();
        if ($realisticMime !== 'text/xml') {
            return new Response("File is not recognized as text/xml", 400);
        }
        $this->importProcessManager->startImportProcess($file->move('/tmp', $safeFilename));
        return new Response("Import process started on \"$safeFilename\"", 200);
    }
}
