<?php

namespace App\Features\ImportLogs\Controllers;

use App\Features\ImportLogs\Services\ImportLogRepository;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Autoconfigure(tags: ['controller.service_arguments'])]
class ImportLogController
{
    private ImportLogRepository $logRepository;
    private SerializerInterface $serializer;

    public function __construct(ImportLogRepository $logRepository, SerializerInterface $serializer)
    {
        $this->logRepository = $logRepository;
        $this->serializer = $serializer;
    }

    #[Route(path: '/importLog/datatable', methods: Request::METHOD_GET)]
    public function index(Request $request)
    {
        $top = $this->logRepository->getTop();
        return new JsonResponse($this->serializer->serialize([
            'draw' => $request->query->get('draw'),
            "recordsTotal" => $this->logRepository->getTotalLogs(),
            "recordsFiltered" => count($top),
            "data" => $top,
        ], 'json'), 200, [], true);
    }

    #[Route(path: '/importLog/{uuid}/csv', methods: Request::METHOD_GET)]
    public function downloadCsv(string $uuid)
    {
        if ($log = $this->logRepository->getByUuid($uuid)) {
            $suggestedFilename = $log->getSourceFilename() . "." . $log->getStartedAt()->format(DATE_ATOM) . ".csv";
            return new Response(
                $this->serializer->serialize($log->getStats(), 'csv'), 200, [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment;filename=' . $suggestedFilename
            ]);
        }
        throw new NotFoundHttpException();
    }
}
