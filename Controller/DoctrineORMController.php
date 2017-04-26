<?php

/*
 * This file is part of Twigony.
 *
 * © Timon F <dev@timonf.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Twigony\Bundle\FrameworkBundle\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Templating\EngineInterface;
use Twigony\Bundle\FrameworkBundle\Form\AutomaticFormBuilder;

/**
 * Twigony's DoctrineORM Controller for common tasks (show, list, edit, create)
 *
 * All controller actions can be used in the router definition without having any own controller.
 *
 * @author Timon F <dev@timonf.de>
 */
class DoctrineORMController
{
    use CacheTrait;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var AutomaticFormBuilder
     */
    private $automaticFormBuilder;

    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * @var Session
     */
    private $session;

    /**
     * @var EngineInterface
     */
    private $templateEngine;

    /**
     * @var RouterInterface
     */
    private $router;

    public function __construct(
        AutomaticFormBuilder $automaticFormBuilder,
        EngineInterface $templateEngine,
        RouterInterface $router,
        FormFactoryInterface $formFactory,
        Session $session,
        EntityManagerInterface $entityManager
    ) {
        $this->automaticFormBuilder = $automaticFormBuilder;
        $this->templateEngine = $templateEngine;
        $this->router = $router;
        $this->formFactory = $formFactory;
        $this->session = $session;
        $this->entityManager = $entityManager;
    }

    /**
     * Displays a single entity.
     *
     * @param Request $request
     * @param string  $template Template path and file name
     * @param string  $entity   Full class name of entity to list
     * @param array   $options  Additional configuration options. Following options are possible:
     *                          - "as" (Default: "entity") -> can change the key of the result entity.
     * @return Response
     */
    public function viewAction(Request $request, $template, $entity, $options = []) : Response
    {
        $repository = $this->entityManager->getRepository($entity);
        $id         = $request->get('id');

        $entityKey = array_key_exists('as', $options) ? $options['as'] : 'entity';
        $findOneBy = ['id' => $id];
        $entity    = $repository->findOneBy($findOneBy);

        if (null === $entity) {
            throw new NotFoundHttpException(sprintf(
                'Entity "%s" with id "%s" not found.',
                $entity,
                (string) $id
            ));
        }

        $response = new Response($this->templateEngine->render($template, [
            'options' => $options,
            $entityKey => $entity,
        ]));

        $this->applyCacheOptions($response, $options);

        return $response;
    }

    /**
     * List all entries of an entity.
     *
     * @param Request $request
     * @param string  $template Template path and file name
     * @param string  $entity   Full class name of entity to list
     * @param array   $options  Additional configuration options. Following options are possible:
     *                          - "as" (Default: "entities") -> can change the key of the result entities.
     * @return Response
     */
    public function listAction(Request $request, $template, $entity, $options = []) : Response
    {
        $page = $request->get('page', 1);
        $perPage = array_key_exists('perPage', $options) ? $options['perPage'] : 0;
        $pages = 1;
        $repository = $this->entityManager->getRepository($entity);
        $entities = $this->paginate($options, $repository, $page, $perPage, $pages);

        $entitiesKey = array_key_exists('as', $options) ? $options['as'] : 'entities';

        $response = new Response($this->templateEngine->render($template, [
            'options' => $options,
            $entitiesKey => $entities,
            'pages' => $pages,
            'page' => $page
        ]));

        $this->applyCacheOptions($response, $options);

        return $response;
    }

    /**
     * Displays a form for an existing entity and can save them to database after submitting and validating.
     *
     * @param Request $request
     * @param string  $template Template path and file name
     * @param string  $entity   Full class name of entity to edit
     * @param array   $options  Additional configuration options. Following options are possible:
     *                          - "formClass" (optional) -> Custom form. Otherwise form will be created for you
     *                          - "flash" (optional) -> Flash bag message on success (will be added as "notice")
     *                          - "redirect" (optional) -> Route to redirect after success
     * @return Response
     */
    public function editAction(Request $request, $template, $entity, $options) : Response
    {
        $repository = $this->entityManager->getRepository($entity);
        $findOneBy = ['id' => $request->query->get('id')];

        $form = $this->handleForm($options, $repository->findOneBy($findOneBy));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($form->getData());
            $this->entityManager->flush();
            $this->applyFlashMessage($options);

            if (array_key_exists('redirect', $options)) {
                return new RedirectResponse($this->router->generate($options['redirect']), 301);
            }
        }

        $response = new Response($this->templateEngine->render($template, [
            'options' => $options,
            'form' => $form->createView(),
        ]));

        $this->applyCacheOptions($response, $options);

        return $response;
    }

    /**
     * Displays a form for an entity and can save them to database after submitting and validating.
     *
     * @param Request $request
     * @param string  $template Template path and file name
     * @param string  $entity   Full class name of entity to edit
     * @param array   $options  Additional configuration options. Following options are possible:
     *                          - "formClass" (optional) -> Custom form. Otherwise form will be created for you
     *                          - "flash" (optional) -> Flash bag message on success (will be added as "notice")
     *                          - "redirect" (optional) -> Route to redirect after success
     * @return Response
     */
    public function createAction(Request $request, $template, $entity, $options) : Response
    {
        $form = $this->handleForm($options, new $entity());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($form->getData());
            $this->entityManager->flush();
            $this->applyFlashMessage($options);

            if (array_key_exists('redirect', $options)) {
                return new RedirectResponse($this->router->generate($options['redirect']), 301);
            }
        }

        $response = new Response($this->templateEngine->render($template, [
            'options' => $options,
            'form' => $form->createView(),
        ]));

        $this->applyCacheOptions($response, $options);

        return $response;
    }

    /**
     * @param array  $options Options array, defined in routing files
     * @param object $entity  Instance of an given entity - or "null"
     * @return FormInterface
     */
    protected function handleForm($options, $entity) : FormInterface
    {
        if (null === $entity) {
            throw new NotFoundHttpException('Entity not found.');
        }

        if (array_key_exists('formClass', $options)) {
            return $this->formFactory->create($options['formClass'], $entity);
        } else {
            return $this->automaticFormBuilder->buildFormByClass($entity);
        }
    }

    /**
     * @param array  $options Options array, defined in routing files
     * @param string $type
     */
    protected function applyFlashMessage($options, $type = 'notice')
    {
        if (array_key_exists('flash', $options)) {
            $this->session->getFlashBag()->add($type, $options['flash']);
        }
    }

    /**
     * @param array $options
     * @param EntityRepository $repository
     * @param int $page
     * @param int $perPage If 0 all results will be returned
     * @param int $pages
     * @return mixed|array
     */
    protected function paginate(
        array $options,
        EntityRepository $repository,
        int $page = 1,
        int $perPage = 0,
        int &$pages = 1
    ) {
        $page = abs($page - 1); // internally we start with a 0

        if ($perPage > 0) {
            $allEntities = (int)$repository
                ->createQueryBuilder('e')
                ->select('COUNT(e)')
                ->getQuery()
                ->getSingleScalarResult();

            $pages = ceil($allEntities / $perPage);
        }

        $result = $repository
            ->createQueryBuilder('e')
            ->select('e')
            ->setFirstResult($perPage > 0 ? $perPage * $page : 0);

        if (array_key_exists('orderBy', $options)) {
            list($key, $direction) = $options['orderBy'];

            $result->orderBy('e.'.$key, strtoupper($direction) === 'DESC' ? 'DESC' : 'ASC');
        }

        if ($perPage > 0) {
            $result->setMaxResults($perPage);
        }

        return $result
            ->getQuery()
            ->getResult();
    }
}
