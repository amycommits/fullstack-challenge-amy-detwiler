import { describe, it, expect, vi } from 'vitest'
import { useWeather } from '../useWeather'
import api from '../../plugins/axios'

describe('useWeather', () => {
  it('should fetch weather data successfully', async () => {
    const mockWeatherData = [
      {
        name: 'Test User',
        icon: '/profile.png',
        weatherInfo: {
          main: { temp: 72 },
          weather: [{ description: 'sunny', icon: '01d' }],
        },
        last_updated: expect.any(Number),
        error: undefined
      }
    ]
    vi.spyOn(api, 'get').mockResolvedValueOnce({ data: mockWeatherData })

    const { weatherData, loading, error, fetchWeatherData } = useWeather()
    await fetchWeatherData()

    expect(loading.value).toBe(false)
    expect(error.value).toBe(null)
    expect(weatherData.value).toEqual(
      expect.arrayContaining([
        expect.objectContaining({
          name: 'Test User',
          icon: '/profile.png',
          weatherInfo: expect.objectContaining({
            main: expect.objectContaining({ temp: 72 }),
            weather: expect.arrayContaining([expect.objectContaining({ description: 'sunny' })])
          }),
          last_updated: expect.any(Number),
          error: undefined
        })
      ])
    )
    expect(api.get).toHaveBeenCalledWith('/api/users/weather')
  })

  it('should handle errors when fetching weather data', async () => {
    vi.spyOn(api, 'get').mockRejectedValueOnce(new Error('Network error'))

    const { weatherData, loading, error, fetchWeatherData } = useWeather()
    await fetchWeatherData()

    expect(loading.value).toBe(false)
    expect(weatherData.value).toBe(null)
    expect(error.value).toBe('Failed to fetch weather data')
    expect(api.get).toHaveBeenCalledWith('/api/users/weather')
  })
}) 