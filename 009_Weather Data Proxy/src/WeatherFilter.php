<?php
class WeatherFilter {
    public function applyFilters($weatherData, $filters) {
        $filteredData = $weatherData;
        
        // Filter by temperature range
        if (isset($filters['min_temp']) && is_numeric($filters['min_temp'])) {
            $filteredData = $this->filterByMinTemp($filteredData, (float)$filters['min_temp']);
        }
        
        if (isset($filters['max_temp']) && is_numeric($filters['max_temp'])) {
            $filteredData = $this->filterByMaxTemp($filteredData, (float)$filters['max_temp']);
        }
        
        return $filteredData;
    }
    
    private function filterByMinTemp($weatherData, $minTemp) {
        // Filter current weather
        if (isset($weatherData['current']['temperature']) && 
            $weatherData['current']['temperature'] < $minTemp) {
            $weatherData['current']['temperature'] = null;
        }
        
        // Filter forecast
        if (isset($weatherData['forecast'])) {
            foreach ($weatherData['forecast'] as &$day) {
                if ($day['avg_temp'] < $minTemp) {
                    $day['filtered'] = true;
                }
            }
        }
        
        return $weatherData;
    }
    
    private function filterByMaxTemp($weatherData, $maxTemp) {
        // Filter current weather
        if (isset($weatherData['current']['temperature']) && 
            $weatherData['current']['temperature'] > $maxTemp) {
            $weatherData['current']['temperature'] = null;
        }
        
        // Filter forecast
        if (isset($weatherData['forecast'])) {
            foreach ($weatherData['forecast'] as &$day) {
                if ($day['avg_temp'] > $maxTemp) {
                    $day['filtered'] = true;
                }
            }
        }
        
        return $weatherData;
    }
}
?>