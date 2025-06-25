<script setup lang="ts">
import { Head, usePage } from '@inertiajs/vue3';
import { ref, onMounted, computed, nextTick } from 'vue';
import * as THREE from 'three';
import { GLTFLoader } from 'three/examples/jsm/loaders/GLTFLoader.js';
import { OrbitControls } from 'three/examples/jsm/controls/OrbitControls.js';

const page = usePage();
const baseUrl = computed(() => {
    const ziggy = (page.props as any).ziggy;
    return ziggy?.url || window.location.origin;
});

// Props
interface Cliente {
    nombre_completo: string;
    nombre: string;
    apellido_paterno: string;
    apellido_materno: string;
    correo: string;
}

interface Props {
    cliente: Cliente;
}

const props = defineProps<Props>();

const isLoading = ref(true);
const robotContainer = ref<HTMLElement>();
const robotLoaded = ref(false);
const showDialog = ref(false);
const currentMessage = ref('');
const animationFinished = ref(false);

// Mensajes personalizados con nombre completo
const messages = [

    `${props.cliente.nombre} ${props.cliente.apellido_paterno} ${props.cliente.apellido_materno}, me alegra verte, bienvenido a Innovation Day. Gracias por estar aquí 🎉`,
    `¡Hola, ${props.cliente.nombre} ${props.cliente.apellido_paterno} ${props.cliente.apellido_materno}. Bienvenido a Innovation Day. Adelante, el futuro te espera! ✨`
];

// Variables Three.js
let scene: THREE.Scene;
let camera: THREE.PerspectiveCamera;
let renderer: THREE.WebGLRenderer;
let robot: THREE.Group;
let mixer: THREE.AnimationMixer;
let controls: OrbitControls;
let dialogSprite: THREE.Sprite;
let robotHead: THREE.Object3D | null = null;

const handleLoaded = () => {
    isLoading.value = false;
};

// Configuración SEO personalizada
const seoConfig = computed(() => ({
    title: `¡Hola ${props.cliente.nombre} ${props.cliente.apellido_paterno} ${props.cliente.apellido_materno}! - Innovation Day 2025`,
    description: `Saludo personalizado para ${props.cliente.nombre_completo} en Innovation Day 2025`,
    ogImage: `${baseUrl.value}/images/banner.webp`,
    favicon: `${baseUrl.value}/images/favicon.png`
}));

// Función para crear speech bubble 3D estilo tradicional con alta calidad
const createDialogSprite = (message: string) => {
    const canvas = document.createElement('canvas');
    const context = canvas.getContext('2d');
    if (!context) return;

    // Canvas de alta resolución para mejor calidad (2x pixel ratio)
    const pixelRatio = 2;
    canvas.width = 400 * pixelRatio;
    canvas.height = 200 * pixelRatio;

    // Escalar el contexto para mantener las dimensiones visuales
    context.scale(pixelRatio, pixelRatio);

    // Habilitar suavizado mejorado
    context.imageSmoothingEnabled = true;
    context.imageSmoothingQuality = 'high';

    // Limpiar canvas
    context.clearRect(0, 0, 400, 200);

    // Dimensiones del globo principal (manteniendo proporciones originales)
    const bubbleWidth = 370;
    const bubbleHeight = 140;
    const bubbleX = 15;
    const bubbleY = 15;
    const cornerRadius = 18;

    // Crear path para el speech bubble con cola
    context.beginPath();

    // Globo principal con bordes redondeados más suaves
    context.moveTo(bubbleX + cornerRadius, bubbleY);
    context.lineTo(bubbleX + bubbleWidth - cornerRadius, bubbleY);
    context.quadraticCurveTo(bubbleX + bubbleWidth, bubbleY, bubbleX + bubbleWidth, bubbleY + cornerRadius);
    context.lineTo(bubbleX + bubbleWidth, bubbleY + bubbleHeight - cornerRadius);
    context.quadraticCurveTo(bubbleX + bubbleWidth, bubbleY + bubbleHeight, bubbleX + bubbleWidth - cornerRadius, bubbleY + bubbleHeight);

    // Cola del speech bubble más suave
    const tailWidth = 20;
    const tailHeight = 25;
    const tailStartX = bubbleX + bubbleWidth / 2 - tailWidth / 2;
    const tailEndX = bubbleX + bubbleWidth / 2 + tailWidth / 2;
    const tailPointX = bubbleX + bubbleWidth / 2;
    const tailPointY = bubbleY + bubbleHeight + tailHeight;

    context.lineTo(tailEndX, bubbleY + bubbleHeight);
    context.lineTo(tailPointX, tailPointY);
    context.lineTo(tailStartX, bubbleY + bubbleHeight);

    context.lineTo(bubbleX + cornerRadius, bubbleY + bubbleHeight);
    context.quadraticCurveTo(bubbleX, bubbleY + bubbleHeight, bubbleX, bubbleY + bubbleHeight - cornerRadius);
    context.lineTo(bubbleX, bubbleY + cornerRadius);
    context.quadraticCurveTo(bubbleX, bubbleY, bubbleX + cornerRadius, bubbleY);
    context.closePath();

    // Gradiente elegante para el fondo
    const gradient = context.createLinearGradient(bubbleX, bubbleY, bubbleX, bubbleY + bubbleHeight);
    gradient.addColorStop(0, 'rgba(255, 255, 255, 0.98)');
    gradient.addColorStop(0.5, 'rgba(248, 250, 252, 0.96)');
    gradient.addColorStop(1, 'rgba(241, 245, 249, 0.98)');

    // Sombra más elegante y suave
    context.shadowColor = 'rgba(0, 0, 0, 0.25)';
    context.shadowBlur = 12;
    context.shadowOffsetX = 4;
    context.shadowOffsetY = 6;
    context.fillStyle = gradient;
    context.fill();

    // Borde más elegante con gradiente sutil
    context.shadowColor = 'transparent';
    const borderGradient = context.createLinearGradient(bubbleX, bubbleY, bubbleX, bubbleY + bubbleHeight);
    borderGradient.addColorStop(0, '#eb1c2d');
    borderGradient.addColorStop(0.5, '#dc2626');
    borderGradient.addColorStop(1, '#b91c1c');
    context.strokeStyle = borderGradient;
    context.lineWidth = 2.5;
    context.stroke();

    // Brillo interno sutil
    context.beginPath();
    context.moveTo(bubbleX + cornerRadius + 2, bubbleY + 2);
    context.lineTo(bubbleX + bubbleWidth - cornerRadius - 2, bubbleY + 2);
    context.quadraticCurveTo(bubbleX + bubbleWidth - 2, bubbleY + 2, bubbleX + bubbleWidth - 2, bubbleY + cornerRadius + 2);
    const highlightGradient = context.createLinearGradient(0, bubbleY, 0, bubbleY + 30);
    highlightGradient.addColorStop(0, 'rgba(255, 255, 255, 0.6)');
    highlightGradient.addColorStop(1, 'rgba(255, 255, 255, 0)');
    context.strokeStyle = highlightGradient;
    context.lineWidth = 1;
    context.stroke();

    // Configurar texto más elegante con mejor tipografía
    context.fillStyle = '#1f2937';
    context.font = 'bold 16px -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif';
    context.textAlign = 'center';
    context.textBaseline = 'middle';

    // Dividir texto en líneas con mejor ajuste
    const words = message.split(' ');
    const lines = [];
    let currentLine = '';
    const maxWidth = bubbleWidth - 50; // Margen interno más amplio

    for (let i = 0; i < words.length; i++) {
        const testLine = currentLine + words[i] + ' ';
        const metrics = context.measureText(testLine);
        if (metrics.width > maxWidth && i > 0) {
            lines.push(currentLine.trim());
            currentLine = words[i] + ' ';
        } else {
            currentLine = testLine;
        }
    }
    lines.push(currentLine.trim());

    // Sombra sutil para el texto
    context.shadowColor = 'rgba(255, 255, 255, 0.8)';
    context.shadowBlur = 1;
    context.shadowOffsetX = 0;
    context.shadowOffsetY = 1;

    // Dibujar texto centrado con mejor espaciado
    const lineHeight = 22;
    const totalTextHeight = lines.length * lineHeight;
    const textStartY = bubbleY + (bubbleHeight - totalTextHeight) / 2 + lineHeight / 2;

    lines.forEach((line, index) => {
        context.fillText(
            line,
            bubbleX + bubbleWidth / 2,
            textStartY + index * lineHeight
        );
    });

    // Crear sprite con mejor configuración de textura
    const texture = new THREE.CanvasTexture(canvas);
    texture.needsUpdate = true;
    texture.generateMipmaps = true;
    texture.minFilter = THREE.LinearMipmapLinearFilter;
    texture.magFilter = THREE.LinearFilter;
    texture.format = THREE.RGBAFormat;

    const material = new THREE.SpriteMaterial({
        map: texture,
        transparent: true,
        alphaTest: 0.01,
        depthWrite: false,
        depthTest: true
    });
    const sprite = new THREE.Sprite(material);

    return sprite;
};

// Función para mostrar mensaje random
const showRandomMessage = () => {
    const randomIndex = Math.floor(Math.random() * messages.length);
    currentMessage.value = messages[randomIndex];
    showDialog.value = true;

    // Crear nuevo sprite de diálogo
    if (dialogSprite) {
        scene.remove(dialogSprite);
    }

    const newDialogSprite = createDialogSprite(currentMessage.value);
    if (newDialogSprite && robot) {
        dialogSprite = newDialogSprite;

        // Posicionar el speech bubble relativo a la cabeza del robot más grande
        if (robotHead) {
            const headWorldPosition = new THREE.Vector3();
            robotHead.getWorldPosition(headWorldPosition);
            dialogSprite.position.copy(headWorldPosition);
            dialogSprite.position.y += 1.8; // Más arriba para robot grande, aprovechando espacio superior
            dialogSprite.position.x += 0.1; // Ligero desplazamiento lateral
        } else {
            // Fallback a posición fija si no se encuentra la cabeza (ajustado para robot grande)
            dialogSprite.position.set(0.6, 1.5, 0); // Posición más alta para aprovechar espacio
        }

        // Escala del speech bubble ajustada para robot grande
        dialogSprite.scale.set(1.4, 0.8, 1); // Más grande para ser proporcional al robot
        scene.add(dialogSprite);

        // Ocultar diálogo después de 15 segundos
        setTimeout(() => {
            showDialog.value = false;
            if (dialogSprite) {
                scene.remove(dialogSprite);
            }
        }, 15000);
    }
};

// Función para inicializar Three.js
const initThreeJS = async () => {
    if (!robotContainer.value) return;

    // Configurar escena
    scene = new THREE.Scene();
    scene.background = null; // Fondo transparente para mostrar el fondo de imagen

    // Configurar cámara adaptativa para diferentes resoluciones verticales
    const aspect = robotContainer.value.clientWidth / robotContainer.value.clientHeight;
    const containerHeight = robotContainer.value.clientHeight;
    const isVertical = aspect < 1; // Detectar si es vertical

    // FOV adaptativo según resolución
    let initialFOV;
    if (isVertical) {
        if (containerHeight <= 700) {
            initialFOV = 70; // Móviles pequeños
        } else if (containerHeight <= 900) {
            initialFOV = 65; // Móviles grandes
        } else {
            initialFOV = aspect < 0.7 ? 68 : 60; // Pantallas grandes
        }
    } else {
        initialFOV = 50; // Horizontal
    }

    camera = new THREE.PerspectiveCamera(
        initialFOV,
        aspect,
        0.1,
        1000
    );

    // Posición inicial de cámara adaptativa
    if (isVertical) {
        if (containerHeight <= 700) {
            camera.position.set(0, 0, 4.0); // Móviles pequeños - más lejos
        } else if (containerHeight <= 900) {
            camera.position.set(0, 0, 3.7); // Móviles grandes - intermedio
        } else {
            camera.position.set(0, 0, 3.5); // Pantallas grandes - cerca
        }
    } else {
        camera.position.set(0, 0, 4.0); // Horizontal - mantener
    }

    // Configurar renderer
    renderer = new THREE.WebGLRenderer({ alpha: true, antialias: true });
    renderer.setClearColor(0x000000, 0); // Fondo completamente transparente
    renderer.setSize(robotContainer.value.clientWidth, robotContainer.value.clientHeight);
    renderer.shadowMap.enabled = true;
    renderer.shadowMap.type = THREE.PCFSoftShadowMap;
    robotContainer.value.appendChild(renderer.domElement);

    // Configurar controles optimizados para vertical
    controls = new OrbitControls(camera, renderer.domElement);
    controls.enableDamping = true;
    controls.dampingFactor = 0.05;
    controls.enableZoom = false; // DESHABILITAR ZOOM COMPLETAMENTE

    // Configuración específica para vertical vs horizontal
    if (isVertical) {
        // Ya no necesitamos maxDistance/minDistance porque el zoom está deshabilitado
        controls.maxPolarAngle = Math.PI * 0.8; // Limitar rotación vertical
        controls.minPolarAngle = Math.PI * 0.2;
    } else {
        // Ya no necesitamos configurar distancias para zoom
    }

    controls.target.set(0, 0, 0); // Centrar en el robot
    controls.enablePan = false; // Deshabilitar paneo para mejor control

    // Iluminación
    const ambientLight = new THREE.AmbientLight(0xffffff, 0.6);
    scene.add(ambientLight);

    const directionalLight = new THREE.DirectionalLight(0xffffff, 0.8);
    directionalLight.position.set(10, 10, 5);
    directionalLight.castShadow = true;
    scene.add(directionalLight);

    const pointLight = new THREE.PointLight(0xeb1c2d, 0.5, 100);
    pointLight.position.set(0, 5, 0);
    scene.add(pointLight);

    // Cargar modelo robot
    const loader = new GLTFLoader();
    try {
        const gltf = await loader.loadAsync(`${baseUrl.value}/model/robot.glb`);
        robot = gltf.scene;

                        // Calcular el bounding box del robot para escalarlo apropiadamente
        const box = new THREE.Box3().setFromObject(robot);
        const center = box.getCenter(new THREE.Vector3());
        const size = box.getSize(new THREE.Vector3());

        console.log('Robot original - Centro:', center, 'Tamaño:', size);

        // Detectar si es pantalla vertical y su resolución para escalado adaptativo
        const containerAspect = robotContainer.value!.clientWidth / robotContainer.value!.clientHeight;
        const containerHeight = robotContainer.value!.clientHeight;
        const containerWidth = robotContainer.value!.clientWidth;
        const isVerticalScreen = containerAspect < 1;

        // Escalado adaptativo basado en resolución vertical
        let targetSize;
        let scale;

        if (isVerticalScreen) {
            // Para pantallas verticales, ajustar según la altura real de la pantalla
            if (containerHeight <= 700) {
                // Móviles pequeños - robot más pequeño para que no se corte
                targetSize = 1.4;
            } else if (containerHeight <= 900) {
                // Móviles grandes/tablets pequeñas
                targetSize = 1.7;
            } else {
                // Pantallas verticales grandes - mantener tamaño actual
                targetSize = 2;
            }
            scale = targetSize / size.y; // Escalar basado en altura para vertical
        } else {
            // Para pantallas horizontales, mantener configuración actual
            const maxDimension = Math.max(size.x, size.y, size.z);
            targetSize = 3;
            scale = targetSize / maxDimension;
        }

        robot.scale.set(scale, scale, scale);

        // Posicionamiento adaptativo del robot
        robot.position.x = 0;
        robot.position.z = 0;

        // Posición Y adaptativa según resolución
        if (isVerticalScreen) {
            if (containerHeight <= 700) {
                // Móviles pequeños - posición más centrada
                robot.position.y = -0.8;
            } else if (containerHeight <= 900) {
                // Móviles grandes - posición intermedia
                robot.position.y = -1.0;
            } else {
                // Pantallas grandes - mantener posición actual
                robot.position.y = -1.2;
            }
        } else {
            // Horizontal - mantener configuración actual
            robot.position.y = -0.6;
        }

        // Asegurar que el robot esté completamente estático y mirando de frente
        robot.rotation.set(0, -Math.PI / 2, 0); // Rotar -90 grados en Y para que mire de frente
        robot.updateMatrix(); // Actualizar matriz de transformación

        console.log('Robot escalado - Pantalla vertical:', isVerticalScreen, 'Escala:', scale, 'Nueva posición:', robot.position, 'Ajuste hacia arriba:', robot.position.y);

                // Configurar sombras y buscar la cabeza del robot
        robot.traverse((child) => {
            if ((child as THREE.Mesh).isMesh) {
                child.castShadow = true;
                child.receiveShadow = true;
            }
            // Buscar por nombres comunes de cabeza en modelos GLTF
            if (child.name.toLowerCase().includes('head') ||
                child.name.toLowerCase().includes('cabeza') ||
                child.name.toLowerCase().includes('skull') ||
                child.name.toLowerCase().includes('face')) {
                robotHead = child;
                console.log('Cabeza del robot encontrada:', child.name);
            }
        });

        scene.add(robot);

        // Configurar animaciones (deshabilitadas para robot estático)
        if (gltf.animations && gltf.animations.length > 0) {
            // Mixer disponible pero sin reproducir animaciones
            mixer = new THREE.AnimationMixer(robot);
            console.log(`Robot cargado con ${gltf.animations.length} animaciones disponibles (no reproducidas)`);
        }

        // Robot completamente estático desde el inicio
        animationFinished.value = true;

        // Si no encontramos la cabeza por nombre, usar el objeto más alto
        if (!robotHead) {
            let highestY = -Infinity;
            robot.traverse((child) => {
                const worldPosition = new THREE.Vector3();
                child.getWorldPosition(worldPosition);
                if (worldPosition.y > highestY) {
                    highestY = worldPosition.y;
                    robotHead = child;
                }
            });
            console.log('Cabeza del robot determinada por posición más alta');
        }

        robotLoaded.value = true;

                // Ajustar cámara para mostrar el robot en la parte inferior
        const robotBox = new THREE.Box3().setFromObject(robot);
        const robotCenter = robotBox.getCenter(new THREE.Vector3());
        const robotSize = robotBox.getSize(new THREE.Vector3());

        // Mantener target fijo en el centro para vista frontal
        controls.target.set(0, 0, 0); // Target ligeramente abajo para seguir al robot

        // Configuración de cámara adaptativa según resolución
        const currentAspect = robotContainer.value!.clientWidth / robotContainer.value!.clientHeight;
        const currentHeight = robotContainer.value!.clientHeight;
        const isCurrentlyVertical = currentAspect < 1;

        if (isCurrentlyVertical) {
            // Para pantallas verticales - ajustar distancia según resolución
            if (currentHeight <= 700) {
                // Móviles pequeños - cámara más lejos para ver robot completo
                camera.position.set(0, 0, 3.5);
            } else if (currentHeight <= 900) {
                // Móviles grandes - distancia intermedia
                camera.position.set(0, 0, 3.0);
            } else {
                // Pantallas grandes - mantener distancia actual
                camera.position.set(0, 0, 2.5);
            }
        } else {
            // Para pantallas horizontales - mantener configuración actual
            camera.position.set(0, 0, 3.0);
        }

        controls.update();

        // Mostrar primer mensaje después de cargar
        setTimeout(() => {
            showRandomMessage();
        }, 2000);

    } catch (error) {
        console.error('Error cargando el robot:', error);
    }
};

// Función de animación
const animate = () => {
    requestAnimationFrame(animate);

    if (mixer) {
        mixer.update(0.016); // 60 FPS
    }

    if (controls) {
        controls.update();
    }

    // Robot completamente estático - sin rotación ni movimiento
    // El robot permanece en su posición final después de la animación inicial

    // Mantener el speech bubble en posición fija sobre la cabeza del robot grande
    if (dialogSprite && robotHead && showDialog.value) {
        const headWorldPosition = new THREE.Vector3();
        robotHead.getWorldPosition(headWorldPosition);
        dialogSprite.position.copy(headWorldPosition);
        dialogSprite.position.y += 1.2; // Consistente con robot grande
        dialogSprite.position.x += 0.2; // Consistente con robot grande
        dialogSprite.lookAt(camera.position);
    }

    if (renderer && scene && camera) {
        renderer.render(scene, camera);
    }
};

// Redimensionar adaptativo para diferentes resoluciones verticales
const handleResize = () => {
    if (!robotContainer.value || !camera || !renderer) return;

    const width = robotContainer.value.clientWidth;
    const height = robotContainer.value.clientHeight;
    const newAspect = width / height;
    const isNowVertical = newAspect < 1;

    // Actualizar aspect ratio
    camera.aspect = newAspect;

    // Ajustar FOV según orientación y resolución
    if (isNowVertical) {
        if (height <= 700) {
            // Móviles pequeños - FOV más amplio para ver más contenido
            camera.fov = 70;
        } else if (height <= 900) {
            // Móviles grandes - FOV intermedio
            camera.fov = 65;
        } else {
            // Pantallas grandes - FOV actual
            camera.fov = newAspect < 0.7 ? 68 : 60;
        }
    } else {
        camera.fov = 50; // Mantener para horizontales
    }

    camera.updateProjectionMatrix();
    renderer.setSize(width, height);

    // Actualizar controles
    if (controls) {
        controls.update();
    }

    console.log('Redimensionado adaptativo - Aspecto:', newAspect, 'Altura:', height, 'Vertical:', isNowVertical);
};

onMounted(async () => {
    // Ocultar el preloader después de 2 segundos
    setTimeout(() => {
        isLoading.value = false;
    }, 2000);

    // Inicializar Three.js después de que el DOM esté listo
    await nextTick();
    if (robotContainer.value) {
        await initThreeJS();
        animate();

        // Event listeners
        window.addEventListener('resize', handleResize);

        // Mostrar mensaje random cada 10 segundos
        setInterval(() => {
            if (robotLoaded.value) {
                showRandomMessage();
            }
        }, 10000);
    }
});
</script>

<template>
    <Head>
        <title>{{ seoConfig.title }}</title>
        <meta name="description" :content="seoConfig.description" />
        <meta name="robots" content="noindex, nofollow" />
        <link rel="icon" type="image/png" :href="seoConfig.favicon" />

        <!-- Open Graph -->
        <meta property="og:title" :content="seoConfig.title" />
        <meta property="og:description" :content="seoConfig.description" />
        <meta property="og:image" :content="seoConfig.ogImage" />
        <meta property="og:type" content="website" />
    </Head>

    <v-app>
        <!-- Preloader personalizado -->
        <v-overlay v-model="isLoading" class="align-center justify-center">
            <div class="text-center">
                <v-progress-circular
                    color="primary"
                    indeterminate
                    size="64"
                    width="6"
                ></v-progress-circular>
                <p class="tw-mt-4 tw-text-lg tw-text-gray-600">
                    Preparando tu saludo personalizado...
                </p>
            </div>
        </v-overlay>

        <v-main>
            <!-- Contenedor principal con fondo completo -->
            <div class="fullscreen-container">
                <!-- Menú superior con logos -->
                <header class="top-header">
                    <div class="logo-left">
                        <img
                            :src="`${baseUrl}/images/logo.svg`"
                            alt="Pentafon Logo"
                            class="logo-image"
                        />
                    </div>
                    <div class="logo-right">
                        <img
                            :src="`${baseUrl}/images/innovation.svg`"
                            alt="Innovation Logo"
                            class="innovation-image"
                        />
                    </div>
                </header>

                <!-- Robot 3D - Canvas que ocupa toda la pantalla -->
                <div
                    ref="robotContainer"
                    class="robot-container-fullscreen"
                    @click="showRandomMessage"
                >
                    <div v-if="!robotLoaded" class="robot-loading">
                        <v-progress-circular
                            indeterminate
                            color="primary"
                            size="64"
                        ></v-progress-circular>
                        <p class="tw-mt-4 tw-text-gray-600 tw-text-lg">Cargando robot...</p>
                    </div>
                </div>
            </div>
        </v-main>
    </v-app>
</template>

<style scoped>
.fullscreen-container {
    width: 100vw;
    height: 100vh;
    display: flex;
    flex-direction: column;
    padding: 0;
    margin: 0;
    /* Agregar el fondo de la imagen */
    background-image: url('/images/fondo1.png');
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    background-attachment: fixed;
    position: relative;
}

/* Overlay semitransparente para mejorar la legibilidad */
.fullscreen-container::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: rgba(255, 255, 255, 0.1);
    z-index: 1;
    pointer-events: none;
}

.top-header {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    height: 100px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0 2rem;
    background: rgba(255, 255, 255, 0.9);
    backdrop-filter: blur(10px);
    z-index: 1000;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.logo-left {
    display: flex;
    align-items: center;
}

.logo-right {
    display: flex;
    align-items: center;
}

.logo-image {
    height: 60px;
    width: auto;
    transition: transform 0.3s ease;
}

.logo-image:hover {
    transform: scale(1.05);
}

.innovation-image {
    height: 90px;
    width: auto;
    transition: transform 0.3s ease;
}

.innovation-image:hover {
    transform: scale(1.05);
}

.robot-container-fullscreen {
    width: 100vw;
    height: 100vh;
    background: transparent;
    position: relative;
    overflow: hidden;
    cursor: pointer;
    border: none;
    z-index: 2;
    /* Ocupar toda la pantalla */
    margin-top: 0;
    padding-top: 100px; /* Espacio para el header */
    box-sizing: border-box;
}

.robot-loading {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    display: flex;
    flex-direction: column;
    align-items: center;
    color: #666;
    z-index: 3;
}

.robot-container-fullscreen canvas {
    border-radius: 0;
    background: transparent !important;
}

/* Responsive */
@media (max-width: 768px) {
    .top-header {
        height: 80px;
        padding: 0 1.5rem;
    }

    .robot-container-fullscreen {
        padding-top: 80px;
    }

    .logo-image {
        height: 45px;
    }

    .innovation-image {
        height: 40px;
    }
}

@media (max-width: 480px) {
    .top-header {
        height: 70px;
        padding: 0 1rem;
    }

    .robot-container-fullscreen {
        padding-top: 70px;
    }

    .logo-image {
        height: 35px;
    }

    .innovation-image {
        height: 30px;
    }
}
</style>
