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
        return $this->commentRepository->getCommentById($id);
    }

}
