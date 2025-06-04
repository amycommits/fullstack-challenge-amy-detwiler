<template>
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
</template>

<script setup lang="ts">
import { computed } from 'vue'
import type { WeatherInfo } from '../../types/weather'

const props = defineProps<{
  weatherInfo?: WeatherInfo;
}>()

// Format time with timezone adjustment
const formatTime = (timestamp: number | undefined) => {
  if (!timestamp || !props.weatherInfo?.timezone) return 'Unknown'
  const localTimestamp = timestamp + props.weatherInfo.timezone
  const date = new Date(localTimestamp * 1000)
  return date.toLocaleString('en-US', {
    hour: 'numeric',
    minute: '2-digit',
    hour12: true,
    timeZone: 'UTC'
  })
}

const sunriseTime = computed(() => formatTime(props.weatherInfo?.sys?.sunrise))
const sunsetTime = computed(() => formatTime(props.weatherInfo?.sys?.sunset))
</script>

<script lang="ts">
export default {
  name: 'DaylightDetails'
}
</script> 