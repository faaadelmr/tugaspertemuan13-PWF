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
        // Get total users
        $totalUsers = User::count();
        
        // Get recent users (using id instead of created_at since created_at column doesn't exist)
        $recentUsers = User::orderBy('id', 'desc')->take(10)->get();
        
        // Get relation statistics
        $relationStats = Relation::all();
        
        // Get document statistics
        $documentStats = Document::all();
        
        return view('dashboard', compact(
            'totalUsers',
            'recentUsers', 
            'relationStats',
            'documentStats'
        ));
    }
    
    public function getChartData()
    {
        // API endpoint for refreshing chart data
        $documentStats = Document::all();
        
        return response()->json([
            'documentStats' => $documentStats,
            'timestamp' => now()
        ]);
    }
    
    public function getDetailedStats()
    {
        // API endpoint for detailed statistics
        return response()->json([
            'totalUsers' => User::count(),
            'totalRelations' => Relation::count(),
            'totalDocuments' => Document::count(),
            'timestamp' => now()
        ]);
    }
}
