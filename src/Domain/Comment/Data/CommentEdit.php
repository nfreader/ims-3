<?php

namespace App\Domain\Comment\Data;

use App\Domain\Role\Data\RoleBadge;
use App\Domain\User\Data\UserBadge;
use DateTimeImmutable;
use SebastianBergmann\Diff\Differ;
use SebastianBergmann\Diff\Output\UnifiedDiffOutputBuilder;

class CommentEdit
{
    public function __construct(
        private int $id,
        private int $comment,
        private string $previous,
        private string $current,
        private DateTimeImmutable $edited,
        private UserBadge $editor,
        private ?RoleBadge $editorRole = null
    ) {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getComment(): int
    {
        return $this->comment;
    }

    public function getPrevious(): string
    {
        return $this->previous;
    }

    public function getCurrent(): string
    {
        return $this->current;
    }

    public function getEdited(): DateTimeImmutable
    {
        return $this->edited;
    }

    public function getDiff()
    {
        $builder = new UnifiedDiffOutputBuilder('');
        $differ = new Differ($builder);
        return $differ->diff($this->previous, $this->current);
    }

    public function getEditor(): UserBadge
    {
        return $this->editor;
    }

    public function getEditorRole(): ?RoleBadge
    {
        return $this->editorRole;
    }
}
