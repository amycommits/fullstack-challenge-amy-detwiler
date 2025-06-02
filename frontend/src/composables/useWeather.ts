import { ref } from 'vue'
import api from '../plugins/axios'

interface WeatherInfo {
  main: {
    temp: number;
  };
  weather: Array<{
    description: string;
    icon: string;
  }>;
  name?: string;
}

interface UserWeather {
  name: string;
  weatherInfo: WeatherInfo;
}

export function useWeather() {
  const weatherData = ref<UserWeather[] | null>(null)
  const loading = ref(false)
  const error = ref<string | null>(null)

  const fetchWeatherData = async () => {
    loading.value = true
    error.value = null
    
    try {
      const response = await api.get<UserWeather[]>('/api/users/weather')
      weatherData.value = response.data
    } catch (e) {
      error.value = e instanceof Error ? e.message : 'Failed to fetch weather data'
    } finally {
      loading.value = false
    }
  }

  return {
    weatherData,
    loading,
    error,
    fetchWeatherData
  }
} 