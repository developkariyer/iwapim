<?php

namespace Blackbit\BlackbitIframePortletBundle;

use Pimcore\Extension\Bundle\AbstractPimcoreBundle;

class BlackbitIframePortletBundle extends AbstractPimcoreBundle
{
    public function getJsPaths()
    {
        return [
            '/bundles/blackbitiframeportlet/js/pimcore/startup.js'
        ];
    }
}