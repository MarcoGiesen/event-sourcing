<?php

declare(strict_types=1);

namespace Patchlevel\EventSourcing\Aggregate;

use DateTimeImmutable;

use function is_string;
use function is_subclass_of;
use function json_decode;
use function json_encode;

use const JSON_THROW_ON_ERROR;

abstract class AggregateChanged
{
    protected string $aggregateId;

    /** @var array<string, mixed> */
    protected array $payload;
    private ?int $playhead;
    private ?DateTimeImmutable $recordedOn;

    /**
     * @param array<string, mixed> $payload
     */
    final private function __construct(string $aggregateId, array $payload = [])
    {
        $this->aggregateId = $aggregateId;
        $this->payload = $payload;
        $this->playhead = null;
        $this->recordedOn = null;
    }

    public function aggregateId(): string
    {
        return $this->aggregateId;
    }

    public function playhead(): ?int
    {
        return $this->playhead;
    }

    public function recordedOn(): ?DateTimeImmutable
    {
        return $this->recordedOn;
    }

    /**
     * @return array<string, mixed>
     */
    public function payload(): array
    {
        return $this->payload;
    }

    /**
     * @param array<string, mixed> $payload
     *
     * @return static
     */
    protected static function occur(string $aggregateId, array $payload = []): self
    {
        return new static($aggregateId, $payload);
    }

    public function recordNow(int $playhead): self
    {
        $event = new static($this->aggregateId, $this->payload);
        $event->playhead = $playhead;
        $event->recordedOn = new DateTimeImmutable();

        return $event;
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function deserialize(array $data): self
    {
        $class = $data['event'];

        if (!is_string($class)) {
            throw new AggregateException();
        }

        if (!is_subclass_of($class, self::class)) {
            throw new AggregateException();
        }

        /** @var array<string, mixed> $payload */
        $payload = json_decode((string)$data['payload'], true, 512, JSON_THROW_ON_ERROR);

        $event = new $class((string)$data['aggregateId'], $payload);
        $event->playhead = (int)$data['playhead'];

        $recordedOn = $data['recordedOn'];

        if (!$recordedOn instanceof DateTimeImmutable) {
            throw new AggregateException();
        }

        $event->recordedOn = $recordedOn;

        return $event;
    }

    /**
     * @return array<string, mixed>
     */
    public function serialize(): array
    {
        return [
            'aggregateId' => $this->aggregateId,
            'playhead' => $this->playhead,
            'event' => static::class,
            'payload' => json_encode($this->payload, JSON_THROW_ON_ERROR),
            'recordedOn' => $this->recordedOn,
        ];
    }
}
