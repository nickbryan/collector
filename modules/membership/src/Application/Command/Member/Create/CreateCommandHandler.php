<?php

declare(strict_types=1);

namespace Collector\Membership\Application\Command\Member\Create;

use Collector\Membership\Application\Command\Member\MemberAlreadyExistsException;
use Collector\Membership\Domain\Member\MemberFactory;
use Collector\Membership\Domain\Member\MemberRepository;
use Collector\Membership\Domain\Member\Specification\UniqueEmailSpecification;
use Collector\SharedKernel\Infrastructure\MessageBus\EventBus;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class CreateCommandHandler implements MessageHandlerInterface
{
    public function __construct(
        private EventBus $eventBus,
        private MemberFactory $memberFactory,
        private UniqueEmailSpecification $uniqueEmailSpecification,
        private MemberRepository $memberRepository,
    ) {}

    public function __invoke(CreateCommand $command): void
    {
        $member = $this->memberFactory->createFromPrimitives($command->email(), $command->password());

        if (! $this->uniqueEmailSpecification->isSatisfiedBy($member)) {
            throw new MemberAlreadyExistsException(
                sprintf('member with email %s already exists', $command->email()),
            );
        }

        $this->memberRepository->add($member);

        $this->eventBus->publish(...$member->recordedEvents());
    }
}
