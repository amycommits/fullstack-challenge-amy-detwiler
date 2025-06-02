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
    const mockUser = {
        name: 'John Doe',
        icon: '/profile.png',
        weatherInfo: {
            main: { temp: 72, humidity: 65, pressure: 1013 },
            weather: [{ description: 'sunny', icon: '01d' }],
            wind: { speed: 5 }
        }
    }

    it('renders modal with user weather details', () => {
        const wrapper = mount(UserWeatherModal, {
            props: {
                user: mockUser,
                show: true
            },
        })

        expect(wrapper.find('[data-test="modal-backdrop"]').exists()).toBe(true)
        expect(wrapper.find('[data-test="modal-title"]').text()).toBe("John Doe's Weather")
        expect(wrapper.find('[data-test="user-icon"]').attributes('src')).toBe('/profile.png')
        expect(wrapper.find('[data-test="weather-icon"]').attributes('src')).toContain('01d')
        expect(wrapper.find('[data-test="temperature"]').text()).toBe('72°F')
        expect(wrapper.find('[data-test="description"]').text()).toBe('sunny')
        expect(wrapper.find('[data-test="humidity"]').text()).toBe('Humidity: 65%')
        expect(wrapper.find('[data-test="pressure"]').text()).toBe('Pressure: 1013 hPa')
        expect(wrapper.find('[data-test="wind"]').text()).toBe('Wind Speed: 5 m/s')
    })

    it('emits close event when close button is clicked', async () => {
        const wrapper = mount(UserWeatherModal, {
            props: {
                user: mockUser,
                show: true
            },
        })

        await wrapper.find('[data-test="close-button"]').trigger('click')
        expect(wrapper.emitted('close')).toBeTruthy()
    })

    it('emits close event when clicking outside modal', async () => {
        const wrapper = mount(UserWeatherModal, {
            props: {
                user: mockUser,
                show: true
            },
        })

        await wrapper.find('[data-test="modal-backdrop"]').trigger('click')
        expect(wrapper.emitted('close')).toBeTruthy()
    })

    it('displays error message when weather data is unavailable', () => {
        const wrapper = mount(UserWeatherModal, {
            props: {
                user: {
                    name: 'John Doe',
                    icon: '/profile.png',
                    error: 'Failed to fetch weather data'
                },
                show: true
            },
        })

        expect(wrapper.find('[data-test="error-message"]').text()).toBe('Failed to fetch weather data')
    })
})
