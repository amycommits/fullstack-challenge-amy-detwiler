<template>
  <!-- Overlay -->
  <div
    v-if="showWeatherModal"
    class="fixed inset-0 bg-black/50 flex justify-center items-center z-50"
    @click.self="closeModal"
  >
    <!-- Modal -->
    <div class="bg-white rounded-lg w-[90%] max-w-3xl shadow-lg flex flex-col max-h-[90vh]">
      <!-- Fixed Header -->
      <div class="flex justify-between items-center p-6">
        <!-- Location Info -->
        <div class="flex items-center space-x-2">
          <svg class="w-6 h-6 text-blue-700" fill="currentColor" viewBox="0 0 20 20">
            <path
              d="M10 2a6 6 0 016 6c0 4.2-6 10-6 10S4 12.2 4 8a6 6 0 016-6zm0 8a2 2 0 100-4 2 2 0 000 4z"
            />
          </svg>
          <span class="font-semibold text-gray-800">{{ location }}</span>
        </div>

        <!-- Close Button -->
        <button @click="closeModal" class="text-gray-500 hover:text-red-500 text-xl font-bold">
          ×
        </button>
      </div>

      <!-- Scrollable Content -->
      <div class="p-6 overflow-y-auto">
        <OverviewSection :weather-info="weatherInfo" :user="user" />
        <WeatherDetails :weather-info="weatherInfo" />
        <WindDetails :weather-info="weatherInfo" />
        <DaylightDetails :weather-info="weatherInfo" />
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import type { WeatherInfo } from '../types/weather'
import WeatherDetails from './WeatherModalContent/WeatherDetails.vue'
import WindDetails from './WeatherModalContent/WindDetails.vue'
import DaylightDetails from './WeatherModalContent/DaylightDetails.vue'
import OverviewSection from './WeatherModalContent/OverviewSection.vue'

interface User {
  name: string;
  icon: string;
}

const props = defineProps<{
  showWeatherModal: boolean;
  weatherInfo?: WeatherInfo;
  user: User;
}>()

const emit = defineEmits<{
  (e: 'close'): void;
}>()

function closeModal() {
  emit('close')
}

const location = computed(() => props.weatherInfo?.name || 'Unknown')

const lastUpdated = computed(() => {
  if (!props.weatherInfo?.dt) return 'Unknown'
  
  const localTimestamp = props.weatherInfo.dt + props.weatherInfo.timezone
  const date = new Date(localTimestamp * 1000)
  
  return date.toLocaleString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: 'numeric',
    minute: '2-digit',
    timeZone: 'UTC'
  })
})

const currentTemp = computed(() => Math.round(props.weatherInfo?.main?.temp || 0))
const maxTemp = computed(() => Math.round(props.weatherInfo?.main?.temp_max || 0))
const minTemp = computed(() => Math.round(props.weatherInfo?.main?.temp_min || 0))

const weatherDescription = computed(() => 
  props.weatherInfo?.weather?.[0]?.description || 'Unknown'
)

// Set default icon to profile.png from public folder
const userIcon = computed(() => props.user.icon || '/profile.png')
</script>
