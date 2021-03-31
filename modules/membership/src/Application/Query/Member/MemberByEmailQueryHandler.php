<?php

declare(strict_types=1);

namespace Collector\Membership\Application\Query\Member;

use Collector\Membership\Infrastructure\Persistence\Postgres\Query\MemberDataByEmail;
use Doctrine\DBAL\Exception;
use Ramsey\Uuid\Rfc4122\UuidV4;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class MemberByEmailQueryHandler implements MessageHandlerInterface
{
    public function __construct(private MemberDataByEmail $memberDataByEmail) {}

    /**
     * @throws Exception
     */
    public function __invoke(MemberByEmailQuery $query): Member
    {
        $this->memberDataByEmail->fetch($query->email());

        if (! $this->memberDataByEmail->hasData()) {
            throw new MemberNotFoundException(
                sprintf('account not found with email %s', $query->email()),
            );
        }

        return new Member(
            UuidV4::fromString($this->memberDataByEmail->identifier()),
            $this->memberDataByEmail->email(),
        );
    }
}