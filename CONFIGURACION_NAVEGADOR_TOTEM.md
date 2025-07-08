# 🎵 Configuración de Navegador para Totem - Audio Automático

## 📋 Guía de Configuración para Totems de Exposición

Esta guía te ayudará a configurar el navegador para que reproduzca audio automáticamente en un totem de exposición sin requerir interacción del usuario.

---

## 🎯 **Configuración Recomendada para Totems**

### 1. **Google Chrome (RECOMENDADO)**

#### **Configuración Básica:**
```bash
# Ejecutar Chrome con flags específicos para totem
chrome.exe --autoplay-policy=no-user-gesture-required --disable-features=VizDisplayCompositor --disable-background-timer-throttling --disable-backgrounding-occluded-windows --disable-renderer-backgrounding --disable-feature=TranslateUI --disable-ipc-flooding-protection --disable-popup-blocking --disable-prompt-on-repost --disable-hang-monitor --disable-component-update --disable-background-networking --disable-sync --disable-translate --disable-web-security --disable-features=TranslateUI --disable-extensions --no-first-run --fast --fast-start --disable-default-apps --disable-features=VizDisplayCompositor
```

#### **Configuración Avanzada:**
1. **Navega a:** `chrome://flags/`
2. **Busca y configura:**
   - `Autoplay policy` → **No user gesture required**
   - `Audio service sandbox` → **Disabled**
   - `Audio service out of process` → **Disabled**

#### **Configuración de Políticas (Empresarial):**
```json
{
  "AutoplayAllowed": true,
  "AutoplayWhitelist": ["*"],
  "DefaultNotificationsSetting": 2,
  "DefaultGeolocationSetting": 2,
  "DefaultMediaStreamSetting": 2,
  "AudioCaptureAllowed": true,
  "VideoCaptureAllowed": true
}
```

### 2. **Mozilla Firefox**

#### **Configuración en about:config:**
1. **Navega a:** `about:config`
2. **Busca y modifica:**
   - `media.autoplay.default` → **0** (Allow all)
   - `media.autoplay.enabled.user-gestures-required` → **false**
   - `media.autoplay.ask-permission` → **false**
   - `media.autoplay.block-webaudio` → **false**
   - `media.autoplay.enabled` → **true**

#### **Ejecutar Firefox con flags:**
```bash
firefox.exe -pref media.autoplay.default=0 -pref media.autoplay.enabled.user-gestures-required=false
```

### 3. **Microsoft Edge**

#### **Configuración:**
1. **Navega a:** `edge://flags/`
2. **Busca y configura:**
   - `Autoplay policy` → **No user gesture required**
   - `Audio service sandbox` → **Disabled**

#### **Configuración de Políticas:**
```json
{
  "AutoplayAllowed": true,
  "AutoplayAllowlist": ["*"]
}
```

---

## 🔧 **Configuración del Sistema Operativo**

### **Windows 10/11:**

#### **Configuración de Audio:**
1. **Panel de Control** → **Sonido**
2. **Configurar dispositivo de audio predeterminado**
3. **Desactivar "Mejoras de audio"**
4. **Establecer nivel de volumen al 100%**

#### **Configuración de Energía:**
1. **Panel de Control** → **Opciones de energía**
2. **Configurar plan de energía:** **Alto rendimiento**
3. **Desactivar suspensión de pantalla**
4. **Desactivar hibernación**

#### **Configuración de Pantalla:**
1. **Desactivar protector de pantalla**
2. **Configurar resolución óptima**
3. **Desactivar escala de DPI automática**

---

## 🌐 **Configuración de Servidor Web**

### **Headers HTTP Necesarios:**
```apache
# .htaccess para Apache
Header set Feature-Policy "autoplay 'self'"
Header set Permissions-Policy "autoplay=*"
```

```nginx
# Configuración Nginx
add_header Feature-Policy "autoplay 'self'";
add_header Permissions-Policy "autoplay=*";
```

### **Configuración MIME Types:**
```apache
# Asegurar tipos MIME correctos para audio
AddType audio/mpeg .mp3
AddType audio/wav .wav
AddType audio/ogg .ogg
```

---

## 🔒 **Configuración de Seguridad**

### **Políticas de Autoplay:**
```html
<!-- Configurar Feature Policy en HTML -->
<meta http-equiv="Feature-Policy" content="autoplay 'self'">
<meta http-equiv="Permissions-Policy" content="autoplay=*">
```

### **Configuración HTTPS:**
- **Importante:** Algunos navegadores requieren HTTPS para autoplay
- **Certificado SSL:** Configurar certificado válido o usar localhost

---

## 📱 **Configuración Específica para Dispositivos**

### **Tablets Android:**
```bash
# Chrome para Android
chrome --autoplay-policy=no-user-gesture-required --disable-features=VizDisplayCompositor
```

### **iPad/iOS:**
- **Safari:** Configurar en **Ajustes** → **Safari** → **Reproducción automática**
- **Chrome iOS:** Limitado por restricciones del sistema

### **Navegadores Embedded:**
- **Electron:** Configurar `autoplayPolicy: 'no-user-gesture-required'`
- **WebView:** Configurar `setMediaPlaybackRequiresUserGesture(false)`

---

## 🔧 **Script de Configuración Automática**

### **Windows Batch Script:**
```batch
@echo off
echo Configurando navegador para totem...

REM Crear carpeta de perfil personalizado
mkdir "%USERPROFILE%\TotemProfile"

REM Iniciar Chrome con configuración de totem
start chrome.exe ^
    --user-data-dir="%USERPROFILE%\TotemProfile" ^
    --autoplay-policy=no-user-gesture-required ^
    --disable-features=VizDisplayCompositor ^
    --disable-web-security ^
    --disable-background-timer-throttling ^
    --disable-backgrounding-occluded-windows ^
    --disable-renderer-backgrounding ^
    --disable-extensions ^
    --no-first-run ^
    --kiosk ^
    "http://localhost:8000/cliente/saludo"

echo Navegador configurado para totem
pause
```

### **Linux Bash Script:**
```bash
#!/bin/bash
echo "Configurando navegador para totem..."

# Crear carpeta de perfil personalizado
mkdir -p ~/TotemProfile

# Iniciar Chrome con configuración de totem
google-chrome \
    --user-data-dir=~/TotemProfile \
    --autoplay-policy=no-user-gesture-required \
    --disable-features=VizDisplayCompositor \
    --disable-web-security \
    --disable-background-timer-throttling \
    --disable-backgrounding-occluded-windows \
    --disable-renderer-backgrounding \
    --disable-extensions \
    --no-first-run \
    --kiosk \
    "http://localhost:8000/cliente/saludo"

echo "Navegador configurado para totem"
```

---

## 🔍 **Solución de Problemas**

### **Problema: Audio no reproduce automáticamente**
**Soluciones:**
1. **Verificar flags de Chrome:** `--autoplay-policy=no-user-gesture-required`
2. **Verificar volumen del sistema:** No debe estar muteado
3. **Verificar dispositivo de audio:** Debe estar conectado y funcionando
4. **Verificar archivos de audio:** Deben estar en formato MP3 y ser válidos

### **Problema: Audio se reproduce con retraso**
**Soluciones:**
1. **Precargar audios:** Implementado en el código
2. **Configurar buffer:** Ajustar `audio.preload = 'auto'`
3. **Verificar performance:** Usar `--disable-features=VizDisplayCompositor`

### **Problema: Página se congela o crashea**
**Soluciones:**
1. **Verificar memoria:** `--disable-background-timer-throttling`
2. **Verificar extensiones:** `--disable-extensions`
3. **Verificar GPU:** `--disable-features=VizDisplayCompositor`

---

## 🎵 **Verificación de Funcionamiento**

### **Checklist de Verificación:**
- [ ] Audio reproduce automáticamente al cargar
- [ ] No se muestra ningún overlay de activación
- [ ] Audios aleatorios se reproducen después del principal
- [ ] No hay errores en la consola del navegador
- [ ] El totem funciona sin teclado/mouse conectado

### **Comando de Verificación:**
```javascript
// Ejecutar en consola del navegador
console.log('Audio Context State:', AudioContext ? new AudioContext().state : 'No disponible');
console.log('Autoplay Policy:', navigator.getAutoplayPolicy ? navigator.getAutoplayPolicy('mediaelement') : 'No disponible');
```

---

## 📞 **Soporte y Contacto**

Si tienes problemas con la configuración:
1. **Verificar logs de consola:** F12 → Console
2. **Verificar Network tab:** Revisar si los audios se cargan correctamente
3. **Verificar configuración de sistema:** Audio, energía, pantalla

---

## 🔄 **Actualización de Navegadores**

**Importante:** Mantener navegadores actualizados, pero probar configuración después de cada actualización ya que las políticas pueden cambiar.

### **Configuración de Actualización Automática:**
```batch
REM Desactivar actualización automática de Chrome
reg add "HKLM\SOFTWARE\Policies\Google\Update" /v AutoUpdateCheckPeriodMinutes /t REG_DWORD /d 0
```

---

## ✅ **Configuración Recomendada Final**

**Para máxima compatibilidad en totem:**

1. **Navegador:** Google Chrome con flags específicos
2. **Sistema:** Windows 10/11 con plan de energía alto rendimiento
3. **Servidor:** Apache/Nginx con headers correctos
4. **Archivos:** MP3 en directorio `public/audio/`
5. **Verificación:** Probar en condiciones reales de totem

**Comando final recomendado:**
```batch
chrome.exe --autoplay-policy=no-user-gesture-required --disable-web-security --kiosk "http://localhost:8000/cliente/saludo"
``` 
