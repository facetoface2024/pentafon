<script setup lang="ts">
</script>

<template>
    <v-footer
        color="#09152b"
        class="text-center"
    >
        <v-container>
            <v-row>
                <v-col cols="12">
                    <div class="footer-content">
                        <a
                            href="/privacy-policy.pdf"
                            target="_blank"
                            class="privacy-link"
                        >
                            Aviso de privacidad
                        </a>
                        <span class="mx-2">·</span>
                        <span>Legal</span>
                        <span class="mx-2">·</span>
                        <span>© 2025 PENTAFON, S.A. de C.V - RFC: PEN180716FU2. Todos los derechos reservados.</span>
                    </div>
                </v-col>
            </v-row>
        </v-container>
    </v-footer>
</template>

<style scoped>
.footer-content {
    font-size: 14px;
    color: #fff;
    background-color: #09152b;
    padding: 24px 0;
}

.privacy-link {
    color: #fff;
    text-decoration: none;
    transition: color 0.3s ease;
}

.privacy-link:hover {
    color: #fff;
    text-decoration: underline;
}

@media (max-width: 600px) {
    .footer-content {
        font-size: 12px;
    }
}
</style>
