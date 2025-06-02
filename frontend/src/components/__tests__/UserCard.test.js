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
        weatherInfo: {
            main: { temp: 72 },
            weather: [{ description: 'sunny' }]
        }
    }

    it('renders user information correctly', () => {
        const wrapper = mount(UserCard, {
            props: {
                user: mockUser
            }
        })

        expect(wrapper.find('[data-test="user-name"]').text()).toBe('John Doe')
        expect(wrapper.find('[data-test="user-icon"]').attributes('src')).toBe('/profile.png')
        expect(wrapper.find('[data-test="weather-description"]').text()).toBe('sunny')
        expect(wrapper.find('[data-test="temperature"]').text()).toBe('72°F')
    })

    it('emits click event when card is clicked', async () => {
        const wrapper = mount(UserCard, {
            props: {
                user: mockUser
            }
        })

        await wrapper.trigger('click')
        expect(wrapper.emitted('click')).toBeTruthy()
        expect(wrapper.emitted('click')[0][0]).toEqual(mockUser)
    })

    it('displays error message when weather data is not available', () => {
        const wrapper = mount(UserCard, {
            props: {
                user: {
                    name: 'John Doe',
                    icon: '/profile.png'
                }
            }
        })

        expect(wrapper.find('[data-test="error-message"]').text()).toBe('Weather data unavailable')
    })
}) 
