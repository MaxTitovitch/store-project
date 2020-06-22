<template>
  <v-container fluid class="mt-5 mb-5">
    <v-layout align-center justify-center>
      <v-flex xs10>
        <v-card class="elevation-12 ">
          <v-toolbar dark class="gradient-project">
            <v-toolbar-title class="w-100 text-center">Акции: {{ sale.name }}</v-toolbar-title>
          </v-toolbar>
          <v-img
              class="white--text align-end"
              height="200px"
              :src="image"
          >
            <v-card-title class="text-up-line">
              <h1>{{ sale.name }}</h1>
            </v-card-title>
          </v-img>


          <v-card-text class="text--primary mt-1">
            <div v-html="sale.description"></div>
          </v-card-text>

          <v-card-text>
            <v-list subheader class="product-list">
              <h2 class="text--secondary">Скидки на товары</h2>

              <v-list-item
                  v-for="(item, index) of sale.products"
                  :key="'products' + item.id"
                  :to="'/products/' + item.id"
                  three-line
              >
                <v-list-item-avatar height="150px" width="150px" style="border-radius: 0!important;">
                  <v-img :src="'/storage/products/' + item.slug + '-1.png'"/>
                </v-list-item-avatar>

                <v-list-item-content>
                  <strong>
                    <v-list-item-title class="product-list-content" v-text="item.name"/>
                  </strong>
                  <v-list-item-content class="post-text product-list-content" v-html="item.description"/>
                  <v-list-item-content>
                    <strong>
                      <big class="red--text">{{ Math.floor((item.price - (item.price * sale.sales[index].percent / 100))*100)/100 }}</big>
                      <span class="text-decoration-line-through">{{item.price}}</span>
                    </strong>
                      <big>{{ `(${sale.sales[index].date_start} - ${sale.sales[index].date_end})` }}</big>
                  </v-list-item-content>
                </v-list-item-content>

                <v-list-item-action>
                  <v-btn color="red" x-large fab dark>
                        <span class="text-decoration-none">
                          -{{  sale.sales[index].percent }}%
                        </span>
                  </v-btn>
                </v-list-item-action>
              </v-list-item>
            </v-list>
          </v-card-text>

          <v-card-actions>
            <v-btn color="#FF9765" text small>
              {{ sale.created_at.substr(0, 10) }}
            </v-btn>
          </v-card-actions>
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
        image: '/images/sale.png',
        sale: { sales: {}, products: {}, created_at: '' },
      }
    },
    created () {
      let id = Number.parseInt(this.$route.params.id)

      this.$store.dispatch('getOneEntity', { entity: 'sale-categories', id })
        .then((resp) => {
          this.sale = resp.data
          console.log(this.sale)
        })
        .catch(err => this.$router.push('/'))
    },
  }
</script>

<style>
  .text-up-line {
    text-shadow: 2px 0 2px #000000,
    0 2px 2px #000000,
    -2px 0 2px #000000,
    0 -2px 2px #000000;
  }

  a:hover {
    text-decoration: none;
  }

  .product-list a:hover .product-list-content {
    text-decoration: underline;
  }

  .text-decoration-line-through {
    text-decoration: line-through;
  }
</style>