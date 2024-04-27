<template>
    <Head title="Прототип хостинга картинок" />
    <Layout>
        <q-page>
            <div class="q-pa-md row items-start q-gutter-md">
                <q-card>
                    <q-card-section>
                        <div class="q-pa-md" style="width: 900px">
                            <q-input
                                v-model="searchImagesByFileName"
                                placeholder="Поиск по имени файла"
                            >
                                <template v-slot:append>
                                    <q-icon name="search" />
                                </template>
                            </q-input>
                        </div>
                    </q-card-section>

                    <q-card-section>
                        <div class="q-pa-md">
                            <div class="q-gutter-sm">
                                <table>
                                    <tr>
                                        <td colspan="3" style="font-size: large; text-align: center;">Поиск по дате/времени</td>
                                    </tr>
                                    <tr>
                                        <td style="font-size: large; text-align: center;">
                                            <q-btn color="primary" :label="startDate" @click="showDialogStartDateTime()"/>
                                        </td>
                                        <td style="font-size: large; text-align: center;">
                                            <q-btn color="primary" :label="endDate" @click="showDialogEndDateTime()"/>
                                        </td>
                                        <td style="font-size: large; text-align: center;">
                                            <q-btn color="primary" icon="search" @click="searchImagesByDateTime()"/>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="font-size: large; text-align: center;">Начало</td>
                                        <td style="font-size: large; text-align: center;">Конец</td>
                                        <td></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </q-card-section>

                    <q-card-section>
                        <div class="text-h6">Загруженные изображения</div>
                        <div class="q-pa-md" style="width: 900px">
                            <div class="q-pa-md">
                                <div class="q-col-gutter-md row items-start">
                                    <div class="col-4" v-for="(image, index) in imagesList" :key="index">
                                        <q-img
                                            :src="image.url"
                                            @click="showImage(image)"
                                            class="rounded-borders"
                                        />
                                        <b>Имя файла</b>: {{ image.name }} <br>
                                        <b>Дата загрузки</b>: {{ image.created_at }} <br>
                                        <table>
                                            <tr>
                                                <td>
                                                    <a :href="route('download.image.archive', image.id)">
                                                        <q-btn color="primary" label="ZIP" />
                                                    </a>
                                                </td>
                                                <td>
                                                    <a href="#" @click="showImage(image)"><q-btn color="primary" label="Просмотр" /></a>
                                                </td>
                                                <td>
                                                    <a href="#" @click="deleteImage(image.id)"><q-btn square color="purple" glossy icon="delete" /></a>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </q-card-section>
                </q-card>
            </div>
        </q-page>
    </Layout>

    <q-dialog ref="dialog">
        <q-card class="my-card" flat bordered>
            <q-img
                :src="imageData.url"
            />
            <q-card-actions align="right">
                <q-btn flat label="Закрыть" color="primary" v-close-popup />
            </q-card-actions>
        </q-card>
    </q-dialog>

    <q-dialog ref="dialogStartDateTime">
        <q-card class="date-card" flat bordered>
            <q-card-section>
                <table style="width: 100%">
                    <tr>
                        <td><q-date v-model="startDate" mask="YYYY-MM-DD HH:mm" color="purple" /></td>
                        <td><q-time v-model="startDate" mask="YYYY-MM-DD HH:mm" color="purple" /></td>
                    </tr>
                </table>
            </q-card-section>
            <q-card-actions align="right">
                <q-btn flat label="Закрыть" color="primary" v-close-popup />
            </q-card-actions>
        </q-card>
    </q-dialog>

    <q-dialog ref="dialogEndDateTime">
        <q-card class="date-card" flat bordered>
            <q-card-section>
                <table style="width: 100%">
                    <tr>
                        <td><q-date v-model="endDate" mask="YYYY-MM-DD HH:mm" color="purple" /></td>
                        <td><q-time v-model="endDate" mask="YYYY-MM-DD HH:mm" color="purple" /></td>
                    </tr>
                </table>
            </q-card-section>
            <q-card-actions align="right">
                <q-btn flat label="Закрыть" color="primary" v-close-popup />
            </q-card-actions>
        </q-card>
    </q-dialog>

</template>

<script>
import Layout from "@/Layouts/Layout.vue";
import { Head } from '@inertiajs/vue3';
import {useQuasar} from 'quasar'
export default {
    name: "ShowImages",
    components: { Layout, Head },
    props: {
        images: Object,
    },
    mounted() {
        this.imagesList = this.images
    },
    data () {
        return {
            imageData: null,
            imagesList: null,
            searchImagesByFileName: null,
            startDate: '2024-04-27 12:44',
            endDate: '2024-05-27 12:44',
            $q: useQuasar(),
        }
    },
    methods: {
        showImage (image) {
            this.imageData = image
            this.$refs.dialog.show()
        },
        showNotify(message) {
            this.$q.notify({
                message: message,
                color: 'primary',
                actions: [
                    {
                        icon: 'close', color: 'white', round: true, handler: () => { /* ... */
                        }
                    }
                ]
            })
        },
        showDialogStartDateTime () {
            this.$refs.dialogStartDateTime.show()
        },
        showDialogEndDateTime () {
            this.$refs.dialogEndDateTime.show()
        },
        deleteImage(id) {
            axios.delete(route('destroy.image', {id: id}))
                .then((res) => {
                    this.imagesList = res.data.images
                })
        },
        searchImagesByDateTime() {
            const data = {
                start_date: this.startDate,
                end_date: this.endDate,
            }
            axios.post(route('search.images.by.date.time'), data)
                .then((res) => {
                    this.imagesList = res.data.images
                })
                .catch((res) => {
                    if (res.response.data.message !== null && res.response.data.message.length > 0) {
                        this.showNotify(res.response.data.message)
                    }
                });
        }
    },
    watch: {
        searchImagesByFileName(newValue, oldValue) {
            axios.post(route('search.images.by.name'), {name: newValue})
                .then((res) => {
                    this.imagesList = res.data.images
                })
        }
    }
}
</script>

<style lang="sass">
.my-card
    width: 100%
    max-width: 600px
.date-card
    width: 100%
    min-width: 620px
</style>
