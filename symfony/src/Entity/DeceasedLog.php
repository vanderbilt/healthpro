<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\DeceasedLogRepository")
 */
class DeceasedLog
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $participantId;

    /**
     * @ORM\Column(type="datetime")
     */
    private $insertTs;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $deceased_ts;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $hpoId;

    /**
     * @ORM\Column(type="string", length=2000, nullable=true)
     */
    private $emailNotified;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $deceasedStatus;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getParticipantId(): ?string
    {
        return $this->participantId;
    }

    public function setParticipantId(string $participantId): self
    {
        $this->participantId = $participantId;

        return $this;
    }

    public function getInsertTs(): ?\DateTimeInterface
    {
        return $this->insertTs;
    }

    public function setInsertTs(\DateTimeInterface $insertTs): self
    {
        $this->insertTs = $insertTs;

        return $this;
    }

    public function getDeceasedTs(): ?\DateTimeInterface
    {
        return $this->deceased_ts;
    }

    public function setDeceasedTs(?\DateTimeInterface $deceased_ts): self
    {
        $this->deceased_ts = $deceased_ts;

        return $this;
    }

    public function getHpoId(): ?string
    {
        return $this->hpoId;
    }

    public function setHpoId(?string $hpoId): self
    {
        $this->hpoId = $hpoId;

        return $this;
    }

    public function getEmailNotified(): ?string
    {
        return $this->emailNotified;
    }

    public function setEmailNotified(?string $emailNotified): self
    {
        $this->emailNotified = $emailNotified;

        return $this;
    }

    public function getDeceasedStatus(): ?string
    {
        return $this->deceasedStatus;
    }

    public function setDeceasedStatus(?string $deceasedStatus): self
    {
        $this->deceasedStatus = $deceasedStatus;

        return $this;
    }
}
