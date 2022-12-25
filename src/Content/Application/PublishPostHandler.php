<?php

namespace App\Content\Application;
use App\Content\Domain\Post;
use App\Content\Domain\PostId;
use App\Content\Domain\Events\PostPublished;
use App\Framework\Application\Event\EventBus;
use App\Content\Domain\PostRepositoryInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

final class PublishPostHandler implements MessageHandlerInterface
{

    public function __construct(
        private readonly PostRepositoryInterface $postRepository,
        private readonly EventBus $eventBus
    )
    {
    }

    public function __invoke(PublishPostCommand $publishPostCommand): void
    {
        $post = $this->postRepository->ofId(PostId::fromString($publishPostCommand->postId()));
        $post->publish();
        $this->postRepository->save($post);
        $this->eventBus->notifyAll($post->releaseEvents());
    }

}