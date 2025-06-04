<template>
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
</template>

<script setup lang="ts">
import { computed } from 'vue'
import type { WeatherInfo } from '../../types/weather'

const props = defineProps<{
  weatherInfo?: WeatherInfo;
}>()

const rainAmount = computed(() => props.weatherInfo?.rain?.['1h'] || null)
const snowAmount = computed(() => props.weatherInfo?.snow?.['1h'] || null)
const humidity = computed(() => props.weatherInfo?.main?.humidity || 0)
const visibility = computed(() => (props.weatherInfo?.visibility || 0) / 1000)
const cloudiness = computed(() => props.weatherInfo?.clouds?.all || 0)
</script>

<script lang="ts">
export default {
  name: 'WeatherDetails'
}
</script> 