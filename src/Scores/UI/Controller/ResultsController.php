<?php

declare(strict_types=1);

namespace App\Rezig\Scores\UI\Controller;

use App\Rezig\Scores\Converter\Converter;
use App\Rezig\Scores\DataProvider\GameResultsDataProviderInterface;
use App\Scores\DataProvider\Exception\ResourceNotFound;
use Doctrine\ODM\MongoDB\MongoDBException;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\Exception\HttpExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class ResultsController
{
    /**
     * example query values:
     *  ?sortByDate=asc
     *  ?sortByScore=dsc
     *
     * @param Request                           $request
     * @param string                            $gameId
     * @param GameResultsDataProviderInterface  $dataProvider
     * @param Converter                         $converter
     * @param LoggerInterface                   $logger
     *
     * @return Response
     */
    public function get(
        Request $request,
        string $gameId,
        GameResultsDataProviderInterface $dataProvider,
        Converter $converter,
        LoggerInterface $logger
    ): Response
    {
        $sortByDate = $request->query->get('sortByDate');
        $sortByScore = $request->query->get('sortByScore');

        try {
            $result = $dataProvider->getResultsByGameId($gameId, $sortByDate, $sortByScore);
        } catch (ResourceNotFound $e) {
            return new JsonResponse(sprintf('Game with id %s not found', $gameId), 404);
        } catch (HttpExceptionInterface $e) {
            $logger->error('Http Exception', ['stack trace' => $e->getTrace()]);

            return new JsonResponse('Internal server error', 500);
        } catch (TransportExceptionInterface $e) {
            $logger->error('Transport Exception', ['stack trace' => $e->getTrace()]);

            return new JsonResponse('Internal server error', 500);
        } catch (MongoDBException $e) {
            $logger->error('MongoDB Exception', ['stack trace' => $e->getTrace()]);

            return new JsonResponse('Internal server error', 500);
        } catch (\InvalidArgumentException $e) {
            $logger->error('Invalid argument', ['stack trace' => $e->getTrace()]);

            return new JsonResponse('Internal server error', 500);
        }

        $data = $converter->getData($result);

        return new JsonResponse($data, 200, [], true);
    }
}
