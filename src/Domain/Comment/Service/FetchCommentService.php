<?php

namespace App\Domain\Comment\Service;

use App\Domain\Comment\Data\Comment;
use App\Domain\Comment\Repository\CommentRepository;

class FetchCommentService
{
    public function __construct(
        private CommentRepository $commentRepository
    ) {

    }

    public function getComment(int $id): Comment
    {
        $comment = $this->commentRepository->getCommentById($id);
        $comment->setEdits($this->commentRepository->getCommentEdits($comment->getId()));
        return $comment;
    }

}
