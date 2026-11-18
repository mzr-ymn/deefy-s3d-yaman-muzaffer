<?php
declare(strict_types=1);

namespace iutnc\deefy\render;

use iutnc\deefy\audio\tracks\PodcastTrack;

class PodcastRenderer extends AudioTrackRenderer {
    protected function renderCompact(): string {
        $p = $this->track;
        return "<div class=\"podcast-compact\">
    <span>Podcast : {$p->titre} (par {$p->auteur})</span>
    <audio controls src=\"{$p->fichierAudio}\"></audio>
</div>";
    }

    protected function renderLong(): string {
        $p = $this->track;
        return "<div class=\"podcast-long\">
    <h2>Podcast : {$p->titre}</h2>
    <p>Auteur : {$p->auteur}</p>
    <p>Date : {$p->date}</p>
    <p>Genre : {$p->genre} | Durée : {$p->duree} s</p>
    <audio controls src=\"{$p->fichierAudio}\"></audio>
</div>";
    }
}