<template>
    <Head title="Прототип хостинга картинок" />
    <Layout>
        <q-page>
            <div class="q-pa-md row items-start q-gutter-md">
                <q-card class="my-card">
                    <q-card-section>
                        <div class="text-h6">API</div>
                        <div class="q-pa-md" style="width: 600px">
                            <table>
                                <tr>
                                    <td>GET /api/get-all-images</td>
                                    <td>Возвращает данные всех загруженных изображений</td>
                                </tr>
                                <tr>
                                    <td>GET /get-image/id</td>
                                    <td>Возвращает данные изображения с идентификатором id</td>
                                </tr>
                            </table>
                        </div>
                    </q-card-section>
                </q-card>
            </div>
        </q-page>
    </Layout>
</template>

<script>
import Layout from "@/Layouts/Layout.vue";
import {Head} from "@inertiajs/vue3";

export default {
    name: "ShowApiInfo",
    components: {Head, Layout},
}
</script>

<style scoped lang="sass">
table
    border-collapse: separate
    border-spacing: 1em 1em
</style>
