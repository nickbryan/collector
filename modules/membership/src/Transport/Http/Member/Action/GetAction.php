<?php

declare(strict_types=1);

namespace Collector\Membership\Transport\Http\Member\Action;

use Collector\Membership\Transport\Http\Member\Request\GetRequest;
use Symfony\Component\HttpFoundation\JsonResponse;

final class GetAction
{
    public function __invoke(GetRequest $request): JsonResponse
    {
        return new JsonResponse(['member_id' => $request->memberId()]);
    }
}