<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('public-dashboard', fn () => true);
