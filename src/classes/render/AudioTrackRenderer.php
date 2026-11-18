<?php
declare(strict_types=1);

namespace iutnc\deefy\render;

use iutnc\deefy\audio\tracks\AudioTrack;

abstract class AudioTrackRenderer implements Renderer {
    protected AudioTrack $track;

    public function __construct(AudioTrack $track) {
        $this->track = $track;
    }

    public function render(int $selector = self::COMPACT): string {
        return match ($selector) {
            self::COMPACT => $this->renderCompact(),
            self::LONG    => $this->renderLong(),
            default       => $this->renderCompact(),
        };
    }

    abstract protected function renderCompact(): string;
    abstract protected function renderLong(): string;
}