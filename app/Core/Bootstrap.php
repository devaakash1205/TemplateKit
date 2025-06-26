<?php

use App\Core\Tracker;
use App\Core\Assets;

// Run tracking logic
Tracker::trackVisit();

// Load assets once and share globally
$GLOBALS['assets'] = Assets::load();
