<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Relation;
use App\Models\Document;

class DashboardController extends Controller
{
    public function index()
    {
        // Get total users count
        $totalUsers = User::count();
        
        // Get user statistics (usernames for grouping by first letter)
        $userStats = User::select('username')->get();
        
        // Get relation statistics
        $relationStats = Relation::select('RELATION_DESC')->get();
        
        // Get document statistics
        $documentStats = Document::select('TYPE_NAME', 'TYPE_ID')->get();
        
        return view('dashboard', compact(
            'totalUsers',
            'userStats', 
            'relationStats', 
            'documentStats'
        ));
    }
    
    // API endpoint for refreshing chart data
    public function getChartData()
    {
        return response()->json([
            'totalUsers' => User::count(),
            'userStats' => User::select('username')->get(),
            'relationStats' => Relation::select('RELATION_DESC')->get(),
            'documentStats' => Document::select('TYPE_NAME', 'TYPE_ID')->get(),
            'timestamp' => now()
        ]);
    }
}
