# ☁️ Weather App Backend (Laravel)

Provides city suggestions and weather data via API for a frontend weather dashboard.

---

## 🔗 Integrates with OpenWeatherMap API

---

## 🚀 Features

### ✅ City Autocomplete

-   `GET /api/city-suggestions?q={query}` — Fetches matching city names

### ✅ Weather Data

-   `GET /api/weather?city={query}&unit=${query}` — Returns current weather details for a given city with temperature in Celsius or Fahrenheit

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
git clone https://github.com/kabakadev/weather-backend
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

-   **Endpoint:** `GET http://localhost:8000/api/city-suggestions?q=Ber`

#### Example Response:

```json
[
    "Berg, Grand Est, FR",
    "Veria, Macedonia and Thrace, GR",
    "B\u00e9r, HU",
    "Ber, Timbuktu, ML",
    "Ber, Rajasthan, IN"
]
```

### 2. Weather Data

-   **Endpoint:** `GET http://localhost:8000/api/weather?city=Nairobi&unit=metric`

#### Example Response:

```json
{
    "city": "Nairobi, Nairobi County",
    "unit": "C",
    "temperature": 20,
    "description": "overcast clouds",
    "icon": "04d",
    "humidity": 70,
    "wind_speed": 3.38,
    "wind_direction": 74,
    "forecast": [
        {
            "day": "26 Apr",
            "icon": "01d",
            "high": 25,
            "low": 14,
            "description": "clear sky"
        },
        {
            "day": "27 Apr",
            "icon": "02d",
            "high": 26,
            "low": 14,
            "description": "few clouds"
        },
        {
            "day": "28 Apr",
            "icon": "03d",
            "high": 25,
            "low": 16,
            "description": "scattered clouds"
        }
    ]
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

For bug reports or feature suggestions, reach out to: my personal [email](mailto:iankabaka1@gmail.com)
