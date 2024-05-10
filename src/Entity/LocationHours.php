<?php

namespace OHMedia\ContactBundle\Entity;

use App\Repository\LocationHoursRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LocationHoursRepository::class)]
class LocationHours
{
    public const DAY_SUNDAY = 'Su';
    public const DAY_MONDAY = 'Mo';
    public const DAY_TUESDAY = 'Tu';
    public const DAY_WEDNESDAY = 'We';
    public const DAY_THURSDAY = 'Th';
    public const DAY_FRIDAY = 'Fr';
    public const DAY_SATURDAY = 'Sa';
    public const DAY_HOLIDAY = 'Ho';

    public static function getDayChoices(): array
    {
        return [
            'Sunday' => self::DAY_SUNDAY,
            'Monday' => self::DAY_MONDAY,
            'Tuesday' => self::DAY_TUESDAY,
            'Wednesday' => self::DAY_WEDNESDAY,
            'Thursday' => self::DAY_THURSDAY,
            'Friday' => self::DAY_FRIDAY,
            'Saturday' => self::DAY_SATURDAY,
            'Holiday' => self::DAY_HOLIDAY,
        ];
    }

    public static function getDayMap(): array
    {
        return array_flip(self::getDayChoices());
    }

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    private ?bool $closed = null;

    #[ORM\Column(length: 2, nullable: true)]
    private ?string $day = null;

    #[ORM\Column(type: Types::TIME_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $open = null;

    #[ORM\Column(type: Types::TIME_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $close = null;

    #[ORM\Column(nullable: true)]
    private ?bool $next_day_close = null;

    #[ORM\ManyToOne(inversedBy: 'hours')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Location $location = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function isClosed(): ?bool
    {
        return $this->closed;
    }

    public function setClosed(?bool $closed): static
    {
        $this->closed = $closed;

        return $this;
    }

    public function getDay(): ?string
    {
        return $this->day;
    }

    public function setDay(string $day): static
    {
        $this->day = $day;

        return $this;
    }

    public function getOpen(): ?\DateTimeImmutable
    {
        return $this->open;
    }

    public function setOpen(?\DateTimeImmutable $open): static
    {
        $this->open = $open;

        return $this;
    }

    public function getClose(): ?\DateTimeImmutable
    {
        return $this->close;
    }

    public function setClose(?\DateTimeImmutable $close): static
    {
        $this->close = $close;

        return $this;
    }

    public function isNextDayClose(): ?bool
    {
        return $this->next_day_close;
    }

    public function setNextDayClose(?bool $next_day_close): static
    {
        $this->next_day_close = $next_day_close;

        return $this;
    }

    public function getLocation(): ?Location
    {
        return $this->location;
    }

    public function setLocation(?Location $location): static
    {
        $this->location = $location;

        return $this;
    }
}
