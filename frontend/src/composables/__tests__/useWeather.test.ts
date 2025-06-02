import { describe, it, expect, vi, beforeEach } from 'vitest'
import { useWeather } from '../useWeather'
import api from '@/plugins/axios'

vi.mock('@/plugins/axios', () => ({
  default: {
    get: vi.fn(),
  },
}))

describe('useWeather', () => {
  beforeEach(() => {
    vi.clearAllMocks()
  })

  it('should fetch weather data successfully', async () => {
    const mockWeatherData = [
      {
        name: 'Test User',
        weatherInfo: {
          main: { temp: 72 },
          weather: [{ description: 'sunny', icon: '01d' }],
        },
      },
    ]

    vi.mocked(api.get).mockResolvedValueOnce({ data: mockWeatherData })

    const { weatherData, loading, error, fetchWeatherData } = useWeather()
    
    expect(loading.value).toBe(false)
    expect(weatherData.value).toBe(null)
    expect(error.value).toBe(null)

    await fetchWeatherData()

    expect(loading.value).toBe(false)
    expect(weatherData.value).toEqual(mockWeatherData)
    expect(error.value).toBe(null)
    expect(api.get).toHaveBeenCalledWith('/api/users/weather')
  })

  it('should handle errors when fetching weather data', async () => {
    const mockError = new Error('Network error')
    vi.mocked(api.get).mockRejectedValueOnce(mockError)

    const { weatherData, loading, error, fetchWeatherData } = useWeather()
    
    await fetchWeatherData()

    expect(loading.value).toBe(false)
    expect(weatherData.value).toBe(null)
    expect(error.value).toBe('Network error')
    expect(api.get).toHaveBeenCalledWith('/api/users/weather')
  })
}) 