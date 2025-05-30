<?php

declare(strict_types=1);

namespace Patt\SuluConfigurationBundle\Controller;

use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\RestBundle\View\ViewHandlerInterface;
use Patt\SuluConfigurationBundle\Services\ConfigKeyManipulator;
use Patt\SuluConfigurationBundle\Services\ConfigService;
use Sulu\Component\Rest\AbstractRestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class ConfigController extends AbstractRestController implements ClassResourceInterface
{
    public function __construct(
        ViewHandlerInterface $viewHandler,
        private readonly ConfigService $configService,
        private readonly ConfigKeyManipulator $configKeyManipulator,
        ?TokenStorageInterface $tokenStorage = null,
    ) {
        parent::__construct($viewHandler, $tokenStorage);
    }

    public function getAction(Request $request): Response
    {
        $configKeyPrefix = $request->get('id');

        $configs = $this->configService->getConfigsByPrefix($configKeyPrefix);

        return $this->handleView(
            $this->view(
                $this->configKeyManipulator->removePrefixFromArrayKeys($configs, $configKeyPrefix),
            ),
        );
    }

    public function putAction(Request $request): Response
    {
        $configKeyPrefix = $request->get('id');
        $configArray = $this->configKeyManipulator->prefixArrayKeys(
            $request->getPayload()->all(),
            $configKeyPrefix,
        );

        $this->configService->saveConfigs($configArray);

        return $this->getAction($request);
    }
}
