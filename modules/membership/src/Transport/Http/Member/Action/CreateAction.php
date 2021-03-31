<?php

declare(strict_types=1);

namespace Collector\Membership\Transport\Http\Member\Action;

use Collector\Membership\Application\Command\Member\Create\CreateCommand;
use Collector\Membership\Application\Query\Member\Member;
use Collector\Membership\Application\Query\Member\MemberByEmailQuery;
use Collector\Membership\Transport\Http\Member\Request\CreateRequest;
use Collector\SharedKernel\Infrastructure\MessageBus\CommandBus;
use Collector\SharedKernel\Infrastructure\MessageBus\QueryBus;
use Symfony\Component\HttpFoundation\JsonResponse;

final class CreateAction
{
    public function __construct(private CommandBus $commandBus, private QueryBus $queryBus) {}

    public function __invoke(CreateRequest $request): JsonResponse
    {
        $this->commandBus->execute(new CreateCommand($request->email(), $request->password()));

        $member = $this->queryBus->query(new MemberByEmailQuery($request->email()));
        assert($member instanceof Member);

        return new JsonResponse(['identifier' => $member->identifier()->toString()]);
    }
}