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
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
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

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">
                        <i class="fas fa-chart-donut text-green-500 mr-2"></i>
                        Statistik Jenis Relation Berdasarkan Huruf Awal
                    </h3>
                </div>
                <div class="h-80 relative">
                    <canvas id="relationsByLetterChart"></canvas>
                    <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                        <div class="text-center">
                            <div class="text-2xl font-bold text-gray-900" id="relationsTotalCount">0</div>
                            <div class="text-sm text-gray-500">Total Relations</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">
                        <i class="fas fa-chart-pie text-purple-500 mr-2"></i>
                        Statistik Jenis Document berdasarkan Huruf Awal
                    </h3>
                </div>
                <div class="h-80">
                    <canvas id="documentsByLetterChart"></canvas>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-8 mb-8">
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">
                        <i class="fas fa-chart-bar text-blue-500 mr-2"></i>
                        Statistik User Berdasarkan Huruf Awal Username
                    </h3>
                    <div class="text-sm text-gray-500">
                        <span id="usersTotalDisplay">Total: {{ $totalUsers }} users</span>
                    </div>
                </div>
                <div class="h-96">
                    <canvas id="usersByLetterChart"></canvas>
                </div>
            </div>
        </div>
    </main>

    <script>
        Chart.defaults.responsive = true;
        Chart.defaults.maintainAspectRatio = false;

        const userNames = {!! json_encode($userStats->pluck('username')) !!};
        const totalUsersCount = {{ $totalUsers }};

        const userLetterGroups = {};
        userNames.forEach((userName) => {
            const firstLetter = userName.charAt(0).toUpperCase();
            if (!userLetterGroups[firstLetter]) {
                userLetterGroups[firstLetter] = 0;
            }
            userLetterGroups[firstLetter] += 1;
        });

        const sortedUserLetters = Object.keys(userLetterGroups).sort();
        const sortedUserCounts = sortedUserLetters.map(letter => userLetterGroups[letter]);

        const userBarColors = [
            '#3B82F6', '#1D4ED8', '#1E40AF', '#1E3A8A', '#312E81',
            '#60A5FA', '#3B82F6', '#2563EB', '#1D4ED8', '#1E40AF',
            '#93C5FD', '#60A5FA', '#3B82F6', '#2563EB', '#1D4ED8',
            '#DBEAFE', '#93C5FD', '#60A5FA', '#3B82F6', '#2563EB'
        ];

        const usersByLetterCtx = document.getElementById('usersByLetterChart').getContext('2d');
        const usersByLetterChart = new Chart(usersByLetterCtx, {
            type: 'bar',
            data: {
                labels: sortedUserLetters,
                datasets: [{
                    label: 'Jumlah User',
                    data: sortedUserCounts,
                    backgroundColor: userBarColors.slice(0, sortedUserLetters.length),
                    borderColor: userBarColors.slice(0, sortedUserLetters.length),
                    borderWidth: 2,
                    borderRadius: 8,
                    borderSkipped: false
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            font: {
                                size: 12,
                                weight: 'bold'
                            },
                            color: '#374151'
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        titleColor: '#ffffff',
                        bodyColor: '#ffffff',
                        borderColor: '#3B82F6',
                        borderWidth: 1,
                        callbacks: {
                            label: function(context) {
                                const value = context.parsed.y;
                                const percentage = totalUsersCount > 0 ? ((value / totalUsersCount) * 100).toFixed(1) : 0;
                                return `Huruf "${context.label}": ${value} users (${percentage}%)`;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1,
                            color: '#6B7280',
                            font: {
                                size: 11
                            }
                        },
                        grid: {
                            color: '#E5E7EB',
                            drawBorder: false
                        },
                        title: {
                            display: true,
                            text: 'Jumlah User',
                            color: '#374151',
                            font: {
                                size: 12,
                                weight: 'bold'
                            }
                        }
                    },
                    x: {
                        ticks: {
                            color: '#6B7280',
                            font: {
                                size: 11,
                                weight: 'bold'
                            }
                        },
                        grid: {
                            display: false
                        },
                        title: {
                            display: true,
                            text: 'Huruf Awal Username',
                            color: '#374151',
                            font: {
                                size: 12,
                                weight: 'bold'
                            }
                        }
                    }
                },
                animation: {
                    duration: 2000,
                    easing: 'easeInOutQuart'
                }
            }
        });

        const relationsByLetterCtx = document.getElementById('relationsByLetterChart').getContext('2d');
        const relationTypes = {!! json_encode($relationStats->pluck('RELATION_DESC')) !!};
        
        const relationLetterGroups = {};
        const allowedLetters = ['I', 'J', 'U'];
        
        relationTypes.forEach((relationDesc) => {
            const firstLetter = relationDesc.charAt(0).toUpperCase();
            if (allowedLetters.includes(firstLetter)) {
                if (!relationLetterGroups[firstLetter]) {
                    relationLetterGroups[firstLetter] = 0;
                }
                relationLetterGroups[firstLetter] += 1;
            }
        });

        const sortedRelationLetters = ['I', 'J', 'U'];
        const sortedRelationCounts = sortedRelationLetters.map(letter => relationLetterGroups[letter] || 0);
        const totalRelationsFiltered = sortedRelationCounts.reduce((a, b) => a + b, 0);
        
        document.getElementById('relationsTotalCount').textContent = totalRelationsFiltered;

        const relationColors = ['#10B981', '#059669', '#047857'];
        const hasRelationData = sortedRelationCounts.some(count => count > 0);

        if (hasRelationData) {
            const relationsByLetterChart = new Chart(relationsByLetterCtx, {
                type: 'doughnut',
                data: {
                    labels: sortedRelationLetters.map(letter => `Letter "${letter}"`),
                    datasets: [{
                        label: 'Relation Types',
                        data: sortedRelationCounts,
                        backgroundColor: relationColors,
                        borderColor: '#ffffff',
                        borderWidth: 3,
                        hoverOffset: 8,
                        cutout: '60%'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 20,
                                usePointStyle: true,
                                font: {
                                    size: 12,
                                    weight: 'bold'
                                },
                                generateLabels: function(chart) {
                                    const data = chart.data;
                                    if (data.labels.length && data.datasets.length) {
                                        return data.labels.map((label, i) => {
                                            const value = data.datasets[0].data[i];
                                            const percentage = totalRelationsFiltered > 0 ? ((value / totalRelationsFiltered) * 100).toFixed(1) : 0;
                                            return {
                                                text: `${label}: ${value} (${percentage}%)`,
                                                fillStyle: data.datasets[0].backgroundColor[i],
                                                strokeStyle: data.datasets[0].borderColor,
                                                lineWidth: data.datasets[0].borderWidth,
                                                hidden: false,
                                                index: i
                                            };
                                        });
                                    }
                                    return [];
                                }
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            titleColor: '#ffffff',
                            bodyColor: '#ffffff',
                            borderColor: '#10B981',
                            borderWidth: 1,
                            callbacks: {
                                label: function(context) {
                                    const label = context.label || '';
                                    const value = context.parsed;
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                                    return `${label}: ${value} types (${percentage}%)`;
                                }
                            }
                        }
                    },
                    animation: {
                        animateRotate: true,
                        animateScale: true,
                        duration: 2000,
                        easing: 'easeInOutQuart'
                    }
                }
            });
        } else {
            relationsByLetterCtx.font = "16px Arial";
            relationsByLetterCtx.fillStyle = "#6B7280";
            relationsByLetterCtx.textAlign = "center";
            relationsByLetterCtx.fillText("No Data Available", relationsByLetterCtx.canvas.width/2, relationsByLetterCtx.canvas.height/2);
            document.getElementById('relationsTotalCount').textContent = '0';
        }

                const documentsByLetterCtx = document.getElementById('documentsByLetterChart').getContext('2d');
        const documentTypes = {!! json_encode($documentStats->pluck('TYPE_NAME')) !!};
        const documentCounts = {!! json_encode($documentStats->pluck('TYPE_ID')) !!};
        
        const documentLetterGroups = {};
        documentTypes.forEach((typeName, index) => {
            const firstLetter = typeName.charAt(0).toUpperCase();
            if (!documentLetterGroups[firstLetter]) {
                documentLetterGroups[firstLetter] = 0;
            }
            documentLetterGroups[firstLetter] += parseInt(documentCounts[index]) || 1;
        });

        const sortedDocumentLetters = Object.keys(documentLetterGroups).sort();
        const sortedDocumentCounts = sortedDocumentLetters.map(letter => documentLetterGroups[letter]);

        const documentColors = [
            '#8B5CF6', '#7C3AED', '#6D28D9', '#5B21B6', '#4C1D95',
            '#C4B5FD', '#A78BFA', '#8B5CF6', '#7C3AED', '#6D28D9',
            '#DDD6FE', '#C4B5FD', '#A78BFA', '#8B5CF6', '#7C3AED'
        ];

        const documentsByLetterChart = new Chart(documentsByLetterCtx, {
            type: 'pie',
            data: {
                labels: sortedDocumentLetters.map(letter => `Letter "${letter}"`),
                datasets: [{
                    label: 'Document Types',
                    data: sortedDocumentCounts,
                    backgroundColor: documentColors.slice(0, sortedDocumentLetters.length),
                    borderColor: '#ffffff',
                    borderWidth: 2,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 15,
                            usePointStyle: true,
                            font: {
                                size: 11
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
                animation: {
                    animateRotate: true,
                    animateScale: true,
                    duration: 1500
                }
            }
        });

        window.addEventListener('resize', function() {
            setTimeout(() => {
                usersByLetterChart.resize();
                if (typeof relationsByLetterChart !== 'undefined') {
                    relationsByLetterChart.resize();
                }
                documentsByLetterChart.resize();
            }, 100);
        });
    </script>
</body>
</html>
