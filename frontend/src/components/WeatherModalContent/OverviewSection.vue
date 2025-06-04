<template>
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
</template>

<script setup lang="ts">
import { computed } from 'vue'
import type { WeatherInfo } from '../../types/weather'

interface User {
  name: string;
  icon: string;
}

const props = defineProps<{
  weatherInfo?: WeatherInfo;
  user: User;
}>()

const currentTemp = computed(() => Math.round(props.weatherInfo?.main?.temp || 0))
const maxTemp = computed(() => Math.round(props.weatherInfo?.main?.temp_max || 0))
const minTemp = computed(() => Math.round(props.weatherInfo?.main?.temp_min || 0))

const weatherDescription = computed(() => 
  props.weatherInfo?.weather?.[0]?.description || 'Unknown'
)

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

// Set default icon to profile.png from public folder
const userIcon = computed(() => props.user.icon || '/profile.png')
</script> 