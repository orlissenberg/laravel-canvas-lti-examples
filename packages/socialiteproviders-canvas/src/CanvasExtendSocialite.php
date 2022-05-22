<?php

namespace Orlissenberg\SocialiteProviders\Canvas;

use SocialiteProviders\Manager\SocialiteWasCalled;

class CanvasExtendSocialite
{
    /**
     * Register the provider.
     */
    public function handle(SocialiteWasCalled $socialiteWasCalled)
    {
        $socialiteWasCalled->extendSocialite('canvas', Provider::class);
    }
}