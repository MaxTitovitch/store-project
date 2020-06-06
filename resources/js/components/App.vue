<template>
    <v-app id="inspire">
        <v-navigation-drawer v-model="drawer" app temporary>
            <v-list>
                <v-list-item
                        link v-for="link in linkList"
                        :key="link.title"
                        :to="link.url"
                        :color="mainColor"
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

        <v-app-bar app :color="mainColor" dark>
            <v-app-bar-nav-icon
                    @click.stop="drawer = !drawer"
                    class="hidden-md-and-up"
            />
            <v-toolbar-title>
                <router-link to="/" tag="span" class="pointer">
                    <!--          <big>☭</big> КОММУНИЗМ <big>☭</big>-->
                    NEWS
                </router-link>
            </v-toolbar-title>
            <v-spacer/>
            <v-toolbar-items class="hidden-sm-and-down">
                <v-btn
                        :color="mainColor"
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

        <v-footer :color="mainColor" app class="text-center">
            <v-container fluid>
                <v-row>
                    <v-col cols="12">
                        <span class="white--text">News топ: &copy; {{(new Date).getFullYear() }}</span>
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
                // {title: 'Домой', icon: 'mdi-home', url: '/'},
                { title: 'Авторизация', icon: 'mdi-login', url: '/login' },
                { title: 'Регистрация', icon: 'mdi-target', url: '/registry' },
                { title: 'Новости', icon: 'mdi-newspaper', url: '/news' },
                { title: 'Добавить', icon: 'mdi-plus', url: '/append' },
                { title: 'Мои', icon: 'mdi-account-outline', url: '/my-news' }
            ]
        })
    }
</script>

<style scoped>
    .pointer {
        cursor: pointer;
    }
</style>