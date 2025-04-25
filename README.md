# ☁️ Weather App Backend (Laravel)

Provides city suggestions and weather data via API for a frontend weather dashboard.

---

## 🔗 Integrates with OpenWeatherMap API

---

## 🚀 Features

### ✅ City Autocomplete

-   `GET /api/city-suggestions?q={query}` — Fetches matching city names

### ✅ Weather Data

-   `GET /api/weather?city={name}&unit=C|F` — Returns current weather details for a given city with temperature in Celsius or Fahrenheit

---

## 🛠️ Tech Stack

-   **Backend**: Laravel 12.9.2
-   **PHP**: 8.3.6
-   **Database**: None (uses external APIs only)

### Key Dependencies

-   `laravel/framework`
-   `guzzlehttp/guzzle` — HTTP Client for API calls
-   `fruitcake/laravel-cors` — CORS middleware
-   `laravel/tinker` (optional for CLI debugging)

---

## ⚙️ Setup & Installation

### 1. Clone the Repository

```bash
git clone https://github.com/your-repo/weather-backend.git
cd weather-backend
```

### 2. Install Dependencies

```bash
composer install
```

### 3. Set Up Environment Variables

Copy `.env.example` to `.env` and add your API key:

```ini
OPENWEATHER_API_KEY=your_api_key_here
```

### 4. Run the Backend

```bash
php artisan serve
```

Runs at `http://localhost:8000` by default.

---

## 🌐 API Documentation

### 1. City Suggestions

-   **Endpoint:** `GET /api/city-suggestions?q=Berlin`

#### Example Response:

```json
["Berlin, Germany", "Berlin, Connecticut, US", "Berlin, New Hampshire, US"]
```

### 2. Weather Data

-   **Endpoint:** `GET /api/weather?city=Berlin&unit=C`

#### Example Response:

```json
{
    "city": "Berlin",
    "temperature": 22.5,
    "unit": "C",
    "description": "Partly Cloudy",
    "icon": "02d",
    "humidity": 65,
    "wind_speed": 4.5,
    "forecast": []
}
```

---

## 🚨 Rate Limiting & Caching

-   **Rate Limiting**: Enabled (default Laravel throttle middleware)
-   **Caching**: Responses are cached for 10 minutes to reduce OpenWeatherMap API usage

---

## 📜 License

MIT License - Free for open-source use.

---

## 📩 Contributing

For bug reports or feature suggestions, reach out to: [iankabaka1@gmail.com](mailto:iankabaka1@gmail.com)
