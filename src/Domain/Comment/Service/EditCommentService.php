<?php

namespace App\Domain\Comment\Service;

use App\Domain\Comment\Data\Comment;
use App\Domain\Comment\Repository\CommentRepository;
use App\Domain\User\Data\User;
use DI\Attribute\Inject;
use JustSteveKing\StatusCode\Http;
use Symfony\Component\HttpKernel\Exception\HttpException;

class EditCommentService
{
    #[Inject]
    private CommentRepository $commentRepository;

    public function editComment(Comment $comment, array $data, User $user): Comment
    {
        //Check permissions here
        //
        //
        if($comment->getText() === $data['text'] || !$data['text']) {
            throw new HttpException(Http::BAD_REQUEST->value, "No changes were made to this comment");
        }
        $this->commentRepository->updateCommentRow(
            id:$comment->getId(),
            newText:$data['text'],
            editor:$user->getId(),
            editorRole:$user->getActiveRole()?->getRoleId()
        );
        $this->commentRepository->insertCommentEdit(
            id: $comment->getId(),
            previous:$comment->getText(),
            current:$data['text'],
            editor:$user->getId(),
            role:$user->getActiveRole()?->getRoleId()
        );
        $comment->setText($data['text']);
        return $comment;
    }

}
