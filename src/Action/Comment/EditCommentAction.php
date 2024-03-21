<?php

namespace App\Action\Comment;

use App\Action\Action;
use App\Action\ActionInterface;
use App\Domain\Comment\Service\EditCommentService;
use App\Domain\Comment\Service\FetchCommentService;
use App\Exception\RedirectToSafetyException;
use DI\Attribute\Inject;
use Exception;
use Nyholm\Psr7\Response;

class EditCommentAction extends Action implements ActionInterface
{
    #[Inject]
    private EditCommentService $commentService;

    #[Inject]
    private FetchCommentService $fetchComment;

    public function action(): Response
    {
        $user = $this->getUser();
        $comment = $this->fetchComment->getComment($this->getArg('comment'));
        try {
            $comment = $this->commentService->editComment(
                $comment,
                $this->getRequest()->getParsedBody(),
                $user
            );
            $this->addSuccessMessage("Your comment has been edited");
            return $this->redirectFor('event.view', [
                'incident' => $comment->getIncident(),
                'event' => $comment->getEvent()
            ]);
        } catch (Exception $e) {
            $this->addMessage($e->getMessage());
            return $this->redirectFor('event.view', [
                'incident' => $comment->getIncident(),
                'event' => $comment->getEvent()
            ]);
        }
    }
}
