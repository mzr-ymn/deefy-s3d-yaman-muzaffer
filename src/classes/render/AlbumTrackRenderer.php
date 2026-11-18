<?php
declare(strict_types=1);

namespace iutnc\deefy\render;

use iutnc\deefy\audio\tracks\AlbumTrack;

class AlbumTrackRenderer extends AudioTrackRenderer {
    protected function renderCompact(): string {
        $t = $this->track;
        return "<div class=\"track-compact\">
    <span>{$t->numero}. {$t->titre} - {$t->artiste}</span>
    <audio controls src=\"{$t->fichierAudio}\"></audio>
</div>";
    }

    protected function renderLong(): string {
        $t = $this->track;
        return "<div class=\"track-long\">
    <h2>{$t->numero}. {$t->titre}</h2>
    <p>Artiste : {$t->artiste}</p>
    <p>Album : {$t->album} ({$t->annee})</p>
    <p>Genre : {$t->genre} | Durée : {$t->duree} s</p>
    <audio controls src=\"{$t->fichierAudio}\"></audio>
</div>";
    }
}