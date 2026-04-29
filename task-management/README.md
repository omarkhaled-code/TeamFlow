# TaskSync - Task Management Dashboard

A modern task management application built with Vue 3, Tailwind CSS, and Pinia for state management.

## Features

- **User Authentication**: Login/Register with token-based authentication
- **Dashboard**: Comprehensive dashboard with task statistics and management
- **Task Management**: Create, read, update, and delete tasks
- **Real-time Updates**: Automatic UI updates when tasks are modified
- **Responsive Design**: Mobile-friendly interface built with Tailwind CSS
- **API Integration**: Axios-based API calls with automatic token handling

## Tech Stack

- **Vue 3** with Composition API
- **Tailwind CSS** for styling
- **Pinia** for state management
- **Vue Router** for navigation
- **Axios** for API calls
- **Vite** for build tooling

## Project Setup

```sh
# Install dependencies
npm install

# Start development server
npm run dev

# Build for production
npm run build
```

## Environment Configuration

Create a `.env` file in the root directory:

```env
VITE_API_BASE_URL=http://localhost:3000
```

## Development with Fake Data

The application currently uses **fake/mock data** for development purposes. When you visit the dashboard, it will automatically simulate a login and load sample data including:

- **Fake User**: John Doe with sample profile information
- **Sample Tasks**: 7 pre-created tasks with different priorities and completion statuses
- **Real-time Updates**: All CRUD operations work with the fake data

### Switching to Real API

To use real API data, update the methods in `src/stores/user.js` to make actual HTTP requests instead of using the fake data. The API structure is already prepared for:

```javascript
// Replace fake data calls with real API calls
const response = await api.get('/user/profile')
this.user = response.data
```

## API Endpoints

The application expects the following API endpoints:

### Authentication

- `POST /api/auth/login` - User login
- `POST /api/auth/register` - User registration

### User Profile

- `GET /api/user/profile` - Get user profile information

### Tasks

- `GET /api/tasks` - Get all user tasks
- `POST /api/tasks` - Create a new task
- `PUT /api/tasks/:id` - Update a task
- `DELETE /api/tasks/:id` - Delete a task

### Task Data Structure

```json
{
  "id": "string",
  "title": "string",
  "description": "string (optional)",
  "priority": "low|medium|high",
  "completed": "boolean",
  "createdAt": "ISO date string",
  "updatedAt": "ISO date string"
}
```

### User Data Structure

```json
{
  "id": "string",
  "name": "string",
  "email": "string",
  "avatar": "string (optional)",
  "createdAt": "ISO date string"
}
```

## Authentication

The app uses JWT tokens stored in localStorage. The token is automatically included in all API requests via axios interceptors.

## Routes

- `/` - Home page
- `/registeration` - Login/Register page
- `/dashboard` - Main dashboard (requires authentication)
- `/about` - About page

## Recommended IDE Setup

[VS Code](https://code.visualstudio.com/) + [Vue (Official)](https://marketplace.visualstudio.com/items?itemName=Vue.volar) (and disable Vetur).

## Recommended Browser Setup

- Chromium-based browsers (Chrome, Edge, Brave, etc.):
  - [Vue.js devtools](https://chromewebstore.google.com/detail/vuejs-devtools/nhdogjmejiglipccpnnnanhbledajbpd)
  - [Turn on Custom Object Formatter in Chrome DevTools](http://bit.ly/object-formatters)
- Firefox:
  - [Vue.js devtools](https://addons.mozilla.org/en-US/firefox/addon/vue-js-devtools/)
  - [Turn on Custom Object Formatter in Firefox DevTools](https://fxdx.dev/firefox-devtools-custom-object-formatters/)
# Task-Management
