<template>
  <v-app id="inspire">
    <v-navigation-drawer v-model="drawerAdmin" app temporary dark>
      <v-list>
        <v-list-item to="/admin" style="text-decoration: none;">
          <v-list-item-icon>
            <v-icon x-large left>mdi-account-outline</v-icon>
          </v-list-item-icon>
          <v-list-item-content>
            <v-list-item-title class="h2">Панель</v-list-item-title>
            <v-list-item-title class="h2">Администратора</v-list-item-title>
          </v-list-item-content>
        </v-list-item>
        <v-list-item
            link v-for="link in adminLinkList"
            :key="link.title"
            :to="link.url"
            dark
        >
          <v-list-item-content>
            <v-list-item-title>{{ link.title }}</v-list-item-title>
          </v-list-item-content>
        </v-list-item>
      </v-list>
    </v-navigation-drawer>

    <v-navigation-drawer v-model="drawer" app temporary class="gradient-project">
      <v-list>
        <v-toolbar-title class="hidden-md-and-up">
          <router-link to="/">
            <v-img src="/Logo-big.png" class="logo-image logo-image-small"/>
          </router-link>
        </v-toolbar-title>
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

        <v-list-item
            class="gradient-project"
        >
          <v-text-field
              hide-details
              prepend-icon="mdi-magnify"
              label="Искать..."
              color="black"
              regular
          />
        </v-list-item>


        <v-expansion-panels class="hidden-md-and-up" expandable>
          <v-expansion-panel
              class="gradient-project"
          >
            <v-expansion-panel-header>
              {{ isLoggedName }}
              <v-icon x-large>mdi-account-circle-outline</v-icon>
            </v-expansion-panel-header>
            <v-expansion-panel-content>
              <v-list dark class="gradient-project" style="padding: 0px">
                <v-list-item
                    link v-for="link in userLinkList"
                    :key="link.title"
                    :to="link.url"
                    class="gradient-project"
                    light
                    style="border: 1px solid #222222"
                    v-if="!isLoggedInForVerify"
                >
                  <v-list-item-icon>
                    <v-icon left>{{ link.icon }}</v-icon>
                  </v-list-item-icon>
                  <v-list-item-content>
                    <v-list-item-title>{{ link.title }}</v-list-item-title>
                  </v-list-item-content>
                </v-list-item>
                <v-list-item
                    to="/admin"
                    class="gradient-project"
                    light
                    style="border: 1px solid #222222"
                    v-if="isLoggedInForVerify && isLoggedRole === 'Главный администратор' || isLoggedRole === 'Администратор'"
                >
                  <v-list-item-icon>
                    <v-icon left>mdi-account-details-outline</v-icon>
                  </v-list-item-icon>
                  <v-list-item-content>
                    <v-list-item-title>Админка</v-list-item-title>
                  </v-list-item-content>
                </v-list-item>
                <v-list-item
                    to="/personal"
                    class="gradient-project"
                    light
                    style="border: 1px solid #222222"
                    v-if="isLoggedInForVerify"
                >
                  <v-list-item-icon>
                    <v-icon left>mdi-account-details-outline</v-icon>
                  </v-list-item-icon>
                  <v-list-item-content>
                    <v-list-item-title>Кабинет</v-list-item-title>
                  </v-list-item-content>
                </v-list-item>
                <v-list-item
                    class="gradient-project"
                    light
                    style="border: 1px solid #222222"
                    v-on:click="logout"
                    v-if="isLoggedInForVerify"
                >
                  <v-list-item-icon>
                    <v-icon left>mdi-exit-run</v-icon>
                  </v-list-item-icon>
                  <v-list-item-content>
                    <v-list-item-title>Выйти</v-list-item-title>
                  </v-list-item-content>
                </v-list-item>
              </v-list>
            </v-expansion-panel-content>
          </v-expansion-panel>
        </v-expansion-panels>
        <v-row>
          <v-btn href="tel:+375333038199" text target="_blank" color="white">+375333038199</v-btn>
        </v-row>

        <v-row>
          <v-btn href="mailto://maxtitovitch@mail.ru" text target="_blank" color="white">maxtitovitch@mail.ru</v-btn>
        </v-row>
      </v-list>
    </v-navigation-drawer>

    <v-system-bar height="20px" class="background-clear hidden-sm-and-down"
                  style="position:fixed; left: 0; right: 0; z-index: 4">
      <v-btn
          text
          :key="'phone'"
          href="tel:+375333038199"
          target="_blank"
          class="font-weight-bold"
      >
        +375333038199
      </v-btn>
      <v-btn
          text
          :key="'mail'"
          href="mailto://maxtitovitch@mail.ru"
          target="_blank"
          class="font-weight-bold"
      >
        maxtitovitch@mail.ru
      </v-btn>
      <v-spacer/>
      <v-btn
          text v-for="link in storeLinkList"
          :key="link.title"
          :to="link.url"
          class="font-weight-bold"
      >
        {{ link.title }}
      </v-btn>
    </v-system-bar>

    <v-app-bar app class="gradient-project main-header" dark>
      <v-app-bar-nav-icon
          @click.stop="drawerAdmin = !drawerAdmin"
          style="background: black; border-radius: 5px;"
          class="mr-3"
          v-if="isLoggedInForVerify && isLoggedRole === 'Главный администратор' || isLoggedRole === 'Администратор'"
      />

      <v-app-bar-nav-icon
          @click.stop="drawer = !drawer"
          class="hidden-md-and-up"
      />

      <v-toolbar-title class="hidden-sm-and-down">
        <router-link to="/">
          <v-img src="/Logo-big.png" class="logo-image"/>
        </router-link>
      </v-toolbar-title>
      <v-spacer/>
      <v-toolbar-title class="hidden-md-and-up">
        <router-link to="/">
          <v-img src="/Logo-big.png" class="logo-image logo-image-small"/>
        </router-link>
      </v-toolbar-title>


      <v-toolbar-items class="hidden-sm-and-down">
        <v-btn
            class="gradient-project"
            elevation="0"
            link v-for="link in linkList"
            :key="link.title"
            :to="link.url"
            style="text-decoration: none;"
        >
          <v-icon left>{{ link.icon }}</v-icon>
          {{ link.title }}
        </v-btn>
        <v-list-item
            class="gradient-project"
        >
          <v-text-field
              hide-details
              prepend-icon="mdi-magnify"
              label="Искать..."
              color="white"
              regular
          />
        </v-list-item>
        <v-menu
            :close-on-click="true"
            :close-on-content-click="true"
            :offset-y="true"
        >
          <template v-slot:activator="{ on, attrs }">
            <v-btn
                color="primary"
                dark
                v-bind="attrs"
                v-on="on"
                class="gradient-project"
            >
              {{ isLoggedName }}
              <v-icon x-large>mdi-account-circle-outline</v-icon>
            </v-btn>
          </template>
          <v-list light class="gradient-project" style="padding: 0px">
            <v-list-item
                link v-for="link in userLinkList"
                :key="link.title"
                :to="link.url"
                class="gradient-project"
                dark
                style="border: 1px solid white"
                v-if="!isLoggedInForVerify"
            >
              <v-list-item-icon>
                <v-icon left>{{ link.icon }}</v-icon>
              </v-list-item-icon>
              <v-list-item-content>
                <v-list-item-title>{{ link.title }}</v-list-item-title>
              </v-list-item-content>
            </v-list-item>
            <v-list-item
                to="/admin"
                class="gradient-project"
                dark
                style="border: 1px solid white"
                v-if="isLoggedInForVerify && isLoggedRole === 'Главный администратор' || isLoggedRole === 'Администратор'"
            >
              <v-list-item-icon>
                <v-icon left>mdi-account-details-outline</v-icon>
              </v-list-item-icon>
              <v-list-item-content>
                <v-list-item-title>Админка</v-list-item-title>
              </v-list-item-content>
            </v-list-item>
            <v-list-item
                to="/personal"
                class="gradient-project"
                dark
                style="border: 1px solid white"
                v-if="isLoggedInForVerify"
            >
              <v-list-item-icon>
                <v-icon left>mdi-account-details-outline</v-icon>
              </v-list-item-icon>
              <v-list-item-content>
                <v-list-item-title>Личный кабинет</v-list-item-title>
              </v-list-item-content>
            </v-list-item>
            <v-list-item
                class="gradient-project"
                dark
                style="border: 1px solid white"
                v-on:click="logout"
                v-if="isLoggedInForVerify"
            >
              <v-list-item-icon>
                <v-icon left>mdi-exit-run</v-icon>
              </v-list-item-icon>
              <v-list-item-content>
                <v-list-item-title>Выйти</v-list-item-title>
              </v-list-item-content>
            </v-list-item>
          </v-list>
        </v-menu>
      </v-toolbar-items>
    </v-app-bar>

    <v-content>
      <div v-if="isLoading" class="loading-image"></div>
        <router-view :style="isLoading ? 'opacity: 0' : ''"></router-view>
    </v-content>

    <v-footer app class="text-center gradient-project">
      <v-container fluid>
        <v-row>
          <v-col cols="4" class="hidden-sm-and-down">

            <v-row>
              <v-btn href="tel:+375333038199" text target="_blank" color="white">+375333038199</v-btn>
            </v-row>

            <v-row>
              <v-btn href="mailto://maxtitovitch@mail.ru" text target="_blank" color="white">maxtitovitch@mail.ru
              </v-btn>
            </v-row>
          </v-col>
          <v-col cols="4" class="hidden-sm-and-down">
            <span
                class="white--text">НаНастолке<sup>&copy;</sup> {{(new Date).getFullYear() }} - Все права защищены</span>
          </v-col>
          <v-col cols="4" class="hidden-sm-and-down">
            <v-toolbar-title>
              <router-link to="/">
                <v-img src="/Logo-big.png" class="logo-image" style="margin: auto"/>
              </router-link>
            </v-toolbar-title>
          </v-col>
          <v-col cols="12" class="hidden-md-and-up">
            <span
                class="white--text">НаНастолке<sup>&copy;</sup> {{(new Date).getFullYear() }} - Все права защищены</span>
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
      drawerAdmin: false,
      mainColor: 'red darken-4',
      linkList: [
        { title: 'Акции', icon: 'mdi-sale', url: '/sales' },
        { title: 'Товары', icon: 'mdi-cart-outline', url: '/categories' },
        { title: 'Статьи', icon: 'mdi-post-outline', url: '/pages' },
        { title: 'Топы', icon: 'mdi-medal-outline', url: '/tops' }
      ],
      userLinkList: [
        { title: 'Вход', icon: 'mdi-login-variant', url: '/login' },
        { title: 'Регистрация', icon: 'mdi-account-plus-outline', url: '/register' },
        // { title: 'Выход', icon: 'mdi-exit-run', url: '/logout' },
      ],
      storeLinkList: [
        { title: 'О компании', url: '/about' },
        { title: 'Оплата и доставка', url: '/delivery' },
        { title: 'Контакты', url: '/contacts' },
      ],
      adminLinkList: [
        { title: 'Товары', url: '/admin/products' },
        { title: 'Категории', url: '/admin/categories' },
        { title: 'Заказы', url: '/admin/orders' },
        { title: 'Характеристики', url: '/admin/characteristics' },
        { title: 'Посты', url: '/admin/posts' },
        { title: 'Акции', url: '/admin/sale-categories' },
        { title: 'Теги', url: '/admin/tags' },
        { title: 'Комментарии', url: '/admin/comments' },
        { title: 'Адреса', url: '/admin/addresses' },
        { title: 'Пользователи', url: '/admin/users' },
        { title: 'Массовая загрузка', url: '/admin/mass-upload' },
      ],
    }),
    methods: {
      logout () {
        this.$store.dispatch('logout').then(() => {
          if (this.$router.history.current.fullPath !== '/')
            this.$router.push('/')
        })
      }
    },
    computed: {
      isLoggedIn: function () { return this.$store.getters.isLoggedIn },
      isLoggedInForVerify: function () { return this.$store.getters.isLoggedInForVerify },
      isLoggedRole: function () { return this.$store.getters.isLoggedRole },
      isLoggedName: function () { return this.$store.getters.isLoggedName },
      isLoading: function () {return this.$store.getters.isLoading }
    },
    created () {
      this.$http.interceptors.response.use(undefined, function (err) {
        return new Promise(function (resolve, reject) {
          if (err.status === 401 && err.config && !err.config.__isRetryRequest) {
            this.$store.dispatch('logout')
          }
          throw err
        })
      })
    },
  }
</script>

<style>
  .pointer {
    cursor: pointer;
  }

  body, main {
    background-image: linear-gradient(rgba(255,255,255,.6), rgba(255,255,255,.6)), url(/fon.png);
    background-size: 100%;
    background-repeat: repeat;
  }

  .gradient-project {
    background: linear-gradient(180deg, #FF5F66, #FF9765);
  }

  .gradient-project-text {
    color: #FF9765;
  }

  .logo-image {
    height: 62px;
    width: 220px;
    background-size: auto 100%;
  }

  .logo-image-small {
    margin: auto;
    background-size: auto 100%;
    height: 40px;
    width: 150px;
  }

  .background-clear {
    background-image: linear-gradient(rgba(255,255,255,.6), rgba(255,255,255,.6)), url(/fon.png);
    background-size: 100%;
    background-repeat: repeat;
  }

  *::-webkit-scrollbar-track {
    -webkit-box-shadow: inset 0 0 6px rgba(0, 0, 0, 0.3);
    border-radius: 10px;
    background-color: #F5F5F5;
  }

  *::-webkit-scrollbar {
    width: 10px;
    background-color: #F5F5F5;
  }

  *::-webkit-scrollbar-thumb {
    border-radius: 10px;
    -webkit-box-shadow: inset 0 0 6px rgba(0, 0, 0, .3);
    background-color: #9E9897;
  }

  .main-header {
    margin-top: 20px !important;
  }

  @media only screen and (max-width: 960px) {
    .main-header {
      margin-top: 0 !important;
    }
  }

  .loading-image {
    width: 100%;
    height: 100%;
    background-image: url(/loading.gif);
    background-position: center;
    background-size: 50px;
    background-color: rgba(0,0,0,0.5);
    position: absolute;
    left: 0;
    top: 0;
  }
</style>