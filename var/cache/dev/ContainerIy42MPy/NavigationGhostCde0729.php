<?php

namespace ContainerIy42MPy;
include_once \dirname(__DIR__, 4).'/vendor/pimcore/pimcore/lib/Twig/Extension/Templating/Navigation.php';

class NavigationGhostCde0729 extends \Pimcore\Twig\Extension\Templating\Navigation implements \Symfony\Component\VarExporter\LazyObjectInterface
{
    use \Symfony\Component\VarExporter\LazyGhostTrait;

    private const LAZY_OBJECT_PROPERTY_SCOPES = [
        "\0".'*'."\0".'charset' => [parent::class, 'charset', null],
        "\0".parent::class."\0".'builder' => [parent::class, 'builder', null],
        "\0".parent::class."\0".'rendererLocator' => [parent::class, 'rendererLocator', null],
        'builder' => [parent::class, 'builder', null],
        'charset' => [parent::class, 'charset', null],
        'rendererLocator' => [parent::class, 'rendererLocator', null],
    ];
}

// Help opcache.preload discover always-needed symbols
class_exists(\Symfony\Component\VarExporter\Internal\Hydrator::class);
class_exists(\Symfony\Component\VarExporter\Internal\LazyObjectRegistry::class);
class_exists(\Symfony\Component\VarExporter\Internal\LazyObjectState::class);

if (!\class_exists('NavigationGhostCde0729', false)) {
    \class_alias(__NAMESPACE__.'\\NavigationGhostCde0729', 'NavigationGhostCde0729', false);
}
