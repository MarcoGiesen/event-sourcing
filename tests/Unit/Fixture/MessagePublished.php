<?php

declare(strict_types=1);

namespace Patchlevel\EventSourcing\Tests\Unit\Fixture;

use Patchlevel\EventSourcing\Aggregate\AggregateChanged;

final class MessagePublished extends AggregateChanged
{
    public static function raise(
        ProfileId $id,
        Message $message
    ): self {
        return self::occur(
            $id->toString(),
            [
                'message' => $message->toArray(),
            ]
        );
    }

    public function profileId(): ProfileId
    {
        return ProfileId::fromString($this->aggregateId);
    }

    public function message(): Message
    {
        return Message::fromArray($this->payload['message']);
    }
}
