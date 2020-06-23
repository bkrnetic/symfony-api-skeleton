<?php

namespace App\Controller\Api\v1;

use App\Controller\BaseController;
use App\Entity\Foo;
use App\Exception\VerboseExceptionInterface;
use App\Factory\EntityFactory;
use App\Repository\FooRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Swagger\Annotations as SWG;

class FooController extends BaseController
{
    /**
     * @var EntityFactory
     */
    private $factory;
    /**
     * @var FooRepository
     */
    private $fooRepository;

    /**
     * FooController constructor.
     * @param EntityFactory $factory
     * @param FooRepository $fooRepository
     */
    public function __construct(EntityFactory $factory, FooRepository $fooRepository)
    {
        $this->factory = $factory;
        $this->fooRepository = $fooRepository;
    }

    /**
     * @SWG\Get(
     *   tags={"Foo"},
     *   @SWG\Parameter(
     *     name="title",
     *     in="query",
     *     type="string",
     *     required=true,
     *     description="Title",
     *   )
     * )
     * @SWG\Response(
     *   response=200,
     *   description="Foo fetched",
     *   @Model(type=Foo::class, groups={"foo:read"})
     * )
     *
     * @param Request $request
     * @return JsonResponse
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws VerboseExceptionInterface
     */
    public function index(Request $request)
    {
        $title = $request->get('title');

        if (is_null($title)) throw new BadRequestHttpException('Title query parameter is obligatory');

        $foo = $this->fooRepository->findOneBy(['title' => $title]);

        if ($foo instanceof Foo) {
            return $this->serializeToJsonResponse($foo, ['foo:read']);
        }

        $data = [
            'title' => $title
        ];

        /** @var Foo $foo */
        $foo = $this->factory->create($data, Foo::class, ['foo:read']);

        $foo = $this->fooRepository->merge($foo);

        return $this->serializeToJsonResponse($foo, ['foo:read']);
    }
}
