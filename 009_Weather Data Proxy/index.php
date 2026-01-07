<?php
session_start();
require_once 'config/constants.php';
require_once 'config/api_keys.php';
require_once 'src/WeatherFetcher.php';
require_once 'src/WeatherFilter.php';

// Enable error reporting for debugging (disable in production)
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Handle routing
$request = $_GET['route'] ?? 'home';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    handleWeatherRequest();
} else {
    displayWeatherForm();
}

function displayWeatherForm() {
    include 'templates/weather.php';
}

function handleWeatherRequest() {
    $city = $_POST['city'] ?? '';
    $country = $_POST['country'] ?? '';
    $units = $_POST['units'] ?? 'metric';
    $days = $_POST['forecast_days'] ?? 5;
    $min_temp = $_POST['min_temp'] ?? null;
    $max_temp = $_POST['max_temp'] ?? null;
    
    // Validate input
    if (empty($city)) {
        $_SESSION['error'] = "Please enter a city name";
        header('Location: /');
        exit;
    }
    
    $fetcher = new WeatherFetcher();
    $filter = new WeatherFilter();
    
    try {
        // Fetch weather data
        $weatherData = $fetcher->getWeather($city, $country, $units, $days);
        
        // Apply filters if provided
        $filteredData = $filter->applyFilters($weatherData, [
            'min_temp' => $min_temp,
            'max_temp' => $max_temp,
            'units' => $units
        ]);
        
        // Store in session for display
        $_SESSION['weather_data'] = $filteredData;
        $_SESSION['search_params'] = [
            'city' => $city,
            'country' => $country,
            'units' => $units
        ];
        
        header('Location: /');
        exit;
        
    } catch (Exception $e) {
        $_SESSION['error'] = "Error fetching weather data: " . $e->getMessage();
        header('Location: /');
        exit;
    }
}
?>