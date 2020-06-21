<template>
  <v-container fluid class="mt-5 mb-5">
    <v-layout align-center justify-center>
      <v-flex xs12 md10>
        <v-card class="elevation-12 ">
          <v-toolbar dark class="gradient-project">
            <v-toolbar-title class="w-100 text-center">
              <h1 class="h2">Оплата и доставка</h1>
            </v-toolbar-title>
          </v-toolbar>
          <v-card-text>
            <v-img
                src="/images/pay.png"
            />
            <v-flex xs12 mt-5>
              <h2 class="subheader-card title-admin text-center">Оплата</h2>
              <p>
                Оплатить товары в нашем магазине можно различными способами.
                Способы оплаты:
              </p>
              <ul>
                <li>Наличной картой курьеру</li>
                <li>Банковской картой курьеру</li>
                <li>Банковской картой онлайн</li>
              </ul>
            </v-flex>
            <v-img
                src="/images/delivery.png"
            />
            <v-flex xs12 mt-5>
              <h2 class="subheader-card title-admin text-center">Доставка</h2>
              <p>
                Получить товары в нашем магазине можно различными способами.
                Способы доставки:
              </p>
              <ul>
                <li>Доставка курьеровм (г.Минск)</li>
                <li>Доставка почтой РБ (Беларусь)</li>
                <li>Доставка за рубеж (Согласовывается отдельно)</li>
                <li>Самовывоз (г.Минск ул.Короля 4):</li>
              </ul>
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
