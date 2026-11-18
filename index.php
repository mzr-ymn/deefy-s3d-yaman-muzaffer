<?php
declare(strict_types=1);

require_once 'vendor/autoload.php';

use iutnc\deefy\repository\DeefyRepository;
use iutnc\deefy\dispatch\Dispatcher;
use iutnc\deefy\action\DefaultAction;
use iutnc\deefy\action\DisplayPlaylistAction;
use iutnc\deefy\action\AddPlaylistAction;
use iutnc\deefy\action\AddPodcastTrackAction;

session_start();

DeefyRepository::setConfig(__DIR__ . '/src/config/deefy.db.ini');

$dispatcher = new Dispatcher();

$dispatcher->addRoute('default', DefaultAction::class)
           ->addRoute('playlist', DisplayPlaylistAction::class)
           ->addRoute('add-playlist', AddPlaylistAction::class)
           ->addRoute('add-track', AddPodcastTrackAction::class);
           
$dispatcher->run();