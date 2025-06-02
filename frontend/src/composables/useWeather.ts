import { ref } from 'vue'
import api from '../plugins/axios'

interface WeatherInfo {
  main: {
    temp: number;
    humidity: number;
    pressure: number;
    temp_max: number;
    temp_min: number;
  };
  weather: Array<{
    description: string;
    icon: string;
  }>;
  wind: {
    speed: number;
    deg: number;
    gust?: number;
  };
  name?: string;
  dt: number;
  timezone: number;
  visibility: number;
  clouds: {
    all: number;
  };
  sys: {
    sunrise: number;
    sunset: number;
  };
}

interface User {
  name: string;
  icon: string;
  weatherInfo?: WeatherInfo;
  error?: string;
  last_updated?: number;
}

const getTemperatureUnit = () => {
  const unit = import.meta.env.OPENWEATHER_UNIT || 'imperial'
  console.log('Temperature unit from env:', unit)
  switch (unit) {
    case 'metric':
      return '°C'
    case 'imperial':
      return '°F'
    default:
      return '°K'
  }
}

export function useWeather() {
  const weatherData = ref<User[] | null>(null)
  const loading = ref(false)
  const error = ref<string | null>(null)
  const temperatureUnit = ref(getTemperatureUnit())

  const fetchWeatherData = async () => {
    loading.value = true
    error.value = null
    
    try {
      console.log('Fetching weather data...')
      const response = await api.get<User[]>('/api/users/weather')
      console.log('Weather data response:', response.data)
      
      // Ensure each user has the required properties
      weatherData.value = response.data.map(user => ({
        name: user.name || 'Unknown',
        icon: user.icon || '/profile.png',
        weatherInfo: user.weatherInfo,
        error: user.error,
        last_updated: user.last_updated || Date.now()
      }))
    } catch (e) {
      console.error('Error fetching weather data:', e)
      error.value = 'Failed to fetch weather data'
    } finally {
      loading.value = false
    }
  }

  return {
    weatherData,
    loading,
    error,
    temperatureUnit,
    fetchWeatherData
  }
}
