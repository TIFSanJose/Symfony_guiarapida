<?php
namespace App\Message;

class CommentMessage
{
    private $id;
    private $context;
    private $reviewUrl;

    // public function __construct(int $id, array $context = [])
    public function __construct(int $id, string $reviewUrl, array $context = [])
    {
        $this->id = $id;
        $this->context = $context;
        $this->reviewUrl = $reviewUrl;
    }

    public function getReviewUrl(): string
    {
        return $this->reviewUrl;
    }    

    public function getId(): int
    {
        return $this->id;
    }

    public function getContext(): array
    {
        return $this->context;
    }
}
