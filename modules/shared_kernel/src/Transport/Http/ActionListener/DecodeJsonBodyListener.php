<?php

declare(strict_types=1);

namespace Collector\SharedKernel\Transport\Http\ActionListener;

use JsonException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

final class DecodeJsonBodyListener
{
    private const CONTENT_TYPE = 'json';

    public function __invoke(ControllerEvent $event): void
    {
        $request = $event->getRequest();

        if (! $this->canDecode($request)) {
            return;
        }

        try {
            $data = json_decode(json: $request->getContent(), associative: true, flags: JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            throw new BadRequestHttpException(sprintf('invalid JSON body: %s', $e->getMessage()));
        }

        $request->request->replace($data);
    }

    private function canDecode(Request $request): bool
    {
        return ($request->isMethod(Request::METHOD_POST) || $request->isMethod(Request::METHOD_PUT)) &&
            $request->getContentType() === self::CONTENT_TYPE &&
            $request->getContent() !== '';
    }
}
