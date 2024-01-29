<?php

namespace App\Controller;

use App\Entity\Text;
use Doctrine\ORM\EntityManagerInterface;
use Statickidz\GoogleTranslate;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Annotation\Route;

class TranslateController extends AbstractController
{
    public static function httpRequestShouldHaveSpecificParametersWhenGiven(Request $request, array $parametersThatHttpRequestShouldHave): bool
    {
        $parametersToValidate = $request->query->all();

        if ($parametersToValidate === []) {
            throw new HttpException('400', 'Missing GET parameters');
        }

        foreach ($parametersThatHttpRequestShouldHave as $parameter) {
            if (!in_array($parameter, array_keys($parametersToValidate))) {
                throw new HttpException('400', sprintf('Missing GET parameter %s', $parameter));
            }
        }

        return true;
    }

    #[Route('/translate', name: 'app_translate')]
    public function translate(EntityManagerInterface $entityManager, Request $request): Response
    {
        TranslateController::httpRequestShouldHaveSpecificParametersWhenGiven($request, ['source', 'target', 'text']);

        $target = $request->query->get('target');
        $text = $request->query->get('text');
        $id = $target . '-' . md5($text);
        
        $textRetrieved = $entityManager->getRepository(Text::class)->find($id);

        if ($textRetrieved !== null) {
            return new Response($textRetrieved->getText());
        }

        $trans = new GoogleTranslate();
        $textTranslated = $trans->translate($request->query->get('source'), $target, $text);

        $entity = new Text();
        $entity->setId($id);
        $entity->setText($textTranslated);
        $entityManager->persist($entity);
        $entityManager->flush();

        return new Response($textTranslated);
    }

    #[Route('/translate-date', name: 'app_translate_date')]
    public function translate_date(Request $request): Response
    {
        TranslateController::httpRequestShouldHaveSpecificParametersWhenGiven($request, ['source', 'target', 'date']);

        $formatter = new \IntlDateFormatter($request->query->get('source'), \IntlDateFormatter::SHORT, \IntlDateFormatter::NONE);
        if (!$formatter instanceof \IntlDateFormatter) {
            throw new HttpException(400, intl_get_error_message());
        }
        $timestamp = $formatter->parse($request->query->get('date'));
        $date = new \DateTime;
        $date->setTimestamp($timestamp);

        $formatter = new \IntlDateFormatter($request->query->get('target'), \IntlDateFormatter::SHORT, \IntlDateFormatter::NONE);
        if (!$formatter instanceof \IntlDateFormatter) {
            throw new HttpException(400, intl_get_error_message());
        }

        return new Response($date->format(str_replace('M', 'm', str_replace('yy', 'y', $formatter->getPattern()))));
    }

    #[Route('/translate-time', name: 'app_translate_time')]
    public function translate_time(Request $request): Response
    {
        TranslateController::httpRequestShouldHaveSpecificParametersWhenGiven($request, ['source', 'target', 'time']);

        $formatter = new \IntlDateFormatter($request->query->get('source'), \IntlDateFormatter::NONE, \IntlDateFormatter::SHORT);
        if (!$formatter instanceof \IntlDateFormatter) {
            throw new HttpException(400, intl_get_error_message());
        }
        $timestamp = $formatter->parse($request->query->get('time'));
        $date = new \DateTime;
        $date->setTimestamp($timestamp);

        $formatter = new \IntlDateFormatter($request->query->get('target'), \IntlDateFormatter::NONE, \IntlDateFormatter::SHORT);
        if (!$formatter instanceof \IntlDateFormatter) {
            throw new HttpException(400, intl_get_error_message());
        }

        return new Response($date->format(str_replace('HH', 'H', str_replace('mm', 'i', $formatter->getPattern()))));
    }
}
