<template>
  <v-container fluid class="mt-5 mb-5">
    <v-layout align-center justify-center>
      <v-flex xs12 md10>
        <v-card class="elevation-12 ">
          <v-toolbar dark class="gradient-project">
            <v-toolbar-title class="w-100 text-center">
              <h1 class="h2">Контакты</h1>
            </v-toolbar-title>
          </v-toolbar>
          <v-card-text>
            <v-flex xs12 mt-5>
              <h2 class="subheader-card title-admin text-center">Можете связаться с нами:</h2>
              <ul>
                <li>По почте: <strong><a href="mailto://maxtitovitch@mail.ru" target="_blank">maxtitovitch@mail.ru</a></strong></li>
                <li>По телефону: <strong><a href="tel:+375333038199" target="_blank" >+375333038199</a></strong></li>
                <li>Месседжеры: (<strong>WhatsApp</strong>, <strong>Telegram</strong>, <strong>Viber</strong>) по указанному выше номеру</li>
              </ul>
            </v-flex>
            <v-flex xs12 mt-5>
              <p>
                Главный офис и пункт самовывоза находятся по адресу (г.Минск, ул.Короля 4):
              </p>
            </v-flex>
            <iframe
                src="https://yandex.ru/map-widget/v1/?um=constructor%3Abbe5d4dabf502dad9d2f830d8e6be7b05bd2b8ea431f36001cef7ba954b61226&amp;source=constructor"
                width="100%"
                height="580"
                frameborder="0"

            ></iframe>
          </v-card-text>
        </v-card>
      </v-flex>
    </v-layout>
  </v-container>
</template>

<script>
  export default {
    data () {
      return {}
    },
  }
</script>

<style>
  strong a {
    color: rgba(0,0,0,.6) !important;
  }
</style>
