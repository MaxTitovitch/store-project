<template>
  <v-container fluid class="mt-5 mb-5">
    <v-layout align-center justify-center class="flex-wrap">
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
                  <v-card class="card-info card-product d-flex flex-wrap flex-column px-1 h-100" color="#white"
                          fill-height hover>
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
                    v-if="userRanking == null"
                    v-model="product.ranking"
                    class="flex justify-content-center"
                    color="#FF9765"
                    background-color="#FF5F66"
                    size="40"
                    style="display:flex;"
                    dense
                    hover
                    @input="changeRank"
                    :readonly="isLoggedId > 0 ? false : true"
                />
                <v-rating
                    v-if="userRanking !== null"
                    v-model="userRanking.point"
                    class="flex justify-content-center"
                    color="#FF9765"
                    background-color="#FF5F66"
                    size="40"
                    style="display:flex;"
                    dense
                    hover
                    @input="changeRank"
                />
              </v-flex>
            </v-flex>

            <v-flex xs12 md5>
              <v-layout class="flex-wrap px-5" align-flex-start justify-start>
                <v-flex xs12>
                  <h1 class="text-lg-left text--secondary">{{product.name}}</h1>
                </v-flex>
                <v-flex xs12>
                  <a class="text-lg-left text--secondary subtitle-1 category-hover"
                     :href="`/categories/${product.category.id}`">{{product.category.name}}</a>
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
                      <span class="font-weight-bold red--text">{{product.price}} руб.</span>
                    </h2>
                  </div>
                </v-flex>
                <v-flex xs12>
                  <v-btn dark color="#FF9765" @click="goToPay()">
                    Купить в 1 клик
                  </v-btn>
                  <v-btn dark color="#FF5F66" @click="addToCart()">
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

      <v-flex xs12 mt-5>
        <h2 class="display-1 subheader-card title-admin">Вам может быть интересно:</h2>
      </v-flex>
      <v-flex xs12 mt-5 px-5 align-items-center>
        <swiper class="swiper" :options="swiperOption">
          <swiper-slide
              v-for="product in bestProducts"
              :key="'bp-' + product.id + Math.random() "
          >
            <v-card class="card-info card-product d-flex flex-wrap flex-column" color="#white" fill-height hover>
              <a :href="'/products/' + product.id"
                 style="height: 100%; width: 100%; display: flex; justify-content: center; align-items: center">
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

      <v-flex xs12 mt-5 px-5 align-items-center>

        <v-list subheader class="product-list" >
          <h2 class="text--secondary">Комментарии</h2>
          <v-list-item two-line>

            <v-list-item-content>
            </v-list-item-content>

            <v-list-item-action>
              <v-btn color="red" x-large fab dark>
                  <span class="text-decoration-none">
                    Save
                  </span>
              </v-btn>
            </v-list-item-action>
          </v-list-item>

          <v-list-item
              v-for="(item, index) of comments"
              :key="'products' + item.id"
              :to="'/users/' + item.user.id"
              two-line
          >
            <v-list-item-avatar height="150px" width="150px" style="border-radius: 0!important;">
              <v-img :src="'/storage/users/' + item.user.slug + '.png'"/>
            </v-list-item-avatar>

            <v-list-item-content>
              <strong>
                <v-list-item-title class="product-list-content" v-text="item.user.name + ' ' + item.user.last_name"/>
              </strong>
              <v-list-item-content class="post-text product-list-content" v-html="item.text"/>
            </v-list-item-content>

            <v-list-item-action>
              <v-btn color="red" x-large fab dark>
                  <span class="text-decoration-none">
                    L
                  </span>
              </v-btn>
            </v-list-item-action>
          </v-list-item>
        </v-list>
      </v-flex>
    </v-layout>
  </v-container>
</template>

<script>
  export default {
    data () {
      return {
        id: null,
        bestProducts: [],
        comments: [],
        userRanking: null,
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
    computed: {
      isLoggedId: function () { return this.$store.getters.isLoggedId },
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
          if(this.isLoggedId)
            this.$store.dispatch('getEntity', { entity: 'rankings', data: { where: `product_id = ${this.product.id} AND user_id = ${this.isLoggedId}`} })
              .then((resp) => {
                this.userRanking = resp.data[0] || null;
              })
              .catch(err => {this.$router.push('/')})


          this.page = Number.parseInt(this.$route.query.page) || 1;
          let offset = (this.page - 1) * 10;
          let data = { where: `entity_id = ${this.product.id} AND entity_type="product"`, with: 'user', offset, limit: 10, order: 'id desc'};
          this.$store.dispatch('getEntity', { entity: 'comments', data})
            .then((resp) => {
              this.comments = resp.data
            })
            .catch(err => {this.$router.push('/')})
        })
        .catch(err => {this.$router.push('/')})

      this.$store.dispatch('getEntity', { entity: 'products', data: { 'user-products': 'true' } })
        .then((resp) => {
          this.bestProducts = resp.data
        })
        .catch(err => {this.$router.push('/')})

    },
    methods: {
      isByMobile () {
        if (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
          return true
        } else {
          return false
        }
      },
      popSlide () {
        this.images.pop()
      },
      addToCart () {
        this.$store.dispatch('pushProductToCart', this.product)
      },
      goToPay () {
        this.addToCart()
        this.$router.push('/order');
      },
      changeRank(value) {
        let ranking = {
          point: value,
          user_id: this.isLoggedId,
          product_id: this.product.id
        }
        if (this.userRanking === null) {
          this.$store.dispatch('postEntity', { entity: 'rankings/store-user', data: ranking })
            .then((resp) => {})
            .catch(err => {this.error = 'Ошибка добавления'})
        } else {
          console.log(11)
          this.$store.dispatch('putEntity', { entity: 'rankings/update-user', data: ranking, id: this.userRanking.id })
            .then((resp) => {})
            .catch(err => {this.error = 'Ошибка добавления'})
        }
      }
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
    background-color: rgba(255, 255, 255, 0.8);
    width: 30px;
    height: 30px;
  }

  .swiper-button-prev:hover, .swiper-button-next:hover {
    background-color: rgba(255, 255, 255, 1);
  }

  .swiper-button-prev:after, .swiper-button-next:after {
    font-size: 20px;
    font-weight: bold;
  }

  .category-hover:hover {
    text-decoration: underline;
  }

  /*Customer's products*/

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

  .card-product {
    border: 1px solid !important;
  }

  .image-card {
    border-bottom: 1px solid !important;
    height: 100%;
    width: 100%;
    display: flex;
    justify-content: center;
    align-items: center
  }
</style>