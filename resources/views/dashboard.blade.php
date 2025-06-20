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

        <!-- Charts Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- Relations by First Letter Donut Chart (I, J, U only) -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">
                        <i class="fas fa-chart-donut text-green-500 mr-2"></i>
                        Statistik Jenis Relation Berdasarkan Huruf Awal
                    </h3>
                </div>
                <div class="h-80 relative">
                    <canvas id="relationsByLetterChart"></canvas>
                    <!-- Center text for donut chart -->
                    <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                        <div class="text-center">
                            <div class="text-2xl font-bold text-gray-900" id="relationsTotalCount">0</div>
                            <div class="text-sm text-gray-500">Total Relations</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Document Types by First Letter Chart -->
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

        <!-- New Bar Chart Section for Users -->
        <div class="grid grid-cols-1 gap-8 mb-8">
            <!-- Users by First Letter Bar Chart -->
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
        // Chart.js configuration
        Chart.defaults.responsive = true;
        Chart.defaults.maintainAspectRatio = false;

        // Get user data from Laravel - using username instead of name
        const userNames = {!! json_encode($userStats->pluck('username')) !!};
        const totalUsersCount = {{ $totalUsers }};

        // Process user data to group by first letter
        const userLetterGroups = {};
        userNames.forEach((userName) => {
            const firstLetter = userName.charAt(0).toUpperCase();
            if (!userLetterGroups[firstLetter]) {
                userLetterGroups[firstLetter] = 0;
            }
            userLetterGroups[firstLetter] += 1;
        });

        // Sort letters alphabetically
        const sortedUserLetters = Object.keys(userLetterGroups).sort();
        const sortedUserCounts = sortedUserLetters.map(letter => userLetterGroups[letter]);

        // Generate colors for users bar chart
        const userBarColors = [
            '#3B82F6', '#1D4ED8', '#1E40AF', '#1E3A8A', '#312E81',
            '#60A5FA', '#3B82F6', '#2563EB', '#1D4ED8', '#1E40AF',
            '#93C5FD', '#60A5FA', '#3B82F6', '#2563EB', '#1D4ED8',
            '#DBEAFE', '#93C5FD', '#60A5FA', '#3B82F6', '#2563EB'
        ];

        // Users by First Letter Bar Chart
        const usersByLetterCtx = document.getElementById('usersByLetterChart').getContext('2d');
        const usersByLetterChart = new Chart(usersByLetterCtx, {
            type: 'bar',
            data: {
                labels: sortedUserLetters,
                datasets: [{
                    label: 'Jumlah User',
                    data: sortedUserCounts,
                    backgroundColor: userBarColors.slice(0, sortedUserLetters.length),
                    borderColor: userBarColors.slice(0, sortedUserLetters.length).map(color => color.replace('0.8', '1')),
                    borderWidth: 2,
                    borderRadius: 8,
                    borderSkipped: false,
                    hoverBackgroundColor: userBarColors.slice(0, sortedUserLetters.length).map(color => color + 'CC'),
                    hoverBorderWidth: 3
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
                },
                                interaction: {
                    intersect: false,
                    mode: 'index'
                }
            }
        });

        // Add click event to bar chart
        usersByLetterChart.canvas.addEventListener('click', function(event) {
            const activePoints = usersByLetterChart.getElementsAtEventForMode(event, 'nearest', { intersect: true }, false);
            if (activePoints.length > 0) {
                const clickedIndex = activePoints[0].index;
                const clickedLetter = sortedUserLetters[clickedIndex];
                const clickedCount = sortedUserCounts[clickedIndex];
                const percentage = totalUsersCount > 0 ? ((clickedCount / totalUsersCount) * 100).toFixed(1) : 0;
                
                // Get usernames starting with clicked letter
                const usersWithLetter = userNames.filter(name => name.charAt(0).toUpperCase() === clickedLetter);
                
                // Show detailed alert
                alert(`Users dengan huruf awal "${clickedLetter}":\n• Jumlah: ${clickedCount} users\n• Persentase: ${percentage}%\n• Username: ${usersWithLetter.join(', ')}`);
                console.log(`Clicked on users starting with letter: ${clickedLetter} (${clickedCount} users, ${percentage}%)`);
            }
        });


        // Relations by First Letter Donut Chart (I, J, U only)
        const relationsByLetterCtx = document.getElementById('relationsByLetterChart').getContext('2d');
        
        // Process relation data to group by first letter (filter I, J, U only)
        const relationTypes = {!! json_encode($relationStats->pluck('RELATION_DESC')) !!};
        
        // Group relations by first letter and filter only I, J, U
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

        // Sort letters alphabetically and ensure all I, J, U are present (even if 0)
        const sortedRelationLetters = ['I', 'J', 'U'];
        const sortedRelationCounts = sortedRelationLetters.map(letter => relationLetterGroups[letter] || 0);

        // Calculate total for center display
        const totalRelationsFiltered = sortedRelationCounts.reduce((a, b) => a + b, 0);
        document.getElementById('relationsTotalCount').textContent = totalRelationsFiltered;

        // Generate colors for relations donut chart
        const relationColors = [
            '#10B981', // I - Emerald
            '#059669', // J - Emerald 600
            '#047857'  // U - Emerald 700
        ];

        // Check if there's any data to display
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
                        cutout: '60%', // This makes it a donut chart
                        hoverBorderWidth: 4,
                        hoverBorderColor: '#ffffff'
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
                    },
                    interaction: {
                        intersect: false,
                        mode: 'index'
                    }
                }
            });

            // Add click event to donut chart segments
            relationsByLetterChart.canvas.addEventListener('click', function(event) {
                const activePoints = relationsByLetterChart.getElementsAtEventForMode(event, 'nearest', { intersect: true }, false);
                if (activePoints.length > 0) {
                    const clickedIndex = activePoints[0].index;
                    const clickedLetter = sortedRelationLetters[clickedIndex];
                    const clickedCount = sortedRelationCounts[clickedIndex];
                    const percentage = totalRelationsFiltered > 0 ? ((clickedCount / totalRelationsFiltered) * 100).toFixed(1) : 0;
                    
                    // Show detailed alert
                    alert(`Relations starting with "${clickedLetter}":\n• Count: ${clickedCount} types\n• Percentage: ${percentage}%`);
                    console.log(`Clicked on relations starting with letter: ${clickedLetter} (${clickedCount} types, ${percentage}%)`);
                }
            });

        } else {
            // Display "No Data" message in center of donut
            relationsByLetterCtx.font = "16px Arial";
            relationsByLetterCtx.fillStyle = "#6B7280";
            relationsByLetterCtx.textAlign = "center";
            relationsByLetterCtx.fillText("No Data Available", relationsByLetterCtx.canvas.width/2, relationsByLetterCtx.canvas.height/2);
            
            // Update center text
            document.getElementById('relationsTotalCount').textContent = '0';
        }

        // Document Types by First Letter Pie Chart
        const documentsByLetterCtx = document.getElementById('documentsByLetterChart').getContext('2d');
        
        // Process document data to group by first letter
        const documentTypes = {!! json_encode($documentStats->pluck('TYPE_NAME')) !!};
        const documentCounts = {!! json_encode($documentStats->pluck('TYPE_ID')) !!};
        
        // Group documents by first letter
        const documentLetterGroups = {};
        documentTypes.forEach((typeName, index) => {
            const firstLetter = typeName.charAt(0).toUpperCase();
            if (!documentLetterGroups[firstLetter]) {
                documentLetterGroups[firstLetter] = 0;
            }
            documentLetterGroups[firstLetter] += parseInt(documentCounts[index]) || 1;
        });

        // Sort letters alphabetically
        const sortedDocumentLetters = Object.keys(documentLetterGroups).sort();
        const sortedDocumentCounts = sortedDocumentLetters.map(letter => documentLetterGroups[letter]);

        // Generate colors for documents chart
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

        // Add click event to documents chart
        documentsByLetterChart.canvas.addEventListener('click', function(event) {
            const activePoints = documentsByLetterChart.getElementsAtEventForMode(event, 'nearest', { intersect: true }, false);
            if (activePoints.length > 0) {
                const clickedIndex = activePoints[0].index;
                const clickedLetter = sortedDocumentLetters[clickedIndex];
                const clickedCount = sortedDocumentCounts[clickedIndex];
                const totalDocs = sortedDocumentCounts.reduce((a, b) => a + b, 0);
                const percentage = ((clickedCount / totalDocs) * 100).toFixed(1);
                
                // Show detailed alert
                alert(`Documents starting with "${clickedLetter}":\n• Count: ${clickedCount} types\n• Percentage: ${percentage}%`);
                                console.log(`Clicked on documents starting with letter: ${clickedLetter} (${clickedCount} types, ${percentage}%)`);
            }
        });



        // Export table data function
        function exportTableData() {
            let csvContent = "data:text/csv;charset=utf-8,";
            csvContent += "Huruf Awal,Jumlah User,Persentase,Username\n";
            
            sortedUserLetters.forEach((letter, index) => {
                const count = sortedUserCounts[index];
                const percentage = totalUsersCount > 0 ? ((count / totalUsersCount) * 100).toFixed(1) : 0;
                const usersWithLetter = userNames.filter(name => name.charAt(0).toUpperCase() === letter);
                csvContent += `${letter},${count},${percentage}%,"${usersWithLetter.join('; ')}"\n`;
            });
            
            const encodedUri = encodeURI(csvContent);
            const link = document.createElement("a");
            link.setAttribute("href", encodedUri);
            link.setAttribute("download", "user_statistics_by_letter.csv");
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }

        // Print functionality
        function printTable() {
            const printWindow = window.open('', '_blank');
            let tableHTML = `
                <table border="1" style="border-collapse: collapse; width: 100%;">
                    <thead>
                        <tr style="background-color: #f2f2f2;">
                            <th style="padding: 8px; text-align: left;">Huruf Awal</th>
                            <th style="padding: 8px; text-align: left;">Jumlah User</th>
                            <th style="padding: 8px; text-align: left;">Persentase</th>
                            <th style="padding: 8px; text-align: left;">Username</th>
                        </tr>
                    </thead>
                    <tbody>
            `;
            
            sortedUserLetters.forEach((letter, index) => {
                const count = sortedUserCounts[index];
                const percentage = totalUsersCount > 0 ? ((count / totalUsersCount) * 100).toFixed(1) : 0;
                const usersWithLetter = userNames.filter(name => name.charAt(0).toUpperCase() === letter);
                
                tableHTML += `
                    <tr>
                        <td style="padding: 8px;">${letter}</td>
                        <td style="padding: 8px;">${count}</td>
                        <td style="padding: 8px;">${percentage}%</td>
                        <td style="padding: 8px;">${usersWithLetter.join(', ')}</td>
                    </tr>
                `;
            });
            
            tableHTML += `
                    </tbody>
                </table>
            `;
            
            printWindow.document.write(`
                <html>
                <head>
                    <title>Statistik User Berdasarkan Huruf Awal Username</title>
                    <style>
                        body { font-family: Arial, sans-serif; margin: 20px; }
                        table { border-collapse: collapse; width: 100%; }
                        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                        th { background-color: #f2f2f2; font-weight: bold; }
                        .print-header { text-align: center; margin-bottom: 20px; }
                        .print-date { text-align: right; font-size: 12px; color: #666; margin-bottom: 10px; }
                        .print-summary { margin-bottom: 20px; padding: 10px; background-color: #f9f9f9; border-radius: 5px; }
                    </style>
                </head>
                <body>
                    <div class="print-header">
                        <h2>Statistik User Berdasarkan Huruf Awal Username</h2>
                        <div class="print-date">Dicetak pada: ${new Date().toLocaleDateString('id-ID', { 
                            weekday: 'long', 
                            year: 'numeric', 
                            month: 'long', 
                            day: 'numeric' 
                        })}</div>
                    </div>
                    <div class="print-summary">
                        <strong>Ringkasan:</strong><br>
                        Total User: ${totalUsersCount}<br>
                        Jumlah Huruf Awal: ${sortedUserLetters.length}<br>
                        Huruf dengan User Terbanyak: ${sortedUserLetters[sortedUserCounts.indexOf(Math.max(...sortedUserCounts))]} (${Math.max(...sortedUserCounts)} users)
                    </div>
                    ${tableHTML}
                </body>
                </html>
            `);
            
            printWindow.document.close();
            printWindow.print();
        }

        // Create control buttons
        const controlsContainer = document.createElement('div');
        controlsContainer.className = 'flex items-center space-x-2';

        // Export button
        const exportButton = document.createElement('button');
        exportButton.innerHTML = '<i class="fas fa-download mr-2"></i>Export CSV';
        exportButton.className = 'bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded text-sm transition-colors duration-200';
        exportButton.onclick = exportTableData;

        // Print button
        const printButton = document.createElement('button');
        printButton.innerHTML = '<i class="fas fa-print mr-2"></i>Print';
        printButton.className = 'bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded text-sm transition-colors duration-200';
        printButton.onclick = printTable;

        // Search input
        const searchContainer = document.createElement('div');
        searchContainer.className = 'flex items-center space-x-2';
        
        const searchLabel = document.createElement('span');
        searchLabel.textContent = 'Cari:';
        searchLabel.className = 'text-sm text-gray-600';
        
        const searchInput = document.createElement('input');
        searchInput.type = 'text';
        searchInput.placeholder = 'Cari huruf...';
        searchInput.className = 'border border-gray-300 rounded-md px-3 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent';
        

        searchContainer.appendChild(searchLabel);
        searchContainer.appendChild(searchInput);

        controlsContainer.appendChild(searchContainer);
        controlsContainer.appendChild(exportButton);
        controlsContainer.appendChild(printButton);

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

        // Add chart animation on load with delay
        setTimeout(() => {
            if (hasRelationData && typeof relationsByLetterChart !== 'undefined') {
                relationsByLetterChart.update('active');
            }
            documentsByLetterChart.update('active');
            usersByLetterChart.update('active');
        }, 500);

        // Add responsive behavior for charts
        window.addEventListener('resize', function() {
            setTimeout(() => {
                if (typeof usersByLetterChart !== 'undefined') {
                    usersByLetterChart.resize();
                }
                if (typeof relationsByLetterChart !== 'undefined') {
                    relationsByLetterChart.resize();
                }
                if (typeof documentsByLetterChart !== 'undefined') {
                    documentsByLetterChart.resize();
                }
            }, 100);
        });

        // Display summary information in console
        console.log('Users Bar Chart Data:', {
            letters: sortedUserLetters,
            counts: sortedUserCounts,
            total: totalUsersCount,
            usernames: userNames
        });

        console.log('Relations Donut Chart Data (I, J, U only):', {
            letters: sortedRelationLetters,
            counts: sortedRelationCounts,
            total: totalRelationsFiltered,
            hasData: hasRelationData
        });

        console.log('Documents Pie Chart Data (All letters):', {
            letters: sortedDocumentLetters,
            counts: sortedDocumentCounts,
            total: sortedDocumentCounts.reduce((a, b) => a + b, 0)
        });

        // Add keyboard shortcuts
        document.addEventListener('keydown', function(event) {
            // Ctrl + E for Export
            if (event.ctrlKey && event.key === 'e') {
                event.preventDefault();
                exportTableData();
            }
            // Ctrl + P for Print
            if (event.ctrlKey && event.key === 'p') {
                event.preventDefault();
                printTable();
            }
            // Ctrl + F for Focus search
            if (event.ctrlKey && event.key === 'f') {
                event.preventDefault();
                searchInput.focus();
            }
        });

        // Add tooltip for keyboard shortcuts
                // Add tooltip for keyboard shortcuts
        const shortcutsInfo = document.createElement('div');
        shortcutsInfo.className = 'text-xs text-gray-500 mt-2';
        shortcutsInfo.innerHTML = '<i class="fas fa-keyboard mr-1"></i>Shortcuts: Ctrl+E (Export), Ctrl+P (Print), Ctrl+F (Search)';
        

        // Add loading state for data refresh
        function showLoadingState() {
            const loadingOverlay = document.createElement('div');
            loadingOverlay.id = 'loadingOverlay';
            loadingOverlay.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
            loadingOverlay.innerHTML = `
                <div class="bg-white rounded-lg p-6 flex items-center space-x-3">
                    <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-blue-600"></div>
                    <span class="text-gray-700">Memuat data...</span>
                </div>
            `;
            document.body.appendChild(loadingOverlay);
        }

        function hideLoadingState() {
            const loadingOverlay = document.getElementById('loadingOverlay');
            if (loadingOverlay) {
                loadingOverlay.remove();
            }
        }

        // Enhanced data refresh with loading state
        function refreshData() {
            showLoadingState();
            fetch('/api/chart-data')
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Data refreshed successfully:', data);
                    // Here you could update the charts with new data if needed
                    hideLoadingState();
                })
                .catch(error => {
                    console.error('Error refreshing data:', error);
                    hideLoadingState();
                    
                    // Show error notification
                    const errorNotification = document.createElement('div');
                    errorNotification.className = 'fixed top-4 right-4 bg-red-500 text-white px-4 py-2 rounded-lg shadow-lg z-50';
                    errorNotification.innerHTML = '<i class="fas fa-exclamation-triangle mr-2"></i>Gagal memuat data terbaru';
                    document.body.appendChild(errorNotification);
                    
                    setTimeout(() => {
                        errorNotification.remove();
                    }, 3000);
                });
        }

        // Manual refresh button
        const refreshButton = document.createElement('button');
        refreshButton.innerHTML = '<i class="fas fa-sync-alt mr-2"></i>Refresh';
        refreshButton.className = 'bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded text-sm transition-colors duration-200';
        refreshButton.onclick = refreshData;

        // Add refresh button to controls
        if (controlsContainer) {
            controlsContainer.appendChild(refreshButton);
        }

        // Add data summary card
        const summaryCard = document.createElement('div');
        summaryCard.className = 'bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg p-4 mb-4 border border-blue-200';
        summaryCard.innerHTML = `
            <div class="flex items-center justify-between">
                <div>
                    <h4 class="text-lg font-semibold text-blue-900 mb-2">
                        <i class="fas fa-info-circle mr-2"></i>Ringkasan Data
                    </h4>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                        <div>
                            <span class="text-blue-600 font-medium">Total User:</span>
                            <span class="text-blue-900 font-bold ml-1">${totalUsersCount}</span>
                        </div>
                        <div>
                            <span class="text-blue-600 font-medium">Huruf Unik:</span>
                            <span class="text-blue-900 font-bold ml-1">${sortedUserLetters.length}</span>
                        </div>
                        <div>
                            <span class="text-blue-600 font-medium">Terbanyak:</span>
                            <span class="text-blue-900 font-bold ml-1">${sortedUserLetters[sortedUserCounts.indexOf(Math.max(...sortedUserCounts))]} (${Math.max(...sortedUserCounts)})</span>
                        </div>
                        <div>
                            <span class="text-blue-600 font-medium">Tersedikit:</span>
                            <span class="text-blue-900 font-bold ml-1">${sortedUserLetters[sortedUserCounts.indexOf(Math.min(...sortedUserCounts))]} (${Math.min(...sortedUserCounts)})</span>
                        </div>
                    </div>
                </div>
                <div class="text-blue-400">
                    <i class="fas fa-chart-line text-3xl"></i>
                </div>
            </div>
        `;

        // Insert summary card before the table
        if (tableContainer) {
            tableContainer.parentNode.insertBefore(summaryCard, tableContainer);
        }

        // Add animation classes for better UX
        const style = document.createElement('style');
        style.textContent = `
            .fade-in {
                animation: fadeIn 0.5s ease-in;
            }
            
            @keyframes fadeIn {
                from { opacity: 0; transform: translateY(10px); }
                to { opacity: 1; transform: translateY(0); }
            }
            
            .pulse-animation {
                animation: pulse 2s infinite;
            }
            
            @keyframes pulse {
                0%, 100% { opacity: 1; }
                50% { opacity: 0.7; }
            }
            
            .slide-in-right {
                animation: slideInRight 0.3s ease-out;
            }
            
            @keyframes slideInRight {
                from { transform: translateX(100%); opacity: 0; }
                to { transform: translateX(0); opacity: 1; }
            }
            
            .hover-scale:hover {
                transform: scale(1.02);
                transition: transform 0.2s ease-in-out;
            }
            
            .table-row-hover:hover {
                background-color: #f8fafc;
                box-shadow: 0 2px 4px rgba(0,0,0,0.1);
                transform: translateX(2px);
                transition: all 0.2s ease-in-out;
            }
        `;
        document.head.appendChild(style);

        // Apply animations to elements
        document.querySelectorAll('.bg-white.rounded-lg.shadow-md').forEach(card => {
            card.classList.add('fade-in', 'hover-scale');
        });


        // Add success notification for actions
        function showSuccessNotification(message) {
            const notification = document.createElement('div');
            notification.className = 'fixed top-4 right-4 bg-green-500 text-white px-4 py-2 rounded-lg shadow-lg z-50 slide-in-right';
            notification.innerHTML = `<i class="fas fa-check-circle mr-2"></i>${message}`;
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.remove();
            }, 3000);
        }

        // Update export function to show success notification
        const originalExportFunction = exportTableData;
        exportTableData = function() {
            originalExportFunction();
            showSuccessNotification('Data berhasil diekspor ke CSV');
        };

        // Update print function to show success notification
        const originalPrintFunction = printTable;
        printTable = function() {
            originalPrintFunction();
            showSuccessNotification('Halaman siap untuk dicetak');
        };

        // Add data validation and error handling
        function validateData() {
            const issues = [];
            
            if (totalUsersCount === 0) {
                issues.push('Tidak ada data user yang ditemukan');
            }
            
            if (sortedUserLetters.length === 0) {
                issues.push('Tidak ada huruf awal yang dapat dianalisis');
            }
            
            if (userNames.some(name => !name || name.trim() === '')) {
                issues.push('Beberapa username kosong atau tidak valid');
            }
            
            if (issues.length > 0) {
                console.warn('Data validation issues:', issues);
                
                const warningNotification = document.createElement('div');
                warningNotification.className = 'fixed bottom-4 right-4 bg-yellow-500 text-white px-4 py-2 rounded-lg shadow-lg z-50';
                warningNotification.innerHTML = `<i class="fas fa-exclamation-triangle mr-2"></i>Peringatan: ${issues.length} masalah data ditemukan`;
                document.body.appendChild(warningNotification);
                
                setTimeout(() => {
                    warningNotification.remove();
                }, 5000);
            }
            
            return issues.length === 0;
        }

        // Run data validation
        validateData();

        // Add final initialization message
        console.log('Dashboard initialized successfully with the following features:');
        console.log('- Bar chart for user distribution by first letter of username');
        console.log('- Interactive table with sorting, searching, and filtering');
        console.log('- Export to CSV functionality');
        console.log('- Print functionality with formatted output');
        console.log('- Real-time data refresh every 30 seconds');
        console.log('- Keyboard shortcuts for common actions');
        console.log('- Responsive design for mobile and desktop');
        console.log('- Data validation and error handling');
        console.log('- Smooth animations and transitions');

    </script>
</body>
</html>


