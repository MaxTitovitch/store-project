<template>
  <v-container grid-list-md text-xs-center class="mt-5">
    <v-layout row wrap>
      <v-flex v-for="statistic in statistics" :key="statistic.link" md4 xs12>
        <v-card class="card-info d-flex flex-wrap flex-column" color="#d0d0d0" fill-height hover>
          <v-card-title class="p-1 text-gray-head headline">{{statistic.name}}</v-card-title>
          <v-card-text class="p-1 text-gray-main display-4 flex-grow-1 d-flex justify-content-center align-items-center">
              {{statistic.value}}
          </v-card-text>
          <v-card-actions class="p-1 text-gray-footer headline">
            <a :href="statistic.link">Подробнее...</a>
          </v-card-actions>
        </v-card>
      </v-flex>
      <v-flex  xs12 mt-5>
        <h2 class="display-1 subheader-card title-admin">Самые рейтинговые товары</h2>
      </v-flex>
      <v-flex v-for="product in productsByRank" :key="'a-' + product.id" md3 xs12 mt-5 px-5 align-items-center>
        <v-card class="card-info card-product d-flex flex-wrap flex-column" color="#white" fill-height hover>
          <a :href="'/products/' + product.id" style="height: 100%; width: 100%; display: flex; justify-content: center; align-items: center">
            <img :src="'/storage/products/' + product.slug + '-1.png'" :alt="product.name" style="max-height: 100%; max-width: 100%">
          </a>
        </v-card>
        <a :href="'/products/' + product.id" style="width: 100%;" class="text-gray-footer title text-center">
          {{product.name}}
        </a>
      </v-flex>
      <v-flex  xs12 mt-5>
        <h2 class="display-1 subheader-card title-admin">Самые просматриваемые товары</h2>
      </v-flex>
      <v-flex v-for="product in productsByViews" :key="'b-' + product.id" md3 xs12 mt-5 px-5 align-items-center>
        <v-card class="card-info card-product d-flex flex-wrap flex-column" color="#white" fill-height hover>
          <a :href="'/products/' + product.id" style="height: 100%; width: 100%; display: flex; justify-content: center; align-items: center">
            <img :src="'/storage/products/' + product.slug + '-1.png'" :alt="product.name" style="max-height: 100%; max-width: 100%">
          </a>
        </v-card>
        <a :href="'/products/' + product.id" style="width: 100%;" class="text-gray-footer title text-center">
          {{product.name}}
        </a>
      </v-flex>
      <v-flex  xs12 mt-5>
        <h2 class="display-1 subheader-card title-admin">Самые покупаемые товары</h2>
      </v-flex>
      <v-flex v-for="product in productsByOrders" :key="'c-' + product.id" md3 xs12 mt-5 px-5 align-items-center>
        <v-card class="card-info card-product d-flex flex-wrap flex-column" color="#white" fill-height hover>
          <a :href="'/products/' + product.id" style="height: 100%; width: 100%; display: flex; justify-content: center; align-items: center">
            <img :src="'/storage/products/' + product.slug + '-1.png'" :alt="product.name" style="max-height: 100%; max-width: 100%">
          </a>
        </v-card>
        <a :href="'/products/' + product.id" style="width: 100%;" class="text-gray-footer title text-center">
          {{product.name}}
        </a>
      </v-flex>
    </v-layout>
  </v-container>
</template>

<script>
  export default {
    data () {
      return {
        id: 0,
        statistics: {},
        productsByRank: {},
        productsByViews: {},
        productsByOrders: {},
      }
    },
    created () {
      this.$store.dispatch('getStatistics', null)
        .then((resp) => {
          this.statistics = resp.data
        })
        .catch(err => this.$router.push('/'));
      this.$store.dispatch('getEntity', {entity: 'products', data: {limit: 4, order: 'ranking desc'}})
        .then((resp) => {
          this.productsByRank = resp.data
        })
        .catch(err => this.$router.push('/'));
      this.$store.dispatch('getEntity', {entity: 'products', data: {limit: 4, withCount: 'views', order: 'views_count desc'}})
        .then((resp) => {
          this.productsByViews = resp.data
        })
        .catch(err => this.$router.push('/'));
      this.$store.dispatch('getEntity', {entity: 'products', data: {limit: 4, withCount: 'orders', order: 'orders_count desc'}})
        .then((resp) => {
          this.productsByOrders = resp.data
        })
        .catch(err => this.$router.push('/'));
    },
  }
</script>

<style scoped>
  .card-info {
    height: 200px;
  }

  .card-info > div {
    width: 100%;
  }

  .text-gray-head {
    color: #727271 !important;
    border-bottom: 2px solid white;
    border-radius: 0 !important;
  }

  .text-gray-footer {
    color: #727271 !important;
    border-top: 2px solid white;
    border-radius: 0 !important;
  }

  .text-gray-footer a {
    color: #727271 !important;
  }

  .text-gray-main {
    font-size: 30px;
    color: white !important;
    text-shadow: 1px 0 1px black,
    0 1px 1px black,
    -1px 0 1px black,
    0 -1px 1px black;
  }

  .subheader-card {
    color: #646464;
    width: 100%;
    border-bottom: 1px solid #646464;
  }

  .card-product {
    border: 1px solid #646464 !important;
    height: 250px;
  }

  @media screen  and (max-device-width: 960px) {
    .title-admin {
      text-align: center;
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