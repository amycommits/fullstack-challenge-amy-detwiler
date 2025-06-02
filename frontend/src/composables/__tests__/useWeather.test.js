import { describe, it, expect, vi, beforeEach } from 'vitest'
import { useWeather } from '../useWeather'
import api from '../../plugins/axios'

// Mock the axios instance
vi.mock('../../plugins/axios', () => ({
  default: {
    get: vi.fn()
  }
}))

describe('useWeather', () => {
  beforeEach(() => {
    vi.clearAllMocks()
  })

  it('fetches weather data successfully', async () => {
    const mockData = [
      {
        name: 'John Doe',
        weatherInfo: {
          main: { temp: 72 },
          weather: [{ description: 'sunny' }]
        }
      }
    ]

    api.get.mockResolvedValueOnce({ data: mockData })

    const { weatherData, loading, error, fetchWeatherData } = useWeather()
    
    expect(loading.value).toBe(false)
    expect(error.value).toBeNull()
    
    await fetchWeatherData()
    
    expect(loading.value).toBe(false)
    expect(error.value).toBeNull()
    expect(weatherData.value).toEqual(mockData)
    expect(api.get).toHaveBeenCalledWith('/api/users/weather')
  })

  it('handles fetch error', async () => {
    api.get.mockRejectedValueOnce(new Error('Network error'))

    const { weatherData, loading, error, fetchWeatherData } = useWeather()
    
    expect(loading.value).toBe(false)
    expect(error.value).toBeNull()
    
    await fetchWeatherData()
    
    expect(loading.value).toBe(false)
    expect(error.value).toBe('Failed to fetch weather data')
    expect(weatherData.value).toBeNull()
    expect(api.get).toHaveBeenCalledWith('/api/users/weather')
  })
})
