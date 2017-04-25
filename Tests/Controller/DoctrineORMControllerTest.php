<?php

/*
 * This file is part of Twigony.
 *
 * © Timon F <dev@timonf.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Twigony\Bundle\FrameworkBundle\Tests\Controller;

use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bundle\FrameworkBundle\Tests\TestCase;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Templating\EngineInterface;
use Twigony\Bundle\FrameworkBundle\Controller\DoctrineORMController;
use Twigony\Bundle\FrameworkBundle\Form\AutomaticFormBuilder;

class DoctrineORMControllerTest extends TestCase
{
    /**
     * @var DoctrineORMController
     */
    protected $controller;

    // Setting up complex example data
    public function setUp()
    {
        // Entity Post, instance 1
        $blogPost1 = new class {
            protected $id = 'id-1';
            public $title = 'Foo';
        };

        // Entity Post, instance 1
        $blogPost2 = new class {
            protected $id = 'id-2';
            public $title = 'Bar';
        };

        $query = $this
            ->getMockBuilder(AbstractQuery::class)
            ->disableOriginalConstructor()
            ->getMock();
        $query->expects($this->any())
            ->method('getSingleScalarResult')
            ->will($this->returnValue(2));
        $query->expects($this->any())
            ->method('getResult')
            ->will($this->returnValue([$blogPost1, $blogPost2]));

        $queryBuilder = $this
            ->getMockBuilder(QueryBuilder::class)
            ->disableOriginalConstructor()
            ->getMock();
        $queryBuilder->expects($this->any())
            ->method('select')
            ->will($this->returnValue($queryBuilder));
        $queryBuilder->expects($this->any())
            ->method('setFirstResult')
            ->will($this->returnValue($queryBuilder));
        $queryBuilder->expects($this->any())
            ->method('setMaxResults')
            ->will($this->returnValue($queryBuilder));
        $queryBuilder->expects($this->any())
            ->method('orderBy')
            ->will($this->returnValue($queryBuilder));
        $queryBuilder->expects($this->any())
            ->method('getQuery')
            ->will($this->returnValue($query));

        // Post Repository
        $blogPostRepository = $this
            ->getMockBuilder(EntityRepository::class)
            ->disableOriginalConstructor()
            ->getMock();
        $blogPostRepository->expects($this->any())
            ->method('findAll')
            ->will($this->returnValue([$blogPost1, $blogPost2]));
        $blogPostRepository->expects($this->any())
            ->method('find')
            ->will($this->returnValue($blogPost1));
        $blogPostRepository->expects($this->any())
            ->method('findOneBy')
            ->will($this->returnValue($blogPost1));
        $blogPostRepository->expects($this->any())
            ->method('createQueryBuilder')
            ->will($this->returnValue($queryBuilder));

        // Entity Manager
        $entityManager = $this
            ->getMockBuilder(EntityManagerInterface::class)
            ->getMock();
        $entityManager->expects($this->any())
            ->method('getRepository')
            ->will($this->returnValue($blogPostRepository));

        // Template Engine
        $renderMethod = function (string $name, array $parameters) : string {
            return 'Template name: ' . $name . PHP_EOL . (string) print_r($parameters, true);
        };
        $templateEngine = $this
            ->getMockBuilder(EngineInterface::class)
            ->getMock();
        $templateEngine->expects($this->any())
            ->method('render')
            ->willReturnCallback($renderMethod);

        // Form Factory
        $formFactory = $this
            ->getMockBuilder(FormFactoryInterface::class)
            ->getMock();

        // Session
        $session = $this
            ->getMockBuilder(Session::class)
            ->disableOriginalConstructor()
            ->getMock();

        // Router
        $router = $this
            ->getMockBuilder(RouterInterface::class)
            ->getMock();

        $this->controller = new DoctrineORMController(
            new AutomaticFormBuilder($formFactory),
            $templateEngine,
            $router,
            $formFactory,
            $session,
            $entityManager
        );
    }

    public function testListAction()
    {
        $response = $this->controller->listAction(Request::create('list'), 'xyzTemplate', 'Post', []);

        $this->assertContains('Foo', $response->getContent());
        $this->assertContains('Bar', $response->getContent());
        $this->assertContains('Template name: xyzTemplate', $response->getContent());
    }

    public function testViewAction()
    {
        $request = Request::create('view?id=1', 'GET');
        $response = $this->controller->viewAction($request, 'postTemplate', 'Post', []);

        $this->assertContains('id-1', $response->getContent());
        $this->assertNotContains('id-2', $response->getContent());
        $this->assertContains('Template name: postTemplate', $response->getContent());
    }
}
