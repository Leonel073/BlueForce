# EJEMPLOS DE CONSUMO DE API DESDE FRONTEND

## 📌 OPCIÓN 1: JAVASCRIPT VANILLA (Fetch API)

### 1.1 Configuración Inicial

```javascript
// config/api.js
const API_BASE_URL = 'http://localhost:8000/api/v1';

class ApiClient {
  constructor() {
    this.token = localStorage.getItem('api_token');
  }

  // Obtener headers con autenticación
  getHeaders(contentType = 'application/json') {
    return {
      'Content-Type': contentType,
      'Accept': 'application/json',
      ...(this.token && { 'Authorization': `Bearer ${this.token}` })
    };
  }

  // Método genérico para requests
  async request(endpoint, options = {}) {
    const url = `${API_BASE_URL}${endpoint}`;
    const config = {
      ...options,
      headers: this.getHeaders(options.headers?.['Content-Type'])
    };

    try {
      const response = await fetch(url, config);
      const data = await response.json();

      if (!response.ok) {
        throw new Error(data.message || 'Error en la solicitud');
      }

      return data;
    } catch (error) {
      console.error('API Error:', error);
      throw error;
    }
  }

  // GET
  async get(endpoint) {
    return this.request(endpoint, { method: 'GET' });
  }

  // POST
  async post(endpoint, body) {
    return this.request(endpoint, {
      method: 'POST',
      body: JSON.stringify(body)
    });
  }

  // PUT
  async put(endpoint, body) {
    return this.request(endpoint, {
      method: 'PUT',
      body: JSON.stringify(body)
    });
  }

  // DELETE
  async delete(endpoint) {
    return this.request(endpoint, { method: 'DELETE' });
  }
}

export default new ApiClient();
```

### 1.2 Login

```javascript
// auth/login.js
import apiClient from '../config/api';

async function login(email, password) {
  try {
    const response = await apiClient.post('/auth/login', {
      email,
      password
    });

    if (response.success) {
      // Guardar token
      localStorage.setItem('api_token', response.data.token);
      localStorage.setItem('user', JSON.stringify(response.data.user));
      
      // Actualizar cliente API
      apiClient.token = response.data.token;

      return response.data;
    }
  } catch (error) {
    console.error('Login failed:', error);
    throw error;
  }
}

async function logout() {
  try {
    await apiClient.post('/auth/logout', {});
    localStorage.removeItem('api_token');
    localStorage.removeItem('user');
    apiClient.token = null;
  } catch (error) {
    console.error('Logout failed:', error);
  }
}

export { login, logout };
```

### 1.3 Listar Documentos

```javascript
// services/correspondencia.js
import apiClient from '../config/api';

async function getDocumentos(page = 1, perPage = 10, filters = {}) {
  try {
    const params = new URLSearchParams({
      page,
      per_page: perPage,
      ...filters
    });

    const response = await apiClient.get(`/documentos?${params}`);
    return response;
  } catch (error) {
    console.error('Error fetching documentos:', error);
    throw error;
  }
}

async function getDocumento(id) {
  try {
    const response = await apiClient.get(`/documentos/${id}`);
    return response.data;
  } catch (error) {
    console.error('Error fetching documento:', error);
    throw error;
  }
}

async function createDocumento(data) {
  try {
    const response = await apiClient.post('/documentos', data);
    return response.data;
  } catch (error) {
    console.error('Error creating documento:', error);
    throw error;
  }
}

async function updateDocumento(id, data) {
  try {
    const response = await apiClient.put(`/documentos/${id}`, data);
    return response.data;
  } catch (error) {
    console.error('Error updating documento:', error);
    throw error;
  }
}

async function deleteDocumento(id) {
  try {
    await apiClient.delete(`/documentos/${id}`);
    return true;
  } catch (error) {
    console.error('Error deleting documento:', error);
    throw error;
  }
}

export { getDocumentos, getDocumento, createDocumento, updateDocumento, deleteDocumento };
```

### 1.4 Uso en HTML

```html
<!DOCTYPE html>
<html>
<head>
  <title>Correspondencia</title>
</head>
<body>
  <div id="app">
    <button id="loginBtn">Login</button>
    <button id="logoutBtn" style="display:none">Logout</button>
    
    <div id="documentosList"></div>
  </div>

  <script type="module">
    import { login, logout } from './auth/login.js';
    import { getDocumentos } from './services/correspondencia.js';

    // Login
    document.getElementById('loginBtn').addEventListener('click', async () => {
      try {
        await login('admin@system.com', 'password');
        document.getElementById('loginBtn').style.display = 'none';
        document.getElementById('logoutBtn').style.display = 'block';
        
        // Cargar documentos después del login
        await cargarDocumentos();
      } catch (error) {
        alert('Error de login: ' + error.message);
      }
    });

    // Logout
    document.getElementById('logoutBtn').addEventListener('click', async () => {
      await logout();
      document.getElementById('loginBtn').style.display = 'block';
      document.getElementById('logoutBtn').style.display = 'none';
      document.getElementById('documentosList').innerHTML = '';
    });

    // Cargar documentos
    async function cargarDocumentos() {
      try {
        const response = await getDocumentos(1, 10);
        
        let html = '<h2>Documentos</h2><table border="1">';
        html += '<tr><th>CITE</th><th>Asunto</th><th>Fecha</th><th>Estado</th></tr>';
        
        response.data.forEach(doc => {
          html += `<tr>
            <td>${doc.cite}</td>
            <td>${doc.asunto}</td>
            <td>${doc.fecha}</td>
            <td>${doc.estado?.nombre || 'N/A'}</td>
          </tr>`;
        });
        
        html += '</table>';
        document.getElementById('documentosList').innerHTML = html;
      } catch (error) {
        alert('Error cargando documentos: ' + error.message);
      }
    }
  </script>
</body>
</html>
```

---

## 📌 OPCIÓN 2: AXIOS (Recomendado para SPA)

### 2.1 Configuración con Axios

```javascript
// config/axios.js
import axios from 'axios';

const apiClient = axios.create({
  baseURL: 'http://localhost:8000/api/v1',
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json'
  }
});

// Interceptor para agregar token
apiClient.interceptors.request.use(
  (config) => {
    const token = localStorage.getItem('api_token');
    if (token) {
      config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
  },
  (error) => Promise.reject(error)
);

// Interceptor para manejar errores
apiClient.interceptors.response.use(
  (response) => response,
  (error) => {
    if (error.response?.status === 401) {
      // Token expirado
      localStorage.removeItem('api_token');
      window.location.href = '/login';
    }
    return Promise.reject(error);
  }
);

export default apiClient;
```

### 2.2 Servicio de Autenticación

```javascript
// services/auth.service.js
import apiClient from '../config/axios';

export const authService = {
  async login(email, password) {
    const { data } = await apiClient.post('/auth/login', { email, password });
    localStorage.setItem('api_token', data.data.token);
    localStorage.setItem('user', JSON.stringify(data.data.user));
    return data.data;
  },

  async logout() {
    await apiClient.post('/auth/logout');
    localStorage.removeItem('api_token');
    localStorage.removeItem('user');
  },

  async getProfile() {
    const { data } = await apiClient.get('/auth/me');
    return data.data;
  },

  getToken() {
    return localStorage.getItem('api_token');
  }
};
```

### 2.3 Servicio de Documentos

```javascript
// services/documentos.service.js
import apiClient from '../config/axios';

export const documentosService = {
  async listar(page = 1, perPage = 10, filters = {}) {
    const { data } = await apiClient.get('/documentos', {
      params: { page, per_page: perPage, ...filters }
    });
    return data;
  },

  async obtener(id) {
    const { data } = await apiClient.get(`/documentos/${id}`);
    return data.data;
  },

  async crear(documento) {
    const { data } = await apiClient.post('/documentos', documento);
    return data.data;
  },

  async actualizar(id, documento) {
    const { data } = await apiClient.put(`/documentos/${id}`, documento);
    return data.data;
  },

  async eliminar(id) {
    await apiClient.delete(`/documentos/${id}`);
    return true;
  },

  async obtenerDerivaciones(id) {
    const { data } = await apiClient.get(`/documentos/${id}/derivaciones`);
    return data.data;
  },

  async obtenerSeguimiento(id) {
    const { data } = await apiClient.get(`/documentos/${id}/seguimiento`);
    return data.data;
  },

  async cambiarEstado(id, idEstado) {
    const { data } = await apiClient.post(`/documentos/${id}/cambiar-estado`, {
      idEstado
    });
    return data.data;
  }
};
```

---

## 📌 OPCIÓN 3: VUE 3 + PINIA (Aplicación Moderna)

### 3.1 Store de Autenticación

```javascript
// stores/auth.js
import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import apiClient from '../config/axios';

export const useAuthStore = defineStore('auth', () => {
  const user = ref(null);
  const token = ref(localStorage.getItem('api_token'));
  const isAuthenticated = computed(() => !!token.value);

  async function login(email, password) {
    try {
      const { data } = await apiClient.post('/auth/login', { email, password });
      token.value = data.data.token;
      user.value = data.data.user;
      localStorage.setItem('api_token', token.value);
      localStorage.setItem('user', JSON.stringify(user.value));
      return data.data;
    } catch (error) {
      throw error;
    }
  }

  async function logout() {
    try {
      await apiClient.post('/auth/logout');
    } finally {
      token.value = null;
      user.value = null;
      localStorage.removeItem('api_token');
      localStorage.removeItem('user');
    }
  }

  return { user, token, isAuthenticated, login, logout };
});
```

### 3.2 Componente Vue

```vue
<template>
  <div>
    <h1>Documentos de Correspondencia</h1>
    
    <!-- Tabla de documentos -->
    <table border="1">
      <thead>
        <tr>
          <th>CITE</th>
          <th>Asunto</th>
          <th>Fecha</th>
          <th>Estado</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="doc in documentos" :key="doc.idDocumento">
          <td>{{ doc.cite }}</td>
          <td>{{ doc.asunto }}</td>
          <td>{{ doc.fecha }}</td>
          <td>{{ doc.estado?.nombre }}</td>
          <td>
            <button @click="editar(doc)">Editar</button>
            <button @click="eliminar(doc.idDocumento)">Eliminar</button>
          </td>
        </tr>
      </tbody>
    </table>

    <!-- Paginación -->
    <div>
      <button 
        v-for="p in pagination.last_page" 
        :key="p"
        @click="cargarPagina(p)"
        :disabled="p === pagination.current_page"
      >
        {{ p }}
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { documentosService } from '../services/documentos.service';

const documentos = ref([]);
const pagination = ref({});
const page = ref(1);

async function cargarDocumentos() {
  try {
    const response = await documentosService.listar(page.value, 10);
    documentos.value = response.data;
    pagination.value = response.pagination;
  } catch (error) {
    console.error('Error:', error);
  }
}

function cargarPagina(p) {
  page.value = p;
  cargarDocumentos();
}

async function eliminar(id) {
  if (confirm('¿Estás seguro?')) {
    try {
      await documentosService.eliminar(id);
      cargarDocumentos();
    } catch (error) {
      console.error('Error:', error);
    }
  }
}

function editar(doc) {
  console.log('Editar:', doc);
  // Implementar lógica de edición
}

onMounted(() => {
  cargarDocumentos();
});
</script>
```

---

## 📌 OPCIÓN 4: REACT + AXIOS

### 4.1 Hook Personalizado

```javascript
// hooks/useApi.js
import { useState, useCallback } from 'react';
import apiClient from '../config/axios';

export function useApi() {
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState(null);

  const request = useCallback(async (method, endpoint, data = null) => {
    setLoading(true);
    setError(null);
    try {
      const response = await apiClient[method](endpoint, data);
      return response.data;
    } catch (err) {
      setError(err.response?.data?.message || err.message);
      throw err;
    } finally {
      setLoading(false);
    }
  }, []);

  return { request, loading, error };
}
```

### 4.2 Componente React

```jsx
// components/DocumentosList.jsx
import React, { useState, useEffect } from 'react';
import { documentosService } from '../services/documentos.service';
import { useApi } from '../hooks/useApi';

export function DocumentosList() {
  const [documentos, setDocumentos] = useState([]);
  const [pagination, setPagination] = useState({});
  const [page, setPage] = useState(1);
  const { loading, error, request } = useApi();

  useEffect(() => {
    cargarDocumentos();
  }, [page]);

  const cargarDocumentos = async () => {
    try {
      const response = await documentosService.listar(page, 10);
      setDocumentos(response.data);
      setPagination(response.pagination);
    } catch (err) {
      console.error('Error:', err);
    }
  };

  const eliminar = async (id) => {
    if (window.confirm('¿Estás seguro?')) {
      try {
        await documentosService.eliminar(id);
        cargarDocumentos();
      } catch (err) {
        console.error('Error:', err);
      }
    }
  };

  if (loading) return <p>Cargando...</p>;
  if (error) return <p>Error: {error}</p>;

  return (
    <div>
      <h1>Documentos</h1>
      <table border="1">
        <thead>
          <tr>
            <th>CITE</th>
            <th>Asunto</th>
            <th>Fecha</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          {documentos.map(doc => (
            <tr key={doc.idDocumento}>
              <td>{doc.cite}</td>
              <td>{doc.asunto}</td>
              <td>{doc.fecha}</td>
              <td>
                <button onClick={() => eliminar(doc.idDocumento)}>Eliminar</button>
              </td>
            </tr>
          ))}
        </tbody>
      </table>

      <div>
        {Array.from({ length: pagination.last_page }, (_, i) => i + 1).map(p => (
          <button
            key={p}
            onClick={() => setPage(p)}
            disabled={p === pagination.current_page}
          >
            {p}
          </button>
        ))}
      </div>
    </div>
  );
}
```

---

## 🔒 MANEJO DE ERRORES

```javascript
// Interceptor global para manejo de errores
apiClient.interceptors.response.use(
  response => response,
  error => {
    const status = error.response?.status;

    switch (status) {
      case 401:
        // Token expirado
        localStorage.removeItem('api_token');
        window.location.href = '/login';
        break;
      case 403:
        // Prohibido
        console.error('No tienes permiso para esta acción');
        break;
      case 404:
        // No encontrado
        console.error('Recurso no encontrado');
        break;
      case 422:
        // Error de validación
        console.error('Datos inválidos:', error.response.data.errors);
        break;
      case 500:
        // Error del servidor
        console.error('Error del servidor');
        break;
    }

    return Promise.reject(error);
  }
);
```

---

## ✅ CHECKLIST DE DESARROLLO

- [ ] Configurar cliente HTTP (Axios/Fetch)
- [ ] Implementar autenticación (Login/Logout)
- [ ] Guardar token en localStorage
- [ ] Agregar interceptores para token
- [ ] Crear servicios para cada módulo
- [ ] Implementar manejo de errores
- [ ] Crear componentes para CRUD
- [ ] Agregar paginación
- [ ] Agregar búsqueda y filtrado
- [ ] Agregar validación de formularios
- [ ] Testear todos los endpoints

