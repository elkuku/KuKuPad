<?php

declare(strict_types=1);

use Rector\Core\Configuration\Option;
use Rector\Php74\Rector\Property\TypedPropertyRector;
use Rector\Php80\Rector\Class_\AnnotationToAttributeRector;
use Rector\Php80\ValueObject\AnnotationToAttribute;
use Rector\Set\ValueObject\SetList;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\SymfonyPhpConfig\ValueObjectInliner;

return static function (ContainerConfigurator $containerConfigurator): void {
    // get parameters
    // $parameters = $containerConfigurator->parameters();
    //
    // $parameters->set(Option::SETS, [
    //             SetList::PHP_80,
    // ]);

    // Define what rule sets will be applied
    // $containerConfigurator->import(SetList::DEAD_CODE);
    $containerConfigurator->import(SetList::PHP_80);

    $services = $containerConfigurator->services();

    $services->set(AnnotationToAttributeRector::class)
        ->call(
            'configure',
            [
                [
                    AnnotationToAttributeRector::ANNOTATION_TO_ATTRIBUTE => ValueObjectInliner::inline(
                        [
                            new AnnotationToAttribute(
                                'Symfony\Component\Routing\Annotation\Route',
                                'Symfony\Component\Routing\Annotation\Route'
                            ),
                        ]
                    ),
                ],
            ]
        );

    // get services (needed for register a single rule)
    // $services = $containerConfigurator->services();

    // register a single rule
    // $services->set(TypedPropertyRector::class);
};
