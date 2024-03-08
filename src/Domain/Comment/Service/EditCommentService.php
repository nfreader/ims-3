<?php

namespace App\Domain\Comment\Service;

use App\Domain\Comment\Data\Comment;
use App\Domain\Comment\Repository\CommentRepository;
use App\Domain\User\Data\User;
use App\Exception\RedirectWithMessageException;
use DI\Attribute\Inject;

class EditCommentService
{
    #[Inject]
    private CommentRepository $commentRepository;

    public function editComment(int $id, array $data, User $user): Comment|string
    {
        $comment = $this->commentRepository->getCommentById($id);
        //Check permissions here
        //
        //
        if($comment->getText() === $data['text'] || !$data['text']) {
            throw new RedirectWithMessageException("No changes were made to this comment", 'event.view', [
                'incident' => $comment->getIncident(),
                'event' => $comment->getEvent()
            ]);
        }
        $this->commentRepository->updateCommentRow($comment->getId(), $data['text'], $user->getId());
        $this->commentRepository->insertCommentEdit($comment->getId(), $data['text'], $comment->getText(), $user->getId());
        $comment->setText($data['text']);
        return $comment;
    }

}
