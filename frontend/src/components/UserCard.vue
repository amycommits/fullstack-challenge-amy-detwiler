<template>
  <div class="flex justify-between items-center border rounded p-4 shadow">
    <!-- Left Section -->
    <div class="flex items-center space-x-4">
      <img
        :src="userIcon"
        alt="User Icon"
        class="w-20 h-20 rounded-full cursor-pointer"
        @click="openModal"
      />
      <div class="text-center">
        <h3 class="text-lg font-bold">{{ userName }}</h3>
        <p class="text-sm text-gray-600">{{ userLocation }}</p>
        <p v-if="lastUpdated" class="text-xs text-gray-500">
          Last updated: {{ lastUpdated }}
        </p>
        <p v-if="hasError" class="text-xs text-red-500">
          {{ user.error }}
        </p>
      </div>
    </div>

    <!-- Right Section -->
    <div v-if="!hasError" class="flex items-center space-x-4">
      <div v-if="weatherData" class="text-center">
        <p class="text-xl font-semibold">
          {{ formattedTemperature }}
        </p>
        <p class="text-sm capitalize">{{ weatherData.description }}</p>
      </div>
      <div v-else class="text-center">
        <p class="text-sm text-gray-500">Loading weather data...</p>
      </div>
      <img
        v-if="hasWeatherIcon"
        :src="weatherIcon"
        alt="Weather Icon"
        class="w-12 h-12"
      />
    </div>

    <!-- Modal -->
    <UserWeatherModal
      :show-weather-modal="isModalVisible"
      :user="user"
      :weather-info="weatherInfo"
      @close="closeModal"
    />
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'
import UserWeatherModal from "./UserWeatherModal.vue";

interface WeatherInfo {
  main?: {
    temp: number;
  };
  weather?: Array<{
    description: string;
    icon: string;
  }>;
  name?: string;
}

interface User {
  name: string;
  icon: string;
  last_updated?: number;
  error?: string;
}

const props = defineProps<{
  user: User;
  weatherInfo?: WeatherInfo;
}>()

// Set default icon to profile.png from public folder
const userIcon = computed(() => props.user.icon || '/profile.png')

const userName = computed(() => props.user.name || 'Anonymous')

const lastUpdated = computed(() => {
  if (!props.user.last_updated) return null;
  return new Date(props.user.last_updated * 1000).toLocaleString();
})

const isModalVisible = ref(false)

function openModal() {
  isModalVisible.value = true;
}

function closeModal() {
  isModalVisible.value = false;
}

const weatherIcon = computed(() => {
  if (!props.weatherInfo?.weather?.[0]?.icon) return ''
  return `https://openweathermap.org/img/wn/${props.weatherInfo.weather[0].icon}@2x.png`
})

const userLocation = computed(() => {
  return props.weatherInfo?.name || 'Location not available'
})

const hasError = computed(() => !!props.user.error)

const hasWeatherInfo = computed(() => 
  !hasError.value && 
  !!props.weatherInfo?.main && 
  !!props.weatherInfo?.weather?.[0]
)

const weatherData = computed(() => {
  if (!hasWeatherInfo.value || !props.weatherInfo) return null;
  const temp = props.weatherInfo.main?.temp;
  const description = props.weatherInfo.weather?.[0]?.description;
  if (typeof temp !== 'number' || !description) return null;
  return {
    temp,
    description
  };
});

const formattedTemperature = computed(() => {
  if (!weatherData.value) return '';
  return `${Math.round(weatherData.value.temp)}°`;
});

const hasWeatherIcon = computed(() => 
  !!props.weatherInfo?.weather?.[0]?.icon
);
</script>
