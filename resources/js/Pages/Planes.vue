<script setup lang="ts">
import { ref } from 'vue'; // Asegúrate de que esto esté correctamente importado
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import axios from 'axios';

// Definimos los tipos para los campos del formulario
type Plan = {
  nombre_plan: string;
  descripcion: string;
  costo_mensual: number;
  costo_anual: number;
  descuentos: number | null;
  soporte: boolean;
  anuncios: boolean;
  estatus: string;
};

// Definimos las propiedades reactivas para cada campo
const nombrePlan = ref<string>('');
const descripcion = ref<string>('');
const costoMensual = ref<number>(0);
const costoAnual = ref<number>(0);
const descuentos = ref<number | null>(null);
const soporte = ref<boolean>(false);
const anuncios = ref<boolean>(false);
const estatus = ref<string>('');
const dialog = ref<boolean>(false); // Dialog como ref, asegurando que sea reactivo
// Opciones para el campo de "estatus"
const estatusOptions = [
  { value: 'activo', text: 'Activo' },
  { value: 'inactivo', text: 'Inactivo' }
];

// Función para manejar el envío del formulario
const submitForm = async () => {
  const formData: Plan = {
    nombre_plan: nombrePlan.value,
    descripcion: descripcion.value,
    costo_mensual: costoMensual.value,
    costo_anual: costoAnual.value,
    descuentos: descuentos.value,
    soporte: soporte.value,
    anuncios: anuncios.value,
    estatus: estatus.value,
  };

  try {
    const response = await axios.post('/api/planes', formData);
    console.log('Plan creado:', response.data);
    dialog.value = false; // Cierra el diálogo al enviar el formulario
    // Lógica adicional después de que el formulario se envíe correctamente
  } catch (error) {
    console.error('Error al enviar el formulario:', error);
  }
};
</script>

<template>
  <Head title="Crear Plan" />

  <AuthenticatedLayout>
    <template #header>
      <h2 class="tw-text-xl tw-font-semibold tw-leading-tight tw-text-gray-800">
        Crear Plan
      </h2>
    </template>

    <div class="tw-py-12">
      <div class="tw-mx-auto tw-max-w-7xl sm:tw-px-6 lg:tw-px-8">
        <div class="tw-overflow-hidden tw-bg-white tw-shadow-sm sm:tw-rounded-lg">
          <div class="tw-p-6 tw-text-gray-900">
            <!-- Tabla de Planes -->
            <v-data-table
              :headers="[
                { title: 'Nombre del Plan', key: 'nombre_plan' },
                { title: 'Descripción', key: 'descripcion' },
                { title: 'Costo Mensual', key: 'costo_mensual' },
                { title: 'Costo Anual', key: 'costo_anual' },
                { title: 'Estatus', key: 'estatus' },
                { title: 'Acciones', key: 'acciones', sortable: false }]"
              :items="[]"
              item-value="nombre_plan"
            >
              <template v-slot:item.acciones="{ item }">
                <v-btn icon @click="dialog = true">
                  <v-icon>mdi-pencil</v-icon>
                </v-btn>
              </template>
            </v-data-table>

            <!-- Botón para añadir nuevo plan -->
            <v-btn color="primary" @click="dialog = true" class="tw-mt-4">Añadir Plan</v-btn>

            <!-- Diálogo para el formulario -->
            <v-dialog v-model="dialog" max-width="600px">
              <v-card>
                <v-card-title>
                  <span class="text-h5">Crear Plan</span>
                </v-card-title>

                <v-card-text>
                  <v-form @submit.prevent="submitForm">
                    <v-row>
                      <v-col cols="12" md="6">
                        <v-text-field v-model="nombrePlan" label="Nombre del Plan" color="secondary" variant="outlined" required />
                      </v-col>

                      <v-col cols="12" md="6">
                        <v-textarea v-model="descripcion" label="Descripción" color="secondary" variant="outlined" required />
                      </v-col>

                      <v-col cols="12" md="6">
                        <v-text-field v-model="costoMensual" label="Costo Mensual" color="secondary" type="number" variant="outlined" required />
                      </v-col>

                      <v-col cols="12" md="6">
                        <v-text-field v-model="costoAnual" label="Costo Anual" color="secondary" type="number" variant="outlined" required />
                      </v-col>

                      <v-col cols="12" md="6">
                        <v-text-field v-model="descuentos" label="Descuentos" color="secondary" type="number" variant="outlined" />
                      </v-col>

                      <v-col cols="12" md="6">
                        <v-switch v-model="soporte" label="Soporte" color="secondary" variant="outlined" />
                      </v-col>

                      <v-col cols="12" md="6">
                        <v-switch v-model="anuncios" label="Anuncios" color="secondary" variant="outlined" />
                      </v-col>

                      <v-col cols="12" md="6">
                        <v-select v-model="estatus" :items="estatusOptions" label="Estatus" color="secondary" variant="outlined" required />
                      </v-col>

                      <v-col cols="12">
                        <v-btn type="submit" color="primary" block>Crear Plan</v-btn>
                      </v-col>
                    </v-row>
                  </v-form>
                </v-card-text>
              </v-card>
            </v-dialog>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<style scoped>
.v-field__input {
  border: none !important;
  box-shadow: none !important;
}
</style>
