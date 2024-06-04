<?php

namespace ContainerIy42MPy;
include_once \dirname(__DIR__, 4).'/vendor/scheb/2fa-bundle/Security/TwoFactor/Condition/TwoFactorConditionInterface.php';
include_once \dirname(__DIR__, 4).'/vendor/scheb/2fa-bundle/Security/TwoFactor/Condition/IpWhitelistCondition.php';

class IpWhitelistConditionGhostB63188f extends \Scheb\TwoFactorBundle\Security\TwoFactor\Condition\IpWhitelistCondition implements \Symfony\Component\VarExporter\LazyObjectInterface
{
    use \Symfony\Component\VarExporter\LazyGhostTrait;

    private const LAZY_OBJECT_PROPERTY_SCOPES = [
        "\0".parent::class."\0".'ipWhitelistProvider' => [parent::class, 'ipWhitelistProvider', null],
        'ipWhitelistProvider' => [parent::class, 'ipWhitelistProvider', null],
    ];
}

// Help opcache.preload discover always-needed symbols
class_exists(\Symfony\Component\VarExporter\Internal\Hydrator::class);
class_exists(\Symfony\Component\VarExporter\Internal\LazyObjectRegistry::class);
class_exists(\Symfony\Component\VarExporter\Internal\LazyObjectState::class);

if (!\class_exists('IpWhitelistConditionGhostB63188f', false)) {
    \class_alias(__NAMESPACE__.'\\IpWhitelistConditionGhostB63188f', 'IpWhitelistConditionGhostB63188f', false);
}
