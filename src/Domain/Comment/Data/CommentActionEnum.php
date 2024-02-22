<?php

namespace App\Domain\Comment\Data;

enum CommentActionEnum: string
{
    case COMMENT = 'comment';
    case PREPEND = 'prepend';
    case APPEND = 'append';
    case REPLACE = 'replace';

    public function getTitle(): string
    {
        return match($this) {
            CommentActionEnum::PREPEND => "This comment was prepended to this event",
            CommentActionEnum::APPEND => "This comment was appended to this event",
            CommentActionEnum::REPLACE => "This comment replaced the previous event description",
            default => "This is a comment"
        };
    }

    public function getPastTense(): string
    {
        return match($this) {
            CommentActionEnum::PREPEND => "prepended",
            CommentActionEnum::APPEND => "appended",
            CommentActionEnum::REPLACE => "replaced",
            default => "This is a comment"
        };
    }

    public function showTag(): bool
    {
        return match($this) {
            default => true,
            CommentActionEnum::COMMENT => false
        };
    }
}
