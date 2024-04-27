<template>
    <Head title="Прототип хостинга картинок" />
    <Layout>
        <q-page>
            <div class="q-pa-md row items-start q-gutter-md">
                <q-card class="my-card">
                    <q-card-section>
                        <div class="text-h6">Форма загрузки изображений</div>
                        <div class="text-subtitle2">Допустимые форматы: jpg, jpeg, png</div>
                        <div class="text-subtitle2">Максимальный размер файла: 1 Mb</div>
                        <div class="q-pa-md" style="width: 600px">
                            <q-form @submit="submit" class="q-gutter-md">
                                <template v-for="(image, index) in form.images" :key="index">
                                    <table style="width: 100%">
                                        <tr>
                                            <td style="width: 90%">
                                                <q-file color="blue-12" v-model="image.file"
                                                        label="Выберите изображение">
                                                    <template v-slot:prepend>
                                                        <q-icon name="attach_file"/>
                                                    </template>
                                                </q-file>
                                            </td>
                                            <td style="width: 10%">
                                                <q-btn class="glossy"
                                                       round
                                                       icon="clear"
                                                       size="sm"
                                                       @click="removeImageField(index)">
                                                </q-btn>
                                            </td>
                                        </tr>
                                    </table>

                                </template>
                                <q-btn label="Загрузить" type="submit" color="primary"/>
                                <q-btn
                                    @click="addImageField()"
                                    label="Добавить поле"
                                    color="primary"
                                    class="ml-6"/>
                            </q-form>
                        </div>
                    </q-card-section>
                </q-card>
            </div>
        </q-page>
    </Layout>
</template>

<script>
import {Head, router, useForm} from '@inertiajs/vue3';
import Layout from '@/Layouts/Layout.vue';
import InputError from "@/Components/InputError.vue";
import {useQuasar} from 'quasar'

export default {
    components: {InputError, Head, useForm, Layout},
    data() {
        return {
            form: useForm({
                images: [
                    {
                        file: null
                    },
                ],
            }),
            $q: useQuasar(),
        }
    },
    methods: {
        validateForm() {
            let result = false
            for (let key in this.form.data().images) {
                if (!this.form.data().images.hasOwnProperty(key)) {
                    return false
                }
                if (this.form.data().images[key].file !== null) {
                    result = true
                }
            }

            if (!result) {
                this.showNotify("Пожалуйста, выберите для загрузки хотя бы один файл.")
            }

            return result
        },
        submit() {
            if (!this.validateForm()) {
                return 0
            }
            axios.post(route('store.images'), this.form, {
                headers: {
                    'Content-Type': 'multipart/form-data'
                }
            })
                .then((res) => {
                    router.get(route('show.images.page'))
                })
                .catch((res) => {
                    if (res.response.data.message !== null && res.response.data.message.length > 0) {
                        this.showNotify(res.response.data.message)
                    }
                });
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
        addImageField() {
            if (this.form.images.length === 5) {
                this.showNotify("Согласно ТЗ максимальное количество полей 5.")
                return 0
            }
            this.form.images.push({
                file: null
            })
        },
        removeImageField(index) {
            if (this.form.images.length === 1) {
                this.showNotify("Минимальное количество полей 1")
                return 0
            }
            this.form.images.splice(index)
        }
    }
}
</script>
