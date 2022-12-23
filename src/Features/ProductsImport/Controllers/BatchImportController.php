<?php

namespace App\Features\ProductsImport\Controllers;

use App\Features\BatchDataImport\Services\BatchImportProcessManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Throwable;

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
        try {
            /** @var UploadedFile $file */
            $file = $request->files->get('importSource');
            if (empty($file)) {
                throw new BadRequestHttpException("No file");
            }
            $safeFilename = preg_replace("#[^\w\s]#", "",
                    pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . ".xml";
            if (is_file('/tmp/' . $safeFilename)) {
                throw new \Exception("File \"$safeFilename\" already exists in the storage");
            }
            $realisticMime = $file->getMimeType();
            if ($realisticMime !== 'text/xml') {
                throw new \Exception("File is not recognized as text/xml");
            }
            $this->importProcessManager->startImportProcess($file->move('/tmp', $safeFilename));
            $this->addFlash('success', "Import process started on \"$safeFilename\"");
        } catch (Throwable $exception) {
            error_log($exception);
            $this->addFlash('error', $exception->getMessage());
        }
        return new RedirectResponse('/import');
    }
}
