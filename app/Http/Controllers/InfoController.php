<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InfoController extends Controller
{
    //
    public function serverInfo()
    {
        return response()->json([
            'php_version' => phpversion()
        ]);
    }

    public function clientInfo(Request $request)
    {
        return response()->json([
            'ip' => $request->ip(),
            'user_agent' => $request->header('User-Agent')
        ]);
    }

    public function databaseInfo()
    {
        // Здесь добавьте логику для получения данных о базе данных
        return response()->json([
            'database' => DB::connection()->getDatabaseName()
        ]);
    }

}

