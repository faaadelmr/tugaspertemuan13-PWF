<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Statistics</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50 min-h-screen">
    <!-- Header -->
    <header class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-6">
                <div class="flex items-center">
                    <h1 class="text-3xl font-bold text-gray-900">
                        <i class="fas fa-chart-bar text-blue-600 mr-3"></i>
                        Dashboard Statistics
                    </h1>
                </div>
                <div class="text-sm text-gray-500">
                    <i class="fas fa-calendar-alt mr-1"></i>
                    {{ date('F d, Y') }}
                </div>
            </div>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <!-- Total Users Card -->
            <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-blue-500">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-users text-3xl text-blue-500"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total Users</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $totalUsers }}</p>
                    </div>
                </div>
            </div>

            <!-- Total Relations Card -->
            <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-green-500">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-sitemap text-3xl text-green-500"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total Relations</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $relationStats->count() }}</p>
                    </div>
                </div>
            </div>

            <!-- Total Documents Card -->
            <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-purple-500">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-file-alt text-3xl text-purple-500"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Document Types</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $documentStats->count() }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Document Types by First Letter Chart -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-chart-pie text-purple-500 mr-2"></i>
                    Document Types Distribution by First Letter
                </h3>
            </div>
            <div class="h-96 flex justify-center">
                <canvas id="documentsByLetterChart" style="max-width: 600px;"></canvas>
            </div>
        </div>

        <!-- Data Tables Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Recent Users Table -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">
                    <i class="fas fa-user-clock text-blue-500 mr-2"></i>
                    Recent Users
                </h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Username</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($recentUsers as $user)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ $user->username }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                        Active
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Relations List -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">
                    <i class="fas fa-list text-green-500 mr-2"></i>
                    Relation Types
                </h3>
                <div class="space-y-2 max-h-64 overflow-y-auto">
                    @foreach($relationStats->take(10) as $relation)
                    <div class="flex items-center justify-between p-2 bg-gray-50 rounded">
                        <span class="text-sm text-gray-700">{{ $relation->RELATION_DESC }}</span>
                        <span class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded">
                            ID: {{ $relation->RELATION_CODE }}
                        </span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </main>

    <script>
        // Chart.js configuration
        Chart.defaults.responsive = true;
        Chart.defaults.maintainAspectRatio = false;

        // Document Types by First Letter Pie Chart
        const documentsByLetterCtx = document.getElementById('documentsByLetterChart').getContext('2d');
        
        // Process document data to group by first letter
        const documentTypes = {!! json_encode($documentStats->pluck('TYPE_NAME')) !!};
        const documentCounts = {!! json_encode($documentStats->pluck('TYPE_ID')) !!};
        
        // Group documents by first letter
        const letterGroups = {};
        documentTypes.forEach((typeName, index) => {
            const firstLetter = typeName.charAt(0).toUpperCase();
            if (!letterGroups[firstLetter]) {
                letterGroups[firstLetter] = 0;
            }
            letterGroups[firstLetter] += parseInt(documentCounts[index]) || 1;
        });

        // Sort letters alphabetically
        const sortedLetters = Object.keys(letterGroups).sort();
        const sortedCounts = sortedLetters.map(letter => letterGroups[letter]);

        // Generate colors for each letter
        const colors = [
            '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF',
            '#FF9F40', '#FF6384', '#C9CBCF', '#4BC0C0', '#FF6384',
            '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF', '#FF9F40',
            '#FF6384', '#C9CBCF', '#4BC0C0', '#36A2EB', '#FFCE56',
            '#9966FF', '#FF9F40', '#FF6384', '#C9CBCF', '#4BC0C0',
            '#36A2EB'
        ];

        const documentsByLetterChart = new Chart(documentsByLetterCtx, {
            type: 'pie',
            data: {
                labels: sortedLetters.map(letter => `Letter "${letter}"`),
                datasets: [{
                    label: 'Document Types',
                    data: sortedCounts,
                    backgroundColor: colors.slice(0, sortedLetters.length),
                    borderColor: '#ffffff',
                    borderWidth: 2,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'right',
                        labels: {
                            padding: 20,
                            usePointStyle: true,
                            font: {
                                size: 12
                            }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.parsed;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = ((value / total) * 100).toFixed(1);
                                return `${label}: ${value} types (${percentage}%)`;
                            }
                        }
                    }
                },
                layout: {
                    padding: {
                        left: 20,
                        right: 20,
                        top: 20,
                        bottom: 20
                    }
                }
            }
        });

        // Add some animation and interactivity
        document.addEventListener('DOMContentLoaded', function() {
            // Animate counter numbers
            const counters = document.querySelectorAll('.text-2xl.font-bold');
            counters.forEach(counter => {
                const target = parseInt(counter.textContent);
                let current = 0;
                const increment = target / 50;
                const timer = setInterval(() => {
                    current += increment;
                    if (current >= target) {
                        counter.textContent = target;
                        clearInterval(timer);
                    } else {
                        counter.textContent = Math.floor(current);
                    }
                }, 30);
            });

            // Add hover effects to cards
            const cards = document.querySelectorAll('.bg-white.rounded-lg.shadow-md');
            cards.forEach(card => {
                card.addEventListener('mouseenter', function() {
                    this.classList.add('transform', 'scale-105', 'transition-transform', 'duration-200');
                });
                card.addEventListener('mouseleave', function() {
                    this.classList.remove('transform', 'scale-105', 'transition-transform', 'duration-200');
                });
            });
        });

        // Refresh data every 30 seconds
        setInterval(function() {
            fetch('/api/chart-data')
                .then(response => response.json())
                .then(data => {
                    // Update charts with new data if needed
                    console.log('Data refreshed:', data);
                })
                .catch(error => console.error('Error refreshing data:', error));
        }, 30000);
    </script>

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-200 mt-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex justify-between items-center">
                <p class="text-sm text-gray-500">
                    © {{ date('Y') }} Dashboard Statistics. Built with Laravel & Tailwind CSS.
                </p>
                <div class="flex space-x-4">
                    <a href="#" class="text-gray-400 hover:text-gray-500">
                        <i class="fab fa-github"></i>
                    </a>
                    <a href="#" class="text-gray-400 hover:text-gray-500">
                        <i class="fab fa-twitter"></i>
                    </a>
                </div>
            </div>
        </div>
    </footer>
</body>
</html>
