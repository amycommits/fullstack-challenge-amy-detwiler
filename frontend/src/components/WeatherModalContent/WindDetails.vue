<template>
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
</template>

<script setup lang="ts">
import { computed } from 'vue'
import type { WeatherInfo } from '../../types/weather'

const props = defineProps<{
  weatherInfo?: WeatherInfo;
}>()

const windSpeed = computed(() => props.weatherInfo?.wind?.speed || 0)
const windDirection = computed(() => props.weatherInfo?.wind?.deg || 0)
const windGust = computed(() => props.weatherInfo?.wind?.gust || 'N/A')
</script>

<script lang="ts">
export default {
  name: 'WindDetails'
}
</script> 