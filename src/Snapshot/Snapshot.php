<?php

declare(strict_types=1);

namespace Patchlevel\EventSourcing\Snapshot;

use Patchlevel\EventSourcing\Aggregate\SnapshotableAggregateRoot;

final class Snapshot
{
    /** @var class-string<SnapshotableAggregateRoot> */
    private string $aggregate;

    private string $id;

    private int $playhead;

    /** @var array<string, mixed> */
    private array $payload;

    /**
     * @param class-string<SnapshotableAggregateRoot> $aggregate
     * @param array<string, mixed>                    $payload
     */
    public function __construct(string $aggregate, string $id, int $playhead, array $payload)
    {
        $this->aggregate = $aggregate;
        $this->id = $id;
        $this->playhead = $playhead;
        $this->payload = $payload;
    }

    /**
     * @return class-string<SnapshotableAggregateRoot>
     */
    public function aggregate(): string
    {
        return $this->aggregate;
    }

    public function id(): string
    {
        return $this->id;
    }

    public function playhead(): int
    {
        return $this->playhead;
    }

    /**
     * @return array<string, mixed>
     */
    public function payload(): array
    {
        return $this->payload;
    }
}
