<?php
declare(strict_types=1);

namespace iutnc\deefy\audio\tracks;

use iutnc\deefy\exception\InvalidPropertyNameException;
use iutnc\deefy\exception\InvalidPropertyValueException;

abstract class AudioTrack {
    protected ?int $id = null;
    protected string $titre;
    protected string $fichierAudio;
    protected ?string $genre = null;
    protected ?int $duree = null;

    public function __construct(string $titre, string $fichierAudio) {
        $this->titre = $titre;
        $this->fichierAudio = $fichierAudio;
    }

    public function __get(string $property): mixed {
        if (property_exists($this, $property)) {
            return $this->$property;
        }
        throw new InvalidPropertyNameException("invalid property: {$property}");
    }

    public function setId(int $id): void {
        $this->id = $id;
    }

    public function setGenre(string $genre): void {
        $this->genre = $genre;
    }

    public function setDuree(int $duree): void {
        if ($duree < 1) {
            throw new InvalidPropertyValueException("valeur invalide pour la duree : {$duree}");
        }
        $this->duree = $duree;
    }

    public function __toString(): string {
        return (string)json_encode(get_object_vars($this));
    }
}