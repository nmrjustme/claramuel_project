<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monthly Income Dashboard</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        
        .dashboard-container {
            width: 100%;
            max-width: 1200px;
            background: white;
            border-radius: 20px;
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        
        .dashboard-header {
            background: linear-gradient(90deg, #4b6cb7 0%, #182848 100%);
            color: white;
            padding: 25px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
        }
        
        .dashboard-header h1 {
            font-weight: 600;
            font-size: 28px;
        }
        
        .dashboard-header p {
            opacity: 0.8;
            margin-top: 5px;
        }
        
        .stats-container {
            display: flex;
            justify-content: space-around;
            flex-wrap: wrap;
            padding: 20px;
            background: #f8f9fa;
            border-bottom: 1px solid #eaeaea;
        }
        
        .stat-box {
            background: white;
            border-radius: 12px;
            padding: 20px;
            margin: 10px;
            min-width: 200px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            flex: 1;
        }
        
        .stat-box h3 {
            font-size: 16px;
            color: #6c757d;
            margin-bottom: 10px;
        }
        
        .stat-value {
            font-size: 24px;
            font-weight: 700;
            color: #4b6cb7;
        }
        
        .stat-box.best-month {
            background: linear-gradient(90deg, #4b6cb7 0%, #182848 100%);
            color: white;
        }
        
        .stat-box.best-month h3, .stat-box.best-month .stat-value {
            color: white;
        }
        
        .chart-container {
            padding: 30px;
            position: relative;
            height: 400px;
        }
        
        .loading {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 300px;
            flex-direction: column;
        }
        
        .spinner {
            width: 50px;
            height: 50px;
            border: 5px solid rgba(75, 108, 183, 0.2);
            border-radius: 50%;
            border-top-color: #4b6cb7;
            animation: spin 1s linear infinite;
            margin-bottom: 20px;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .error-message {
            text-align: center;
            padding: 30px;
            color: #e74c3c;
            display: none;
        }
        
        @media (max-width: 768px) {
            .stats-container {
                flex-direction: column;
            }
            
            .stat-box {
                width: 100%;
                margin: 10px 0;
            }
            
            .chart-container {
                height: 300px;
            }
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <div class="dashboard-header">
            <div>
                <h1>Monthly Income Dashboard</h1>
                <p>Visualization of revenue data</p>
            </div>
        </div>
        
        <div class="stats-container">
            <div class="stat-box">
                <h3>Total Revenue</h3>
                <div class="stat-value" id="total-revenue">0.00</div>
            </div>
            
            <div class="stat-box">
                <h3>Average Monthly</h3>
                <div class="stat-value" id="average-monthly">0.00</div>
            </div>
            
            <div class="stat-box best-month">
                <h3>Best Month</h3>
                <div class="stat-value" id="best-month-income">0.00</div>
                <div id="best-month-name">No data available</div>
            </div>
        </div>
        
        <div class="error-message" id="error-message">
            <h3>Unable to load data</h3>
            <p>Please check your connection and try again</p>
            <button onclick="loadData()" style="margin-top: 15px; padding: 8px 15px; background: #4b6cb7; color: white; border: none; border-radius: 5px; cursor: pointer;">Retry</button>
        </div>
        
        <div class="chart-container">
            <div class="loading" id="loading">
                <div class="spinner"></div>
                <p>Loading income data...</p>
            </div>
            <canvas id="incomeChart"></canvas>
        </div>
    </div>

    <script>
        // Global chart variable
        let incomeChart;
        
        // Format currency
            function formatCurrency(amount) {
                  return '₱' + parseFloat(amount || 0).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
            }
        
        
        // Load data from API
        function loadData() {
            // Show loading, hide error
            document.getElementById('loading').style.display = 'flex';
            document.getElementById('error-message').style.display = 'none';
            
            // Make AJAX request to Laravel API endpoint
            fetch('/api/monthly-income')
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    // Hide loading
                    document.getElementById('loading').style.display = 'none';
                    
                    // Update stats
                    document.getElementById('total-revenue').textContent = formatCurrency(data.totalRevenue);
                    document.getElementById('average-monthly').textContent = formatCurrency(data.averageMonthly);
                    document.getElementById('best-month-income').textContent = formatCurrency(data.bestMonth.income);
                    document.getElementById('best-month-name').textContent = data.bestMonth.month;
                    
                    // Create or update chart
                    createOrUpdateChart(data.chartData);
                })
                .catch(error => {
                    console.error('Error fetching data:', error);
                    document.getElementById('loading').style.display = 'none';
                    document.getElementById('error-message').style.display = 'block';
                });
        }
        
        // Create or update the chart
        function createOrUpdateChart(chartData) {
            const ctx = document.getElementById('incomeChart').getContext('2d');
            
            // If chart already exists, destroy it
            if (incomeChart) {
                incomeChart.destroy();
            }
            
            // Create new chart
            incomeChart = new Chart(ctx, {
                type: 'line',
                data: chartData,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        tooltip: {
                            mode: 'index',
                            intersect: false,
                            callbacks: {
                                label: function(context) {
                                    return `Income: ${formatCurrency(context.raw)}`;
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(0, 0, 0, 0.05)'
                            },
                            ticks: {
                                callback: function(value) {
                                    return formatCurrency(value);
                                }
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });
        }
        
        // Load data when page loads
        document.addEventListener('DOMContentLoaded', loadData);
    </script>
</body>
</html>