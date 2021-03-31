<?php

declare(strict_types=1);

namespace Collector\Membership\Infrastructure\Persistence\Postgres\Query;

use Collector\Membership\Infrastructure\Persistence\Postgres\MembershipConnection;
use Doctrine\DBAL\Exception;

final class MemberDataByEmail
{
    /** @var array<string, mixed> */
    private array $data;

    public function __construct(private MembershipConnection $db) {}

    public function hasData(): bool
    {
        return isset($this->data);
    }

    /**
     * @throws Exception
     */
    public function fetch(string $email): void
    {
        $query = $this->db->createQueryBuilder()
            ->select('m.uuid as identifier', 'm.email as email')
            ->from('members', 'm')
            ->where('m.email = :mail')
            ->setParameter('email', $email);

        $result = $this->db->fetchAssociative($query->getSQL(), $query->getParameters());

        if ($result === false) {
            return;
        }

        $this->data = $result;
    }

    public function identifier(): string
    {
        return $this->data['identifier'];
    }

    public function email(): string
    {
        return $this->data['email'];
    }
}
