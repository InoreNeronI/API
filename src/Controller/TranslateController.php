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

        if (empty($parametersToValidate)) {
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
    public function index(EntityManagerInterface $entityManager, Request $request): Response
    {
        TranslateController::httpRequestShouldHaveSpecificParametersWhenGiven($request, ['source', 'target', 'text']);

        $target = $request->query->get('target');
        $text = $request->query->get('text');
        $id = $target . '-' . md5($text);
        
        $textRetrieved = $entityManager->getRepository(Text::class)->find($id);

        if ($textRetrieved) {
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
}
