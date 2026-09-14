<?php

namespace App\Http\Controllers;

use App\Models\Service;

class ServiceController extends Controller
{
    public function byDirection(int $directionId)
    {
        $services = Service::query()
            ->where('direction_id', $directionId)
            ->orderBy('nom')
            ->get(['id', 'nom']);

        return response()->json($services);
    }
}
