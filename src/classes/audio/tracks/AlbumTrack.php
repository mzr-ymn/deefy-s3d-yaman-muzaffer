<?php
declare(strict_types=1);

namespace iutnc\deefy\audio\tracks;

class AlbumTrack extends AudioTrack {
    protected string $album;
    protected int $numero;
    protected ?string $artiste = null;
    protected ?int $annee = null;

    public function __construct(string $titre, string $fichierAudio, string $album = "", int $numero = 0) {
        parent::__construct($titre, $fichierAudio);
        $this->album = $album;
        $this->numero = $numero;
    }

    public function setArtiste(string $artiste): void {
        $this->artiste = $artiste;
    }

    public function setAnnee(int $annee): void {
        $this->annee = $annee;
    }
}