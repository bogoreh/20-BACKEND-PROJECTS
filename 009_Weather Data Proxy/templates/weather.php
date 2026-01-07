<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Weather Data Proxy</title>
    <link rel="stylesheet" href="/public/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="container">
        <header class="header">
            <h1><i class="fas fa-cloud-sun"></i> Weather Data Proxy</h1>
            <p>Fetch and filter weather data from multiple sources</p>
        </header>
        
        <div class="main-content">
            <aside class="weather-form">
                <h2><i class="fas fa-search"></i> Search Weather</h2>
                <form method="POST" action="/">
                    <?php if (isset($_SESSION['error'])): ?>
                        <div class="error-message">
                            <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($_SESSION['error']) ?>
                            <?php unset($_SESSION['error']); ?>
                        </div>
                    <?php endif; ?>
                    
                    <div class="form-group">
                        <label for="city"><i class="fas fa-city"></i> City</label>
                        <input type="text" id="city" name="city" class="form-control" 
                               value="<?= htmlspecialchars($_SESSION['search_params']['city'] ?? '') ?>" 
                               placeholder="Enter city name" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="country"><i class="fas fa-globe"></i> Country (Optional)</label>
                        <input type="text" id="country" name="country" class="form-control" 
                               value="<?= htmlspecialchars($_SESSION['search_params']['country'] ?? '') ?>"
                               placeholder="Country code (e.g., US, UK)">
                    </div>
                    
                    <div class="form-group">
                        <label><i class="fas fa-thermometer-half"></i> Temperature Units</label>
                        <div style="display: flex; gap: 20px; margin-top: 10px;">
                            <label style="display: flex; align-items: center; gap: 5px;">
                                <input type="radio" name="units" value="metric" 
                                       <?= ($_SESSION['search_params']['units'] ?? 'metric') === 'metric' ? 'checked' : '' ?>> °C
                            </label>
                            <label style="display: flex; align-items: center; gap: 5px;">
                                <input type="radio" name="units" value="imperial"
                                       <?= ($_SESSION['search_params']['units'] ?? '') === 'imperial' ? 'checked' : '' ?>> °F
                            </label>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="forecast_days"><i class="fas fa-calendar-alt"></i> Forecast Days (1-5)</label>
                        <select id="forecast_days" name="forecast_days" class="form-control">
                            <?php for($i = 1; $i <= 5; $i++): ?>
                                <option value="<?= $i ?>" <?= (($_POST['forecast_days'] ?? 5) == $i) ? 'selected' : '' ?>>
                                    <?= $i ?> day<?= $i > 1 ? 's' : '' ?>
                                </option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label><i class="fas fa-filter"></i> Temperature Filters</label>
                        <div class="temp-filters">
                            <div>
                                <label for="min_temp" class="temp-label">Min Temp (°<?= ($_SESSION['search_params']['units'] ?? 'metric') === 'metric' ? 'C' : 'F' ?>)</label>
                                <input type="number" id="min_temp" name="min_temp" class="form-control temp-input"
                                       step="0.1" placeholder="Min temperature">
                            </div>
                            <div>
                                <label for="max_temp" class="temp-label">Max Temp (°<?= ($_SESSION['search_params']['units'] ?? 'metric') === 'metric' ? 'C' : 'F' ?>)</label>
                                <input type="number" id="max_temp" name="max_temp" class="form-control temp-input"
                                       step="0.1" placeholder="Max temperature">
                            </div>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn">
                        <i class="fas fa-cloud-sun-rain"></i> Get Weather Data
                    </button>
                    
                    <button type="button" id="detect-location" class="btn" style="margin-top: 10px; background: linear-gradient(to right, #9b59b6, #e74c3c);">
                        <i class="fas fa-location-crosshairs"></i> Detect My Location
                    </button>
                </form>
            </aside>
            
            <main class="weather-display">
                <?php if (isset($_SESSION['weather_data'])): 
                    $weather = $_SESSION['weather_data'];
                    $units = $weather['units'] ?? 'metric';
                    $unitSymbol = $units === 'metric' ? '°C' : '°F';
                    $speedUnit = $units === 'metric' ? 'm/s' : 'mph';
                ?>
                    <div class="weather-header">
                        <div class="location">
                            <h2><i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($weather['location']['full']) ?></h2>
                            <p>Last updated: <?= $weather['timestamp'] ?></p>
                        </div>
                        <div class="units">
                            <span>Units: <?= strtoupper($units) ?> (<?= $unitSymbol ?>)</span>
                        </div>
                    </div>
                    
                    <section class="current-weather">
                        <div class="temperature">
                            <?php if (isset($weather['current']['temperature'])): ?>
                                <?= round($weather['current']['temperature']) ?><?= $unitSymbol ?>
                            <?php else: ?>
                                N/A
                            <?php endif; ?>
                        </div>
                        <div class="feels-like">
                            Feels like: 
                            <?php if (isset($weather['current']['feels_like'])): ?>
                                <?= round($weather['current']['feels_like']) ?><?= $unitSymbol ?>
                            <?php else: ?>
                                N/A
                            <?php endif; ?>
                        </div>
                        <div class="weather-description">
                            <h3><?= htmlspecialchars($weather['current']['description'] ?? 'No data') ?></h3>
                        </div>
                        <div class="weather-details">
                            <div class="detail-item">
                                <div class="detail-label">Humidity</div>
                                <div class="detail-value"><?= $weather['current']['humidity'] ?? 'N/A' ?>%</div>
                            </div>
                            <div class="detail-item">
                                <div class="detail-label">Wind Speed</div>
                                <div class="detail-value"><?= $weather['current']['wind_speed'] ?? 'N/A' ?> <?= $speedUnit ?></div>
                            </div>
                            <div class="detail-item">
                                <div class="detail-label">Pressure</div>
                                <div class="detail-value"><?= $weather['current']['pressure'] ?? 'N/A' ?> hPa</div>
                            </div>
                            <div class="detail-item">
                                <div class="detail-label">Sunrise</div>
                                <div class="detail-value"><?= $weather['current']['sunrise'] ?? 'N/A' ?></div>
                            </div>
                            <div class="detail-item">
                                <div class="detail-label">Sunset</div>
                                <div class="detail-value"><?= $weather['current']['sunset'] ?? 'N/A' ?></div>
                            </div>
                        </div>
                    </section>
                    
                    <?php if (!empty($weather['forecast'])): ?>
                        <section class="forecast-section">
                            <h3><i class="fas fa-calendar-week"></i> <?= count($weather['forecast']) ?>-Day Forecast</h3>
                            <div class="forecast-grid">
                                <?php foreach ($weather['forecast'] as $day): ?>
                                    <div class="forecast-day <?= isset($day['filtered']) && $day['filtered'] ? 'filtered' : '' ?>">
                                        <div class="day-header">
                                            <div class="day-name"><?= $day['day'] ?></div>
                                            <div class="day-date"><?= $day['date'] ?></div>
                                        </div>
                                        <div class="day-temp">
                                            <?= round($day['avg_temp']) ?><?= $unitSymbol ?>
                                        </div>
                                        <div class="day-details">
                                            <div>High: <?= round($day['max_temp']) ?><?= $unitSymbol ?></div>
                                            <div>Low: <?= round($day['min_temp']) ?><?= $unitSymbol ?></div>
                                            <div>Humidity: <?= round($day['avg_humidity']) ?>%</div>
                                            <div><?= htmlspecialchars($day['main_weather']) ?></div>
                                            <?php if (isset($day['filtered']) && $day['filtered']): ?>
                                                <div style="color: #e74c3c; margin-top: 5px; font-size: 0.8rem;">
                                                    <i class="fas fa-filter"></i> Filtered out
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </section>
                    <?php endif; ?>
                    
                    <?php unset($_SESSION['weather_data']); ?>
                <?php else: ?>
                    <div class="welcome-message">
                        <h2>Welcome to Weather Data Proxy</h2>
                        <p>Enter a city name to get started. You can also apply filters to view specific temperature ranges.</p>
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-top: 30px;">
                            <div style="background: linear-gradient(135deg, #3498db, #2980b9); color: white; padding: 20px; border-radius: 10px;">
                                <h3><i class="fas fa-bolt"></i> Fast</h3>
                                <p>Quick access to weather data with caching</p>
                            </div>
                            <div style="background: linear-gradient(135deg, #2ecc71, #27ae60); color: white; padding: 20px; border-radius: 10px;">
                                <h3><i class="fas fa-filter"></i> Filtered</h3>
                                <p>Apply custom filters to view specific data</p>
                            </div>
                            <div style="background: linear-gradient(135deg, #9b59b6, #8e44ad); color: white; padding: 20px; border-radius: 10px;">
                                <h3><i class="fas fa-chart-line"></i> Detailed</h3>
                                <p>Get current weather and 5-day forecasts</p>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </main>
        </div>
        
        <footer class="footer">
            <p>Weather Data Proxy &copy; <?= date('Y') ?> | 
                Powered by <a href="https://openweathermap.org" target="_blank">OpenWeatherMap</a> |
                <a href="#" onclick="alert('Cache cleared!')">Clear Cache</a>
            </p>
        </footer>
    </div>
    
    <script src="/public/js/script.js"></script>
</body>
</html>