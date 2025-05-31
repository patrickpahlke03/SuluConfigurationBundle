<?php

declare(strict_types=1);

/*
 * This file is part of the SuluConfigurationBundle.
 *
 * (c) Patrick Pahlke
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace PatLabs\SuluConfigurationBundle\Controller;

use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\RestBundle\View\ViewHandlerInterface;
use PatLabs\SuluConfigurationBundle\Services\ConfigKeyManipulator;
use PatLabs\SuluConfigurationBundle\Services\ConfigService;
use Sulu\Component\Rest\AbstractRestController;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
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

        if (!\is_string($configKeyPrefix)) {
            throw new BadRequestException('id must be type of string');
        }

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

        if (!\is_string($configKeyPrefix)) {
            throw new BadRequestException('id must be type of string');
        }

        $configArray = $this->configKeyManipulator->prefixArrayKeys(
            $request->getPayload()->all(),
            $configKeyPrefix,
        );

        $this->configService->saveConfigs($configArray);

        return $this->getAction($request);
    }
}
