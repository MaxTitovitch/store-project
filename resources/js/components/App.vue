<template>
    <v-app id="inspire">
        <v-navigation-drawer v-model="drawer" app temporary class="gradient-project">
            <v-list>
                <v-list-item
                        link v-for="link in linkList"
                        :key="link.title"
                        :to="link.url"
                        class="gradient-project"
                >
                    <v-list-item-icon>
                        <v-icon left>{{ link.icon }}</v-icon>
                    </v-list-item-icon>
                    <v-list-item-content>
                        <v-list-item-title>{{ link.title }}</v-list-item-title>
                    </v-list-item-content>
                </v-list-item>
            </v-list>
        </v-navigation-drawer>

        <v-app-bar app class="gradient-project" dark>
            <v-app-bar-nav-icon
                    @click.stop="drawer = !drawer"
                    class="hidden-md-and-up"
            />
            <v-toolbar-title class="hidden-sm-and-down">
                <router-link to="/" >
                    <v-img src="/Logo-big.png" class="logo-image"></v-img>
                </router-link>
            </v-toolbar-title>
            <v-spacer />

            <v-toolbar-title class="hidden-md-and-up">
                <router-link to="/" >
                    <v-img src="/Logo-big.png" class="logo-image logo-image-small"></v-img>
                </router-link>
            </v-toolbar-title>
            <v-toolbar-items class="hidden-sm-and-down">
                <v-btn
                        class="gradient-project"
                        elevation="0"
                        link v-for="link in linkList"
                        :key="link.title"
                        :to="link.url"
                >
                    <v-icon left>{{ link.icon }}</v-icon>
                    {{ link.title }}
                </v-btn>
            </v-toolbar-items>
        </v-app-bar>

        <v-content>
            <router-view></router-view>
        </v-content>

        <v-footer app class="text-center gradient-project">
                <v-container fluid>
                    <v-row>
                        <v-col cols="12">
                            <span class="white--text">НаНастолке<sup>&copy;</sup> {{(new Date).getFullYear() }}</span>
                        </v-col>
                    </v-row>
                </v-container>
        </v-footer>
    </v-app>
</template>

<script>
    export default {
        data: () => ({
            drawer: false,
            mainColor: 'red darken-4',
            linkList: [
                { title: 'Акции', icon: 'mdi-sale', url: '/sales' },
                { title: 'Товары', icon: 'mdi-cart-outline', url: '/products' },
                { title: 'Статьи', icon: 'mdi-post-outline', url: '/pages' },
                { title: 'Топы', icon: 'mdi-medal-outline', url: '/tops' }
            ]
        })
    }
</script>

<style scoped>
    .pointer {
        cursor: pointer;
    }

    main {
        background: url(/background.jpg) repeat;
        background-size: 100%;
    }

    .gradient-project {
        background: linear-gradient(180deg, #ff5f66, #ff9765);
    }

    .logo-image {
        height: 62px;
        width: 220px;
        background-size: auto 100%;
    }

    .logo-image-small {
        height: 50px;
        background-size: 80%;
    }

    .logo-image-small>* {
        background-size: 80%;
    }
</style>