# La Malaguía

Guía y comunidad online para argentinos en Málaga y Andalucía. Incluye información sobre trámites, directorio de negocios y servicios argentinos, agenda de eventos, y un asistente de IA conversacional disponible via web y WhatsApp.

---

## Stack tecnológico

### Backend
| Tecnología | Versión |
|---|---|
| PHP | 8.3.30 |
| Laravel | 13.13.0 |
| Composer | 2.8.4 |
| MySQL | 8.4.3 |

### Frontend
| Tecnología | Versión |
|---|---|
| Vue | ^3.4.0 |
| Inertia.js (Vue 3) | ^2.0.0 |
| TypeScript | ^5.6.3 |
| Vite | ^8.0.0 |
| Tailwind CSS | ^3.2.1 |
| vue-tsc | ^2.0.24 |
| Pinia | ^3.0.4 |

### Entorno de desarrollo
| Herramienta | Versión |
|---|---|
| Laragon | 8.6.1 |
| Node.js | 20.19.0 |
| npm | 10.8.2 |

---

## Base de datos

| Tabla | Descripción |
|---|---|
| `users` | Usuarios del CMS (admins y editores) |
| `categoria_negocios` | Categorías del directorio de negocios |
| `negocios` | Directorio de negocios y servicios argentinos en Málaga |
| `tramites` | Guías de trámites (NIE, empadronamiento, etc.) |
| `eventos` | Agenda de eventos de la comunidad |
| `configuraciones` | Configuración general del sitio (key-value) |

---

## Instalación

```bash
# Clonar el repositorio
git clone <repo-url>
cd lamalaguia

# Instalar dependencias PHP
composer install

# Instalar dependencias JS
npm install

# Configurar entorno
cp .env.example .env
php artisan key:generate

# Configurar base de datos en .env y correr migrations
php artisan migrate

# Levantar servidor de desarrollo
php artisan serve
npm run dev
```

---

## Arquitectura IA (roadmap MVP)

El asistente conversacional utiliza:
- **Groq** como LLM (velocidad de inferencia)
- **Laravel** como backend del endpoint `/api/ai-chat`
- **Evolution API + n8n** para el canal de WhatsApp
- Contexto inyectado dinámicamente desde las tablas `tramites` y `negocios`

---

## Roles

| Rol | Permisos |
|---|---|
| `admin` | Acceso total al CMS |
| `editor` | Carga y edición de contenido |

---

## Contexto del proyecto

**Enfoque geográfico:** Málaga y Andalucía (diferenciador vs competidores centrados en Madrid/Barcelona)

**Público objetivo:** Argentinos recién llegados o en proceso de migración a la zona

**Canales:** Web + WhatsApp bot

**Competidores principales:** ArgentApp, Argentinos en Barcelona, Argentinos por España
