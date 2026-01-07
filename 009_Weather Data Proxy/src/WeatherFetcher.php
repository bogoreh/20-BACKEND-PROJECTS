<?php
class WeatherFetcher {
    private $apiKey;
    private $cacheDir;
    
    public function __construct() {
        $this->apiKey = OPENWEATHER_API_KEY;
        $this->cacheDir = __DIR__ . '/../cache/';
        
        // Create cache directory if it doesn't exist
        if (!file_exists($this->cacheDir)) {
            mkdir($this->cacheDir, 0755, true);
        }
    }
    
    public function getWeather($city, $country = '', $units = 'metric', $days = 5) {
        $location = $city . ($country ? ',' . $country : '');
        $cacheKey = md5("weather_{$location}_{$units}_{$days}");
        $cacheFile = $this->cacheDir . $cacheKey . '.json';
        
        // Check cache first
        if (file_exists($cacheFile) && (time() - filemtime($cacheFile)) < CACHE_DURATION) {
            return json_decode(file_get_contents($cacheFile), true);
        }
        
        // Fetch current weather
        $currentWeather = $this->fetchCurrentWeather($location, $units);
        
        // Fetch forecast
        $forecast = $this->fetchForecast($location, $units, $days);
        
        $weatherData = [
            'current' => $currentWeather,
            'forecast' => $forecast,
            'location' => [
                'city' => $city,
                'country' => $country,
                'full' => $location
            ],
            'units' => $units,
            'timestamp' => date('Y-m-d H:i:s')
        ];
        
        // Cache the data
        file_put_contents($cacheFile, json_encode($weatherData));
        
        return $weatherData;
    }
    
    private function fetchCurrentWeather($location, $units) {
        $url = WEATHER_API_URL . "?q=" . urlencode($location) . 
               "&appid=" . $this->apiKey . 
               "&units=" . $units;
        
        $response = $this->makeApiCall($url);
        return $this->parseCurrentWeather($response);
    }
    
    private function fetchForecast($location, $units, $days) {
        $url = FORECAST_API_URL . "?q=" . urlencode($location) . 
               "&appid=" . $this->apiKey . 
               "&units=" . $units . 
               "&cnt=" . ($days * 8); // 8 forecasts per day
        
        $response = $this->makeApiCall($url);
        return $this->parseForecast($response, $days);
    }
    
    private function makeApiCall($url) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode !== 200) {
            throw new Exception("API request failed with code: $httpCode");
        }
        
        $data = json_decode($response, true);
        
        if (isset($data['cod']) && $data['cod'] != 200) {
            throw new Exception($data['message'] ?? 'Unknown API error');
        }
        
        return $data;
    }
    
    private function parseCurrentWeather($data) {
        return [
            'temperature' => $data['main']['temp'] ?? null,
            'feels_like' => $data['main']['feels_like'] ?? null,
            'humidity' => $data['main']['humidity'] ?? null,
            'pressure' => $data['main']['pressure'] ?? null,
            'description' => $data['weather'][0]['description'] ?? null,
            'icon' => $data['weather'][0]['icon'] ?? null,
            'wind_speed' => $data['wind']['speed'] ?? null,
            'wind_direction' => $data['wind']['deg'] ?? null,
            'clouds' => $data['clouds']['all'] ?? null,
            'sunrise' => isset($data['sys']['sunrise']) ? date('H:i', $data['sys']['sunrise']) : null,
            'sunset' => isset($data['sys']['sunset']) ? date('H:i', $data['sys']['sunset']) : null
        ];
    }
    
    private function parseForecast($data, $days) {
        $forecast = [];
        $dailyData = [];
        
        if (!isset($data['list'])) {
            return $forecast;
        }
        
        // Group by day
        foreach ($data['list'] as $item) {
            $date = date('Y-m-d', $item['dt']);
            
            if (!isset($dailyData[$date])) {
                $dailyData[$date] = [];
            }
            
            $dailyData[$date][] = [
                'time' => date('H:i', $item['dt']),
                'temp' => $item['main']['temp'],
                'feels_like' => $item['main']['feels_like'],
                'humidity' => $item['main']['humidity'],
                'description' => $item['weather'][0]['description'],
                'icon' => $item['weather'][0]['icon'],
                'wind_speed' => $item['wind']['speed']
            ];
        }
        
        // Take only the requested number of days
        $dailyData = array_slice($dailyData, 0, $days);
        
        // Calculate daily averages
        foreach ($dailyData as $date => $readings) {
            $temps = array_column($readings, 'temp');
            $humidity = array_column($readings, 'humidity');
            
            $forecast[] = [
                'date' => $date,
                'day' => date('D', strtotime($date)),
                'min_temp' => min($temps),
                'max_temp' => max($temps),
                'avg_temp' => array_sum($temps) / count($temps),
                'avg_humidity' => array_sum($humidity) / count($humidity),
                'main_weather' => $readings[0]['description'],
                'icon' => $readings[0]['icon'],
                'readings' => $readings
            ];
        }
        
        return $forecast;
    }
}
?>