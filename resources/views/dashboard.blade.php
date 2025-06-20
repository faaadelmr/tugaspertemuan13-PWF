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

    </main>

    <script>
        // Chart.js configuration
        Chart.defaults.responsive = true;
        Chart.defaults.maintainAspectRatio = false;

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

        // Generate gradient colors for better visual appeal
        const relationGradientColors = relationColors.map((color, index) => {
            const gradient = relationsByLetterCtx.createLinearGradient(0, 0, 0, 400);
            gradient.addColorStop(0, color);
            gradient.addColorStop(1, color + '80'); // Add transparency
            return gradient;
        });

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

            // Add hover effect to donut chart center text
            const centerText = document.getElementById('relationsTotalCount');
            if (centerText) {
                relationsByLetterCtx.canvas.addEventListener('mousemove', function(event) {
                    const rect = this.getBoundingClientRect();
                    const x = event.clientX - rect.left;
                    const y = event.clientY - rect.top;
                    const centerX = this.width / 2;
                    const centerY = this.height / 2;
                    const distance = Math.sqrt(Math.pow(x - centerX, 2) + Math.pow(y - centerY, 2));
                    
                    // Check if mouse is in center area
                    if (distance < 60) {
                        centerText.style.transform = 'scale(1.1)';
                        centerText.style.color = '#10B981';
                    } else {
                        centerText.style.transform = 'scale(1)';
                        centerText.style.color = '#1F2937';
                    }
                });
            }
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

        // Add chart animation on load with delay
        setTimeout(() => {
            if (hasRelationData && typeof relationsByLetterChart !== 'undefined') {
                relationsByLetterChart.update('active');
            }
            documentsByLetterChart.update('active');
        }, 500);

        // Display summary information
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
    </script>
</body>
</html>
    