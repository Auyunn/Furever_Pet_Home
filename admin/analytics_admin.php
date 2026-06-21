<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analytics - Furever Pet Home</title>
    <link rel="stylesheet" href="../css/base.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root {
            --primary-gray-mid: #4a4a4a;
            --primary-gray-light: #e0e0e0;
            --bg-light: #fafafa;
            --bg-card: #ffffff;
            --text-dark: #1e1e1e;
            --text-mid: #3a3a3a;
            --border-light: #cccccc;
            --nav-bg: #ffffff;
            --footer-bg: #1e1e1e;
            --shadow-subtle: rgba(0,0,0,0.05);
            --hover-border: #888888;
            --dot-gray: #757575;
        }

        body {
            font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: var(--bg-light);
            color: var(--text-dark);
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        /* --- Main Content --- */
        main {
            flex: 1;
            max-width: 1100px;
            margin: 140px auto 40px auto;
            padding: 0 20px;
            width: 100%;
        }

        h2 {
            color: var(--text-dark);
            border-left: 5px solid var(--primary-gray-mid);
            padding-left: 15px;
            margin-bottom: 30px;
            font-weight: 600;
            letter-spacing: -0.2px;
        }

        .dashboard-container {
            display: grid;
            grid-template-columns: 1.2fr 0.8fr;
            gap: 30px;
            background: var(--bg-card);
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 4px 15px var(--shadow-subtle);
            border: 1px solid var(--border-light);
        }

        .chart-wrapper {
            background: #fff;
            padding: 10px;
            height: 400px;
            border-radius: 12px;
        }

        /* --- Reasons Section (PD)--- */
        .reasons-container {
            margin-top: 10px;
            border-top: 1px solid var(--border-light);
            padding-top: 25px;
        }

        .reasons-container h3 {
            font-size: 1.2rem;
            color: var(--primary-gray-dark);
            margin-bottom: 20px;
            font-weight: 600;
            border-bottom: 1px solid var(--border-light);
            padding-bottom: 8px;
        }

        .reason-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 18px;
        }

        .reason-card {
            background: #fefefe;
            border: 1px solid var(--border-light);
            border-radius: 10px;
            padding: 16px 18px;
            display: flex;
            align-items: flex-start;
            transition: transform 0.2s, border-color 0.2s , box-shadow 0.2s;
            box-shadow: 0 1px 2px rgba(0,0,0,0.02);
        }

        .reason-card:hover {
            transform: translateY(-3px);
            border-color: var(--hover-border);
            background-color: #fcfcfc;
        }

        .status-dot {
            width: 12px;
            height: 12px;
            background: var(--dot-gray);
            border-radius: 50%;
            margin-top: 5px;
            margin-right: 15px;
            flex-shrink: 0;
        }

        .reason-card p {
            margin: 0;
            line-height: 1.6;
            font-size: 0.95rem;
            color: var(--text-mid);
        }

        .reason-card strong {
            color: var(--primary-gray-dark);
            font-weight: 600;
        }

        @media (max-width: 768px) {
            .dashboard-container { grid-template-columns: 1fr; }
            .bar { flex-direction: column; gap: 15px; }
            .nav a { margin: 0 10px; font-size: 0.8rem; }
             .reason-grid { grid-template-columns: 1fr; }
        }

    </style>
</head>
<body>

    <div class="bar">
        <nav class="navbar" id="navbar">
        <!--logo and profile-->
        <div class ="navbar-top">
            <a href="#" class="nav-logo">
            <img src="../image/icons/logo.png" alt="Furever Pet Home">
            <span>Furever Pet Home</span>
            </a>
            <div class="nav-right">
            <button class="notif-btn" title="Notifications" onclick="window.location.href='resident/inbox.php';">🔔<span class="notif-dot"></span></button>
            <div class="avatar" title="My Profile" onclick="window.location.href='User Login.html';">AT</div>
            </div>
        </div>

        <!---Tab Navigation-->
        <div class="nav-links">
            <a href="dashboard.php" class="nav-tab"> Dashboard</a>
            <a href=" " class="nav-tab"> Users/NGOs</a>
            <a href=" " class="nav-tab"> Report</a>
            <a href="analytics_admin.php" class="nav-tab"> Analytics</a>
            <a href="pet_communityadmin.php" class="nav-tab">  Pet Community</a>
            <a href="help_center.html" class="nav-tab"> Help Center</a>
        </div>
        </nav>
    </div>

    <main>
        <h2>Pet Adoption Insights: Bandar Klang</h2>
        
        <div class="dashboard-container">
    <!-- Graph Section (top) -->
    <div class="chart-wrapper">
        <canvas id="adoptionRateChart"></canvas>
    </div>

    <!-- Insights Section moved below chart -->
    <div class="reasons-container">
        <h3>Primary Drivers</h3>
        <div class="reason-grid">
            <div class="reason-card">
                <div class="status-dot"></div>
                <p><strong>Weather Impact:</strong> Higher adoption rates are observed during festive seasons and school holidays in Selangor when families have more time to settle a new pet.</p>
            </div>

            <div class="reason-card">
                <div class="status-dot"></div>
                <p><strong>Urban Relocation:</strong> A significant portion of community growth stems from residents moving to newer Bandar Klang developments seeking pet-friendly environments.</p>
            </div>

            <div class="reason-card">
                <div class="status-dot"></div>
                <p><strong>Economic Factors:</strong> Families with stable incomes and financial security feel more confident about taking on the responsibility of pet ownership, leading to higher adoption rates.</p>
            </div>

            <div class="reason-card">
                <div class="status-dot"></div>
                <p><strong>Health & Wellness Trends:</strong> Growing awareness of the mental health benefits of pets—such as reducing stress and loneliness—encourages adoption, particularly among urban residents seeking companionship.</p>
            </div>

            

            <div class="reason-card">
                <div class="status-dot"></div>
                <p><strong>Social Media Influence:</strong> Viral posts, influencer endorsements, or heartwarming rescue stories shared online can inspire spontaneous adoptions.</p>
            </div>

        



        </dive>
    </div>
</div>
    </main>

    <!-- Footer -->
    <footer>
            <div class="footer-grid">
            <div>
                <div style="font-size:2rem;">🐾</div>
                <div class="footer-brand-name">Furever Pet Home</div>
                <p class="footer-tagline">A compassionate digital hub for stray pet adoption and community care in Bandar Klang, Selangor.</p>
            </div>
            <div>
                <p class="footer-col-title">Platform</p>
                <ul class="footer-links-list">
                <li><a href="#">Find A Pet</a></li>
                <li><a href="#">Report Animal</a></li>
                <li><a href="#">Community Board</a></li>
                <li><a href="#">Analytics</a></li>
                </ul>
            </div>
            <div>
                <p class="footer-col-title">Account</p>
                <ul class="footer-links-list">
                <li><a href="#">My Profile</a></li>
                <li><a href="#">My Applications</a></li>
                <li><a href="#">Favourites</a></li>
                <li><a href="#">Inbox</a></li>
                </ul>
            </div>
            <div>
                <p class="footer-col-title">Contact</p>
                <ul class="footer-links-list">
                <li><a href="#">41700 Bandar Klang, Selangor</a></li>
                <li><a href="mailto:info@fureverpethome.com">info@fureverpethome.com</a></li>
                <li><a href="#">+60 123-456-7890</a></li>
                <li><a href="#">Facebook · Instagram · X</a></li>
                </ul>
            </div>
            </div>
            <div class="footer-bottom">
            <span>© 2026 Furever Pet Home — Urban Pet Adoption & Community Management</span>
            <span>Made with ❤️ for Bandar Klang</span>
            </div>
        </footer>

    <!-- Internal Script for Chart-->
    <script>
        const ctx = document.getElementById('adoptionRateChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Weather Trends', 'UrbanRelocation', 'Economic Factors','Health & Wellness Trends' ,'Social Media Influence'],
                datasets: [{
                    label: 'Adoption Frequency',
                    data: [78, 45, 60, 85, 70],
                    backgroundColor: ['#4A90E2', '#F5A623', '#50E3C2', '#2ECC71', '#9B59B6'],
                    borderRadius: 8,
                    barThickness: 60,
                    borderColor: '#2c2c2c',
                    borderWidth: 0.5
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#2c2c2c',
                        titleColor: '#e0e0e0',
                        bodyColor: '#ccc',
                        borderColor: '#6b6b6b',
                        borderWidth: 1
                    }
                },
                scales: {
                    y: { 
                        beginAtZero: true,
                        grid: { color: '#e2e2e2', lineWidth: 0.5 },
                        title: {
                            display: false
                        },
                        ticks: {
                            color: '#3a3a3a'
                        }
                    },
                    x: { 
                        grid: { display: false },
                        ticks: {
                            color: '#2c2c2c',
                            font: { weight: '500' }
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>