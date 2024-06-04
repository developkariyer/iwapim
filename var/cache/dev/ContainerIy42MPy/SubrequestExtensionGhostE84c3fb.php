<?php

namespace ContainerIy42MPy;
include_once \dirname(__DIR__, 4).'/vendor/pimcore/pimcore/lib/Twig/Extension/SubrequestExtension.php';

class SubrequestExtensionGhostE84c3fb extends \Pimcore\Twig\Extension\SubrequestExtension implements \Symfony\Component\VarExporter\LazyObjectInterface
{
    use \Symfony\Component\VarExporter\LazyGhostTrait;

    private const LAZY_OBJECT_PROPERTY_SCOPES = [
        "\0".'*'."\0".'incHelper' => [parent::class, 'incHelper', null],
        'incHelper' => [parent::class, 'incHelper', null],
    ];
}

// Help opcache.preload discover always-needed symbols
class_exists(\Symfony\Component\VarExporter\Internal\Hydrator::class);
class_exists(\Symfony\Component\VarExporter\Internal\LazyObjectRegistry::class);
class_exists(\Symfony\Component\VarExporter\Internal\LazyObjectState::class);

if (!\class_exists('SubrequestExtensionGhostE84c3fb', false)) {
    \class_alias(__NAMESPACE__.'\\SubrequestExtensionGhostE84c3fb', 'SubrequestExtensionGhostE84c3fb', false);
}
