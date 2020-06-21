<template>
  <v-container grid-list-md text-xs-center class="mt-5 mb-5">
    <v-layout row wrap>
      <v-flex xs12 mt-5 px-5 align-items-center>
        <v-carousel
            cycle
            show-arrows-on-hover
            interval="3000"
        >
          <v-carousel-item
              v-for="product in productsByViews"
              :key="'b-' + product.id"
              :src="'/storage/products/' + product.slug + '-1.png'"
              :to="'products' + product.id"
              reverse-transition="fade-transition"
          />
        </v-carousel>
      </v-flex>
      <v-flex  xs12 mt-5>
        <h2 class="display-1 subheader-card title-admin">Новинки в магазине</h2>
      </v-flex>
      <v-flex xs12 mt-5 px-5 align-items-center>
        <swiper class="swiper" :options="swiperOption">
          <swiper-slide
              v-for="product in newProducts"
              :key="'a-' + product.id"
          >
            <v-card class="card-info card-product d-flex flex-wrap flex-column" color="#white" fill-height hover>
              <a :href="'/products/' + product.id" style="height: 100%; width: 100%; display: flex; justify-content: center; align-items: center">
                <img :src="'/storage/products/' + product.slug + '-1.png'" :alt="product.name" class="card-image">
              </a>
              <v-card-actions>
                <a :href="'/products/' + product.id" style="width: 100%;" class="text-gray-footer title text-center">
                  {{product.name}}
                </a>
              </v-card-actions>
            </v-card>

          </swiper-slide>
          <div class="swiper-button-prev" slot="button-prev"></div>
          <div class="swiper-button-next" slot="button-next"></div>
        </swiper>
      </v-flex>
      <v-flex  xs12 mt-5>
        <h2 class="display-1 subheader-card title-admin">Самые рейтинговые товары</h2>
      </v-flex>
      <v-flex xs12 mt-5 px-5 align-items-center>
        <swiper class="swiper" :options="swiperOption">
          <swiper-slide
              v-for="product in productsByRank"
              :key="'a-' + product.id"
          >
            <v-card class="card-info card-product d-flex flex-wrap flex-column" color="#white" fill-height hover>
              <a :href="'/products/' + product.id" style="height: 100%; width: 100%; display: flex; justify-content: center; align-items: center">
                <img :src="'/storage/products/' + product.slug + '-1.png'" :alt="product.name" class="card-image">
              </a>
              <v-card-actions>
                <a :href="'/products/' + product.id" style="width: 100%;" class="text-gray-footer title text-center">
                  {{product.name}}
                </a>
              </v-card-actions>
            </v-card>

          </swiper-slide>
          <div class="swiper-button-prev" slot="button-prev"></div>
          <div class="swiper-button-next" slot="button-next"></div>
        </swiper>
      </v-flex>
      <v-flex  xs12 mt-5>
        <h2 class="display-1 subheader-card title-admin">Самые покупаемые товары</h2>
      </v-flex>
      <v-flex xs12 mt-5 px-5 align-items-center>
        <swiper class="swiper" :options="swiperOption">
          <swiper-slide
              v-for="product in productsByOrders"
              :key="'a-' + product.id"
          >
            <v-card class="card-info card-product d-flex flex-wrap flex-column" color="#white" fill-height hover>
              <a :href="'/products/' + product.id" style="height: 100%; width: 100%; display: flex; justify-content: center; align-items: center">
                <img :src="'/storage/products/' + product.slug + '-1.png'" :alt="product.name" class="card-image">
              </a>
              <v-card-actions>
                <a :href="'/products/' + product.id" style="width: 100%;" class="text-gray-footer title text-center">
                  {{product.name}}
                </a>
              </v-card-actions>
            </v-card>

          </swiper-slide>
          <div class="swiper-button-prev" slot="button-prev"></div>
          <div class="swiper-button-next" slot="button-next"></div>
        </swiper>
      </v-flex>
    </v-layout>
  </v-container>
</template>

<script>
  import { Swiper, SwiperSlide } from 'vue-awesome-swiper'
  import 'swiper/css/swiper.css'
  export default {
    components: {
      Swiper,
      SwiperSlide
    },
    data () {
      return {
        id: 0,
        productsByRank: {},
        productsByViews: {},
        productsByOrders: {},
        newProducts: {},
        swiperOption: {
          slidesPerView: 3,
          spaceBetween: 30,
          loop: true,
          navigation: {
            nextEl: '.swiper-button-next',
            prevEl: '.swiper-button-prev'
          },
          autoplay: {
            delay: 2500,
            disableOnInteraction: false
          },
        }
      }
    },
    created () {
      this.swiperOption.slidesPerView = this.isByMobile() ? 1 : 4;
      this.$store.dispatch('getEntity', {entity: 'products', data: {limit: 8, order: 'ranking desc'}})
        .then((resp) => {
          this.productsByRank = resp.data
        })
        .catch(err => this.$router.push('/'));
      this.$store.dispatch('getEntity', {entity: 'products', data: {limit: 8, withCount: 'views', order: 'views_count desc'}})
        .then((resp) => {
          this.productsByViews = resp.data
        })
        .catch(err => this.$router.push('/'));
      this.$store.dispatch('getEntity', {entity: 'products', data: {limit: 8, withCount: 'orders', order: 'orders_count desc'}})
        .then((resp) => {
          this.productsByOrders = resp.data
        })
        .catch(err => this.$router.push('/'));
      this.$store.dispatch('getEntity', {entity: 'products', data: {limit: 8, order: 'id desc'}})
        .then((resp) => {
          this.newProducts = resp.data
        })
        .catch(err => this.$router.push('/'));
    },
    methods: {
      isByMobile (){
        if (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
          return true;
        } else {
          return false;
        }
      }
    }
  }
</script>

<style scoped>
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
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
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

  .card-image {
    height: 200px;
    width: 100%;
  }

  @media screen  and (max-device-width: 960px) {
    .card-image {
      height: 300px;
      width: 100%;
    }
  }

  .swiper-button-prev, .swiper-button-next {
    color: #FF9765;
  }
  .swiper-button-prev:after, .swiper-button-next:after {
    font-size: 30px;
    font-weight: bold;
  }

  .card-product {
    border: 1px solid!important;
  }

  .image-card {
    border-bottom: 1px solid!important;
    height: 100%;
    width: 100%;
    display: flex;
    justify-content: center;
    align-items: center
  }
</style>
