<?php
declare(strict_types=1);

namespace Kraz\Entity;

use DateTimeInterface;

class IterableResult
{
    protected $from;
    protected $id;
    protected $data;

    public function getFrom(): ?DateTimeInterface
    {
        return $this->from;
    }

    public function setFrom(DateTimeInterface $from): self
    {
        $this->from = $from;
        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function getData(): ?iterable
    {
        return $this->data;
    }

    public function setData(iterable $data): self
    {
        $this->data = $data;
        return $this;
    }
}
