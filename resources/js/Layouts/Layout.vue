<template>
    <q-layout view="hHh lpR fFf" class="bg-grey-1">
        <q-header elevated class="bg-white text-grey-8 q-py-xs" height-hint="58">
            <q-toolbar>
                <q-btn
                    flat
                    dense
                    round
                    @click="toggleLeftDrawer"
                    aria-label="Menu"
                    icon="menu"
                />

                <q-btn flat no-caps no-wrap class="q-ml-xs" v-if="$q.screen.gt.xs">
                    <q-toolbar-title shrink class="text-weight-bold">
                        Banki.shop: Тестовое задание. Прототип разработан Гановичевым Дмитрием.
                    </q-toolbar-title>
                </q-btn>

            </q-toolbar>
        </q-header>

        <q-drawer
            v-model="leftDrawerOpen"
            show-if-above
            bordered
            class="bg-grey-2"
            :width="240"
        >
            <q-scroll-area class="fit">
                <q-list padding>
                    <q-item v-for="link in links" :key="link.text" v-ripple clickable @click="goTo(link.url)">
                        <q-item-section avatar>
                            <q-icon color="grey" :name="link.icon" />
                        </q-item-section>
                        <q-item-section>
                            <q-item-label>{{ link.text }}</q-item-label>
                        </q-item-section>
                    </q-item>
                </q-list>
            </q-scroll-area>
        </q-drawer>

        <q-page-container>
            <slot />
        </q-page-container>
    </q-layout>
</template>

<script>
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'

export default {
    name: 'Layout',

    setup () {
        const leftDrawerOpen = ref(false)

        function goTo(url) {
            router.get(url)
        }

        function toggleLeftDrawer () {
            leftDrawerOpen.value = !leftDrawerOpen.value
        }

        return {
            leftDrawerOpen,
            toggleLeftDrawer,
            goTo,
            links: [
                { icon: 'home', text: 'Загрузка', url: route('home') },
                { icon: 'whatshot', text: 'Просмотр', url: route('show.images.page') },
                { icon: 'whatshot', text: 'Api Info', url: route('show.api.info.page') },
            ],
        }
    }
}
</script>

<style lang="sass">
.YL

    &__toolbar-input-container
        min-width: 100px
        width: 55%

    &__toolbar-input-btn
        border-radius: 0
        border-style: solid
        border-width: 1px 1px 1px 0
        border-color: rgba(0,0,0,.24)
        max-width: 60px
        width: 100%

    &__drawer-footer-link
        color: inherit
        text-decoration: none
        font-weight: 500
        font-size: .75rem

        &:hover
            color: #000
</style>
