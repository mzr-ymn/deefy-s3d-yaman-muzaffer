<?php
declare(strict_types=1);

namespace iutnc\deefy\audio\lists;

use iutnc\deefy\audio\tracks\AudioTrack;
use iutnc\deefy\exception\InvalidPropertyNameException;

class AudioList {
    protected string $nom;
    protected int $nbPistes = 0;
    protected int $dureeTotale = 0;
    protected array $pistes = [];

    public function __construct(string $nom, array $pistes = []) {
        $this->nom = $nom;
        $this->pistes = $pistes;
        $this->nbPistes = count($pistes);

        foreach ($pistes as $piste) {
            $this->dureeTotale += (int)$piste->duree;
        }
    }

    public function __get(string $property): mixed {
        if (property_exists($this, $property)) {
            return $this->$property;
        }
        throw new InvalidPropertyNameException("invalid property: {$property}");
    }
}