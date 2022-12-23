<?php

namespace App\Features\ProductsImport\Controllers;

use League\Flysystem\FilesystemOperator;
use Pimcore\Controller\FrontendController;
use Pimcore\Model\Asset;
use Pimcore\Model\Asset\Listing;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Throwable;

class BatchImportController extends FrontendController
{
    #[Route(path: "/import", methods: Request::METHOD_GET, name: 'products.page.import')]
    public function importFormView(): Response
    {
        return $this->render('products/import.html.twig', [
            'importSources' => (new Listing)
                ->setCondition('type = ?', ['text'])
                ->setCondition('mimetype = ?', ['text/xml'])
                ->setCondition('path = ?', ['/importSources/'])
                ->setOrderKey('creationDate')
                ->getAssets()
        ]);
    }

    #[Route(path: "/import/upload", methods: Request::METHOD_POST)]
    public function importUpload(Request $request, FilesystemOperator $pimcoreAssetStorage): Response
    {
        try {
            /** @var UploadedFile $file */
            $file = $request->files->get('importSource');
            $safeFilename = preg_replace("#[^\w\s]#", "",
                    pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . ".xml";
            if ($pimcoreAssetStorage->fileExists('/importSources/' . $safeFilename)) {
                throw new \Exception("File \"$safeFilename\" already exists in the storage");
            }
            $realisticMime = $file->getMimeType();
            if ($realisticMime !== 'text/xml') {
                throw new \Exception("File is not recognized as text/xml");
            }
            $stream = fopen($file->getPathname(), 'r');
            $pimcoreAssetStorage->writeStream('/importSources/' . $safeFilename, $stream);
            fclose($stream);
            $newAsset = (new Asset)
                ->setType('text')
                ->setMimeType($realisticMime)
                ->setParent(Asset\Service::createFolderByPath('/importSources'))
                ->setFilename($safeFilename)
                ->save();
            $this->addFlash('success', "New import source added as Asset #{$newAsset->getId()} \"$safeFilename\"");
        } catch (Throwable $exception) {
            error_log($exception);
            $this->addFlash('error', $exception->getMessage());
        }
        return new RedirectResponse('/import');
    }
}
