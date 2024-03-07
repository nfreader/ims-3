<?php

namespace App\Action\Comment;

use App\Action\Action;
use App\Action\ActionInterface;
use App\Domain\Comment\Service\EditCommentService;
use App\Exception\RedirectToSafetyException;
use DI\Attribute\Inject;
use Exception;
use Nyholm\Psr7\Response;

class EditCommentAction extends Action implements ActionInterface
{
    #[Inject]
    private EditCommentService $commentService;

    public function action(): Response
    {
        $user = $this->getUser();
        try {
            $comment = $this->commentService->editComment(
                $this->getArg('comment'),
                $this->getRequest()->getParsedBody(),
                $user
            );
            $this->addSuccessMessage("Your comment has been edited");
            return $this->redirectFor('event.view', [
                'incident' => $comment->getIncident(),
                'event' => $comment->getEvent()
            ]);
        } catch (RedirectWithMessage $e) {
            $this->addMessage($e->getMessage());
            return $this->redirectFor($e->getRoute(), $e->getArgs());
        }
    }
}
