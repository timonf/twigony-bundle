<?php

/*
 * This file is part of Twigony.
 *
 * © Timon F <dev@timonf.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Twigony\Bundle\FrameworkBundle\Form;

use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;

class AutomaticFormBuilder
{
    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    public function __construct(FormFactoryInterface $formFactory)
    {
        $this->formFactory = $formFactory;
    }

    /**
     * Builds a form automatically by given model class instance.
     *
     * Attention/Warning: Use this method ONLY for simple prototyping! You should create your own Form class:
     * @see http://symfony.com/doc/current/best_practices/forms.html#building-forms
     *
     * @param  object $data  Should contain a model or entity with public methods or setters and getters.
     * @return FormInterface
     */
    public function buildFormByClass($data) : FormInterface
    {
        $this->formFactory->createBuilder(FormType::class, $data, []);
        $reflectionClass = new \ReflectionClass($data);
        $accessor = PropertyAccess::createPropertyAccessor();

        $properties = [];

        foreach ($reflectionClass->getProperties() as $property) {
            if ($accessor->isWritable($data, $property->getName())) {
                $properties[] = $property->getName();
            }
        }

        $formBuilder = $this->formFactory->createBuilder(FormType::class, $data, [
            'data_class' => get_class($data),
        ]);

        foreach ($properties as $property) {
            $formBuilder->add($property);
        }

        return $formBuilder->getForm();
    }
}
