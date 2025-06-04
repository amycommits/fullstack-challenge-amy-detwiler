export interface WeatherInfo {
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
    icon?: string;
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