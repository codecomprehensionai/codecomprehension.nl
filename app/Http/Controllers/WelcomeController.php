<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WelcomeController
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        // TODO: show nicer error
        return 'Open the assignment in canvas.';
    }
}
