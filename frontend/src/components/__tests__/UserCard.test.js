import { describe, it, expect, vi } from 'vitest'
import { mount } from '@vue/test-utils'
import UserCard from '../UserCard.vue'

// Mock the environment variable
vi.mock('../composables/useWeather', () => ({
  useWeather: () => ({
    temperatureUnit: '°F'
  })
}))

describe('UserCard', () => {
    const mockUser = {
        name: 'John Doe',
        icon: '/profile.png',
        last_updated: 1748897299
    }

    const mockWeatherInfo = {
        main: { temp: 72 },
        weather: [{ description: 'sunny', icon: '01d' }],
        name: 'New York'
    }

    it('renders user information correctly', () => {
        const wrapper = mount(UserCard, {
            props: {
                user: mockUser,
                weatherInfo: mockWeatherInfo
            }
        })

        expect(wrapper.find('h3').text()).toBe('John Doe')
        expect(wrapper.find('img').attributes('src')).toBe('/profile.png')
        expect(wrapper.find('.text-sm.capitalize').text()).toBe('sunny')
        expect(wrapper.find('.text-xl.font-semibold').text()).toBe('72°')
        expect(wrapper.find('.text-sm.text-gray-600').text()).toBe('New York')
    })

    it('emits click event when card is clicked', async () => {
        const wrapper = mount(UserCard, {
            props: {
                user: mockUser,
                weatherInfo: mockWeatherInfo
            }
        })

        await wrapper.find('img').trigger('click')
        expect(wrapper.findComponent({ name: 'UserWeatherModal' }).exists()).toBe(true)
    })

    it('displays error message when user has error', () => {
        const wrapper = mount(UserCard, {
            props: {
                user: {
                    ...mockUser,
                    error: 'Failed to fetch weather data'
                },
                weatherInfo: mockWeatherInfo
            }
        })

        expect(wrapper.find('.text-red-500').text()).toBe('Failed to fetch weather data')
    })
}) 
