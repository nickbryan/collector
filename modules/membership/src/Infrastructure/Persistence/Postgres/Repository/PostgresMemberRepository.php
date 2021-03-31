<?php

declare(strict_types=1);

namespace Collector\Membership\Infrastructure\Persistence\Postgres\Repository;

use Collector\Membership\Domain\Member\Exception\MemberNotFoundException;
use Collector\Membership\Domain\Member\Member;
use Collector\Membership\Domain\Member\MemberFactory;
use Collector\Membership\Domain\Member\MemberRepository;
use Collector\Membership\Domain\Member\ValueObject\Email;
use Collector\Membership\Infrastructure\Persistence\Postgres\MembershipConnection;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Query\QueryBuilder;

final class PostgresMemberRepository implements MemberRepository
{
    public function __construct(private MembershipConnection $db, private MemberFactory $memberFactory) {}

    /**
     * @throws Exception
     */
    public function add(Member $member): void
    {
        $sql = <<<SQL
            INSERT INTO members
                (uuid, email, password, created_at, updated_at)
            VALUES
                (:uuid, :email, :password, NOW(), NOW())
            ;
        SQL;

        $this->db->executeStatement($sql, [
            'uuid' => $member->identifier(),
            'email' => $member->email(),
            'password' => $member->password(),
        ]);
    }

    /**
     * @throws MemberNotFoundException
     * @throws Exception
     */
    public function whereEmailIs(Email $email): Member
    {
        return $this->fetch(
            static fn (QueryBuilder $qb): QueryBuilder => $qb
                ->where('m.email = :email')->setParameter('email', $email),
            sprintf('member not found with email: %s', $email),
        );
    }

    /**
     * @param callable(QueryBuilder): QueryBuilder $constraints
     *
     * @throws MemberNotFoundException
     * @throws Exception
     */
    private function fetch(callable $constraints, string $notFoundMessage): Member
    {
        $query = $this->db->createQueryBuilder()
            ->select('m.uuid as identifier', 'm.email as email', 'm.password as password')
            ->from('members', 'm');

        $query = $constraints($query);

        assert($query instanceof QueryBuilder);

        $row = $this->db->fetchAssociative($query->getSQL(), $query->getParameters());

        if ($row === false) {
            throw new MemberNotFoundException($notFoundMessage);
        }

        return $this->memberFactory->restoreFromPrimitives(
            $row['identifier'],
            $row['email'],
            $row['password'],
        );
    }
}