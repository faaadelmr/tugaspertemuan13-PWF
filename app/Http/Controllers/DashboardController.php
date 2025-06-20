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
        // Get users statistics
        $totalUsers = User::count();
        $recentUsers = User::select('username', 'id')
            ->orderBy('id', 'desc')
            ->limit(5)
            ->get();

        // Get relation statistics
        $relationStats = Relation::select('RELATION_DESC', 'RELATION_CODE')
            ->orderBy('RELATION_CODE')
            ->get();

        // Get document statistics
        $documentStats = Document::select('TYPE_NAME', 'TYPE_ID')
            ->orderBy('TYPE_ID')
            ->get();

        // Users by month (simulated data)
        $usersByMonth = [
            'January' => rand(5, 20),
            'February' => rand(5, 20),
            'March' => rand(5, 20),
            'April' => rand(5, 20),
            'May' => rand(5, 20),
            'June' => rand(5, 20),
        ];

        // Additional statistics
        $stats = [
            'total_users' => $totalUsers,
            'total_relations' => $relationStats->count(),
            'total_documents' => $documentStats->count(),
            'active_users' => $totalUsers, // Assuming all users are active
        ];

        return view('dashboard', compact(
            'totalUsers',
            'recentUsers',
            'relationStats',
            'documentStats',
            'usersByMonth',
            'stats'
        ));
    }

    public function getChartData()
    {
        $relationStats = Relation::select('RELATION_DESC', 'RELATION_CODE')->get();
        $documentStats = Document::select('TYPE_NAME', 'TYPE_ID')->get();
        $userStats = User::count();

        return response()->json([
            'relations' => $relationStats,
            'documents' => $documentStats,
            'users_total' => $userStats,
            'users_by_month' => [
                'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                'data' => [rand(5, 20), rand(5, 20), rand(5, 20), rand(5, 20), rand(5, 20), rand(5, 20)]
            ]
        ]);
    }

    public function getDetailedStats()
    {
        // Get more detailed statistics
        $relationsByType = Relation::selectRaw('
            CASE 
                WHEN RELATION_DESC IN ("Suami", "Istri") THEN "Spouse"
                WHEN RELATION_DESC IN ("Ayah", "Ibu") THEN "Parents"
                WHEN RELATION_DESC IN ("Anak", "Cucu") THEN "Children"
                WHEN RELATION_DESC IN ("Kakak", "Adik") THEN "Siblings"
                ELSE "Others"
            END as category,
            COUNT(*) as count
        ')
        ->groupBy('category')
        ->get();

        $documentsByCategory = Document::selectRaw('
            CASE 
                WHEN TYPE_NAME IN ("E-KTP", "ID WNA", "KIA") THEN "Identity"
                WHEN TYPE_NAME IN ("SIM", "Passport") THEN "License"
                WHEN TYPE_NAME IN ("Akta Lahir", "KK") THEN "Civil"
                ELSE "Others"
            END as category,
            COUNT(*) as count
        ')
        ->groupBy('category')
        ->get();

        return response()->json([
            'relations_by_type' => $relationsByType,
            'documents_by_category' => $documentsByCategory,
        ]);
    }
}