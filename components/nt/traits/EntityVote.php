<?php
declare(strict_types=1);

namespace nt\traits;


trait EntityVote
{
  private $voteTargetType = 3;

  public function voteUp(\WebUser $webUser): self
  {
    \nt\Vote::voteUp($this, $webUser);
    return $this;
  }

  public function removeVote(\WebUser $webUser): self
  {
    \nt\Vote::removeVote($this, $webUser);
    return $this;
  }

  public function voteDown(\WebUser $webUser): self
  {
    \nt\Vote::voteDown($this, $webUser);
    return $this;
  }

  public function vote(\WebUser $webUser, int $vote): self
  {
    \nt\Vote::vote($this, $webUser, $vote);
    return $this;
  }

  public function getVoteSum(): int
  {
    return \nt\Vote::getVoteSum($this);
  }

  public function getVote(): array
  {
    return \nt\Vote::getVote($this);
  }

  public function setVoteTargetType(int $targetType) : self
  {
    $this->voteTargetType = $targetType;
    return $this;
  }

  public function getVoteTargetType(): int
  {
    return $this->voteTargetType;
  }
}