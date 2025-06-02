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
        <!-- Overview Section -->
        <div class="flex justify-between items-start mb-6">
          <!-- Left: Weather Overview -->
          <div>
            <div class="text-4xl font-bold text-gray-800">
              {{ currentTemp }}°
            </div>
            <div class="capitalize text-gray-600">
              {{ weatherDescription }}
            </div>
            <div class="flex items-center space-x-2 mt-2 text-sm text-gray-500">
              <span class="flex items-center">
                ⬆ {{ maxTemp }}°
              </span>
              /
              <span class="flex items-center">
                ⬇ {{ minTemp }}°
              </span>
            </div>
          </div>

          <!-- Right: User Info -->
          <div class="text-center">
            <img
              :src="userIcon"
              alt="User"
              class="w-20 h-20 rounded-full mx-auto mb-2"
            />
            <div class="font-semibold text-gray-800">{{ user.name }}</div>
            <div class="text-xs text-gray-500">
              Last updated {{ lastUpdated }}
            </div>
          </div>
        </div>

        <!-- Weather Details Section -->
        <div class="bg-blue-700 text-white p-4 rounded-lg grid grid-cols-2 gap-4 mb-6">
          <!-- Conditional Rain/Snow -->
          <div
            v-if="rainAmount"
            class="bg-white text-blue-700 rounded p-3"
          >
            <h4 class="font-semibold mb-1">Rain</h4>
            <p>{{ rainAmount }} mm/h</p>
          </div>
          <div
            v-else-if="snowAmount"
            class="bg-white text-blue-700 rounded p-3"
          >
            <h4 class="font-semibold mb-1">Snow</h4>
            <p>{{ snowAmount }} mm/h</p>
          </div>

          <!-- Humidity -->
          <div class="bg-white text-blue-700 rounded p-3">
            <h4 class="font-semibold mb-1">Humidity</h4>
            <p>{{ humidity }}%</p>
          </div>

          <!-- Visibility -->
          <div class="bg-white text-blue-700 rounded p-3">
            <h4 class="font-semibold mb-1">Visibility</h4>
            <p>{{ visibility }} km</p>
          </div>

          <!-- Cloudiness -->
          <div class="bg-white text-blue-700 rounded p-3">
            <h4 class="font-semibold mb-1">Cloudiness</h4>
            <p>{{ cloudiness }}%</p>
          </div>
        </div>

        <!-- Wind Section -->
        <div class="bg-blue-700 text-white p-4 rounded-lg">
          <h4 class="font-semibold mb-4">Wind</h4>
          <div class="grid grid-cols-3 gap-4">
            <div class="bg-white text-blue-700 rounded p-2 text-center">
              <h5 class="font-semibold">Speed</h5>
              <p>{{ windSpeed }} m/s</p>
            </div>
            <div class="bg-white text-blue-700 rounded p-2 text-center">
              <h5 class="font-semibold">Direction</h5>
              <p>{{ windDirection }}°</p>
            </div>
            <div class="bg-white text-blue-700 rounded p-2 text-center">
              <h5 class="font-semibold">Gust</h5>
              <p>{{ windGust }} m/s</p>
            </div>
          </div>
        </div>

        <!-- Sunrise/Sunset Section -->
        <div class="bg-blue-700 text-white p-4 rounded-lg mt-6">
          <h4 class="font-semibold mb-4">Daylight</h4>
          <div class="grid grid-cols-2 gap-4">
            <div class="bg-white text-blue-700 rounded p-3">
              <h5 class="font-semibold mb-1">Sunrise</h5>
              <p>{{ sunriseTime }}</p>
            </div>
            <div class="bg-white text-blue-700 rounded p-3">
              <h5 class="font-semibold mb-1">Sunset</h5>
              <p>{{ sunsetTime }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'

interface WeatherInfo {
  dt: number;
  timezone: number;
  name?: string;
  main: {
    temp: number;
    temp_max: number;
    temp_min: number;
    humidity: number;
  };
  weather: Array<{
    description: string;
  }>;
  rain?: {
    '1h': number;
  };
  snow?: {
    '1h': number;
  };
  visibility: number;
  clouds: {
    all: number;
  };
  wind: {
    speed: number;
    deg: number;
    gust?: number;
  };
  sys: {
    sunrise: number;
    sunset: number;
  };
}

interface User {
  name: string;
  icon: string;
}

const props = defineProps<{
  showWeatherModal: boolean;
  weatherInfo: WeatherInfo;
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
    dateStyle: 'short',
    timeStyle: 'short',
    timeZone: 'UTC'
  })
})

const currentTemp = computed(() => Math.round(props.weatherInfo?.main?.temp || 0))
const maxTemp = computed(() => Math.round(props.weatherInfo?.main?.temp_max || 0))
const minTemp = computed(() => Math.round(props.weatherInfo?.main?.temp_min || 0))

const weatherDescription = computed(() => 
  props.weatherInfo?.weather?.[0]?.description || 'Unknown'
)

const rainAmount = computed(() => props.weatherInfo?.rain?.['1h'] || null)
const snowAmount = computed(() => props.weatherInfo?.snow?.['1h'] || null)

const humidity = computed(() => props.weatherInfo?.main?.humidity || 0)
const visibility = computed(() => (props.weatherInfo?.visibility || 0) / 1000)
const cloudiness = computed(() => props.weatherInfo?.clouds?.all || 0)

const windSpeed = computed(() => props.weatherInfo?.wind?.speed || 0)
const windDirection = computed(() => props.weatherInfo?.wind?.deg || 0)
const windGust = computed(() => props.weatherInfo?.wind?.gust || 'N/A')

// Format time with timezone adjustment
const formatTime = (timestamp: number | undefined) => {
  if (!timestamp) return 'Unknown'
  const localTimestamp = timestamp + props.weatherInfo.timezone
  const date = new Date(localTimestamp * 1000)
  return date.toLocaleString('en-US', {
    hour: 'numeric',
    minute: '2-digit',
    hour12: true,
    timeZone: 'UTC'
  })
}

// Sunrise and sunset times
const sunriseTime = computed(() => formatTime(props.weatherInfo?.sys?.sunrise))
const sunsetTime = computed(() => formatTime(props.weatherInfo?.sys?.sunset))

// Set default icon to profile.png from public folder
const userIcon = computed(() => props.user.icon || '/profile.png')
</script>
