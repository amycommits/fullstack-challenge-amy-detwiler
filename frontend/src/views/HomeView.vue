<script setup lang="ts">
import { useWeather } from '../composables/useWeather';
import { onMounted } from 'vue';
import UserCard from '../components/UserCard.vue';

const { weatherData, loading, error, fetchWeatherData } = useWeather();

onMounted(() => {
  fetchWeatherData();
});
</script>

<template>
  <main>
    <h1>Weather Data</h1>
    <div class="weather-container">
      <div v-if="loading" class="loading">
        Loading weather data...
      </div>
      
      <div v-else-if="error" class="error">
        {{ error }}
      </div>
      
      <div v-else-if="weatherData" class="weather-data">
        <UserCard
          v-for="user in weatherData"
          :key="user.name"
          :user="user"
          :weatherInfo="user.weatherInfo"
        />
      </div>
    </div>
  </main>
</template>

<style scoped>
.weather-container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 2rem;
}

.loading, .error {
  text-align: center;
  padding: 2rem;
}

.error {
  color: #dc2626;
}

.weather-data {
  display: flex;
  flex-direction: column;
  gap: 1rem;
  background-color: #f3f4f6;
  padding: 1rem;
  border-radius: 0.5rem;
}
</style>
