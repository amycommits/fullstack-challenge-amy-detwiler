import { describe, it, expect, vi } from 'vitest'
import { mount } from '@vue/test-utils'
import UserWeatherModal from '../UserWeatherModal.vue'

// Mock the environment variable
vi.mock('../composables/useWeather', () => ({
  useWeather: () => ({
    temperatureUnit: '°F'
  })
}))

describe('UserWeatherModal', () => {
    const mockWeatherInfo = {
        dt: 1748897348,
        timezone: 0,
        name: 'New York',
        main: {
            temp: 72,
            temp_max: 75,
            temp_min: 68,
            humidity: 65,
            pressure: 1013
        },
        weather: [{ description: 'sunny', icon: '01d' }],
        visibility: 10000,
        clouds: { all: 20 },
        wind: {
            speed: 5,
            deg: 180,
            gust: 8
        },
        sys: {
            sunrise: 1748897348,
            sunset: 1748897348
        }
    }

    const mockUser = {
        name: 'John Doe',
        icon: '/profile.png'
    }

    it('renders modal with user weather details', () => {
        const wrapper = mount(UserWeatherModal, {
            props: {
                user: mockUser,
                weatherInfo: mockWeatherInfo,
                showWeatherModal: true
            }
        })

        expect(wrapper.find('.fixed.inset-0').exists()).toBe(true)
        expect(wrapper.find('.font-semibold.text-gray-800').text()).toBe('New York')
        expect(wrapper.find('img').attributes('src')).toBe('/profile.png')
        expect(wrapper.find('.text-4xl.font-bold').text()).toBe('72°')
        expect(wrapper.find('.capitalize.text-gray-600').text()).toBe('sunny')
    })

    it('emits close event when close button is clicked', async () => {
        const wrapper = mount(UserWeatherModal, {
            props: {
                user: mockUser,
                weatherInfo: mockWeatherInfo,
                showWeatherModal: true
            }
        })

        await wrapper.find('button').trigger('click')
        expect(wrapper.emitted('close')).toBeTruthy()
    })

    it('emits close event when clicking outside modal', async () => {
        const wrapper = mount(UserWeatherModal, {
            props: {
                user: mockUser,
                weatherInfo: mockWeatherInfo,
                showWeatherModal: true
            }
        })

        await wrapper.find('.fixed.inset-0').trigger('click')
        expect(wrapper.emitted('close')).toBeTruthy()
    })

    it('displays error message when weather data is unavailable', () => {
        const wrapper = mount(UserWeatherModal, {
            props: {
                user: mockUser,
                weatherInfo: null,
                showWeatherModal: true
            }
        })

        expect(wrapper.find('.text-gray-800').text()).toBe('Unknown')
    })
})
