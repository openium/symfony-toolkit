<?php

    namespace Openium\SymfonyToolKit;

    use Openium\SymfonyToolKit\DependencyInjection\OpeniumSfToolKitDIExtension;
    use Symfony\Component\HttpKernel\Bundle\Bundle;

    class OpeniumSfToolKit extends Bundle
    {
        public function getContainerExtension()
        {
            return new OpeniumSfToolKitDIExtension();
        }
    }
