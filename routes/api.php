<?php

use App\Http\Controllers\CompetitionController;

Route::post('/validate-competition', [CompetitionController::class, 'validateCompetition']);
