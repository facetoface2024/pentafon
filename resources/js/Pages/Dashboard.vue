<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import { ref, computed, onMounted } from 'vue';
import { useDisplay } from 'vuetify';
// @ts-ignore - QRCode library
import QRCode from 'qrcode';

// Props
interface Cliente {
    id: number;
    nombre: string;
    apellido_paterno: string;
    apellido_materno: string;
    correo: string;
    nombre_completo: string;
    qr_path: string | null;
    qr_token: string;
    qr_activo: boolean;
    qr_url: string;
    created_at: string;
}

interface Props {
    clientes: Cliente[];
}

const props = defineProps<Props>();
const { mobile } = useDisplay();

// Refs
const dialog = ref(false);
const dialogEliminar = ref(false);
const dialogScanner = ref(false);
const dialogSeleccionQr = ref(false);
const loading = ref(false);
const clienteAEliminar = ref<Cliente | null>(null);
const clientesConQr = ref<Cliente[]>([]);
const clientesSeleccionados = ref<number[]>([]);
const archivoExcel = ref<File | null>(null);
const search = ref('');
const snackbar = ref(false);
const snackbarMessage = ref('');
const snackbarColor = ref('success');
const fileInput = ref<HTMLInputElement | null>(null);
const videoElement = ref<HTMLVideoElement | null>(null);
const canvasElement = ref<HTMLCanvasElement | null>(null);
const stream = ref<MediaStream | null>(null);
const scanning = ref(false);

// Computed
const clientes = computed(() => props.clientes || []);

// Headers para la tabla
const headers = [
    { title: 'ID', key: 'id', width: '80px' },
    { title: 'Nombre Completo', key: 'nombre_completo', sortable: true },
    { title: 'Correo', key: 'correo', sortable: true },
    { title: 'QR Estado', key: 'qr_activo', width: '120px' },
    { title: 'Fecha Creación', key: 'created_at', width: '150px' },
    { title: 'Acciones', key: 'actions', sortable: false, width: '250px' }
];

// Methods
const mostrarSnackbar = (mensaje: string, color: string = 'success') => {
    snackbarMessage.value = mensaje;
    snackbarColor.value = color;
    snackbar.value = true;
};

const descargarPlantilla = () => {
    window.open('/plantilla-clientes', '_blank');
};

const exportarClientes = () => {
    window.open('/exportar-clientes', '_blank');
};

const generarQrClientesNuevos = async () => {
    try {
        console.log('🔄 Iniciando generación automática de QR...');

        // Obtener lista de clientes que necesitan QR
        const response = await fetch('/generar-todos-qr', {
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
            },
        });

        const data = await response.json();

        if (!data.success) {
            mostrarSnackbar(`❌ Error al obtener clientes`, 'error');
            router.reload();
            return;
        }

        console.log(`📋 Total de clientes activos: ${data.clientes.length}`);

        // Filtrar clientes sin QR físico guardado
        const clientesSinQr = data.clientes.filter((cliente: any) => !cliente.tiene_archivo_fisico);

        console.log(`🔍 Clientes sin archivo QR físico: ${clientesSinQr.length}`);

        if (clientesSinQr.length > 0) {
            mostrarSnackbar(`🔄 Generando ${clientesSinQr.length} códigos QR con calidad profesional...`, 'info');

            let exitosos = 0;
            let errores = 0;

            // Generar QR para cada cliente usando la librería QRCode.js
            for (const cliente of clientesSinQr) {
                try {
                    console.log(`⚡ Generando QR para: ${cliente.correo}`);

                    // Preparar el QR en el backend
                    const qrResponse = await fetch(`/generar-qr/${cliente.id}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                        },
                    });

                    const qrData = await qrResponse.json();

                    if (qrData.success) {
                                                // Generar QR PNG de alta calidad en el frontend
                        const qrDataUrl = await QRCode.toDataURL(qrData.qr_url, {
                            width: 400,
                            margin: 2,
                            color: {
                                dark: '#000000',
                                light: '#FFFFFF'
                            },
                            errorCorrectionLevel: 'M'
                        });

                        // Convertir DataURL a contenido binario para PNG
                        console.log('🔍 DataURL generado:', qrDataUrl.substring(0, 100) + '...');
                        const base64Data = qrDataUrl.split(',')[1];

                        // Verificar que el base64 es válido
                        if (!base64Data) {
                            throw new Error('Error al extraer datos base64 del QR');
                        }

                        console.log('📦 Base64 extraído, tamaño:', base64Data.length);

                        // Guardar el PNG en el storage
                        await guardarQrEnStorage(cliente.id, base64Data, qrData.cliente.qr_path);
                        exitosos++;
                        console.log(`✅ QR guardado para: ${cliente.correo}`);
                    } else {
                        console.error(`❌ Error en respuesta QR para ${cliente.correo}:`, qrData.message);
                        errores++;
                    }
                } catch (error) {
                    console.error(`❌ Excepción generando QR para ${cliente.correo}:`, error);
                    errores++;
                }
            }

            if (exitosos > 0) {
                mostrarSnackbar(`✅ ${exitosos} códigos QR generados correctamente`, 'success');
            }
            if (errores > 0) {
                mostrarSnackbar(`⚠️ ${errores} errores al generar QR`, 'warning');
            }
        } else {
            console.log('✅ Todos los clientes ya tienen sus QR generados');
            mostrarSnackbar('✅ Todos los códigos QR ya están generados', 'success');
        }

        // Recargar la página para mostrar los cambios
        console.log('🔄 Recargando página...');
        router.reload();

    } catch (error) {
        mostrarSnackbar('❌ Error al generar códigos QR', 'error');
        console.error('Error:', error);
        router.reload();
    }
};


const mostrarDialogoSeleccionQr = async () => {
    loading.value = true;
    try {
        // Obtener lista de clientes con QR disponibles
        const response = await fetch('/clientes-con-qr');
        const data = await response.json();

        if (data.success) {
            clientesConQr.value = data.clientes;
            clientesSeleccionados.value = data.clientes.map((c: Cliente) => c.id); // Seleccionar todos por defecto

            if (data.clientes_sin_qr > 0) {
                mostrarSnackbar(`⚠️ ${data.clientes_sin_qr} clientes aún no tienen QR procesado. Probablemente fueron subidos recientemente.`, 'warning');
            }

            if (data.clientes.length === 0) {
                mostrarSnackbar('❌ No hay códigos QR disponibles para descargar', 'error');
                return;
            }

            dialogSeleccionQr.value = true;
        } else {
            mostrarSnackbar(`❌ ${data.message}`, 'error');
        }
    } catch (error) {
        mostrarSnackbar('❌ Error al obtener lista de clientes', 'error');
        console.error('Error:', error);
    } finally {
        loading.value = false;
    }
};

const descargarQrSeleccionados = async () => {
    if (clientesSeleccionados.value.length === 0) {
        mostrarSnackbar('❌ Debes seleccionar al menos un cliente', 'warning');
        return;
    }

    loading.value = true;
    try {
        mostrarSnackbar('🔄 Preparando descarga de códigos QR seleccionados...', 'info');

        // Descargar el ZIP con los clientes seleccionados
        const response = await fetch('/descargar-qr-seleccionados', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
            },
            body: JSON.stringify({
                clientes_ids: clientesSeleccionados.value
            })
        });

        if (!response.ok) {
            const errorData = await response.json();
            mostrarSnackbar(`❌ ${errorData.message}`, 'error');
            return;
        }

        // Crear descarga del blob
        const blob = await response.blob();
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `codigos_qr_seleccionados_${new Date().toISOString().slice(0,10)}.zip`;
        document.body.appendChild(a);
        a.click();
        window.URL.revokeObjectURL(url);
        document.body.removeChild(a);

        mostrarSnackbar('✅ ZIP descargado exitosamente', 'success');
        dialogSeleccionQr.value = false;

    } catch (error) {
        mostrarSnackbar('❌ Error al descargar ZIP', 'error');
        console.error('Error:', error);
    } finally {
        loading.value = false;
    }
};

const seleccionarTodos = () => {
    clientesSeleccionados.value = clientesConQr.value.map(c => c.id);
};

const deseleccionarTodos = () => {
    clientesSeleccionados.value = [];
};

const abrirDialogoArchivo = () => {
    fileInput.value?.click();
};

const onFileSelected = (event: Event) => {
    const target = event.target as HTMLInputElement;
    if (target.files && target.files.length > 0) {
        archivoExcel.value = target.files[0];
        dialog.value = true;
    }
};

const subirArchivo = async () => {
    if (!archivoExcel.value) return;

    loading.value = true;
    const formData = new FormData();
    formData.append('archivo', archivoExcel.value);

    try {
        const response = await fetch('/subir-excel', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
            },
        });

        const data = await response.json();

                if (data.success) {
            mostrarSnackbar(`✅ ${data.message}`, 'success');
            if (data.errores && data.errores.length > 0) {
                console.warn('Errores encontrados:', data.errores);
            }

            // Si se debe generar QR automáticamente
            if (data.generar_qr_automatico && data.clientes_creados > 0) {
                // Esperar un poco y luego generar QR para todos los clientes nuevos
                setTimeout(async () => {
                    await generarQrClientesNuevos();
                }, 2000); // Aumenté el tiempo de espera
            } else {
                router.reload();
            }
        } else {
            mostrarSnackbar(`❌ ${data.message}`, 'error');
        }
    } catch (error) {
        mostrarSnackbar('❌ Error al subir archivo', 'error');
        console.error('Error:', error);
    } finally {
        loading.value = false;
        dialog.value = false;
        archivoExcel.value = null;
    }
};

const generarQr = async (cliente: Cliente) => {
    loading.value = true;
    try {
        // 1. Preparar el QR en el backend
        const response = await fetch(`/generar-qr/${cliente.id}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
            },
        });

        const data = await response.json();

        if (data.success) {
            // 2. Generar QR PNG en el frontend
            const qrDataUrl = await QRCode.toDataURL(data.qr_url, {
                width: 400,
                margin: 2,
                color: {
                    dark: '#000000',
                    light: '#FFFFFF'
                },
                errorCorrectionLevel: 'M'
            });

                        // Convertir DataURL a contenido binario para PNG
            console.log('🔍 DataURL generado (individual):', qrDataUrl.substring(0, 100) + '...');
            const base64Data = qrDataUrl.split(',')[1];

            // Verificar que el base64 es válido
            if (!base64Data) {
                throw new Error('Error al extraer datos base64 del QR');
            }

            console.log('📦 Base64 extraído (individual), tamaño:', base64Data.length);

            // 3. Guardar el PNG en el storage mediante backend
            await guardarQrEnStorage(cliente.id, base64Data, data.cliente.qr_path);

            mostrarSnackbar('✅ QR generado exitosamente', 'success');
            router.reload();
        } else {
            mostrarSnackbar(`❌ ${data.message}`, 'error');
        }
    } catch (error) {
        mostrarSnackbar('❌ Error al generar QR', 'error');
        console.error('Error:', error);
    } finally {
        loading.value = false;
    }
};

const guardarQrEnStorage = async (clienteId: number, qrContent: string, qrPath: string) => {
    try {
        const response = await fetch('/guardar-qr', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
            },
            body: JSON.stringify({
                cliente_id: clienteId,
                qr_content: qrContent,
                qr_path: qrPath
            })
        });

        const data = await response.json();
        if (!data.success) {
            throw new Error(data.message);
        }
    } catch (error) {
        console.error('Error al guardar QR:', error);
        throw error;
    }
};

const toggleQrEstado = async (cliente: Cliente) => {
    try {
        const response = await fetch(`/toggle-qr/${cliente.id}`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
            },
        });

        const data = await response.json();

        if (data.success) {
            mostrarSnackbar('✅ Estado actualizado', 'success');
            router.reload();
        } else {
            mostrarSnackbar(`❌ ${data.message}`, 'error');
        }
    } catch (error) {
        mostrarSnackbar('❌ Error al actualizar estado', 'error');
        console.error('Error:', error);
    }
};

const confirmarEliminar = (cliente: Cliente) => {
    clienteAEliminar.value = cliente;
    dialogEliminar.value = true;
};

const eliminarCliente = async () => {
    if (!clienteAEliminar.value) return;

    try {
        const response = await fetch(`/clientes/${clienteAEliminar.value.id}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
            },
        });

        const data = await response.json();

        if (data.success) {
            mostrarSnackbar('✅ Cliente eliminado', 'success');
            router.reload();
        } else {
            mostrarSnackbar(`❌ ${data.message}`, 'error');
        }
    } catch (error) {
        mostrarSnackbar('❌ Error al eliminar cliente', 'error');
        console.error('Error:', error);
    } finally {
        dialogEliminar.value = false;
        clienteAEliminar.value = null;
    }
};

const abrirQr = (cliente: Cliente) => {
    window.open(cliente.qr_url, '_blank');
};

const descargarQr = async (cliente: Cliente) => {
    if (!cliente.qr_path) {
        // Si no tiene QR guardado, generarlo al vuelo
        await generarYDescargarQr(cliente);
        return;
    }

    try {
        const response = await fetch(`/storage/${cliente.qr_path}`);
        if (!response.ok) {
            // Si el archivo no existe, generarlo
            await generarYDescargarQr(cliente);
            return;
        }

        const blob = await response.blob();
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        // Usar el correo como nombre del archivo
        const extension = cliente.qr_path.split('.').pop() || 'png';
        a.download = `${cliente.correo}.${extension}`;
        document.body.appendChild(a);
        a.click();
        window.URL.revokeObjectURL(url);
        document.body.removeChild(a);
    } catch (error) {
        mostrarSnackbar('❌ Error al descargar QR', 'error');
        console.error('Error:', error);
    }
};

const generarYDescargarQr = async (cliente: Cliente) => {
    try {
        // Generar QR PNG directamente
        const qrDataUrl = await QRCode.toDataURL(cliente.qr_url, {
            width: 400,
            margin: 2,
            color: {
                dark: '#000000',
                light: '#FFFFFF'
            },
            errorCorrectionLevel: 'M'
        });

        // Convertir DataURL a Blob para PNG
        const base64Data = qrDataUrl.split(',')[1];
        const binaryData = atob(base64Data);
        const bytes = new Uint8Array(binaryData.length);
        for (let i = 0; i < binaryData.length; i++) {
            bytes[i] = binaryData.charCodeAt(i);
        }

        const blob = new Blob([bytes], { type: 'image/png' });
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `${cliente.correo}.png`;
        document.body.appendChild(a);
        a.click();
        window.URL.revokeObjectURL(url);
        document.body.removeChild(a);

        mostrarSnackbar('✅ QR descargado exitosamente', 'success');
    } catch (error) {
        mostrarSnackbar('❌ Error al generar y descargar QR', 'error');
        console.error('Error:', error);
    }
};

// Scanner QR
const iniciarScanner = async () => {
    dialogScanner.value = true;
    scanning.value = true;

    try {
        stream.value = await navigator.mediaDevices.getUserMedia({
            video: { facingMode: 'environment' }
        });

        if (videoElement.value) {
            videoElement.value.srcObject = stream.value;
            videoElement.value.play();
        }

        // Iniciar detección QR
        iniciarDeteccionQr();
    } catch (error) {
        mostrarSnackbar('❌ Error al acceder a la cámara', 'error');
        console.error('Error:', error);
        scanning.value = false;
    }
};

const detenerScanner = () => {
    if (stream.value) {
        stream.value.getTracks().forEach(track => track.stop());
        stream.value = null;
    }
    scanning.value = false;
    dialogScanner.value = false;
};

const iniciarDeteccionQr = () => {
    if (!videoElement.value || !canvasElement.value) return;

    const canvas = canvasElement.value;
    const context = canvas.getContext('2d');
    const video = videoElement.value;

    const detectar = () => {
        if (!scanning.value) return;

        if (video.readyState === video.HAVE_ENOUGH_DATA) {
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            context?.drawImage(video, 0, 0, canvas.width, canvas.height);

            const imageData = context?.getImageData(0, 0, canvas.width, canvas.height);

            // Aquí integrarías una librería de detección QR como jsQR
            // Por simplicidad, simularemos la detección
            // En producción, usarías: const code = jsQR(imageData.data, imageData.width, imageData.height);
        }

        setTimeout(detectar, 100);
    };

    detectar();
};

onMounted(() => {
    // Agregar meta CSRF token si no existe
    if (!document.querySelector('meta[name="csrf-token"]')) {
        const meta = document.createElement('meta');
        meta.name = 'csrf-token';
        meta.content = document.querySelector('meta[name="_token"]')?.getAttribute('content') || '';
        document.head.appendChild(meta);
    }
});
</script>

<template>
    <Head title="Dashboard - Sistema QR" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="tw-text-xl tw-font-semibold tw-leading-tight tw-text-gray-800">
                📊 Dashboard - Sistema QR
            </h2>
        </template>

        <div class="tw-py-12">
            <div class="tw-mx-auto tw-max-w-7xl sm:tw-px-6 lg:tw-px-8">
                <v-container fluid>
                    <!-- Acciones principales -->
                    <v-row class="tw-mb-6">
                        <v-col cols="12">
                            <v-card class="tw-mb-4">
                                <v-card-title class="tw-bg-blue-50">
                                    <v-icon class="tw-mr-2">mdi-tools</v-icon>
                                    Acciones Principales
                                </v-card-title>
                                <v-card-text>
                                    <v-row>
                                        <v-col cols="12" md="2">
                                            <v-btn
                                                color="primary"
                                                variant="elevated"
                                                prepend-icon="mdi-download"
                                                @click="descargarPlantilla"
                                                block
                                            >
                                                Descargar Plantilla
                                            </v-btn>
                                        </v-col>
                                        <v-col cols="12" md="2">
                                            <v-btn
                                                color="success"
                                                variant="elevated"
                                                prepend-icon="mdi-upload"
                                                @click="abrirDialogoArchivo"
                                                block
                                            >
                                                Subir Excel
                                            </v-btn>
                                        </v-col>
                                        <v-col cols="12" md="2">
                                            <v-btn
                                                color="warning"
                                                variant="elevated"
                                                prepend-icon="mdi-file-excel"
                                                @click="exportarClientes"
                                                block
                                            >
                                                Exportar Clientes
                                            </v-btn>
                                        </v-col>
                                        <v-col cols="12" md="2">
                                            <v-btn
                                                color="purple"
                                                variant="elevated"
                                                prepend-icon="mdi-qrcode-scan"
                                                @click="iniciarScanner"
                                                block
                                            >
                                                Escanear QR
                                            </v-btn>
                                        </v-col>
                                        <v-col cols="12" md="2">
                                            <v-btn
                                                color="teal"
                                                variant="elevated"
                                                prepend-icon="mdi-download-multiple"
                                                @click="mostrarDialogoSeleccionQr"
                                                :loading="loading"
                                                block
                                            >
                                                Descargar ZIP QR
                                            </v-btn>
                                        </v-col>
                                    </v-row>
                                </v-card-text>
                            </v-card>
                        </v-col>
                    </v-row>

                    <!-- Tabla de clientes -->
                    <v-row>
                        <v-col cols="12">
                            <v-card>
                                <v-card-title class="tw-bg-gray-50">
                                    <v-row align="center">
                                        <v-col cols="12" md="6">
                                            <div class="tw-flex tw-items-center">
                                                <v-icon class="tw-mr-2">mdi-account-group</v-icon>
                                                <span>Clientes Registrados ({{ clientes.length }})</span>
                                            </div>
                                        </v-col>
                                        <v-col cols="12" md="6">
                                            <v-text-field
                                                v-model="search"
                                                density="compact"
                                                label="Buscar clientes..."
                                                prepend-inner-icon="mdi-magnify"
                                                variant="outlined"
                                                hide-details
                                                single-line
                                            ></v-text-field>
                                        </v-col>
                                    </v-row>
                                </v-card-title>

                                <v-data-table
                                    :headers="headers"
                                    :items="clientes"
                                    :search="search"
                                    :loading="loading"
                                    class="elevation-1"
                                    :items-per-page="10"
                                    :mobile="mobile"
                                >
                                    <template #item.qr_activo="{ item }">
                                        <v-chip
                                            :color="item.qr_activo ? 'success' : 'error'"
                                            size="small"
                                            variant="elevated"
                                        >
                                            {{ item.qr_activo ? 'Activo' : 'Inactivo' }}
                                        </v-chip>
                                    </template>

                                    <template #item.actions="{ item }">
                                        <div class="tw-flex tw-gap-1 tw-flex-wrap">
                                            <!-- Abrir QR -->
                                            <v-tooltip text="Abrir Página QR">
                                                <template #activator="{ props }">
                                                    <v-btn
                                                        v-bind="props"
                                                        icon="mdi-open-in-new"
                                                        size="small"
                                                        color="success"
                                                        variant="elevated"
                                                        @click="abrirQr(item)"
                                                    ></v-btn>
                                                </template>
                                            </v-tooltip>

                                            <!-- Descargar QR -->
                                            <v-tooltip text="Descargar QR">
                                                <template #activator="{ props }">
                                                    <v-btn
                                                        v-bind="props"
                                                        icon="mdi-download"
                                                        size="small"
                                                        color="info"
                                                        variant="elevated"
                                                        :disabled="!item.qr_path"
                                                        @click="descargarQr(item)"
                                                    ></v-btn>
                                                </template>
                                            </v-tooltip>

                                            <!-- Toggle Estado -->
                                            <v-tooltip :text="item.qr_activo ? 'Desactivar QR' : 'Activar QR'">
                                                <template #activator="{ props }">
                                                    <v-btn
                                                        v-bind="props"
                                                        :icon="item.qr_activo ? 'mdi-eye-off' : 'mdi-eye'"
                                                        size="small"
                                                        :color="item.qr_activo ? 'warning' : 'success'"
                                                        variant="elevated"
                                                        @click="toggleQrEstado(item)"
                                                    ></v-btn>
                                                </template>
                                            </v-tooltip>

                                            <!-- Eliminar -->
                                            <v-tooltip text="Eliminar Cliente">
                                                <template #activator="{ props }">
                                                    <v-btn
                                                        v-bind="props"
                                                        icon="mdi-delete"
                                                        size="small"
                                                        color="error"
                                                        variant="elevated"
                                                        @click="confirmarEliminar(item)"
                                                    ></v-btn>
                                                </template>
                                            </v-tooltip>
                    </div>
                                    </template>

                                    <template #no-data>
                                        <div class="tw-text-center tw-py-8">
                                            <v-icon size="64" color="grey">mdi-account-off</v-icon>
                                            <p class="tw-text-gray-500 tw-mt-2">No hay clientes registrados</p>
                </div>
                                    </template>
                                </v-data-table>
                            </v-card>
                        </v-col>
                    </v-row>
                </v-container>
            </div>
        </div>

        <!-- Dialog subir archivo -->
        <v-dialog v-model="dialog" max-width="500">
            <v-card>
                <v-card-title>
                    <v-icon class="tw-mr-2">mdi-upload</v-icon>
                    Subir Archivo Excel
                </v-card-title>
                <v-card-text>
                    <div v-if="archivoExcel" class="tw-mb-4">
                        <v-alert type="info" variant="outlined">
                            <strong>Archivo seleccionado:</strong> {{ archivoExcel.name }}
                        </v-alert>
                    </div>
                    <p class="tw-text-sm tw-text-gray-600">
                        Asegúrate de que el archivo Excel tenga las columnas:
                        <strong>Nombre (obligatorio), Apellido Paterno (opcional), Apellido Materno (opcional), Correo (obligatorio)</strong>
                    </p>
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn @click="dialog = false">Cancelar</v-btn>
                    <v-btn
                        color="primary"
                        :loading="loading"
                        @click="subirArchivo"
                    >
                        Subir
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>

        <!-- Dialog eliminar cliente -->
        <v-dialog v-model="dialogEliminar" max-width="400">
            <v-card>
                <v-card-title class="tw-text-red-600">
                    <v-icon class="tw-mr-2">mdi-alert</v-icon>
                    Confirmar Eliminación
                </v-card-title>
                <v-card-text>
                    ¿Estás seguro de que deseas eliminar al cliente
                    <strong>{{ clienteAEliminar?.nombre_completo }}</strong>?
                    <br><br>
                    <v-alert type="warning" variant="outlined">
                        Esta acción no se puede deshacer
                    </v-alert>
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn @click="dialogEliminar = false">Cancelar</v-btn>
                    <v-btn
                        color="error"
                        @click="eliminarCliente"
                    >
                        Eliminar
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>

        <!-- Dialog Scanner QR -->
        <v-dialog v-model="dialogScanner" max-width="600" persistent>
            <v-card>
                <v-card-title>
                    <v-icon class="tw-mr-2">mdi-qrcode-scan</v-icon>
                    Escáner QR
                </v-card-title>
                <v-card-text>
                    <div class="tw-text-center">
                        <video
                            ref="videoElement"
                            class="tw-w-full tw-max-w-md tw-mx-auto tw-rounded-lg"
                            autoplay
                            muted
                            playsinline
                        ></video>
                        <canvas ref="canvasElement" class="tw-hidden"></canvas>
                        <p class="tw-mt-4 tw-text-gray-600">
                            Apunta la cámara hacia un código QR para escanearlo
                        </p>
                    </div>
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn
                        color="error"
                        @click="detenerScanner"
                    >
                        Cerrar
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>

        <!-- Dialog Selección de Clientes para QR -->
        <v-dialog v-model="dialogSeleccionQr" max-width="800" persistent>
            <v-card>
                <v-card-title class="tw-bg-teal-50">
                    <v-icon class="tw-mr-2">mdi-checkbox-multiple-marked</v-icon>
                    Seleccionar Clientes para Descargar QR
                </v-card-title>
                <v-card-text class="tw-py-4">
                    <div class="tw-mb-4">
                        <v-alert type="info" variant="outlined" class="tw-mb-4">
                            <strong>Selecciona los clientes</strong> de los que deseas descargar sus códigos QR.
                            Solo se muestran clientes que ya tienen QR procesado.
                        </v-alert>

                        <div class="tw-flex tw-gap-2 tw-mb-4">
                            <v-btn
                                color="primary"
                                variant="outlined"
                                size="small"
                                @click="seleccionarTodos"
                            >
                                Seleccionar Todos
                            </v-btn>
                            <v-btn
                                color="secondary"
                                variant="outlined"
                                size="small"
                                @click="deseleccionarTodos"
                            >
                                Deseleccionar Todos
                            </v-btn>
                        </div>
                    </div>

                    <div class="tw-max-h-96 tw-overflow-y-auto">
                        <v-list>
                            <v-list-item
                                v-for="cliente in clientesConQr"
                                :key="cliente.id"
                                class="tw-border-b tw-border-gray-200"
                            >
                                <template #prepend>
                                    <v-checkbox
                                        v-model="clientesSeleccionados"
                                        :value="cliente.id"
                                        hide-details
                                    ></v-checkbox>
                                </template>

                                <v-list-item-title>
                                    {{ cliente.nombre_completo }}
                                </v-list-item-title>
                                <v-list-item-subtitle>
                                    {{ cliente.correo }}
                                </v-list-item-subtitle>

                                <template #append>
                                    <v-chip
                                        color="success"
                                        size="small"
                                        variant="elevated"
                                    >
                                        QR Listo
                                    </v-chip>
                                </template>
                            </v-list-item>
                        </v-list>
                    </div>

                    <div class="tw-mt-4 tw-text-sm tw-text-gray-600">
                        <strong>{{ clientesSeleccionados.length }}</strong> de <strong>{{ clientesConQr.length }}</strong> clientes seleccionados
                    </div>
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn
                        @click="dialogSeleccionQr = false"
                    >
                        Cancelar
                    </v-btn>
                    <v-btn
                        color="teal"
                        variant="elevated"
                        :loading="loading"
                        :disabled="clientesSeleccionados.length === 0"
                        @click="descargarQrSeleccionados"
                    >
                        Descargar ZIP ({{ clientesSeleccionados.length }})
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>

        <!-- Input file oculto -->
        <input
            ref="fileInput"
            type="file"
            accept=".xlsx,.xls"
            @change="onFileSelected"
            style="display: none"
        >

        <!-- Snackbar -->
        <v-snackbar
            v-model="snackbar"
            :color="snackbarColor"
            :timeout="4000"
            location="top right"
        >
            {{ snackbarMessage }}
            <template #actions>
                <v-btn
                    color="white"
                    variant="text"
                    @click="snackbar = false"
                >
                    Cerrar
                </v-btn>
            </template>
        </v-snackbar>
    </AuthenticatedLayout>
</template>

<style scoped>
.v-data-table {
    border-radius: 8px;
}

.v-card-title {
    border-radius: 8px 8px 0 0;
}

.v-chip {
    font-weight: 600;
}

.v-btn {
    text-transform: none;
}
</style>
