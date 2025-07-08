# 📱 Sistema QR - Innovation Day 2025

## 🚀 Descripción

Sistema completo de gestión de códigos QR para clientes con funcionalidades de:
- Tabla interactiva con Vue.js + Vuetify
- Importación/Exportación de Excel
- Generación automática de códigos QR
- Escáner de cámara integrado
- Páginas de saludo personalizadas
- URLs dinámicas por cliente

## 📋 Funcionalidades Principales

### 1. Dashboard Principal (`/dashboard`)
- **Tabla de clientes** con Vue.js + Vuetify
- **Búsqueda en tiempo real**
- **Paginación automática**
- **Botones de acción** para cada cliente

### 2. Gestión de Excel
- **Descargar plantilla**: Excel con columnas predefinidas
- **Subir archivo**: Importación masiva de clientes
- **Exportar clientes**: Descarga de todos los registros
- **Validación automática**: Correos únicos y campos obligatorios

### 3. Sistema QR
- **Generación automática** de códigos QR
- **URLs dinámicas** por cliente (`/saludo/{token}`)
- **Activar/Desactivar** códigos QR
- **Descarga individual** de imágenes QR

### 4. Escáner de Cámara (`/scanner`)
- **Acceso a cámara** del dispositivo
- **Detección automática** de códigos QR
- **Historial de escaneados**
- **Apertura automática** de URLs

### 5. Páginas Personalizadas
- **Saludo individualizado** por cliente
- **Diseño responsivo** similar al Welcome
- **Información del evento**
- **Confirmación de registro**

## 🛠️ Instalación y Configuración

### 1. Instalar Dependencias PHP

```bash
# Instalar paquetes necesarios
composer install

# Instalar paquetes específicos del sistema QR
composer require phpoffice/phpspreadsheet
composer require simplesoftwareio/simple-qrcode
```

### 2. Ejecutar Migraciones

```bash
# Ejecutar migración de clientes
php artisan migrate

# Opcional: Ejecutar seeders con datos de ejemplo
php artisan db:seed --class=ClienteSeeder
```

### 3. Configurar Storage

```bash
# Crear enlace simbólico para storage público
php artisan storage:link
```

Esto permitirá que las imágenes QR sean accesibles desde `/storage/qr_codes/`

### 4. Permisos de Directorio

```bash
# Dar permisos de escritura al storage
chmod -R 775 storage/
chmod -R 775 bootstrap/cache/
```

## 📁 Estructura de Archivos

```
📦 Sistema QR
├── 🗄️ Base de Datos
│   ├── database/migrations/2024_12_28_000000_create_clientes_table.php
│   ├── database/seeders/ClienteSeeder.php
│   └── app/Models/Cliente.php
│
├── 🎮 Backend (Laravel)
│   ├── app/Http/Controllers/SistemaQrController.php
│   └── routes/web.php (rutas agregadas)
│
├── 🎨 Frontend (Vue.js + Vuetify)
│   ├── resources/js/Pages/Dashboard.vue (actualizado)
│   ├── resources/js/Pages/Cliente/Saludo.vue
│   └── resources/js/Pages/Cliente/Scanner.vue
│
└── 📋 Configuración
    ├── composer.json (dependencias agregadas)
    └── SISTEMA_QR_README.md
```

## 🔧 Rutas del Sistema

### Rutas Autenticadas
- `GET /dashboard` - Dashboard principal con tabla de clientes
- `GET /plantilla-clientes` - Descargar plantilla Excel
- `POST /subir-excel` - Subir archivo Excel
- `POST /generar-qr/{id}` - Generar QR para cliente
- `PATCH /toggle-qr/{id}` - Activar/desactivar QR
- `DELETE /clientes/{id}` - Eliminar cliente
- `GET /exportar-clientes` - Exportar todos los clientes
- `GET /scanner` - Página del escáner QR

### Rutas Públicas
- `GET /saludo/{token}` - Página de saludo personalizada

## 📊 Esquema de Base de Datos

### Tabla: `clientes`
| Campo | Tipo | Descripción |
|-------|------|-------------|
| `id` | Primary Key | ID único del cliente |
| `nombre` | String | Nombre del cliente |
| `apellido_paterno` | String | Apellido paterno |
| `apellido_materno` | String | Apellido materno |
| `correo` | String (Unique) | Correo electrónico único |
| `qr_path` | String (Nullable) | Ruta del archivo QR |
| `qr_token` | String (Unique) | Token único para URL |
| `qr_activo` | Boolean | Estado del QR (activo/inactivo) |
| `created_at` | Timestamp | Fecha de creación |
| `updated_at` | Timestamp | Fecha de actualización |

## 💡 Uso del Sistema

### 1. Importar Clientes desde Excel

1. **Descargar plantilla**: Clic en "Descargar Plantilla"
2. **Llenar datos**: Completar Excel con:
   - Columna A: Nombre
   - Columna B: Apellido Paterno  
   - Columna C: Apellido Materno
   - Columna D: Correo (único)
3. **Subir archivo**: Clic en "Subir Excel" y seleccionar archivo
4. **Revisar resultados**: Ver resumen de importación y errores

### 2. Generar Códigos QR

1. **Localizar cliente**: Buscar en la tabla del dashboard
2. **Generar QR**: Clic en botón azul (icono QR)
3. **Descargar imagen**: Clic en botón azul claro (icono descarga)
4. **Abrir página**: Clic en botón verde (icono externa)

### 3. Gestionar Estados QR

- **Activar/Desactivar**: Botón amarillo/verde (icono ojo)
- **Eliminar cliente**: Botón rojo (icono basura) + confirmación

### 4. Escanear Códigos QR

1. **Ir al escáner**: Clic en "Escanear QR" (botón morado)
2. **Permitir cámara**: Autorizar acceso a cámara del dispositivo
3. **Enfocar QR**: Apuntar hacia código QR en el marco verde
4. **Resultado automático**: URLs se abren automáticamente

## 🔒 Seguridad y Validaciones

### Validaciones de Importación
- ✅ **Campos obligatorios**: Todos los campos son requeridos
- ✅ **Correo válido**: Validación de formato email
- ✅ **Correo único**: No se permiten duplicados
- ✅ **Límite de archivo**: Máximo 2MB para Excel

### Tokens de Seguridad
- 🔐 **UUID únicos**: Cada cliente tiene token irrepetible
- 🔐 **URLs dinámicas**: Imposible adivinar URLs de otros clientes
- 🔐 **Estado activo**: QRs inactivos no muestran contenido

## 🎨 Diseño y UX

### Responsivo
- 📱 **Móvil**: Tabla adaptativa y botones optimizados
- 💻 **Desktop**: Interfaz completa con todas las funciones
- 🎯 **Touch-friendly**: Botones grandes para dispositivos táctiles

### Colores del Sistema
- 🔵 **Primario**: Azul para acciones principales
- 🟢 **Éxito**: Verde para confirmaciones
- 🟡 **Advertencia**: Amarillo para cambios de estado
- 🔴 **Error**: Rojo para eliminaciones
- 🟣 **Especial**: Morado para escáner QR

## 🚨 Solución de Problemas

### Error: "Class not found"
```bash
# Ejecutar autoload
composer dump-autoload
```

### Error: "Storage link"
```bash
# Recrear enlace simbólico
php artisan storage:link
```

### Error: "Permission denied"
```bash
# Dar permisos correctos
sudo chmod -R 775 storage/
sudo chown -R www-data:www-data storage/
```

### Error: "Cámara no accesible"
- ✅ Usar **HTTPS** en producción
- ✅ Permitir **permisos de cámara** en navegador
- ✅ Probar en **dispositivo móvil**

## 📈 Mejoras Futuras

### Funcionalidades Adicionales
- [ ] **Notificaciones email** al generar QR
- [ ] **Estadísticas** de escaneos por cliente
- [ ] **Códigos de lote** para eventos específicos
- [ ] **API REST** para integraciones externas
- [ ] **Autenticación por QR** para acceso al evento

### Integraciones
- [ ] **WhatsApp Business** para envío de QRs
- [ ] **Google Analytics** para tracking de escaneos
- [ ] **Mailchimp** para seguimiento de clientes
- [ ] **Zapier** para automatizaciones

## 🤝 Soporte

Para soporte técnico o dudas sobre el sistema:

1. **Revisar logs**: `storage/logs/laravel.log`
2. **Modo debug**: Activar `APP_DEBUG=true` en `.env`
3. **Consola navegador**: Revisar errores JavaScript
4. **Base de datos**: Verificar conexión y migraciones

---

## 📄 Licencia

Sistema desarrollado para **Innovation Day 2025 - Pentafon & Microsoft**

© 2024 - Todos los derechos reservados 

# 🎵 Sistema de Audio Automático para Totem - Innovation Day 2025

## 📋 Descripción del Sistema

El sistema de saludo personalizado ahora incluye un mecanismo de **click automático** para activar el audio sin requerir interacción del usuario, específicamente diseñado para totems de exposición.

---

## 🎯 **Cómo Funciona el Audio Automático**

### 1. **Botón Oculto con Click Automático**
- Se crea un botón completamente invisible en el DOM
- Después de 2 segundos de cargar la página, se ejecuta automáticamente un `click()` en este botón
- Este click simula la interacción necesaria para que el navegador permita la reproducción de audio

### 2. **Secuencia de Activación**
```
Página carga → 2 segundos → Click automático → Audio se reproduce → Audios aleatorios continúan
```

### 3. **Archivos de Audio**
- **Audio principal**: `audio_1.mp3` (se reproduce primero)
- **Audios aleatorios**: `audio_2.mp3` a `audio_6.mp3` (se reproducen después)

---

## 🔧 **Configuración del Navegador**

### **Para que funcione correctamente, usa:**

```batch
chrome.exe --autoplay-policy=no-user-gesture-required --disable-web-security --kiosk "http://localhost:8000/cliente/saludo"
```

### **O ejecuta el script automático:**
```batch
configurar_totem.bat
```

---

## 🧪 **Verificación de Funcionamiento**

### **Logs de Consola (F12):**
```
⏱️ Configurando click automático para totem en 2 segundos...
🎯 Tiempo de click automático alcanzado
✅ Botón automático encontrado, ejecutando click...
🤖 Click automático ejecutado para activar audio del totem...
🎵 Iniciando audio automático para totem...
🎵 Reproduciendo audio: /audio/audio_1.mp3
✅ Audio reproducido exitosamente: /audio/audio_1.mp3
✅ Audio principal iniciado exitosamente
```

### **Checklist de Verificación:**
- [ ] La página carga completamente
- [ ] Después de 2 segundos, el audio se reproduce automáticamente
- [ ] No se requiere ningún click manual
- [ ] Los audios aleatorios se reproducen después del principal
- [ ] No hay errores en la consola

---

## 🎨 **Características del Botón Oculto**

```css
.auto-click-button {
    position: fixed;
    top: -1000px;      /* Fuera de la vista */
    left: -1000px;     /* Fuera de la vista */
    width: 1px;
    height: 1px;
    opacity: 0;        /* Completamente invisible */
    pointer-events: auto; /* Permite el click automático */
    z-index: -1;       /* Detrás de todo */
}
```

---

## 🔄 **Flujo de Reproducción**

1. **Carga inicial**: Página se carga, preloader se muestra
2. **Preloader oculto**: Contenido se muestra después de 2 segundos
3. **Click automático**: Botón oculto se hace clic automáticamente
4. **Audio principal**: `audio_1.mp3` se reproduce
5. **Audios aleatorios**: Cada vez que termina un audio, reproduce uno aleatorio del 2-6

---

## 🚨 **Solución de Problemas**

### **Problema: Audio no se reproduce después de 2 segundos**

**Verificar:**
1. **Archivos de audio**: Deben estar en `public/audio/`
2. **Configuración del navegador**: Usar flags correctos
3. **Consola**: Revisar errores en F12

**Solución:**
```batch
# Verificar archivos
dir public\audio\audio_1.mp3

# Reconfigurar navegador
configurar_totem.bat
```

### **Problema: Audio se reproduce pero con retraso**

**Solución:**
- Los archivos se precargan automáticamente
- El retraso de 2 segundos es intencional para asegurar que todo esté listo

### **Problema: Audio se corta o no continúa**

**Verificar:**
- Que existan los archivos `audio_2.mp3` a `audio_6.mp3`
- Que no haya errores de red en la consola

---

## 🎯 **Uso en Totem**

### **Para un totem de exposición:**
1. Configurar el navegador con `configurar_totem.bat`
2. Cargar la página con el QR del cliente
3. **No se requiere interacción adicional**
4. El audio se reproduce automáticamente después de 2 segundos

### **Comando completo recomendado:**
```batch
chrome.exe ^
    --autoplay-policy=no-user-gesture-required ^
    --disable-web-security ^
    --kiosk ^
    --disable-features=VizDisplayCompositor ^
    --disable-background-timer-throttling ^
    "http://localhost:8000/cliente/saludo"
```

---

## 📞 **Soporte Técnico**

Si el audio no funciona automáticamente:

1. **Verificar logs**: F12 → Console
2. **Verificar archivos**: `public/audio/` debe contener todos los MP3
3. **Verificar navegador**: Debe tener los flags correctos
4. **Verificar red**: Los archivos deben cargarse sin errores

---

## ✅ **Configuración Final para Totem**

```batch
# 1. Ejecutar configuración automática
configurar_totem.bat

# 2. Seleccionar modo Kiosk (opción 1)

# 3. Usar URL completa con cliente
# Ejemplo: http://localhost:8000/cliente/saludo

# 4. ✅ El audio se reproduce automáticamente después de 2 segundos
```

**¡Listo para usar en totem sin interacción!** 🎵 
