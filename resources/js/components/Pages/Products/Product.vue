<template>
  <v-container fluid class="mt-5 mb-5">
    <v-layout align-center justify-center>
      <v-flex xs12>
        <v-card class="elevation-12 py-2">
          <v-layout class="flex-wrap" align-flex-start justify-center>

            <v-flex xs12 md6>
              <v-card class="hover-effect">
                <v-img
                    :src="image"
                />
              </v-card>

              <swiper class="swiper mt-3" :options="swiperOption" style="height: 150px" v-if="images.length>0">
                <swiper-slide v-for="oneImage in images" :key="'a-' + oneImage" class="h-100">
                  <v-card class="card-info card-product d-flex flex-wrap flex-column px-1 h-100" color="#white" fill-height hover>
                    <img
                        :src="oneImage"
                        :alt="product.name"
                        class="w-100 h-100"
                        @error="popSlide()"
                    >
                  </v-card>
                </swiper-slide>
                <div class="swiper-button-prev" slot="button-prev"></div>
                <div class="swiper-button-next" slot="button-next"></div>
              </swiper>

              <v-flex xs12 class="align-items-center">
                <v-rating
                    v-model="product.ranking"
                    class="flex justify-content-center"
                    color="#FF9765"
                    background-color="#FF5F66"
                    size="40"
                    style="display:flex;"
                    dense
                    hover
                />
              </v-flex>
            </v-flex>

            <v-flex xs12 md5>
              <v-layout class="flex-wrap px-5" align-flex-start justify-start>
                <v-flex xs12>
                  <h1 class="text-lg-left text--secondary">{{product.name}}</h1>
                </v-flex>
                <v-flex xs12>
                  <a class="text-lg-left text--secondary subtitle-1 category-hover" :href="`/categories/${product.category.id}`">{{product.category.name}}</a>
                </v-flex>
<!--                <v-flex xs12>-->
<!--                  <v-rating v-model="product.ranking" color="#FF9765" background-color="#FF5F66" size="30"/>-->
<!--                </v-flex>-->
                <v-flex xs12 class="mt-3">
                  <div v-if="product.sales.length > 0">
                  <h2 class="display-1">
                    <span class="red--text font-weight-bold">{{ Math.floor((product.price - (product.price * product.sales[0].percent / 100))*100)/100 }} руб.</span>
                    <small class="text-decoration-line-through">{{product.price}} руб.</small>
                  </h2>
                </div>
                  <div v-if="product.sales.length <= 0">
                    <h2>
                      <span class="font-weight-bold red--text">{{product.price}}</span>
                    </h2>
                  </div>
                </v-flex>
                <v-flex xs12>
                  <v-btn dark color="#FF9765">
                    Купить в 1 клик
                  </v-btn>
                  <v-btn dark  color="#FF5F66">
                    В корзину
                  </v-btn>
                </v-flex>
                <v-flex xs12 class="mt-5">
                  <h3 class="text-sm-left text--secondary">Описание товара:</h3>
                  <p class="text-sm-left text--secondary">{{product.description}}</p>
                </v-flex>
                <v-flex xs12 class="mt-5">
                  <h3 class="text-sm-left text--secondary">Характеристики:</h3>
                  <div class="text-sm-left text--secondary">
                    <p>
                      <strong>Общий рейтинг:</strong>
                      {{ product.ranking }}
                    </p>
                  </div>
                  <div
                    class="text-sm-left text--secondary"
                    v-for="charact in product.product_characteristics"
                  >
                    <p>
                      <strong>{{charact.characteristic.name}}:</strong>
                      {{ charact.number_value || charact.string_value || (charact.boolean_value ? 'Да' : 'Нет') }}</p>
                  </div>
                </v-flex>
              </v-layout>
            </v-flex>
          </v-layout>
        </v-card>
      </v-flex>
    </v-layout>
  </v-container>
</template>

<script>
  export default {
    data () {
      return {
        id: null,
        image: '/images/empty-product.jpg',
        images: ['/images/empty-product.jpg'],
        product: { sales: {}, category: {}, product_characteristics: {} },
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
      this.swiperOption.slidesPerView = this.isByMobile() ? 1 : 3
      let id = Number.parseInt(this.$route.params.id)

      this.$store.dispatch('getOneEntity', { entity: 'products', id })
        .then((resp) => {
          this.product = resp.data
          this.images = []
          this.image = `/storage/products/${this.product.slug}-1.png`
          for (let i = 2; i <= 5; i++) {
            this.images.push(`/storage/products/${this.product.slug}-${i}.png`)
          }
        })
        .catch(err => {this.$router.push('/')})
    },
    methods: {
      initialize () {
      },
      isByMobile () {
        if (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
          return true
        } else {
          return false
        }
      },
      popSlide() {
        this.images.pop()
      },
    }
  }
</script>

<style>
  .text-up-line {
    text-shadow: 2px 0 2px #000000,
    0 2px 2px #000000,
    -2px 0 2px #000000,
    0 -2px 2px #000000;
  }

  .hover-effect:hover {
    -moz-box-shadow: 0 0 10px #000000;
    -webkit-box-shadow: 0 0 10px #000000;
    box-shadow: 0 0 10px #000000;
    -webkit-transition: all 0.5s ease;
    -moz-transition: all 0.5s ease;
    transition: all 0.5s ease;
  }

  .swiper-button-prev, .swiper-button-next {
    color: #FF9765;
    border: 2px solid #FF9765;
    border-radius: 100%;
    background-color: rgba(255,255,255,0.8);
    width: 30px;
    height: 30px;
  }
  .swiper-button-prev:hover, .swiper-button-next:hover {
    background-color: rgba(255,255,255,1);
  }
  .swiper-button-prev:after, .swiper-button-next:after {
    font-size: 20px;
    font-weight: bold;
  }
  .category-hover:hover {
    text-decoration: underline;
  }
</style>